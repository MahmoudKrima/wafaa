@component('mail::message')
# Welcome, {{ $user->name }}

An administrator has created an account for you.
Here are your login credentials:

- **Email:** {{ $user->email }}
- **Phone:** {{ $user->phone }}
- **Password:** {{ $plainPassword }}

@component('mail::button', ['url' => route('user.auth.loginForm')])
Login Now
@endcomponent

⚠️ For security reasons, please log in immediately and change your password.

Thanks,
{{ app('settings')['app_name_en'] }}
@endcomponent