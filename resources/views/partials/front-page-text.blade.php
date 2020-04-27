<h3><b>{{ __('custom.online_voting_system') }}</b></h3>
<span class="font-weight-bold text-danger">{{ __('custom.platform_state') }}</span>
<p>
    {{ __('custom.front_text') }}<br>
    {{ __('custom.front_text_2') }}<br>
    <span class="c-darkBlue font-weight-bold">{{__('custom.information')}} </span>{{ __('custom.front_info_vote') }} <span class="font-weight-bold">
        <a href="{{ Storage::url('downloads/Информация за гласуването за членове на Съвета.pdf') }}" target="_blank">ТУК</a>.
    </span><br>
    <span class="c-darkBlue font-weight-bold">{{__('custom.information')}} </span>{{ __('custom.front_info') }} <span class="font-weight-bold">
        <a href="{{ Storage::url('downloads/Инфо за СРГО.pdf') }}" target="_blank">ТУК</a>.
    </span><br>
    {{ __('custom.read_pointers') }} <span class="c-darkBlue font-weight-bold"> {{ __('custom.pointers') }} </span> {{ __('custom.reg_pointers') }}<span class="font-weight-bold"> <a href="{{ Storage::url('downloads/Указания за регистрация на Платформата за избор на СРГО.pdf') }}" target="_blank">ТУК</a>.</span><br>
    {{ __('custom.front_terms_start') }} <span class="c-darkBlue font-weight-bold">{{__('custom.terms')}} </span> {{ __('custom.front_terms_end') }} <span class="font-weight-bold">
        <a href="{{ Storage::url('downloads/Условия за ползване Платформата за избор на СРГО.pdf') }}" target="_blank">ТУК</a>.
    </span><br>
    {{ __('custom.created_for') }}
</p>
