@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">

                <a href="{{ url()->previous() }}" class="btn btn-primary mb-2">
                    Back
                </a>

                <div class="alert alert-danger" role="alert">
                    {{ $error }}
                </div>

            </div>
        </div>
    </div>
@endsection
