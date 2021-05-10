@include('partials.errors')

<form method="post"
      action="@if(isset($competition)){{ route('competitions.update', ['competition' => $competition]) }}@else{{ route('competitions.store') }}@endif"
      enctype="multipart/form-data">
    @csrf
    @if(isset($competition))
        <a href="{{ $competition->competition_config->getFirstMediaUrl('results_file') }}" class="btn btn-primary" target="_blank">File</a>
        @method('PUT')
    @endif

    <div class="form-group">
        <label>Choose your result file</label>
        <input name="file" type="file" class="form-control-file" @if(!isset($competition))required @endif>
    </div>

    <div class="form-group">
        <label for="name">Name:</label>
        <input name="name" type="text" id="name" required class="form-control" value="{{ $competition->name ?? old('name') }}">
    </div>

    <div class="form-group">
        <label for="original-name">Original name:</label>
        <input name="original_name" type="text" id="original-name" class="form-control"
               value="{{ $competition->original_name ?? old('original_name') }}">
    </div>

    <div class="form-group">
        <label for="city">City:</label>
        <input name="city" type="text" id="city" required class="form-control" value="{{ $competition->city ?? old('city') }}">
    </div>

    <div class="form-group">
        <label for="country">Country:</label>
        <select id="country" name="country_id" class="form-control" required>
            <option value="">Unknown</option>
            @foreach($countries as $country)
                <option value="{{ $country->id }}"
                        @if((isset($competition) && optional($competition->country)->id == $country->id) || old('country_id') == $country->id) selected @endif>
                    {{ $country->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="start_date">Start date:</label>
        <input name="date" type="date" id="start_date" required class="form-control"
               value="{{ $competition->date ?? old('date') }}">
    </div>

    <div class="form-group">
        <label for="end_date">End date:</label>
        <input name="end_date" type="date" id="end_date" class="form-control"
               value="{{ $competition->end_date ?? old('end_date') }}">
    </div>

    <div class="form-group">
        <label for="timekeeping">Timekeeping</label>
        <select id="timekeeping" name="type_of_timekeeping" class="form-control">
            <option value="0">Unknown</option>
            <option value="1">Electronic</option>
            <option value="2">By hand</option>
        </select>
    </div>

    <div class="form-group">
        <label for="comment">Comment:</label>
        <input name="comment" type="text" id="comment" class="form-control" value="{{ $competition->comment ?? old('comment') }}">
    </div>

    @if(isset($competition))
        <button type="submit" class="btn btn-danger" form="delete"
                onclick="return confirm('Are you sure you want to delete this competition config?')">
            Delete
        </button>
    @endif

    <button type="submit" class="btn btn-primary float-right">{{ isset($competition) ? 'Update' : 'Upload' }}</button>
</form>

@if(isset($competition))
<form action="{{ route('competitions.destroy', $competition)}}" method="POST" id="delete">
    @csrf
    @method('DELETE')
</form>
@endif
