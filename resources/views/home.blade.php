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

            <h1>
                The LISASP competition archive
            </h1>

            <p>
                The LISASP Archive is a project developed by <a href="https://lisasp.org" target="_blank">lisasp.org</a>.
                LISASP keeps an archive of Lifesaving competitions.
                LISASP Archive has an extensive and free to use API and powers applications like
                LifesavingRankings.com and JAuswertung.
            </p>

            <a href="{{ route('upload') }}" class="btn btn-primary">Add a competition</a>
            <a href="/api/documentation" class="btn btn-primary" target="_blank">API docs</a>
        </div>
    </div>
@endsection
