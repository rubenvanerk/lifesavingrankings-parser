@extends('layouts.app')

@section('content')

    <a href="{{route('competitions.parse', ['competition' => $competition])}}">< back to config</a> <br>

    <h1>{{$competition->name}}</h1>
    Date: {{$competition->start_date}}<br>
    Location: {{$competition->city}}<br>
    Timekeeping: {{$competition->timekeeping}}<br>

    Result count: {{count($parsedCompetition->results)}}<br>

    <table class="table table-hover">
        <thead class="thead-light">
            <tr>
                <th>Name</th>
                <th>YoB</th>
                <th>Gender</th>
                <th>Nationality</th>
                <th>Team</th>
                <th>Event</th>
                <th>Time / code</th>
                <th>Splits</th>
                <th>Round</th>
                <th>Heat</th>
                <th>Lane</th>
                <th>Reactiontime</th>
                <th>Points</th>
            </tr>
            </thead>
            <tbody>
            @foreach($parsedCompetition->results as $result)
                @php($athlete = $result->athlete ?? $result->athletes)
                <tr>
                    @if(is_array($athlete))
                        <td>@foreach($athlete as $athl) {{$athl->name}}, @endforeach</td>
                        <td></td>
                        <td>{{$athl->gender === 1 ? 'Male' : 'Female'}}</td>
                        <td>{{$athl->nationality}}</td>
                        <td>{{$athl->team}}</td>
                    @else
                        <td>{{$athlete->name}}</td>
                        <td>{{$athlete->yearOfBirth}}</td>
                        <td>{{$athlete->gender === 1 ? 'Male' : 'Female'}}</td>
                        <td>{{$athlete->nationality}}</td>
                        <td>{{$athlete->team}}</td>
                    @endif
                    <td>{{$result->eventId}}</td>
                    <td>{{$result->getTimeStringForDisplay()}}</td>
                    <td>
                        @if($result->splits)
                            <small>
                                @foreach($result->splits as $split)
                                    {{$split->distance}}: {{$split->getTimeStringForDisplay()}}<br>
                                @endforeach
                            </small>
                        @endif
                    </td>
                    <td>{{$result->round}}</td>
                    <td>{{$result->heat}}</td>
                    <td>{{$result->lane}}</td>
                    <td>{{$result->getReactionTimeStringForDisplay()}}</td>
                    <td>0</td>
                </tr>
            @endforeach
            </tbody>
        </table>

@endsection
