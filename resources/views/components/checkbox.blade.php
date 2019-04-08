@php 
    $id = uniqid();
@endphp
<div class="checkbox-container">
    <input id="{{$id}}" type="checkbox" class="checkbox-ams" name="{{isset($name) ? $name : ''}}">
    <label for="{{$id}}"><span class="checkbox"></span></label>
</div>

