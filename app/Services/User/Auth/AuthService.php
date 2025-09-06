<?php

namespace App\Services\User\Auth;

use Throwable;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserResetPasswordMail;
use Illuminate\Support\Facades\Hash;


class AuthService
{
    public function checkAttempts(array $data)
    {
        $user = User::where('phone', $data['phone'])->first();

        if (!$user) {
            return 'wrong credentials';
        }

        if (!Hash::check($data['password'], $user->password)) {
            return 'wrong credentials';
        }

        if ($user->status->value != 'active') {
            return 'not active';
        }

        Auth::guard('web')->login($user);
        return 'login success';
    }


    function sendResetPasswordLink($data)
    {
        $User = User::where('email', $data['email'])
            ->first();
        DB::beginTransaction();
        try {
            $token = hash('sha256', time() . rand(10000, 100000000));
            $User->update([
                'token' => $token
            ]);
            $reset_link = url('User/reset-password/' . $token . '/' . $data['email']);
            $subject = 'Reset Password';
            $message = 'Click On This Link To Reset Password <br/>';
            $message .= '<a href="' . $reset_link . '">Reset Password</a>';
            Mail::to($data['email'])
                ->send(new UserResetPasswordMail($subject, $message));
            DB::commit();
            return redirect()
                ->to(route('user.auth.loginForm'))
                ->with("Success", __('user.check_your_mail'));
        } catch (Throwable $e) {
            DB::rollBack();
            return back()
                ->with('Error', __('user.try_again_later'));
        }
    }

    function resetPasswordForm($token, $email)
    {
        $User = User::where('email', $email)
            ->where('token', $token)
            ->first();
        if (!$User) {
            return redirect()
                ->to(route('user.auth.loginForm'))
                ->with("Error", __('user.try_again_later'));
        }
        return view('user.pages.auth.reset_password', compact('token', 'email'));
    }

    function resetPassword($data)
    {
        $User = User::where('email', $data['email'])
            ->where('token', $data['token'])
            ->first();
        if (!$User) {
            return back()
                ->with("Error", __('user.try_again_later'));
        }
        $User->update([
            'password' => $data['new_password'],
            'token' => null
        ]);
        return redirect(route('user.auth.loginForm'))
            ->with('Success', __('user.password_reset_successfully'));
    }
}
