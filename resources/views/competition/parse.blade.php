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

        <div class="form-group inline-label">
            <input type="radio" name="action" id="save_to_database" value="save_to_database">
            <label for="save_to_database" class="inline">Save to database</label>
        </div>

        <button type="submit" class="btn btn-green">Save</button>

    </form>

    <details open class="mt-5">
        <summary class="shadow px-1 py-3 my-3">Raw data</summary>
        <pre class="overflow-scroll p-3 shadow-inner">
            {{$rawData}}
        </pre>
    </details>

@endsection
