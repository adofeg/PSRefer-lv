<?php

namespace App\Actions\Users;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetUsersAction
{
    public function execute(): LengthAwarePaginator
    {
        return User::latest()->paginate(15);
    }
}
