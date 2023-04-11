@extends('mail.layout')

@section('content')
    <h1 style="font-size: 27px;">{{ __('Welcome Partner') }}</h1>
    <p>{{ __('Hi') }} {{ $user->username }},</p>
    <p>{{ __('Thank you for being our partner. Please click the link below to change your password.') }}</p>
    <p>{{__('Account is your email')}}</p>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0"
           class="btn btn-primary">
        <tbody>
        <tr>
            <td align="left">
                <table role="presentation" border="0" cellpadding="0"
                       cellspacing="0">
                    <tbody>
                    <tr>
                        <td>
                            <a href="{{ config('auth.recovery_password_url'). '?token='.$passwordReset->token.'&email='.$user->email }}"
                               target="_blank">
                                {{ __('Recovery Password') }}
                            </a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <p>
                    {{ __("You are receiving this message because this email address is listed as a recovery email for") }}
                    {{  __("Accounts") }} {{ $user->username }}.
                </p>
            </td>
        </tr>
        </tbody>
    </table>
@endsection
