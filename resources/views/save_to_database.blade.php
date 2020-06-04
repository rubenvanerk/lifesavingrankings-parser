@extends('layouts.base')

@section('content')

    <a href="{{route('config', ['file' => $file])}}">< back to config</a> <br>

    <h1>Competition saved</h1>

@endsection
