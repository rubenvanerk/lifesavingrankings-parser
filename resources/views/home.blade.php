@extends('layouts.app')

@section('content')
    <div class="content-center">
        <h1 class="text-indigo-600 mb-4 text-center">CompVault</h1>

        <p class="mb-4">
            CompVault is a project developed by lisasp.org.
            CompVault keeps an archive of Lifesaving competitions.
            CompVault has an extensive and free to use API and powers applications like LifesavingRankings.com and JAuswertung.
        </p>

        <div class="inline-block mx-auto">
            <a href="{{ route('upload') }}" class="btn btn-green">Add a competition</a>
            <a href="" class="btn btn-green">API docs</a>
        </div>
    </div>
@endsection
