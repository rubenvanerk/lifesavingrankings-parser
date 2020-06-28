@extends('layouts.base')

@section('content')

    <a href="{{route('competitions.parse', ['competition' => $competition])}}">< back to config</a> <br>

    <h1>Competition saved</h1>

@endsection
