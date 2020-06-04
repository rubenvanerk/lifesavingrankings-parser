@extends('layouts.base')

@section('content')
    <form method="post" enctype="multipart/form-data">
        <p>
            <label for="filename">Filename:</label><br>
            <input name="filename" type="text" id="filename" required>
        </p>
        <p>
            <input name="results" type="file" required>
        </p>
        <p>
            <label for="date">Date:</label><br>
            <input name="date" type="date" id="date" required>
        </p>
        <button type="submit">Upload</button>
    </form>
@endsection
