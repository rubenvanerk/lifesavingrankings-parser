@extends('layouts.app')

@section('content')
    <div class="flex">
        <h2 class="inline-block">Parsing {{$file}}</h2>
        <a class="green button inline-block ml-auto" href="{{ $competition->getFirstMediaUrl('results_file') }}" target="_blank">
           View source file
        </a>
    </div>

    <hr class="my-5">

    <form method="post">
        @csrf
        @method('PUT')

        @foreach($config->config as $name => $value)
            @php($parentField = '')
            @include('partials.field')
        @endforeach

        <p>Action:</p>

        <div class="form-group inline-label">
        <input type="radio" name="action" id="save_config" value="save_config" checked>
        <label for="save_config">Save config</label><br>
        </div>

        <div class="form-group inline-label">
            <input type="radio" name="action" id="dry_run" value="dry_run">
            <label for="dry_run">Dry run</label>
        </div>

        @foreach($databases as $name => $config)
            <div class="form-group inline-label">
                <input type="radio" name="action" id="save_{{$name}}_database" value="{{$name}}">
                <label for="save_{{$name}}_database" class="inline">Save to {{$name}} database</label>
            </div>
        @endforeach

        <button type="submit" class="btn btn-green">Save</button>

    </form>

    <details open>
        <summary>Raw data</summary>
        <pre class="overflow-scroll p-3 shadow-inner">
            {{$rawData}}
        </pre>
    </details>

@endsection
