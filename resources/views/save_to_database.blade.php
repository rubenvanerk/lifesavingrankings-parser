@extends('layouts.app')

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <a href="{{ route('competitions.parse', ['competition' => $competition]) }}" class="btn btn-primary mb-2">
                    Back to config
                </a>

                <h1>Competition saved</h1>
            </div>
        </div>
    </div>

@endsection
