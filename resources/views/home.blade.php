@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">Dashboard</div>

        <div class="card-body">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <a href="{{ route('upload') }}" class="btn btn-primary">Add</a>
            <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
        </div>
    </div>
@endsection
