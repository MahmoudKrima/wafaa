<?php

namespace App\Http\Controllers\User\Auth;

use Throwable;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\Auth\ForgetPasswordRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserResetPasswordMail;
use App\Http\Requests\User\Auth\LoginRequest;
use App\Http\Requests\User\Auth\ResetPasswordRequest;
use App\Services\User\Auth\AuthService;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    public function loginForm()
    {
        return view('user.pages.auth.login');
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();
        return $this->authService->checkAttempts($data);
    }

    public function forgetPasswordForm()
    {
        return view('user.pages.auth.forget_password');
    }

    public function forgetPassword(ForgetPasswordRequest $request)
    {
        $data = $request->validated();
        return $this->authService->sendResetPasswordLink($data);
    }

    public function resetPasswordForm($token, $email)
    {
        return $this->authService->resetPasswordForm($token, $email);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $data = $request->validated();
        return $this->authService->resetPassword($data);
    }

    public function logout()
    {
        auth('web')->logout();
        return redirect()
            ->to(route('user.auth.loginForm'));
    }
}
