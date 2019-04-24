@php
    $errorKey = isset($errorKey) ? $errorKey : 'message';
@endphp
@if (!is_array($errors) && $errors->has($errorKey))
<div class="alert alert-error alert-dismissible fade show" role="alert">
    {{ $errors->first($errorKey) }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif
