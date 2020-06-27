@php($fieldName = $parentField ? $parentField . '.' . $name : $name)
@php($fieldType = $config->getType($fieldName))


@if ($fieldType === 'select' || $fieldType === 'select-multiple')
    <div class="row">
        <div class="column">
            <label for="{{$fieldName}}">{{$config->getLabel($fieldName)}}:</label><br>
            <select name="data[{{$fieldName}}]" id="{{$fieldName}}"
                    @if($fieldType === 'select-multiple') multiple @endif>
                @foreach($config->getOptions($fieldName) as $optionValue => $optionName)
                    <option value="{{$optionValue}}" {{$config->{$fieldName} == $optionValue ? 'selected' : ''}}>
                        {{$optionName}}
                    </option>
                @endforeach

                @if ($config->allowCustom($fieldName))
                    <option value="custom" {{$config->valueIsCustom($fieldName) ? 'selected' : ''}}>
                        Custom
                    </option>
                @endif

            </select><br>
        </div>

        @if ($config->allowCustom($fieldName))
            <div class="column">
                <label for="{{$fieldName}}_custom">Custom:</label><br>
                <input name="data[{{$fieldName}}_custom]" type="text" id="{{$fieldName}}_custom"
                       value="{{$config->getValueIfCustom($fieldName)}}"/><br>
            </div>
        @endif

    </div>

@elseif ($fieldType === 'textarea')

    <div class="form-group">
        <label for="{{$fieldName}}">{{$config->getLabel($fieldName)}}:</label><br>
        <textarea id="{{$fieldName}}" name="data[{{$fieldName}}]" cols="100">{{ $config->{$fieldName} }}</textarea><br>
    </div>

@elseif ($fieldType === 'boolean')

    <div class="form-group">
        <label for="{{$fieldName}}">{{$config->getLabel($fieldName)}}:</label>
        <input name="data[{{$fieldName}}]" type="checkbox" id="{{$fieldName}}"
               @if($config->{$fieldName} === 'true') checked @endif value="true"/><br>
    </div>

@elseif (!is_array($value) || $fieldType)

    <div class="form-group">
        <label for="{{$fieldName}}">{{$config->getLabel($fieldName)}}:</label><br>
        <input name="data[{{$fieldName}}]" type="{{$fieldType}}" id="{{$fieldName}}"
               value="{{ $config->{$fieldName} }}"/><br>
    </div>

@else

    @php ($parentField = $parentField ? $parentField .= '.' . $name : $name)
    @php ($values = $value)
    {{--    {{dd($values)}}--}}
    <details>
        <summary>{{$name}}</summary>
        <div class="content">
            @foreach($values as $name => $value)
                @include('partials.field')
            @endforeach
        </div>
    </details>

@endif
