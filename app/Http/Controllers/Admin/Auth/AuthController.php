<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Services\Admin\Auth\AuthService;
use App\Http\Requests\Admin\Auth\LoginRequest;
use App\Http\Requests\Admin\Auth\ResetPasswordRequest;
use App\Http\Requests\Admin\Auth\ForgetPasswordRequest;
use App\Http\Requests\Admin\Auth\ResetPasswordSubmitRequest;
use App\Http\Requests\Admin\Auth\ResetPasswordLinkCheckRequest;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService)
    {
    }

    public function loginForm()
    {
        return view('dashboard.pages.auth.login');
    }

    public function login(LoginRequest $request)
    {
        $res = $this->authService->login($request);
        if ($res == 'wrong credentials') {
            return back()
                ->with('Error', __('admin.credentials_invalid'));
        } elseif ($res == 'not active') {
            return back()
                ->with('Error', __('admin.your_account_is_not_active'));
        }
        Auth::guard('admin')
            ->login($res);
        return redirect()
            ->route('admin.dashboard.index');
    }

    public function forgetPasswordForm()
    {
        return view('dashboard.pages.auth.forget_password');
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
                ->route('admin.auth.forgetPassword')
                ->with('Error', __('admin.wrong_token'));
        }
        return view('dashboard.pages.auth.reset_password');
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
                ->route('admin.auth.loginForm')
                ->with('Success', __('admin.password_restored_successfully'));
        }
    }

    public function logout()
    {
        $this->authService->logout();
        return redirect()
            ->route('admin.auth.loginForm');
    }
}
