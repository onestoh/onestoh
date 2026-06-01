<?php
namespace App\Jobs;

use App\Models\Booking;
use App\Models\Dispute;
use App\Models\Review;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CalculateTrustScoreJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(private int $userId) {}

    public function handle(): void
    {
        $user = User::find($this->userId);
        if (!$user) return;

        $score = 50; // Base score

        // KYC approved: +10
        if ($user->kyc_status === 'approved') {
            $score += 10;
        }

        // Phone verified: +5
        if ($user->phone_verified_at) {
            $score += 5;
        }

        // Email verified: +5
        if ($user->email_verified_at) {
            $score += 5;
        }

        // Completed rentals (as client or owner)
        $completedAsClient = Booking::where('client_id', $user->id)
            ->whereIn('status', ['completed', 'closed'])
            ->count();

        $completedAsOwner = Booking::whereHas('asset', fn($q) => $q->where('owner_id', $user->id))
            ->whereIn('status', ['completed', 'closed'])
            ->count();

        $totalCompleted = $completedAsClient + $completedAsOwner;
        $score += min(15, $totalCompleted * 2); // max +15

        // Average review score contribution
        $avgRating = Review::where('reviewee_id', $user->id)
            ->where('is_published', true)
            ->avg('overall_rating');

        if ($avgRating) {
            // Map 1-5 rating to -10 to +10 range
            $ratingContribution = (($avgRating - 3) / 2) * 10;
            $score += (int) round($ratingContribution);
        }

        // Disputes raised against user (as respondent)
        $disputesAgainst = Dispute::where('status', 'ruled')
            ->whereHas('booking', function ($q) use ($user) {
                $q->where('client_id', $user->id)
                    ->orWhereHas('asset', fn($a) => $a->where('owner_id', $user->id));
            })
            ->where('raised_by', '!=', $user->id)
            ->count();

        $score -= min(20, $disputesAgainst * 5); // max -20

        // Cancellations by user
        $cancellations = Booking::where('client_id', $user->id)
            ->where('status', 'cancelled_by_client')
            ->count();

        $score -= min(10, $cancellations * 2); // max -10

        // Clamp 0-100
        $score = max(0, min(100, $score));

        $user->update(['trust_score' => $score]);

        Log::info('CalculateTrustScoreJob: score updated', [
            'user_id' => $this->userId,
            'score'   => $score,
        ]);
    }
}
