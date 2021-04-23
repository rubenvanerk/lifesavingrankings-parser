@php($fieldName = $parentField ? $parentField . '.' . $name : $name)
@php($fieldType = $config->getType($fieldName))


@if ($fieldType === 'select' || $fieldType === 'select-multiple')
    <div class="row">
        <div class="col-sm">
            <label for="{{$fieldName}}">{{$config->getLabel($fieldName)}}:</label>
            <select name="data[{{$fieldName}}]" id="{{$fieldName}}" class="form-control"
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

            </select>
        </div>

        @if ($config->allowCustom($fieldName))
            <div class="col-sm form-group">
                <label for="{{$fieldName}}_custom">Custom:</label>
                <input name="data[{{$fieldName}}_custom]" type="text" id="{{$fieldName}}_custom"
                       value="{{$config->getValueIfCustom($fieldName)}}" class="form-control text-monospace"/>

            </div>
        @endif
    </div>

@elseif ($fieldType === 'textarea')

    <div class="row">
        <div class="col-sm form-group">
            <label for="{{$fieldName}}">{{$config->getLabel($fieldName)}}:</label>
            <textarea id="{{$fieldName}}" name="data[{{$fieldName}}]"
                      cols="100" class="form-control">{{ $config->{$fieldName} }}</textarea>
        </div>
    </div>

@elseif ($fieldType === 'boolean')

    <div class="row">
        <div class="col-sm">
            <div class="form-check">
                <input name="data[{{$fieldName}}]" class="form-check-input" type="checkbox" id="{{$fieldName}}"
                       @if($config->{$fieldName} === 'true') checked @endif value="true"/>
                <label for="{{$fieldName}}" class="form-check-label">{{$config->getLabel($fieldName)}}</label>
            </div>
        </div>
    </div>

@elseif (!is_array($value) || $fieldType)

    <div class="row">
        <div class="col-sm form-group">
            <label for="{{$fieldName}}">{{$config->getLabel($fieldName)}}:</label>
            <input name="data[{{$fieldName}}]" type="{{$fieldType}}" id="{{$fieldName}}"
                   value="{{ $config->{$fieldName} }}" class="form-control"/>
        </div>
    </div>

@else

    @php ($parentField = $parentField ? $parentField .= '.' . $name : $name)
    @php ($values = $value)
    <details>
        <summary class="py-2">{{$name}}</summary>
        <div class="content p-2 shadow-sm">
            @foreach($values as $name => $value)
                @include('partials.field')
            @endforeach
        </div>
    </details>

@endif
