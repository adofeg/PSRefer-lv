<?php

namespace App\Listeners;

use App\Models\SecurityLog;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Request;

class SecurityEventSubscriber
{
    /**
     * Handle user login events.
     */
    public function handleUserLogin(Login $event): void
    {
        SecurityLog::create([
            'event_type' => 'LOGIN_SUCCESS',
            'actorable_type' => $event->user->profileable_type,
            'actorable_id' => $event->user->profileable_id,
            'email' => $event->user->email,
            'ip_address' => Request::ip(),
            'user_agent' => Request::header('User-Agent'),
            'metadata' => ['guard' => $event->guard],
            'created_at' => now(),
        ]);
    }

    /**
     * Handle user logout events.
     */
    public function handleUserLogout(Logout $event): void
    {
        if (! $event->user) {
            return;
        }

        SecurityLog::create([
            'event_type' => 'LOGOUT',
            'actorable_type' => $event->user->profileable_type,
            'actorable_id' => $event->user->profileable_id,
            'email' => $event->user->email,
            'ip_address' => Request::ip(),
            'user_agent' => Request::header('User-Agent'),
            'created_at' => now(),
        ]);
    }

    /**
     * Handle user login failure events.
     */
    public function handleUserLoginFailed(Failed $event): void
    {
        SecurityLog::create([
            'event_type' => 'LOGIN_FAILED',
            'actorable_type' => $event->user?->profileable_type,
            'actorable_id' => $event->user?->profileable_id,
            'email' => $event->credentials['email'] ?? 'unknown',
            'ip_address' => Request::ip(),
            'user_agent' => Request::header('User-Agent'),
            'metadata' => ['reason' => 'Invalid credentials'],
            'created_at' => now(),
        ]);
    }

    /**
     * Handle password reset events.
     */
    public function handlePasswordReset(PasswordReset $event): void
    {
        SecurityLog::create([
            'event_type' => 'PASSWORD_RESET_COMPLETED',
            'actorable_type' => $event->user->profileable_type,
            'actorable_id' => $event->user->profileable_id,
            'email' => $event->user->email,
            'ip_address' => Request::ip(),
            'user_agent' => Request::header('User-Agent'),
            'created_at' => now(),
        ]);
    }

    /**
     * Register the listeners for the subscriber.
     */
    public function subscribe(Dispatcher $events): array
    {
        return [
            Login::class => 'handleUserLogin',
            Logout::class => 'handleUserLogout',
            Failed::class => 'handleUserLoginFailed',
            PasswordReset::class => 'handlePasswordReset',
        ];
    }
}
