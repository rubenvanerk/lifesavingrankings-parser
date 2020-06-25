@extends('layouts.base')

@section('content')
    <h2>Parsing {{$file}}</h2>
    <a href="{{$temporaryUrl}}" target="_blank">
        <button>View source file</button>
    </a>

    <hr>

    <form method="post">
        @csrf

        @foreach($config->config as $name => $value)
            @php($parentField = '')
            @include('partials.field')
        @endforeach

        <p>Action:</p>

        <input type="radio" name="action" id="save_config" value="save_config" checked>
        <label for="save_config">Save config</label><br>

        <input type="radio" name="action" id="dry_run" value="dry_run">
        <label for="dry_run">Dry run</label><br>

        @foreach($databases as $name => $config)
            <input type="radio" name="action" id="save_{{$name}}_database" value="{{$name}}">
            <label for="save_{{$name}}_database">Save to {{$name}} database</label><br>
        @endforeach

        <button type="submit">Save</button>

    </form>

    <details open>
        <summary>Raw data</summary>
        <pre>
            {{$rawData}}
        </pre>
    </details>

@endsection
