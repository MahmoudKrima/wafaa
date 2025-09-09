@php
    $locale = app()->getLocale();
    $isRtl = in_array($locale, ['ar']);
    $dir = $isRtl ? 'rtl' : 'ltr';
    $align = $isRtl ? 'right' : 'left';
    $oppAlign = $isRtl ? 'left' : 'right';
    $fontFamily = $isRtl ? 'Almarai' : 'sans-serif';
@endphp
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <title>{{ __('admin.forget_password_request') }}</title>
    @if (App::getLocale() === 'ar')
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700;800&display=swap" rel="stylesheet">
    @else
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    @endif
</head>

<body style="margin:0; padding:0; background-color:#f5f6f8;font-family:{{$fontFamily}}">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f5f6f8;">
    <tr>
        <td align="center" style="padding:5px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                   style="background:#ffffff; border-collapse:collapse; border-radius:8px; overflow:hidden;">
                <tr>
                    <td
                        style="padding:20px 24px; background:#1b6aab; color:#ffffff; font-family:{{$fontFamily}}; font-size:16px; font-weight:bold; text-align:{{ $align }};">
                        {{ app('settings')['app_name_' . assetLang()] }}
                    </td>
                </tr>

                <tr>
                    <td style="padding:24px; font-family:{{$fontFamily}}; color:#1f2937; text-align:{{ $align }};">

                        <p style="margin:0 0 16px; font-size:15px; line-height:1.6;font-family:{{$fontFamily}}">
                            {!! $body !!}
                        </p>
                    </td>
                </tr>

                <tr>
                    <td
                        style="padding:16px 24px; background:#f3f4f6; color:#6b7280; font-family:{{$fontFamily}}; font-size:12px; text-align:center;">
                        © {{ date('Y') }} {{ app('settings')['app_name_' . assetLang()] }}.
                        {{ __('admin.all_rights_reserved') }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>

</html>
