<?php

namespace App\Actions\Network;

use App\Models\Associate;
use App\Models\Commission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class GetNetworkOverviewAction
{
    public function execute(User $user): array
    {
        $associate = $user->associateProfile();

        $members = DB::table('networks')
            ->join('associates', 'networks.child_associate_id', '=', 'associates.id')
            ->join('users', function ($join) {
                $join->on('users.profileable_id', '=', 'associates.id')
                    ->where('users.profileable_type', '=', Associate::class);
            })
            ->where('networks.parent_associate_id', $associate?->id)
            ->select('users.name', 'users.email', 'networks.created_at as joined_at', 'networks.total_sales')
            ->get();

        $networkEarnings = Commission::where('associate_id', $associate?->id)
            ->where('commission_type', 'network')
            ->sum('amount');

        return [
            'members' => $members,
            'networkEarnings' => (float) $networkEarnings,
            'inviteLink' => $associate ? url('/register?ref=' . $associate->id) : null,
        ];
    }
}
