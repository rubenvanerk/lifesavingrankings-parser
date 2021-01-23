@extends('layouts.app')

@section('content')
    <h2>Parsing {{$file}}</h2>
    <a class="btn btn-primary" href="{{ $competition->getFirstMediaUrl('results_file') }}"
       target="_blank">
        View source file
    </a>

    <hr class="my-3">

    <form method="post">
        @csrf
        @method('PUT')

        <div class="container">
            @foreach($config->config as $name => $value)
                @php($parentField = '')
                @include('partials.field')
            @endforeach
        </div>

        <label class="font-weight-bolder mt-2">Action:</label>

        <div class="form-check">
            <input type="radio" name="action" id="save_config" value="save_config" checked class="form-check-input">
            <label for="save_config" class="form-check-label">Save config</label><br>
        </div>

        <div class="form-check">
            <input type="radio" name="action" id="dry_run" value="dry_run" class="form-check-input">
            <label for="dry_run" class="form-check-label">Dry run</label>
        </div>

        <div class="form-check">
            <input type="radio" name="action" id="save_to_database" value="save_to_database" class="form-check-input">
            <label for="save_to_database" class="form-check-label">Save to database</label>
        </div>

        <button type="submit" class="btn btn-primary my-3">Save</button>

    </form>

    <h2>Raw data</h2>

    @if($fileExtension === 'csv')
        {!! $rawData !!}
    @else
        <pre class="overflow-scroll">
            {{ $rawData }}
        </pre>
    @endif

@endsection
