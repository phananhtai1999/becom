@extends('mail.layout')

@section('content')
    <h1 style="font-size: 27px;">{{ __('Invite Email') }}</h1>

    <p style="font-weight: bold">{{ __('Hello') }} {{ $invite->email }},</p>
    <p>{{ __('Someone requested to invite you to team, please click button below to join the team.') }}</p>
    @if($password)
        <p>{{ __('Account is your email.') }}</p>
        <p>{{ __('Password:') }} {{$password}}</p>
    @endif
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
                            <b><a href="{{ $url }}">JOIN</a></b>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
@endsection

