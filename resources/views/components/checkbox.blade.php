@php
    $id = uniqid();
@endphp
<div class="checkbox-container">
    <input type="hidden" name="{{ isset($name) ? $name : '' }}" value="0">
    <input
        id="{{ $id }}"
        type="checkbox"
        class="checkbox-ams"
        name="{{ isset($name) ? $name : '' }}"
        {{ (isset($name) && !empty(old($name)) || isset($checked) && $checked) ? ' checked' : '' }}
        {{ isset($readonly) && $readonly ? ' disabled' : '' }}
        value="1"
    >
    <label for="{{ $id }}"><span class="checkbox">{{ isset($label) ? $label : '' }}</span></label>
</div>
