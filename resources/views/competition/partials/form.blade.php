<form method="post" action="@if($competition){{ route('competitions.update', ['competition' => $competition]) }}@else{{ route('upload') }}@endif" enctype="multipart/form-data">
    @csrf
    @if($competition)
        <a href="{{ $competition->getFirstMediaUrl('results_file') }}" class="btn btn-primary">File</a>
        @method('PUT')
    @else
        <div class="form-group">
            <label>Choose your result file</label>
            <input name="file" type="file" class="form-control-file" required>
        </div>
    @endif

    <div class="form-group">
        <label for="name">Name:</label>
        <input name="name" type="text" id="name" required class="form-control" value="{{ $competition->name }}">
    </div>

    <div class="form-group">
        <label for="city">City:</label>
        <input name="city" type="text" id="city" required class="form-control" value="{{ $competition->city }}">
    </div>

    <div class="form-group">
        <label for="country">Country:</label>
        <input name="country" type="text" id="country" required class="form-control" value="{{ $competition->country }}">
    </div>

    <div class="form-group">
        <label for="start_date">Start date:</label>
        <input name="start_date" type="date" id="start_date" required class="form-control" value="{{ $competition->start_date }}">
    </div>

    <div class="form-group">
        <label for="end_date">End date:</label>
        <input name="end_date" type="date" id="end_date" class="form-control" value="{{ $competition->end_date }}">
    </div>

    <div class="form-group">
        <label for="timekeeping">Timekeeping</label>
        <select id="timekeeping" name="timekeeping" class="form-control">
            <option value="">Unknown</option>
            <option value="electronic">Electronic</option>
            <option value="by_hand">By hand</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary">{{ $competition ? 'Update' : 'Upload' }}</button>
</form>
