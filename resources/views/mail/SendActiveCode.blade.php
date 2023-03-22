@extends('mail.layout')

@section('content')
    <h1 style="font-size: 27px;">{{ __('Verify Email') }}</h1>

    <p style="font-weight: bold">{{ __('Hello') }} {{ $user->first_name }},</p>
    <p>{{ __('Someone requested to Verify email, if this was you, please use the verify code to verify your email.') }}</p>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0"
           class="btn btn-primary">
        <tbody>
        <tr>
            <td align="center">
                <table role="presentation" border="0" cellpadding="0"
                       cellspacing="0">
                    <tbody>
                    <tr>
                        <td class="verify_code verify_code_2 ">
                            <b style="letter-spacing: 20px;margin-left:20px;font-size: 40px;color: #000"
                               align="center">{{ $otp->active_code }}</b>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <p>
                    {{ __("All you have to do is copy the verification code paste it to your form to complete the email verification process.") }}
                </p>
            </td>
        </tr>
        </tbody>
    </table>
@endsection

