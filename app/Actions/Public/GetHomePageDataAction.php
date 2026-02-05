<?php

namespace App\Actions\Public;

use Illuminate\Support\Facades\Route;

class GetHomePageDataAction
{
    public function execute(): array
    {
        return [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
        ];
    }
}
