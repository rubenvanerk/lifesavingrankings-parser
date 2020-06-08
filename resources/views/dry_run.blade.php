@extends('layouts.base')

@section('content')

    <a href="{{route('config', ['file' => $file])}}">< back to config</a> <br>

    <h1>{{$competition->name}}</h1>
    Date: {{$competition->date}}<br>
    Location: {{$competition->location}}<br>
    Timekeeping: {{$competition->timekeeping}}<br>
    Credit: {{$competition->credit}}<br><br>

    Result count: {{count($competition->results)}}<br>

        <details>
            <table>
                <thead>
                <tr>
                    <th>Name</th>
                    <th>YoB</th>
                    <th>Gender</th>
                    <th>Nationality</th>
                    <th>Team</th>
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
                @foreach($competition->results as $result)
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
                        <td>{{$result->calculatePoints()}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </details>

@endsection
