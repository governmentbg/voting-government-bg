@php
    $id = uniqid();
@endphp
<div class="checkbox-container">
    <input
        id="{{$id}}"
        type="checkbox"
        class="checkbox-ams"
        name="{{isset($name) ? $name : ''}}"
        {{ isset($checked) && $checked ? ' checked' : '' }}
        {{ isset($readonly) && $readonly ? ' disabled' : '' }}
    >
    <label for="{{$id}}"><span class="checkbox">{{ isset($label) ? $label : '' }}</span></label>
</div>

