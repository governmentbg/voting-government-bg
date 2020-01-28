@extends('layouts.app')

@section('content')
@include('partials.public-nav-bar')
@include('components.breadcrumbs')

<div class="container center-flex">
    <div class="modal inmodal fade" id="info" tabindex="-1" role="dialog"  aria-hidden="true">
        <div class="modal-dialog m-w-1000" >
            <div class="modal-content">
                <div class="p-15 p-b-none">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true" class="c-darkBlue modal-x"><b>&times;</b></span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title c-darkBlue">УСЛОВИЯ ЗА ПОЛЗВАНЕ НА ПЛАТФОРМА ЗА РЕГИСТРАЦИЯ И ИЗБОР НА ЧЛЕНОВЕ НА СЪВЕТА ЗА РАЗВИТИЕ НА ГРАЖДАНСКОТО ОБЩЕСТВО (ПЛАТФОРМАТА)</h4>
                    <hr class="hr-small">
                </div>
                <div class="modal-body">
                    <b>І. ОБЩИ ПОЛОЖЕНИЯ</b><br>
                    1. Платформата е разработена на основание чл. 9 от Правилника за организацията и дейността на Съвета за развитие на гражданското общество (Съвета) и се поддържа от администрацията на Министерския съвет.<br><br>
                    2. Платформата е уеб-базирана електронна система, която позволява регистрацията, кандидатирането и електронното гласуване за членове на Съвета.<br><br>

                    <b>II. РЕГИСТРАЦИЯ И ВИДОВЕ ПОТРЕБИТЕЛИ</b><br>

                    3. Видове потребители на Платформата и права на достъп:<br>
                        <p class="p-l-25"><b>3.1 Регистрирани потребители.</b><br>
                            Регистрирани потребители могат да бъдат само юридически лица с нестопанска цел, осъществяващи дейност в обществена полза. Регистрираните потребители:<br>
                            •	могат да се кандидатират за член на Съвета при отворена процедура за избор на членове на Съвета;<br>
                            •	могат да гласуват в процедура за избор на членове на Съвета (след потвърдена регистрация);<br>
                            •	могат да комуникират с членове на Комисията по чл. 11 от Правилника;
                        </p>

                    <b>3.2 Членове на комисията по чл. 11 от Правилника за избор на членове на Съвета.</b>
                    <p class="p-l-25">Членове на Комисията могат да бъдат служители на администрацията на Министерския съвет, определени със заповед на Главния секретар. Членовете на Комисията по чл. 11 от Правилника:<br>
                        •	верифицират направените регистрации за участие в процедурата за избор и за кандидатиране за членове на Съвета;<br>
                        •	могат да нанасят корекции в данните на регистрираните ЮЛНЦ при подадена заявка;<br>
                        •	комуникират с регистрираните потребители чрез Платформата.
                    </p>
                    <b>3.3 Нерегистрирани потребители.</b><br>
                    <p class="p-l-25">Нерегистрираните потребители са тези, които не са извършили регистрация на Платформата или чиято регистрация е била отхвърлена. Нерегистрираните потребители:<br>
                        •	имат достъп до публикувания на Платформата списък с регистрирани в процедурата за избор  юридически лица с нестопанска цел<br>
                        •	имат достъп до публикувания в Платформата списък с кандидати за членове на Съвета;<br>
                        •	имат достъп до публикувания в Платформата списък с класирани кандидати за членове на Съвета;<br>
                        •	не могат да се кандидатират за член на Съвета;<br>
                        •	не могат да участват в процедура за избор на член на Съвета.
                    </p>
                    <b>3.4 Потребители с „отхвърлена регистрация“</b> са тези, които не отговорят на условията за регистриран потребител. Потребителите с „отхвърлена регистрация“ имат правата на нерегистрирани потребители.<br><br>
                    4. Регистрацията на потребители на Платформата се осъществява както следва:<br>
                    <p class="p-l-25">4.1. Потребителят (чрез законния си представител) попълва регистрационната форма и приема настоящите Общи условия.<br>
                        4.2 Регистрацията е потвърдена, ако са изпълнени следните условия:<br>
                        •	потребителят е юридическо лице с нестопанска цел, осъществяващо дейност в обществена полза;<br>
                        •	потребителят е представил данни за наличието на опит в осъществяването на дейности в обществена полза в подкрепа на развитието на гражданското общество и гражданското участие, в случай, че се е кандидатирал за член на Съвета.<br>
                        4.3 На посочената във формата за регистрацията електронна поща потребителят получава информация за потребителския си профил, чрез който може да има достъп до Платформата.
                    </p>

                    5. Регистрираният потребител е единственият отговорен за своята парола за достъп, както и за последиците от предоставянето й на трети лица.<br><br>
                    6. След потвърдена регистрация, потребителят не може да променя предоставената във формата за регистрация информация, овен ако не е допусната грешка, за което се сезира Комисията по чл. 11 в предвидените в Правилника срокове.<br><br>
                    7. Информацията и данните, предоставени от регистриран потребител, в качеството му на кандидат за член на Съвета са достъпни само за членовете на Комисията по чл. 11 от Правилника.<br><br>
                    8. На Платформата се съхранява история за всички действия, които регистрираните потребители и членовете на Комисията са извършили в системата.<br><br>

                    <b>III. ПРАВИЛА ЗА ИЗПОЛЗВАНЕ НА ИНФОРМАЦИЯТА НА ПЛАТФОРМАТА, ЗА СИГУРНОСТ И ПОВЕРИТЕЛНОСТ</b><br>
                    9. Приемайки настоящите условия, регистрираният потребител се съгласява, че тази част от предоставената от него информация във връзка със статуса му на участник в процедурата за избор на членове на Съвета и във връзка със заявено от него желание за кандидатиране като член на Съвета ще бъде публично достъпна.<br><br>
                    10. Приемайки настоящите условия, потребителят има право на достъп до публикуваната на Платформата информация относно статусите му на регистриран потребител, кандидат за член на Съвета или на потребител с отхвърлена регистрация, и при констатиране на грешки или допуснати неточности да сезира Комисията по чл. 11 чрез посочената на Платформата електронна поща.<br><br>
                    11. Приемайки настоящите условия, потребителят се съгласява:<br>
                    <p class="p-l-25">
                        •	да въвежда данните във формата за регистрация (в това число при описанието на опита си на кандидат) на кирилица;<br>
                        •	да спазва добрия тон;<br>
                        •	да не агитира към расова, полова или етническа дискриминация.<br>
                    </p>
                    12. С приемането на настоящите Общи условия, потребителите декларират своето съгласие, техните данни да се събират, обработват и съхраняват законосъобразно и добросъвестно за целите на регистрацията им в процедурата за избор за членове на Съвета.<br><br>
                    13. Потребителят няма право, използвайки Платформата:<br>
                    <p class="p-l-25">
                        •	да попълва непълни, неверни или погрешни данни, или информация, която не е негова собственост;<br>
                        •	да предоставя информация, която нарушава права върху интелектуална собственост на други лица или правата, свързани със защитата на личните данни на други лица;<br>
                        •	да предоставя информация, която е съдържа неприлични или клеветнически изрази, заплахи или оскърбления;<br>
                        •	да публикува информация, съдържаща вируси, троянски коне, “cancelbots” или да извършва други действия, които са предназначени да причиняват вреди или разрушаване на системата, базата данни или информацията и/или са насочени към извличане и употреба на чужда информация.<br>
                    </p>
                    14. Потребителят нямат право:<br>
                    <p class="p-l-25">
                        •	да използва регистрацията на друг потребител с парола и потребителско име, за използването на които не му е дадено разрешение;<br>
                        •	да прави опити за проучване, сканиране или тестване на уязвимостта на системата или мрежата, или да прави пробив/и в системата, или други действия без съответна оторизация, целящи нарушаване на нейната цялост;<br>
                        •	да правят опити за заразяване с вирус на уеб страницата на Платформата, претоварване на трафика, претоварване на електронната поща, разрушаване на информацията;<br>
                        •	да изпраща нежелани имейли, включително промоции и/или реклами на продукти или услуги до членовете на Комисията по чл. 11 от Правилника;<br>
                        •	да извършват всякаква дейност, водеща до несъразмерни натоварвания на структурата на Платформата.<br>
                    </p>
                    15. Членовете на Комисията се съгласяват да не предоставят на трети лица информацията, която не е публично достъпна и им е станала им известна в рамките на процедура за избор, с изключение на случаите, когато такава информация трябва да се предостави на компетентните орган вследствие на законово изискване, или когато за това има изрично съгласие на потребителя.<br><br>
                    <b>IV. ОТГОВОРНОСТИ</b><br>
                    16. Членовете на Комисията по чл. 11 от Правилника не носят отговорност при неправомерно използване на правно-индивидуализиращите белези на правен субект за извършване на регистрация на Платформата от негово име, но без негово знание и съгласие.<br><br>
                    17. В хипотезата на т. 15, лицето, лицето което твърди, че неправомерно са използвани негови данни за извършване на регистрация в процедура за избор на членове на Съвета, може да уведоми комисията по чл. 11 от Правилника и да поиска:<br>
                    <p class="p-l-25">
                        •	да бъде отхвърлена направената регистрация от негово име, но без негово знание и съгласие, в случай, че не желае да участва в процедурата за избор или<br>
                        •	да използва направената регистрация, като получи достъп до Платформата със следващите се от това възможности – да се кандидатира като член на Съвета и да участва в процедурата по гласуване.<br>
                    </p>
                    18. Администрацията на Министерския съвет, която поддържа Платформата не носи отговорност за каквито и да е вреди, причинени от грешка в системата, прекъсване на достъпа, загуба на информация, дефекти, забавяне на операции, компютърни вируси, проблеми с връзката, разрушаване на системата или неразрешен достъп.<br><br>
                    19. Администрацията на Министерския съвет, която поддържа Платформата не носи отговорност в случай, че потребител използва неправомерно придобита или неразрешена за разпространение информация, както и в случай, когато има злоупотреба, копиране или друг вид неправомерно ползване на чужда търговска марка, запазен знак за произход, както каквато и да е информация, които накърняват имуществени или неимуществени права на трети лица.<br>
                </div>
                <div class="modal-footer border-none">
                    <button type="button" class="btn btn-primary login-btn b-c-darkRed" data-dismiss="modal">{{ __('custom.close') }}</button>
                </div>
            </div>
        </div>
    </div>
    <form id="registerOrg" method="POST" enctype="multipart/form-data" action="{{ route('organisation.store') }}" class="m-t-lg p-sm">
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
                        <label for="eik" class="col-sm-4 col-xs-12 col-form-label"> {{ __('custom.eik_bulstat') }}:</label>
                        <div class="col-sm-8">
                            <input
                                type="text"
                                class="input-box"
                                name="eik"
                                value="{{ old('eik') }}"
                                maxlength="19"
                                required
                                title="{{ __('custom.required_mgs', ['field' => ultrans('custom.eik_bulstat')]) }}"
                            >
                            <span class="error">{{ $errors->first('eik') }}</span>
                        </div>
                    </div>
                    <div class="form-group row required">
                        <label for="name" class="col-sm-4 col-xs-12 col-form-label"> {{ __('custom.org_name') }}:</label>
                        <div class="col-sm-8">
                            <input
                                type="text"
                                class="input-box"
                                name="name"
                                value="{{ old('name') }}"
                                maxlength="255"
                                required
                                title="{{ __('custom.required_mgs', ['field' => ultrans('custom.org_name')]) }}"
                            >
                            <span class="error">{{ $errors->first('name') }}</span>
                        </div>
                    </div>
                    <div class="form-group row required">
                        <label for="address" class="col-sm-4 col-xs-12 col-form-label"> {{ __('custom.management_address') }}:</label>
                        <div class="col-sm-8">
                            <input
                                type="text"
                                class="input-box"
                                name="address"
                                value="{{ old('address') }}"
                                maxlength="512"
                                required
                                title="{{ __('custom.required_mgs', ['field' => ultrans('custom.management_address')]) }}"
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
                                maxlength="512"
                                required
                                title="{{ __('custom.required_mgs', ['field' => ultrans('custom.representative')]) }}"
                            >
                            <span class="error">{{ $errors->first('representative') }}</span>
                        </div>
                    </div>
                    <div class="form-group row required">
                        <label for="phone" class="col-sm-4 col-xs-12 col-form-label"> {{ __('custom.phone_number') }}:</label>
                        <div class="col-sm-8">
                            <input
                                type="text"
                                class="input-box"
                                name="phone"
                                value="{{ old('phone') }}"
                                maxlength="40"
                                required
                                title="{{ __('custom.required_mgs', ['field' => ultrans('custom.phone_number')]) }}"
                            >
                            <span class="error">{{ $errors->first('phone') }}</span>
                        </div>
                    </div>
                    <div class="form-group row required">
                        <label for="email" class="col-sm-4 col-xs-12 col-form-label" title="{{ __('custom.email_hint') }}"> {{ __('custom.email') }}:</label>
                        <div class="col-sm-8">
                            <input
                                type="text"
                                class="input-box"
                                name="email"
                                value="{{ old('email') }}"
                                maxlength="255"
                                required
                                title="{{ __('custom.required_mgs', ['field' => ultrans('custom.email')]) }}"
                            >
                            <span class="error">{{ $errors->first('email') }}</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="in_av" class="col-sm-4 col-xs-12 col-form-label" title="{{ __('custom.av_hint') }}"> {{ __('custom.in_av') }}:</label>
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
                        <label for="is_candidate" class="col-sm-4 col-xs-12 col-form-label" title="{{ __('custom.candidacy_hint') }}">{{ __('custom.request_for_candidacy') }}:</label>
                        <div class="col-sm-8 col-xs-6 p-r-none">
                            @include('components.checkbox', ['name' => 'is_candidate'])
                            <span class="error">{{ $errors->first('is_candidate') }}</span>
                        </div>
                    </div>
                    <div class="form-group row for_org_candidates {{ old('is_candidate') ? '' : 'd-none' }}">
                       <div class="col-sm-12 m-b-15">
                            <span class="alert alert-info warning p-l-none p-t-none p-b-none p-r-none">{{ __('custom.registration_message') }}</span>
                        </div>
                        <label for="description" class="col-sm-4 col-xs-12 col-form-label">{{ __('custom.experience_info') }}:</label>
                        <div class="col-sm-8 col-xs-6 p-r-none txt-area-height nano ">
                            <textarea
                                class="txt-area no-outline p-a-5 nano-content reg-exp"
                                name="description"
                                placeholder="{{ __('custom.experience_info_placeholder') }}"
                                rows="5"
                                cols="40"
                                maxlength="8000"
                            >{{ old('description') }}</textarea>
                        </div>

                        <div class="col-sm-8 col-xs-6 offset-sm-4 p-l-none">
                            <span class="error">{{ $errors->first('description') }}</span>
                        </div>
                    </div>
                    <div class="form-group row for_org_candidates {{ old('is_candidate') ? '' : 'd-none' }}">
                        <label for="references" class="col-sm-4 col-xs-12 col-form-label">{{ __('custom.reference_materials') }}:</label>
                        <div class="col-sm-8 col-xs-6 p-r-none txt-area-height nano">
                            <textarea
                                class="txt-area no-outline p-a-5 nano-content"
                                name="references"
                                placeholder="{{ __('custom.reference_materials_placeholder') }}"
                                rows="5"
                                cols="40"
                                maxlength="8000"
                            >{{ old('references') }}</textarea>
                        </div>
                        <div class="col-sm-8 col-xs-6 offset-sm-4 p-l-none">
                            <span class="error">{{ $errors->first('references') }}</span>
                        </div>
                    </div>
                </div>
                    <hr class="hr-thin">
                <div class="col-md-10">
                    <h5>{{ __('custom.applied_files') }} </h5>
                    <p>{{ __('custom.nonav_org') }}</p>
                    <div class="form-group row">
                        <div class="col-lg-12 p-r-none">
                            @include('components.fileinput', ['name' => 'files[]'])
                        </div>
                        <div class="col-lg-12 p-r-none p-t-5">
                            @php $filesErr = false; @endphp
                            @if ($errors->has('files'))
                                <span class="error">{{ $errors->first('files') }}</span>
                                @php $filesErr = true; @endphp
                            @endif
                            @if ($errors->has('files.*.name'))
                                <span class="error">{{ $errors->first('files.*.name') }}</span>
                                @php $filesErr = true; @endphp
                            @endif
                            @if ($errors->has('files.*.mime_type'))
                                <span class="error">{{ $errors->first('files.*.mime_type') }}</span>
                                @php $filesErr = true; @endphp
                            @endif
                            @if ($errors->has('files.*.data'))
                                <span class="error">{{ $errors->first('files.*.data') }}</span>
                                @php $filesErr = true; @endphp
                            @endif
                            @if ($errors->has('reattach_files') && !$filesErr)
                                <span class="error">{{ $errors->first('reattach_files') }}</span>
                            @endif
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
