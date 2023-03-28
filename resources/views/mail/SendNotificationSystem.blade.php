@extends('mail.layout')

@section('content')
    <h3>{{ __('Hello') }} {{ $user->first_name }},</h3>
    @if($type === 'login')
        <p>{{__("It seems that you logged in from a different country. If it's you, please ignore this email.")}}</p>
        <p>{{__("We wanted to let you know is case someone else was using your account")}}</p>
        <h3>{{__('Logged in from')}} {{$action}}.</h3>
    @elseif($type === 'campaign')
        <p>You {{$action}} the {{$model->tracking_key}} {{$type}} at {{now()->toDateTimeString()}}</p>
    @else
        <p>You {{$action}} the {{$model->name}} {{$type}} at {{now()->toDateTimeString()}}</p>
    @endif
@endsection
