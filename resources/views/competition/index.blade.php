@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">

                <div class="mb-2">
                    <a href="{{ route('competitions.create') }}" class="btn btn-primary">
                        Create
                    </a>
                </div>

                <table class="table table-hover">
                    <thead class="thead-light">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Location</th>
                        <th>Startdate</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($competitions as $competition)
                        <tr>
                            <td>{{ $competition->id }}</td>
                            <td>{{ $competition->name }}</td>
                            <td>{{ $competition->city }}</td>
                            <td>{{ $competition->date }}</td>
                            <td>
                                <a class="btn btn-secondary btn-sm"
                                   href="{{ route('competitions.edit', ['competition' => $competition]) }}">EDIT</a>
                                <a class="btn btn-secondary btn-sm"
                                   href="{{ route('competitions.parse', ['competition' => $competition]) }}">PARSE</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="5">{{ $competitions->links() }}</td>
                    </tr>
                    </tfoot>
                </table>

            </div>
        </div>
    </div>
@endsection

