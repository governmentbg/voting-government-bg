<h3 class="color-black">{{ isset($title) ? $title : '' }}</h3>
    <div class="file-field">
        <div class="btn-rounded waves-effect btn-sm float-left input-f">
            <span class="back-z p-t-5 c-darkBlue">Select file</span>
            <input
                type="file"
                title="Select File"
                class="opacity-0 front-z js-file-input"
                name="{{ isset($name) ? $name : '' }}"
                accept="application/pdf, image/tiff, image/jpg, image/jpeg, image/png, image/bmp"
                data-consec-num="0"
            >
        </div>
    </div>
@if (!(isset($withoutImg) && $withoutImg))
    <img class="display-inline m-b-8 rotate-180 p-r-5" src="{{ asset('img/download.svg') }}" height="35px" width="30px" />
@endif
