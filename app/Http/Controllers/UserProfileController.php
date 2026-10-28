<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class UserProfileController extends Controller
{
    private const PHONE_KEY = 'phone_number';
    private const SOUND_KEY = 'notification_sound';

    public function show()
    {
        $user = auth()->user();
        $phone = $this->detail($user->id, self::PHONE_KEY)
            ?: $this->detail($user->id, 'phone');
        $selectedSound = $this->detail($user->id, self::SOUND_KEY)
            ?: 'teule_notification.mp3';
        $verifiedOtp = DB::table('profile_password_otps')->where('user_id', $user->id)
            ->whereNull('consumed_at')->whereNotNull('verified_at')->orderByDesc('id')->first();
        $passwordOtpVerified = $verifiedOtp
            && Carbon::parse($verifiedOtp->expires_at)->isFuture()
            && Carbon::parse($verifiedOtp->verified_at)->addMinutes(10)->isFuture();

        $permissions = method_exists($user, 'getAllPermissions')
            ? $user->getAllPermissions()->sortBy('name')->values()
            : collect();
        $roles = method_exists($user, 'getRoleNames') ? $user->getRoleNames() : collect();

        $soundDirectory = public_path('sounds/notifications');
        if (!File::isDirectory($soundDirectory)) {
            File::makeDirectory($soundDirectory, 0755, true);
        }

        $sounds = collect(File::files($soundDirectory))
            ->filter(fn ($file) => in_array(strtolower($file->getExtension()), ['mp3', 'wav', 'ogg'], true))
            ->map(fn ($file) => [
                'name' => $file->getFilename(),
                'url' => asset('sounds/notifications/' . rawurlencode($file->getFilename())),
            ])->sortBy('name')->values();

        // Keep the legacy default usable even if no shared sound files have been uploaded yet.
        if ($sounds->isEmpty() && file_exists(public_path('sounds/teule_notification.mp3'))) {
            $sounds->push([
                'name' => 'teule_notification.mp3',
                'url' => asset('sounds/teule_notification.mp3'),
            ]);
        }

        $imagePath = DB::table('user_details')->where('user_id', $user->id)
            ->where('key', 'img_url')->value('value');
        $imageUrl = $imagePath
            ? (filter_var($imagePath, FILTER_VALIDATE_URL) ? $imagePath : asset(ltrim($imagePath, '/')))
            : null;

        return view('admin.profile.show', compact(
            'user', 'phone', 'selectedSound', 'permissions', 'roles', 'sounds', 'imageUrl', 'passwordOtpVerified'
        ));
    }

    public function updateDetails(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'email' => ['required', 'email:rfc', 'max:255', 'regex:/^[A-Z0-9._%+-]+@teulekenya\.org$/i', 'unique:users,email,' . $user->id],
            'phone_number' => ['required', 'string', 'min:7', 'max:25', 'regex:/^[+0-9()\-\s]+$/'],
        ], [
            'email.regex' => 'Your email must use the @teulekenya.org domain.',
            'phone_number.regex' => 'Enter a valid phone number using digits, spaces, +, brackets or hyphens.',
        ]);

        $user->email = strtolower(trim($data['email']));
        $user->save();
        $this->saveDetail($user->id, self::PHONE_KEY, trim($data['phone_number']));

        return back()->with('success', 'Your contact details have been updated.');
    }

    public function updateSound(Request $request)
    {
        $data = $request->validate(['notification_sound' => ['required', 'string', 'max:120']]);
        $filename = basename($data['notification_sound']);
        $allowed = collect(File::files(public_path('sounds/notifications')))
            ->filter(fn ($file) => in_array(strtolower($file->getExtension()), ['mp3', 'wav', 'ogg'], true))
            ->pluck('filename')->all();

        // Legacy default is accepted only if the file actually exists.
        if (empty($allowed) && file_exists(public_path('sounds/teule_notification.mp3'))) {
            $allowed[] = 'teule_notification.mp3';
        }
        if (!in_array($filename, $allowed, true)) {
            throw ValidationException::withMessages(['notification_sound' => 'Select a sound from the available list.']);
        }

        $this->saveDetail($request->user()->id, self::SOUND_KEY, $filename);
        return back()->with('success', 'Your notification sound preference has been saved.');
    }

    public function sendPasswordOtp(Request $request)
    {
        $data = $request->validate(['channel' => ['required', 'in,email,sms']]);
        $user = $request->user();
        $key = 'profile-otp:' . $user->id;
        if (RateLimiter::tooManyAttempts($key, 3)) {
            throw ValidationException::withMessages(['otp' => 'Too many OTP requests. Please wait before trying again.']);
        }
        RateLimiter::hit($key, 600);

        $phone = $this->detail($user->id, self::PHONE_KEY) ?: $this->detail($user->id, 'phone');
        if ($data['channel'] === 'sms' && !$phone) {
            throw ValidationException::withMessages(['channel' => 'Add a phone number to your profile before choosing SMS verification.']);
        }
        if ($data['channel'] === 'email' && empty($user->email)) {
            throw ValidationException::withMessages(['channel' => 'Your account does not have an email address.']);
        }

        $otp = (string) random_int(100000, 999999);
        DB::table('profile_password_otps')->where('user_id', $user->id)->whereNull('consumed_at')->update(['consumed_at' => now()]);
        $id = DB::table('profile_password_otps')->insertGetId([
            'user_id' => $user->id,
            'channel' => $data['channel'],
            'code_hash' => Hash::make($otp),
            'attempts' => 0,
            'expires_at' => now()->addMinutes(10),
            'verified_at' => null,
            'consumed_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        try {
            if ($data['channel'] === 'email') {
                Mail::raw("Your Teule Kenya password reset verification code is {$otp}. It expires in 10 minutes. If you did not request this, ignore this message.", function ($message) use ($user) {
                    $message->to($user->email)->subject('Teule Kenya password verification code');
                });
            } else {
                $this->sendSms($phone, "Your Teule Kenya password verification code is {$otp}. It expires in 10 minutes.");
            }
        } catch (\Throwable $e) {
            DB::table('profile_password_otps')->where('id', $id)->update(['consumed_at' => now(), 'updated_at' => now()]);
            report($e);
            throw ValidationException::withMessages(['channel' => 'We could not send the verification code. Check the configured email/SMS service and try again.']);
        }

        return back()->with('otp_sent', 'A verification code has been sent using your selected method.');
    }

    public function verifyPasswordOtp(Request $request)
    {
        $data = $request->validate(['otp' => ['required', 'digits:6']]);
        $user = $request->user();
        $record = DB::table('profile_password_otps')->where('user_id', $user->id)
            ->whereNull('consumed_at')->orderByDesc('id')->first();

        if (!$record || Carbon::parse($record->expires_at)->isPast() || $record->attempts >= 5) {
            throw ValidationException::withMessages(['otp' => 'The code has expired or is no longer valid. Request a new code.']);
        }
        if (!Hash::check($data['otp'], $record->code_hash)) {
            DB::table('profile_password_otps')->where('id', $record->id)->increment('attempts');
            throw ValidationException::withMessages(['otp' => 'The verification code is incorrect.']);
        }

        DB::table('profile_password_otps')->where('id', $record->id)->update(['verified_at' => now(), 'updated_at' => now()]);
        return back()->with('otp_verified', 'Verification successful. You can now set a new password.');
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'password' => ['required', 'string', 'min:10', 'confirmed', 'regex:/[A-Za-z]/', 'regex:/[0-9]/'],
        ], ['password.regex' => 'Use a password with at least one letter and one number.']);

        $user = $request->user();
        $record = DB::table('profile_password_otps')->where('user_id', $user->id)
            ->whereNull('consumed_at')->whereNotNull('verified_at')->orderByDesc('id')->first();

        if (!$record || Carbon::parse($record->expires_at)->isPast()
            || Carbon::parse($record->verified_at)->addMinutes(10)->isPast()) {
            throw ValidationException::withMessages(['password' => 'Verify a new OTP before changing your password.']);
        }

        $user->password = Hash::make($data['password']);
        $user->save();
        DB::table('profile_password_otps')->where('id', $record->id)->update(['consumed_at' => now(), 'updated_at' => now()]);
        auth()->logoutOtherDevices($data['password']);

        return redirect()->route('admin.profile.show')->with('success', 'Your password has been changed successfully.');
    }

    private function detail(int $userId, string $key): ?string
    {
        $value = DB::table('user_details')->where('user_id', $userId)->where('key', $key)->value('value');
        return is_string($value) && trim($value) !== '' ? trim($value) : null;
    }

    private function saveDetail(int $userId, string $key, string $value): void
    {
        $query = DB::table('user_details')->where('user_id', $userId)->where('key', $key);
        $now = now();

        if ($query->exists()) {
            $updates = ['value' => $value];
            if (Schema::hasColumn('user_details', 'updated_at')) {
                $updates['updated_at'] = $now;
            }
            $query->update($updates);
            return;
        }

        $insert = ['user_id' => $userId, 'key' => $key, 'value' => $value];
        if (Schema::hasColumn('user_details', 'created_at')) {
            $insert['created_at'] = $now;
        }
        if (Schema::hasColumn('user_details', 'updated_at')) {
            $insert['updated_at'] = $now;
        }
        DB::table('user_details')->insert($insert);
    }

    private function sendSms(string $phone, string $message): void
    {
        $endpoint = config('services.teule_sms.endpoint');
        $token = config('services.teule_sms.token');
        $sender = config('services.teule_sms.sender');
        if (!$endpoint || !$token) {
            throw new \RuntimeException('Teule SMS API is not configured.');
        }

        // Generic JSON SMS adapter. Map these fields to the payload required by your SMS provider.
        $response = Http::timeout(15)->withToken($token)->acceptJson()->post($endpoint, array_filter([
            'to' => $phone,
            'message' => $message,
            'from' => $sender,
        ], fn ($value) => $value !== null && $value !== ''));

        if (!$response->successful()) {
            throw new \RuntimeException('SMS provider returned HTTP ' . $response->status() . ': ' . Str::limit($response->body(), 300));
        }
    }
}
