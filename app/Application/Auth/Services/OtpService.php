<?php

namespace App\Application\Auth\Services;

use App\Domain\Auth\Models\OtpCode;
use Illuminate\Support\Facades\DB;

class OtpService
{
    public function send(string $destination, string $channel, string $purpose, ?string $user_id = null): OtpCode
    {
        return DB::transaction(function () use ($destination, $channel, $purpose, $user_id): OtpCode {
            OtpCode::query()
                ->where('destination', $destination)
                ->where('purpose', $purpose)
                ->whereNull('verified_at')
                ->where('expires_at', '>', now())
                ->update(['expires_at' => now()]);

            return OtpCode::query()->create([
                'user_id' => $user_id,
                'channel' => $channel,
                'destination' => $destination,
                'purpose' => $purpose,
                'code' => str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT),
                'expires_at' => now()->addMinutes(10),
                'attempts' => 0,
            ]);
        });
    }

    public function verify(string $destination, string $code, string $purpose): bool
    {
        return DB::transaction(function () use ($destination, $code, $purpose): bool {
            $otp = OtpCode::query()
                ->where('destination', $destination)
                ->where('purpose', $purpose)
                ->whereNull('verified_at')
                ->where('expires_at', '>', now())
                ->latest()
                ->lockForUpdate()
                ->first();

            if (! $otp instanceof OtpCode) {
                return false;
            }

            $otp->increment('attempts');

            if (! hash_equals($otp->code, $code)) {
                return false;
            }

            $otp->forceFill(['verified_at' => now()])->save();

            return true;
        });
    }
}
