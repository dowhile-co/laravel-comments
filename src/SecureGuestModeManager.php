<?php

namespace LakM\Comments;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use LakM\Comments\Models\Guest;
use LakM\Comments\Notifications\Guest\VerifyLinkGenerated;
use LakM\NoPass\Facades\NoPass;
use Livewire\Wireable;

class SecureGuestModeManager implements Wireable
{
    public function enabled(): bool
    {
        return config('comments.guest_mode.secured', false);
    }

    public function allowed(): bool
    {
        if (! $this->enabled()) {
            return true;
        }

        return Helpers::getAuthGuard()->check();
    }

    public function user(): Authenticatable|null
    {
        return Helpers::getAuthGuard()->user();
    }

    public function sendLink(string $name, string $email, string $redirectUrl): void
    {
        $guest = $this->createGuest($name, $email);

        $link = NoPass::for($guest)
            ->email()
            ->routeName('verify-guest')
            ->generate([
                'mail' => $email,
                'redirect_url' => $redirectUrl,
            ]);

        Notification::route('mail', $email)
            ->notify(new VerifyLinkGenerated($link));
    }

    public function createGuest(string $name, string $email): Guest
    {
       $guest =  Guest::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'email' => $email
            ]
        );


        return $guest;
    }

    public function limitLinkSending(string $email)
    {
        return GuestModeRateLimiter::limiter($email);
    }

    public function availableIn(string $email): int
    {
        return RateLimiter::availableIn('guest-mode-verify-link:'.$email);
    }

    public function toLivewire()
    {
        return [];
    }

    public static function fromLivewire($value): static
    {
        return new static();
    }
}
