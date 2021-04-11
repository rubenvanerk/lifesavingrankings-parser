@extends('layouts.app')

@section('content')

    <a href="{{route('competitions.parse', ['competition' => $competition])}}">< back to config</a> <br>

    <div class="alert alert-danger" role="alert">
        {{$error}}
    </div>

@endsection
