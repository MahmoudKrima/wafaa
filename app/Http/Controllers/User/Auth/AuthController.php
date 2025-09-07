<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Auth\ForgetPasswordRequest;
use App\Http\Requests\User\Auth\LoginRequest;
use App\Http\Requests\User\Auth\ResetPasswordRequest;
use App\Http\Requests\User\Auth\ResetPasswordSubmitRequest;
use App\Services\User\Auth\AuthService;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService)
    {
    }

    public function loginForm()
    {
        return view('user.pages.auth.login');
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();
        $res = $this->authService->checkAttempts($data);
        if ($res == 'not active') {
            return back()
                ->with('Error', __('admin.your_account_is_not_active'));
        } elseif ($res == 'login success') {
            return redirect()
                ->to(route('user.dashboard.index'));
        } elseif ($res == 'wrong credentials') {
            return back()
                ->with('Error', __('admin.credentials_invalid'));
        }
        return back()
            ->with('Error', __('admin.credentials_invalid'));
    }

    public function forgetPasswordForm()
    {
        return view('user.pages.auth.forget_password');
    }

    public function forgetPassword(ForgetPasswordRequest $request)
    {
        $res = $this->authService->forgetPassword($request);
        if ($res == 'wrong credentials') {
            return back()
                ->with('Error', __('admin.credentials_invalid'));
        } elseif ($res == 'reset link sent') {
            return back()
                ->with('Success', __('admin.reset_link_sent'));
        } elseif ($res == 'error sending email') {
            return back()
                ->with('Error', __('admin.error_sending_email'));
        } else {
            return back()
                ->with('Error', __('admin.server_error'));
        }
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $isValid = $this->authService->validateResetToken($request);
        if (!$isValid) {
            return redirect()
                ->route('user.auth.forgetPassword')
                ->with('Error', __('admin.wrong_token'));
        }
        return view('user.pages.auth.reset_password');
    }

    public function resetPasswordSubmit(ResetPasswordSubmitRequest $request)
    {
        $response = $this->authService->resetPasswordSubmit($request);
        if ($request == 'invalid token') {
            return back()
                ->with('Error', __('admin.wrong_token'));
        } elseif ($response == 'server error') {
            return back()
                ->with('Error', __('admin.server_error'));
        } else {
            return redirect()
                ->route('user.auth.loginForm')
                ->with('Success', __('admin.password_restored_successfully'));
        }
    }

    public function logout()
    {
        $this->authService->logout();
        return redirect()
            ->route('user.auth.loginForm');
    }
}
