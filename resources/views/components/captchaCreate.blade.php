<div class="row">
    <div class="col-md-4"></div>
    <div class="form-group col-md-4">
        <div class="captcha">
        <span>{!! captcha_img() !!}</span>
        <button type="button" class="btn btn-primary login-btn" id="refresh">{{__('custom.refresh')}}</button>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4"></div>
    <div class="form-group col-md-4">
        <input id="captcha" type="text" class="form-control" placeholder="Enter Captcha" name="captcha">
        <span class="error">{{ $errors->first('captcha') }}</span>
    </div>
</div>
