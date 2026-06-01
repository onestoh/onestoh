<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\CorporateAccount;
use App\Models\CorporateMember;
use App\Services\CorporateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CorporateController extends Controller
{
    public function __construct(private CorporateService $corporateService) {}

    /** POST /corporate */
    public function create(Request $request): JsonResponse
    {
        $data = $request->validate([
            'company_name'         => 'required|string|max:255',
            'kra_pin'              => 'nullable|string|max:20',
            'registration_number'  => 'nullable|string|max:50',
            'billing_email'        => 'required|email',
            'billing_address'      => 'nullable|string|max:500',
            'monthly_spend_limit'  => 'nullable|numeric|min:0',
            'per_booking_limit'    => 'nullable|numeric|min:0',
            'requires_approval'    => 'nullable|boolean',
            'po_reference_format'  => 'nullable|string|max:100',
        ]);

        $account = $this->corporateService->createAccount(Auth::user(), $data);
        return response()->json($account, 201);
    }

    /** GET /corporate */
    public function show(): JsonResponse
    {
        $account = CorporateAccount::where('owner_id', Auth::id())
            ->with('activeMembers.user')
            ->firstOrFail();

        return response()->json($account);
    }

    /** POST /corporate/members */
    public function inviteMember(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => 'required|email',
            'role'  => 'required|in:admin,member',
        ]);

        $account = CorporateAccount::where('owner_id', Auth::id())->firstOrFail();
        $member  = $this->corporateService->inviteMember($account, $data['email'], $data['role']);

        return response()->json($member, 201);
    }

    /** DELETE /corporate/members/{member} */
    public function removeMember(CorporateMember $member): JsonResponse
    {
        $account = CorporateAccount::where('owner_id', Auth::id())->firstOrFail();
        abort_if($member->corporate_account_id !== $account->id, 403);

        $member->update(['is_active' => false]);
        return response()->json(['message' => 'Member deactivated.']);
    }

    /** POST /corporate/bookings/{booking}/approve */
    public function approveBooking(Booking $booking): JsonResponse
    {
        $this->corporateService->approveMemberBooking($booking, Auth::user());
        return response()->json(['message' => 'Booking approved.']);
    }

    /** GET /corporate/invoice/{month} */
    public function monthlyInvoice(string $month): JsonResponse
    {
        $account = CorporateAccount::where('owner_id', Auth::id())->firstOrFail();
        $invoice = $this->corporateService->getMonthlyInvoice($account, $month);
        return response()->json($invoice);
    }
}
