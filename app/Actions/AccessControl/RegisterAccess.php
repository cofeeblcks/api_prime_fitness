<?php

namespace App\Actions\AccessControl;

use App\Constants\StatusesConstants;
use App\Models\AccessControl;
use App\Models\Suscription;
use App\Models\User;
use App\Services\MembershipValidator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

final class RegisterAccess
{
    public function __construct(
        private readonly MembershipValidator $membershipValidator
    ) {}

    /**
     * @return array{access: AccessControl, member: User, subscription: ?Suscription}
     */
    public function execute(string $identification, ?string $date = null): array
    {
        try {
            DB::beginTransaction();

            $accessDate = $date ? Carbon::parse($date) : Carbon::today();

            $member = User::query()
                ->members()
                ->where('identification', $identification)
                ->firstOrFail();

            $subscription = $this->membershipValidator->hasActiveMembership($member, $accessDate);

            $access = AccessControl::create([
                'user_id' => $member->id,
                'status_id' => $subscription
                    ? StatusesConstants::ALLOWED
                    : StatusesConstants::DENIED,
            ]);

            DB::commit();

            return [
                'access' => $access->load(['user', 'status']),
                'member' => $member,
                'subscription' => $subscription,
            ];
        } catch (\Exception $e) {
            Log::channel('AccessControlError')->error("Message: {$e->getMessage()}, File: {$e->getFile()}, Line: {$e->getLine()}");
            DB::rollBack();

            throw $e;
        }
    }
}
