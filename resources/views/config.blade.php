@extends('layouts.base')

@section('content')
    <h2>Parsing {{$file}}</h2>
    <a href="{{$temporaryUrl}}" target="_blank">
        <button>View source file</button>
    </a>

    <hr>

    <form method="post">
        @foreach($config->config as $name => $value)
            @php($parentField = '')
            @include('partials.field')
        @endforeach

        <p>Action:</p>

        <input type="radio" name="action" id="save_config" value="save_config" checked>
        <label for="save_config">Save config</label><br>

        <input type="radio" name="action" id="dry_run" value="dry_run">
        <label for="dry_run">Dry run</label><br>

        <input type="radio" name="action" id="save_database" value="save_database">
        <label for="save_database">Save to database</label><br>

        <button type="submit">Save</button>

    </form>

    <details open>
        <summary>Raw data</summary>
        <pre>
            {{$rawData}}
        </pre>
    </details>

@endsection
