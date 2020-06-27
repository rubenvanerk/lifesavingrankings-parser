@extends('layouts.app')

@section('content')
    <form method="post" action="{{ route('upload') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <input name="file" type="file" required>
        </div>

        <div class="form-group">
            <label for="name">Name:</label>
            <input name="name" type="text" id="name" required>
        </div>

        <div class="form-group">
            <label for="city">City:</label>
            <input name="city" type="text" id="city" required>
        </div>

        <div class="form-group">
            <label for="country">Country:</label>
            <input name="country" type="text" id="country" required>
        </div>

        <div class="form-group">
            <label for="start_date">Start date:</label>
            <input name="start_date" type="date" id="start_date" required>
        </div>

        <div class="form-group">
            <label for="end_date">End date:</label>
            <input name="end_date" type="date" id="end_date">
        </div>

        <div class="form-group">
            <label for="timekeeping">Timekeeping</label>
            <select id="timekeeping" name="timekeeping">
                <option value="">Unknown</option>
                <option value="electronic">Electronic</option>
                <option value="by_hand">By hand</option>
            </select>
        </div>

        <button type="submit" class="btn btn-green mt-5">Upload</button>
    </form>
@endsection
