@extends('mail.layout')

@section('content')
    <p>{{ __('Hi') }} {{ $user->name }},</p>
    <p>{{ __('Someone requested to reset your password, if this was you, please use the following the link bellow to confirm your identity') }}</p>
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
                            <a href="{{ Request::server('HTTP_ORIGIN') . '/auth/password-recovery?token=' . $passwordReset->token }}"
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
                    {{ __("If you don't ask for this request, someone might be trying to access your account") }} {{ $user->username }}.
                    {{ __("Do not forward or give this link to anyone.") }}
                </p>
                <p>
                    {{ __("You are receiving this message because this email address is listed as a recovery email for") }}
                    {{  __("Accounts") }} {{ $user->username }}.
                </p>
            </td>
        </tr>
        </tbody>
    </table>
@endsection
