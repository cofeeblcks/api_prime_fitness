<?php

namespace App\Services;

use App\Constants\StatusesConstants;
use App\Models\Suscription;
use App\Models\User;
use Carbon\Carbon;

class MembershipValidator
{
    public function hasActiveMembership(User $user, ?Carbon $date = null): ?Suscription
    {
        $date = $date ?? Carbon::today();

        if ($user->status_id !== StatusesConstants::ACTIVE) {
            return null;
        }

        return Suscription::query()
            ->where('user_id', $user->id)
            ->where('status_id', StatusesConstants::SUBSCRIPTION_ACTIVE)
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->orderByDesc('end_date')
            ->first();
    }
}
