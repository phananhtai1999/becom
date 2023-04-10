@extends('mail.layout')

@section('content')
    <h1 style="font-size: 27px;">{{ __('Account Email') }}</h1>

    <p style="font-weight: bold">{{ __('Hello') }} {{ $user->email }},</p>
    <p>{{ __('Someone requested to invite you to team, please use account below to login. This account already join the team.') }}</p>
    <p>{{ __('Account:') }} {{ $user->email }}</p>
    <p>{{ __('Password:') }} {{ $password }}</p>

@endsection

