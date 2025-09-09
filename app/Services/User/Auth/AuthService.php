<?php

namespace App\Services\User\Auth;

use Exception;
use App\Models\User;
use Illuminate\Support\Str;
use App\Mail\ForgetPasswordMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;


class AuthService
{
    public function checkAttempts(array $data)
    {
        $user = User::where('phone', $data['phone'])
            ->first();

        if (!$user) {
            return 'wrong credentials';
        }

        if (!Hash::check($data['password'], $user->password)) {
            return 'wrong credentials';
        }

        if ($user->status->value != 'active') {
            return 'not active';
        }

        Auth::guard('web')
            ->login($user);
        return 'login success';
    }


    private function sendForgetPasswordMail($user, $email, $token)
    {
        if (app()->getLocale() == 'ar') {
            $subject = 'استعادة كلمة المرور';
            $encryptedData = Crypt::encryptString($email . '|' . $token);
            $resetLink = url(app()->getLocale() . '/user/reset-password?data=' . urlencode($encryptedData));
            $body = "<p style='color:#545657;font-size:15px;'><span> : مرحبا </span> $user->name </p>";
            $body .= "<p style='color:#545657;font-size:15px;'>لقد قمت بطلب تغيير كلمة المرور </p>";
            $body .= "<p style='color:#545657;font-size:15px;margin-bottom:30px;'>قم بالضغط على الرابط التالي لاستعادة كلمة المرور</p>";
            $body .= "<a style='background:#1b6aab;color:#fff;border-radius:5px;padding:10px;text-decoration:none;' href=\"$resetLink\">استعادة كلمة المرور</a>";

        } else {
            $subject = 'Reset Password';
            $encryptedData = Crypt::encryptString($email . '|' . $token);
            $resetLink = url(app()->getLocale() . '/user/reset-password?data=' . urlencode($encryptedData));
            $body = "<p style='color:#545657;font-size:15px;'> Welcome $user->name </p>";
            $body .= "<p style='color:#545657;font-size:15px;'> You are requesting to reset your password </p>";
            $body .= "<p style='color:#545657;font-size:15px;margin-bottom:30px;'> Click the following link to reset your password:</p>";
            $body .= "<a style='background:#1b6aab;color:#fff;border-radius:5px;padding:10px;text-decoration:none;' href=\"$resetLink\">Reset Password</a>";
        }
        Mail::to($user)
            ->send(new ForgetPasswordMail($subject, $body));
    }

    private function getUserByEmail($email)
    {
        return User::where('email', $email)
            ->first();
    }

    public function forgetPassword($request)
    {
        $data = $request->validated();
        $user = $this->getUserByEmail($data['email']);
        if (!$user) {
            return 'wrong credentials';
        }
        try {
            $token = Str::random(60);
            $user->update([
                'token' => $token,
            ]);
            $this->sendForgetPasswordMail($user, $data['email'], $token);
            DB::commit();
            return 'reset link sent';
        } catch (Exception $e) {
            DB::rollBack();
            return 'error sending email';
        }
    }

    public function validateResetToken($request)
    {
        $data = $request->validated();
        try {
            $encryptedData = $data['data'];
            $decryptedData = Crypt::decryptString($encryptedData);
            list($email, $token) = explode('|', $decryptedData);
            $user = User::where('email', $email)
                ->where('token', $token)
                ->where('status', 'active')
                ->first();
            return $user ? true : false;
        } catch (Exception $e) {
            return false;
        }
    }

    public function resetPasswordSubmit($request)
    {
        $data = $request->validated();
        try {
            $decryptedData = Crypt::decryptString($data['data']);
            list($email, $token) = explode('|', $decryptedData);
            $user = User::where('email', $email)
                ->where('token', $token)
                ->where('status', 'active')
                ->first();
            if (!$user) {
                return 'invalid token';
            }
            $user->update([
                'password' => $data['password'],
                'token' => null,
            ]);
            DB::commit();
            return 'password updated';
        } catch (Exception $e) {
            DB::rollBack();
            return 'server error';
        }
    }

    public function logout()
    {
        Auth::guard('web')
            ->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }
}
