@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <form action="{{ route('competitions.destroy' , $competition->id)}}" method="POST">
                    <input name="_method" type="hidden" value="DELETE">
                    @csrf

                    <div class="modal-footer no-border">
                        <button type="button" class="btn btn-info" data-dismiss="modal">No</button>
                        <button type="submit" class="btn btn-primary">Yes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
