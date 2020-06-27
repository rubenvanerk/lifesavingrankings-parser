@extends('layouts.app')

@section('content')
    <form method="post" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="filename">Filename:</label>
            <input name="filename" type="text" id="filename" required>
        </div>
        <div class="form-group">
            <input name="results" type="file" required>
        </div>
        <div class="form-group">
            <label for="date">Date:</label>
            <input name="date" type="date" id="date" required>
        </div>
        <button type="submit" class="green button mt-5">Upload</button>
    </form>
@endsection
