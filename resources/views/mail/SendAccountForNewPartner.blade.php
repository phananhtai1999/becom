@extends('mail.layout')

@section('content')
    <h1 style="font-size: 27px;">{{ __('Welcome Partner') }}</h1>
    <p>{{ __('Hi') }} {{ $email }},</p>
    <p>{{ __('Thank you for being our partner. This is your account:.') }}</p>
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
                            <p>Email: {{$email}}</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>Password: {{$password}}</p>
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
                    {{  __("Accounts") }} {{ $email }}.
                </p>
            </td>
        </tr>
        </tbody>
    </table>
@endsection
