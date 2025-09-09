<?php

namespace App\Services\Admin\Auth;

use App\Models\Admin;
use Illuminate\Support\Str;
use App\Mail\ForgetPasswordMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgetPasswordAdminMail;
use Illuminate\Support\Facades\Crypt;
use Exception;

class AuthService
{
    private function getAdminByPhone($phone)
    {
        return Admin::where('phone', $phone)
            ->first();
    }

    public function login($request)
    {
        $data = $request->validated();
        $admin = $this->getAdminByPhone($data['phone']);
        if (!$admin) {
            return 'wrong credentials';
        }
        if (!Hash::check($data['password'], $admin->password)) {
            return 'wrong credentials';
        }
        if ($admin->status->value != 'active') {
            return 'not active';
        }
        return $admin;
    }

    private function sendForgetPasswordMail($admin, $email, $token)
    {
        if (app()->getLocale() == 'ar') {
            $subject = 'استعادة كلمة المرور';
            $encryptedData = Crypt::encryptString($email . '|' . $token);
            $resetLink = url(app()->getLocale() . '/admin/reset-password?data=' . urlencode($encryptedData));
            $body = "<p style='color:#545657;font-size:15px;'>   <span> : مرحبا </span> $admin->name </p>";
            $body .= "<p style='color:#545657;font-size:15px;'>لقد قمت بطلب تغيير كلمة المرور </p>";
            $body .= "<p style='color:#545657;font-size:15px;margin-bottom:30px;'>قم بالضغط على الرابط التالي لاستعادة كلمة المرور</p>";
            $body .= "<a style='background:#1b6aab;color:#fff;border-radius:5px;padding:10px;text-decoration:none;' href=\"$resetLink\">استعادة كلمة المرور</a>";

        } else {
            $subject = 'Reset Password';
            $encryptedData = Crypt::encryptString($email . '|' . $token);
            $resetLink = url(app()->getLocale() . '/admin/reset-password?data=' . urlencode($encryptedData));
            $body = "<p style='color:#545657;font-size:15px;'> Welcome $admin->name </p>";
            $body .= "<p style='color:#545657;font-size:15px;'> You are requesting to reset your password </p>";
            $body .= "<p style='color:#545657;font-size:15px;margin-bottom:30px;'> Click the following link to reset your password:</p>";
            $body .= "<a style='background:#1b6aab;color:#fff;border-radius:5px;padding:10px;text-decoration:none;' href=\"$resetLink\">Reset Password</a>";
        }
        Mail::to($admin)
            ->send(new ForgetPasswordMail($subject, $body));
    }

    private function getAdminByEmail($email)
    {
        return Admin::where('email', $email)
            ->first();
    }

    public function forgetPassword($request)
    {
        $data = $request->validated();
        $admin = $this->getAdminByEmail($data['email']);
        if (!$admin) {
            return 'wrong credentials';
        }
        try {
            $token = Str::random(60);
            $admin->update([
                'token' => $token,
            ]);
            $this->sendForgetPasswordMail($admin, $data['email'], $token);
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
            $admin = Admin::where('email', $email)
                ->where('token', $token)
                ->where('status', 'active')
                ->first();
            return $admin ? true : false;
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
            $admin = Admin::where('email', $email)
                ->where('token', $token)
                ->where('status', 'active')
                ->first();
            if (!$admin) {
                return 'invalid token';
            }
            $admin->update([
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
        Auth::guard('admin')
            ->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }
}
