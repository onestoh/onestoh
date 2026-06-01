<?php
namespace App\Services;

use App\Models\Booking;
use App\Models\CorporateAccount;
use App\Models\CorporateMember;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class CorporateService
{
    /**
     * Create a new corporate account owned by the given user.
     */
    public function createAccount(User $owner, array $data): CorporateAccount
    {
        return CorporateAccount::create(array_merge($data, ['owner_id' => $owner->id]));
    }

    /**
     * Invite a user by email to the corporate account with a given role.
     * Creates a User record if one does not yet exist.
     */
    public function inviteMember(CorporateAccount $account, string $email, string $role): CorporateMember
    {
        $user = User::firstOrCreate(['email' => $email], [
            'name'     => explode('@', $email)[0],
            'password' => bcrypt(str()->random(16)),
        ]);

        $member = CorporateMember::updateOrCreate(
            ['corporate_account_id' => $account->id, 'user_id' => $user->id],
            ['role' => $role, 'is_active' => true]
        );

        $user->notify(new \App\Notifications\CorporateInviteNotification($account, $role));

        return $member;
    }

    /**
     * Approve a corporate booking — validates the approver has permission.
     */
    public function approveMemberBooking(Booking $booking, User $approver): void
    {
        $account = $booking->corporateAccount;
        abort_unless($account, 422, 'Booking has no corporate account.');

        $member = CorporateMember::where('corporate_account_id', $account->id)
            ->where('user_id', $approver->id)
            ->where('can_approve_bookings', true)
            ->firstOrFail();

        $booking->update([
            'corporate_approved_at' => now(),
            'corporate_approved_by' => $approver->id,
            'status'                => 'pending_payment',
        ]);

        $booking->client->notify(new \App\Notifications\BookingApprovedNotification($booking));
    }

    /**
     * Return a summary invoice array for a given month (format: Y-m).
     * Also generates and stores a PDF invoice.
     */
    public function getMonthlyInvoice(CorporateAccount $account, string $month): array
    {
        [$year, $mon] = explode('-', $month);
        $from = Carbon::createFromDate($year, $mon, 1)->startOfMonth();
        $to   = $from->copy()->endOfMonth();

        $bookings = Booking::where('corporate_account_id', $account->id)
            ->whereIn('status', ['completed', 'active'])
            ->whereBetween('starts_at', [$from, $to])
            ->with(['asset', 'client'])
            ->get();

        $total = $bookings->sum('total_price');

        $lineItems = $bookings->map(fn ($b) => [
            'booking_id'  => $b->id,
            'asset_name'  => optional($b->asset)->name,
            'client_name' => optional($b->client)->name,
            'starts_at'   => $b->starts_at,
            'ends_at'     => $b->ends_at,
            'amount'      => $b->total_price,
            'po_ref'      => $b->po_reference,
        ])->toArray();

        // Store stub PDF
        $pdfPath = 'corporate-invoices/' . $account->id . '/' . $month . '.pdf';
        Storage::put($pdfPath, 'PDF_INVOICE_PLACEHOLDER');

        return [
            'month'       => $month,
            'company'     => $account->company_name,
            'total'       => round((float) $total, 2),
            'line_items'  => $lineItems,
            'pdf_path'    => $pdfPath,
        ];
    }

    /**
     * Check whether a booking amount is within both account-level and member-level spend limits.
     */
    public function checkSpendLimit(CorporateAccount $account, User $member, float $amount): bool
    {
        // Per-booking limit
        if ($account->per_booking_limit && $amount > (float) $account->per_booking_limit) {
            return false;
        }

        $corporateMember = CorporateMember::where('corporate_account_id', $account->id)
            ->where('user_id', $member->id)
            ->first();

        if ($corporateMember && $corporateMember->individual_spend_limit
            && $amount > (float) $corporateMember->individual_spend_limit) {
            return false;
        }

        // Monthly aggregate
        if ($account->monthly_spend_limit) {
            $spentThisMonth = Booking::where('corporate_account_id', $account->id)
                ->whereIn('status', ['confirmed', 'active', 'completed', 'pending_payment'])
                ->whereBetween('starts_at', [
                    now()->startOfMonth(),
                    now()->endOfMonth(),
                ])
                ->sum('total_price');

            if ((float) $spentThisMonth + $amount > (float) $account->monthly_spend_limit) {
                return false;
            }
        }

        return true;
    }
}
