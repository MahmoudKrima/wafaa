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
            ->where('status', 'active')
            ->first();
    }

    public function login($request)
    {
        $data = $request->validated();
        $admin = $this->getAdminByPhone($data['phone']);
        if (!$admin) {
            return 'wrong credentials';
        }
        if ($admin->status->value != 'active') {
            return 'not active';
        }
        if (!Hash::check($data['password'], $admin->password)) {
            return 'wrong credentials';
        }
        return $admin;
    }

    private function sendForgetPasswordMail($admin, $email, $token)
    {
        if (app()->getLocale() == 'ar') {
            $subject = 'استعادة كلمة المرور';
            $encryptedData = Crypt::encryptString($email . '|' . $token);
            $resetLink = url('/reset-password?data=' . urlencode($encryptedData));
            $body = "<p>قم بالضغط على الرابط لاستعادة كلمة المرور</p>
            <a href=\"$resetLink\">استعادة كلمة المرور</a>";
        } else {
            $subject = 'Reset Password';
            $encryptedData = Crypt::encryptString($email . '|' . $token);
            $resetLink = url('/reset-password?data=' . urlencode($encryptedData));
            $body = "<p>Click the following link to reset your password:</p>
            <a href=\"$resetLink\">Reset Password</a>";
        }
        Mail::to($admin)
            ->send(new ForgetPasswordMail($subject, $body));
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
