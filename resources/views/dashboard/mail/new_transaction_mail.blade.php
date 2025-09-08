@php
    $locale = app()->getLocale();
    $isRtl = in_array($locale, ['ar']);
    $dir = $isRtl ? 'rtl' : 'ltr';
    $align = $isRtl ? 'right' : 'left';
    $oppAlign = $isRtl ? 'left' : 'right';
    $fontFamily = $isRtl ? 'Almarai' : 'sans-serif';

    $amount = number_format((float) ($transaction->amount ?? 0), 2);
    $createdAt = optional($transaction->created_at)->format('Y-m-d H:i');
@endphp
<!doctype html>
<html lang="{{ $locale }}" dir="{{ $dir }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <title>{{ __('admin.transaction_subject', ['code' => $transaction->code]) }}</title>
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
                        <td
                            style="padding:24px; font-family:{{$fontFamily}}; color:#1f2937; text-align:{{ $align }};">
                            <p style="margin:0 0 12px; font-size:16px;">
                                {{ __('admin.hello_name', ['name' => $adminName ?? __('admin.admin')]) }}
                            </p>

                            <p style="margin:0 0 16px; font-size:15px; line-height:1.6;font-family:{{$fontFamily}}">
                                {{ __('admin.transaction_created_successfully') }}
                            </p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="border:1px solid #e5e7eb; border-radius:6px; overflow:hidden;">
                                <tr>
                                    <td
                                        style="padding:14px 16px; background:#f9fafb; font-family:{{$fontFamily}}; font-size:14px; color:#111827; text-align:{{ $align }};">
                                        <strong>{{ __('admin.details') }}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td
                                        style="padding:12px 16px; font-family:{{$fontFamily}}; font-size:14px; color:#374151;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                            style="border-collapse:collapse;">
                                            <tr>
                                                <td style="padding:6px 0; width:40%; text-align:{{ $align }};font-family:{{$fontFamily}};">
                                                    <strong>{{ __('admin.transaction_code_label') }}</strong>
                                                </td>
                                                <td style="padding:6px 0; text-align:{{ $oppAlign }};font-family:{{$fontFamily}};">
                                                    {{ $transaction->code }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0; width:40%; text-align:{{ $align }};font-family:{{$fontFamily}};">
                                                    <strong>{{ __('admin.user') }}</strong>
                                                </td>
                                                <td style="padding:6px 0; text-align:{{ $oppAlign }};font-family:{{$fontFamily}};">
                                                    {{ $transaction->user->name }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0; width:40%; text-align:{{ $align }};font-family:{{$fontFamily}};">
                                                    <strong>{{ __('admin.amount') }}</strong>
                                                </td>
                                                <td style="padding:6px 0; text-align:{{ $oppAlign }};font-family:{{$fontFamily}};">
                                                    {{ $amount . '  ' . __('admin.sar')}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0; width:40%; text-align:{{ $align }};font-family:{{$fontFamily}};">
                                                    <strong>{{ __('admin.bank') }}</strong>
                                                </td>
                                                <td style="padding:6px 0; text-align:{{ $oppAlign }};font-family:{{$fontFamily}};">
                                                    {{ $transaction->bank->name }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0; width:40%; text-align:{{ $align }};font-family:{{$fontFamily}};">
                                                    <strong>{{ __('admin.transaction_status_label') }}</strong>
                                                </td>
                                                <td style="padding:6px 0; text-align:{{ $oppAlign }};font-family:{{$fontFamily}};">
                                                    {{ ucfirst($transaction->status->lang()) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0; width:40%; text-align:{{ $align }};font-family:{{$fontFamily}};">
                                                    <strong>{{ __('admin.transaction_created_at_label') }}</strong>
                                                </td>
                                                <td style="padding:6px 0; text-align:{{ $oppAlign }};font-family:{{$fontFamily}};">{{ $createdAt }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
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
