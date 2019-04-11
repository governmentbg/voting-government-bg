@extends('layouts.app')

@section('content')
@include('partials.public-nav-bar')
@include('components.breadcrumbs')

<div class="container center-flex">
    @include('components.modal', [
        'title' => __('custom.terms_and_conditions'),
        'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quo dicta, nobis recusandae odio
                inventore rerum repellendus ab eaque sunt omnis, facere voluptatem voluptas. Voluptatum at, qui incidunt magni iste deleniti!
                Lorem ipsum dolor sit, amet consectetur adipisicing elit. Officiis ducimus qui aut esse adipisci totam quod possimus tempore quae earum, vero
                ex deleniti dicta. Similique explicabo aperiam et eos perferendis!'
    ])
    <form method="POST" enctype="multipart/form-data" action="{{ route('organisation.store') }}" class="m-t-lg p-sm">
        <div class="col-lg-12 col-md-11 col-xs-12 col-lg-offset-1 m-t-md">
        @include('components.status')
            <div class="row justify-center">
                <div class="col-md-10">
                    <div>
                        <h2 class="color-dark"><b>{{ __('custom.register_clean') }}</b></h2>
                        <h5>{{ __('custom.general_info') }}</h5>
                    </div>
                    {{ csrf_field() }}
                    <div class="form-group row required">
                        <label for="name" class="col-sm-4 col-xs-12 col-form-label"> {{ __('custom.org_name') }}:</label>
                        <div class="col-sm-8">
                            <input
                                type="text"
                                class="input-box"
                                name="name"
                                value="{{ old('name') }}"
                            >
                            <span class="error">{{ $errors->first('name') }}</span>
                        </div>
                    </div>
                    <div class="form-group row required">
                        <label for="eik" class="col-sm-4 col-xs-12 col-form-label"> {{ __('custom.eik_bulstat') }}:</label>
                        <div class="col-sm-8">
                            <input
                                type="text"
                                class="input-box"
                                name="eik"
                                value="{{ old('eik') }}"
                            >
                            <span class="error">{{ $errors->first('eik') }}</span>
                        </div>
                    </div>
                    <div class="form-group row required">
                        <label for="address" class="col-sm-4 col-xs-12 col-form-label"> {{ __('custom.management_address') }}:</label>
                        <div class="col-sm-8">
                            <input
                                type="address"
                                class="input-box"
                                name="address"
                                value="{{ old('address') }}"
                            >
                            <span class="error">{{ $errors->first('address') }}</span>
                        </div>
                    </div>
                    <div class="form-group row required">
                        <label for="representative" class="col-sm-4 col-xs-12 col-form-label"> {{ __('custom.representative') }}:</label>
                        <div class="col-sm-8">
                            <input
                                type="text"
                                class="input-box"
                                name="representative"
                                value="{{ old('representative') }}"
                            >
                            <span class="error">{{ $errors->first('representative') }}</span>
                        </div>
                    </div>
                    <div class="form-group row required">
                        <label for="password-confirm" class="col-sm-4 col-xs-12 col-form-label"> {{ __('custom.phone_number') }}:</label>
                        <div class="col-sm-8">
                            <input
                                type="text"
                                class="input-box"
                                name="phone"
                                value="{{ old('phone') }}"
                            >
                            <span class="error">{{ $errors->first('phone') }}</span>
                        </div>
                    </div>
                    <div class="form-group row required">
                        <label for="description" class="col-sm-4 col-xs-12 col-form-label"> {{ __('custom.email') }}:</label>
                        <div class="col-sm-8">
                            <input
                                type="text"
                                class="input-box"
                                name="email"
                                value="{{ old('email') }}"
                            >
                            <span class="error">{{ $errors->first('email') }}</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="password" class="col-sm-4 col-xs-12 col-form-label"> {{ __('custom.in_av') }}:</label>
                        <div class="col-sm-8">
                            @include('components.checkbox', ['name' => 'in_av'])
                            <span class="error">{{ $errors->first('in_av') }}</span>
                        </div>
                    </div>
                </div>
                    <hr class="hr-thin">
                <div class="col-md-10">
                    <h5>{{ __('custom.committee_member_request') }}</h5>
                    <div class="form-group row">
                        <label class="col-sm-4 col-xs-12 col-form-label">{{ __('custom.request_for_candidacy') }}:</label>
                        <div class="col-sm-8 col-xs-6 p-r-none">
                            @include('components.checkbox', ['name' => 'is_candidate'])
                            <span class="error">{{ $errors->first('is_candidate') }}</span>
                        </div>
                    </div>
                    <div class="form-group row for_org_candidates" style="display: none">
                        <label class="col-sm-4 col-xs-12 col-form-label">{{ __('custom.experience_info') }}:</label>
                        <div class="col-sm-8 col-xs-6 p-r-none">
                            <textarea
                                class="txt-area"
                                name="description"
                                placeholder="{{ __('custom.experience_info_placeholder') }}"
                                rows="3"
                                cols="40"
                                maxlength="8000"
                            >{{ old('description') }}</textarea>
                            <span class="error">{{ $errors->first('description') }}</span>
                        </div>
                    </div>
                    <div class="form-group row for_org_candidates" style="display: none">
                        <label class="col-sm-4 col-xs-12 col-form-label">{{ __('custom.reference_materials') }}:</label>
                        <div class="col-sm-8 col-xs-6 p-r-none">
                            <textarea
                                class="txt-area"
                                name="references"
                                placeholder="{{ __('custom.reference_materials_placeholder') }}"
                                rows="3"
                                cols="40"
                                maxlength="8000"
                            >{{ old('references') }}</textarea>
                            <span class="error">{{ $errors->first('references') }}</span>
                        </div>
                    </div>
                </div>
                    <hr class="hr-thin">
                <div class="col-md-10">
                    <h5>{{ __('custom.applied_files') }}</h5>
                    <div class="form-group row">
                        <label class="col-sm-4 col-xs-12 col-form-label">{{ __('custom.as_document_applied') }}:</label>
                        <div class="col-sm-8 col-xs-6 p-r-none">
                            @include('components.checkbox', ['name' => 'as_doc_applied'])
                            <span class="error">{{ $errors->first('has_as_doc') }}</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-4 col-xs-12 p-r-none">
                            @include('components.fileinput', ['name' => 'files[]', 'withoutImg' => true])
                        </div>
                        <div class="col-sm-8 col-xs-6 p-r-none p-t-5">
                            <div class="js-file-upl w-10">
                                <img class="display-inline rotate-180" src="{{ asset('img/download.svg') }}" height="35px" width="30px" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group row plus-file-container">
                        <div class="col-sm-4 col-xs-12 p-r-none">
                            <div class="js-plus-file-upl w-10">
                                <img class="display-inline rotate-180" src="{{ asset('img/plus.svg') }}" height="35px" width="30px" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-xs-12 col-form-label">
                            {{ __('custom.accept') }} <a href="#" class="js-showTerms">{{ __('custom.the_terms') }}</a>:
                        </label>
                        <div class="col-sm-8 col-xs-6 p-r-none">
                            @include('components.checkbox', ['name' => 'terms_accepted'])
                            <span class="error">{{ $errors->first('terms_accepted') }}</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-8 col-xs-6 p-r-none offset-sm-4 text-center">
                            @include('components.button', ['buttonLabel' => __('custom.register_action')])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection
