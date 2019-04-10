@if(!is_array($errors) && $errors->has('message'))
<div class="alert alert-error alert-dismissible fade show" role="alert">
  {{ $errors->first('message') }}
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
@endif

