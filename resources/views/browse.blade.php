@extends('layouts.app')

@section('content')
    <h1>Browse competitions</h1>

    <p>
        @foreach($breadcrumbs as $breadcrumb)
            <a href="{{route('browse', ['path' => $breadcrumb['path']])}}">{{$breadcrumb['name']}}</a>
            @if(!$loop->last)/@endif
        @endforeach
    </p>

    @if($directories)
        <h2>Directories</h2>
        <ul>
            @foreach($directories as $directory)
                <li><a href="{{route('browse', ['path' => $directory])}}">{{$directory}}</a></li>
            @endforeach
        </ul>
    @endif

    @if($files)
        <h2>Files</h2>
        <ul>
            @foreach($files as $fileName)
                <li>
                    <a href="{{route('config', ['file' => $fileName])}}">
                        {{$fileName}}
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
@endsection
