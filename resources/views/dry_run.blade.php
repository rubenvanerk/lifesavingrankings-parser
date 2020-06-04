@extends('layouts.base')

@section('content')

    <a href="{{route('config', ['file' => $file])}}">< back to config</a> <br>

    <h1>{{$competition->name}}</h1>
    Date: {{$competition->date}}<br>
    Location: {{$competition->location}}<br>
    Timekeeping: {{$competition->timekeeping}}<br>
    Credit: {{$competition->credit}}<br><br>

    Result count: {{$competition->resultCount}}<br>

    @foreach($competition->events as $event)
        <details>
            <summary>{{$event->getName()}}</summary>
            <table>
                <thead>
                <tr>
                    <th>Name</th>
                    <th>YoB</th>
                    <th>Gender</th>
                    <th>Nationality</th>
                    <th>Club</th>
                    <th>Time / code</th>
                    <th>Splits</th>
                    <th>Round</th>
                    <th>Heat</th>
                    <th>Lane</th>
                    <th>Reactiontime</th>
                </tr>
                </thead>
                <tbody>
                @foreach($event->results as $result)
                    @php($athlete = $result->athlete)
                    <tr>
                        <td>{{$athlete->name}}</td>
                        <td>{{$athlete->yearOfBirth}}</td>
                        <td>{{$athlete->gender === 1 ? 'Male' : 'Female'}}</td>
                        <td>{{$athlete->nationality}}</td>
                        <td>{{$athlete->club}}</td>
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
                    </tr>
                @endforeach
                </tbody>
            </table>
        </details>

    @endforeach
@endsection
