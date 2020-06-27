@extends('layouts.app')

@section('content')
    <table class="table-auto">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Location</th>
                <th>Startdate</th>
                <th>Created at</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($competitions as $competition)
                <tr>
                    <td>{{ $competition->id }}</td>
                    <td>{{ $competition->name }}</td>
                    <td>{{ $competition->city }}, {{ $competition->country }}</td>
                    <td>{{ $competition->start_date }}</td>
                    <td>{{ $competition->created_at }}</td>
                    <td>
                        <a href="{{ route('competitions.edit', ['competition' => $competition]) }}">EDIT</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
        <tr>
            <td colspan="6">{{ $competitions->links() }}</td>
        </tr>
        </tfoot>
    </table>
@endsection

