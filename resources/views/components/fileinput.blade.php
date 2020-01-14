@php $multiple = !isset($multiple)? true : $multiple; @endphp

@if (isset($title))
    <h3 class="color-black p-t-15">{{ $title }}</h3>
@endif

<div class="file-info">
    <label>{{ __('custom.files_info') }}</label>
</div>

<div class="multiple-input-container">
    <div class="file-field file-input-container">
        <div class="btn-rounded waves-effect btn-sm float-left input-f">
            <span class="back-z p-t-5 c-darkBlue text-overflow">Select file</span>
            <input
                type="file"
                title="Select File"
                class="opacity-0 front-z js-file-input"
                name="{{ isset($name) ? $name : 'files[]' }}"
                accept="application/pdf, image/tiff, image/jpg, image/jpeg, image/png, image/bmp"
            >
        </div>
        @if (!(isset($withoutImg) && $withoutImg))
            <img
                class="upload-btn js-file-upl display-inline m-b-8 rotate-180 c-pointer"
                src="{{ asset('img/download.svg') }}"
                title="{{ __('custom.select') }}"
                data-toggle="tooltip"
                data-placement="top"
                height="35px"
                width="30px"
            />
        @endif
    </div>
    @if (isset($multiple) && $multiple)
    <div class="add-file-input">
        <img
            class="add-multiple js-plus-file-upl display-inline rotate-180 c-pointer"
            title="{{ __('custom.add') }}"
            data-toggle="tooltip"
            data-placement="top"
            src="{{ asset('img/plus.svg') }}"
            height="35px"
            width="30px"
        />
    </div>
    @endif
</div>

@php
    http2_push_image('/img/download.svg');
    http2_push_image('/img/plus.svg');
@endphp