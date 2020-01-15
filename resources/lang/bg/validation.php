<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => 'Поле :attribute трябва да бъде прието.',
    'active_url'           => 'Поле :attribute не е валиден URL.',
    'after'                => 'Поле :attribute трябва да бъде след :date.',
    'after_or_equal'       => 'Поле :attribute трябва да бъде дата след или равна на :date.',
    'alpha'                => 'Поле :attribute може да съдържа само букви.',
    'alpha_dash'           => 'Поле :attribute може да съдържа само букви, числа и тирета.',
    'alpha_num'            => 'Поле :attribute може да съдържа само букви и числа.',
    'array'                => 'Поле :attribute трябва да бъде масив.',
    'before'               => 'Поле :attribute трябва да бъде дата преди :date.',
    'before_or_equal'      => 'Поле :attribute трябва да бъде дата преди или равна на :date.',
    'between'              => [
        'numeric' => 'Поле :attribute трябва да бъде между :min и :max.',
        'file'    => 'Поле :attribute трябва да бъде между :min и :max килобайта.',
        'string'  => 'Поле :attribute трябва да бъде между :min и :max символа.',
        'array'   => 'Поле :attribute трябва да бъде между :min и :max елемента.',
    ],
    'boolean'              => 'Поле :attribute трябва да бъде true или false.',
    'confirmed'            => 'Поле :attribute потвърждението не съвпада.',
    'date'                 => 'Поле :attribute не е валидна дата.',
    'date_format'          => 'Поле :attribute не съвпада с формат :format.',
    'different'            => 'Поле :attribute и :other трябва да бъдат различни.',
    'digits'               => 'Поле :attribute трябва да бъде :digits цифри.',
    'digits_between'       => 'Поле :attribute трябва да бъде между цифрите :min и :max.',
    'dimensions'           => 'Поле :attribute е с невалидни размери.',
    'distinct'             => 'Поле :attribute има дублирана стойност.',
    'email'                => 'Поле :attribute трябва да бъде валидна електронна поща.',
    'exists'               => 'Поле :attribute е невалидно.',
    'file'                 => 'Поле :attribute трябва да бъде файл.',
    'filled'               => 'Поле :attribute трябва да има стойност.',
    'image'                => 'Поле :attribute трябва да бъде картинка.',
    'in'                   => 'Поле :attribute е невалидно.',
    'in_array'             => 'Поле :attribute не съществува в :other.',
    'integer'              => 'Поле :attribute трябва да бъде цяло число.',
    'ip'                   => 'Поле :attribute трябва да бъде валиден IP адрес.',
    'ipv4'                 => 'Поле :attribute трябва да бъде валиден IPv4 адрес.',
    'ipv6'                 => 'Поле :attribute трябва да бъде валиден IPv6 адрес.',
    'json'                 => 'Поле :attribute трябва да бъде валиден JSON низ.',
    'max'                  => [
        'numeric' => 'Поле :attribute не може да бъде по голямо от :max.',
        'file'    => 'Поле :attribute не може да бъде повече от :max килобайта.',
        'string'  => 'Поле :attribute не може да бъде повече от :max символа.',
        'array'   => 'Поле :attribute не може да бъде повече от :max елемента.',
    ],
    'mimes'                => 'Поле :attribute трябва да бъде файлов тип: :values.',
    'mimetypes'            => 'Поле :attribute трябва да бъде файлов тип: :values.',
    'min'                  => [
        'numeric' => 'Поле :attribute трябва да бъде поне :min.',
        'file'    => 'Поле :attribute трябва да бъде поне :min килобайта.',
        'string'  => 'Поле :attribute трябва да бъде поне :min символа.',
        'array'   => 'Поле :attribute трябва да бъде поне :min елемента.',
    ],
    'not_in'               => 'Поле :attribute е невалидно.',
    'numeric'              => 'Поле :attribute трябва да бъде число.',
    'present'              => 'Поле :attribute трябва да фигурира.',
    'regex'                => 'Форматът на :attribute е невалиден.',
    'required'             => 'Поле :attribute е задължително.',
    'required_if'          => 'Поле :attribute е задължително, когато :other е :value.',
    'required_unless'      => 'Поле :attribute е задължително, освен ако :other не е :values.',
    'required_with'        => 'Поле :attribute е задължително, когато :values е налично.',
    'required_with_all'    => 'Поле :attribute е задължително, когато :values е налично.',
    'required_without'     => 'Поле :attribute е задължително, когато :values не е налично.',
    'required_without_all' => 'Поле :attribute е задължително, когато нито едно от :values е налично.',
    'same'                 => 'Поле :attribute и поле :other трябва да са еднакви.',
    'size'                 => [
        'numeric' => 'Поле :attribute трябва да е :size.',
        'file'    => 'Поле :attribute трябва да е :size килобайта.',
        'string'  => 'Поле :attribute трябва да е :size символа.',
        'array'   => 'Поле :attribute трябва да съдържа :size елемента.',
    ],
    'string'               => 'Поле :attribute трябва да бъде низ.',
    'timezone'             => 'Поле :attribute трябва да бъде валидна зона.',
    'unique'               => 'Поле :attribute е заето.',
    'uploaded'             => 'Файлът :attribute не е качен.',
    'url'                  => 'Форматът на поле :attribute е невалиден.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'username'          => 'потребителско име',
        'password'          => 'парола',
        'password_confirm'  => 'потвърди парола',
        'new_password'      => 'нова парола',
        'first_name'        => 'име',
        'last_name'         => 'фамилия',
        'email'             => 'електронна поща',
        'name'              => 'наименование',
        'status'            => 'статус',
        'statuses'          => 'статуси',
        'statuses.*'        => 'статус',
        'org_id'            => 'организация',
        'org_data'          => 'данни на организация',
        'user_id'           => 'потребител',
        'field'             => 'колона',
        'search'            => 'търси',
        'order_field'       => 'колона за подредба',
        'order_type'        => 'тип на подредба',
        'created_by'        => 'създаден от',
        'valid'             => 'валиден',
        'date_type'         => 'дата тип',
        'records_per_page'  => 'записи за страница',
        'page_number'       => 'номер на страница',
        'active'            => 'активен',
        'description'       => 'описание',
        'contact_name'      => 'име за контакт',
        'notes'             => 'бележки',
        'filename'          => 'име на файл',
        'mimetype'          => 'тип на файл',
        'mime_type'         => 'тип',
        'data'              => 'данни',
        'title'             => 'заглавие',
        'valid_from'        => 'валиден от',
        'valid_to'          => 'валиден до',
        'date_from'         => 'дата от',
        'date_to'           => 'дата до',
        'period_from'       => 'от период',
        'period_to'         => 'до период',
        'add_info'          => 'добави информация',
        'type'              => 'тип',
        'query'             => 'заявка',
        'format'            => 'формат',
        'namespaces'        => 'пространство от имена',
        'id'                => 'идентификатор',
        'read_only'         => 'само за четене',
        'parent_id'         => 'родител',
        'is_default'        => 'по подразбиране',
        'is_admin'          => 'администратор',
        'hash'              => 'хаш',
        'comment'           => 'коментар',
        'color'             => 'цвят',
        'eik'               => 'ЕИК',
        'address'           => 'адрес',
        'representative'    => 'представляващ',
        'phone'             => 'телефон',
        'in_av'             => 'вписан в АВ',
        'is_candidate'      => 'кандидат',
        'references'        => 'референтни материали',
        'status_hint'       => 'пояснение на статус',
        'reg_date_from'     => 'дата на регистрация от',
        'reg_date_to'       => 'дата на регистрация до',
        'files'             => 'приложения',
        'files.*.name'      => 'име на файл',
        'files.*.mime_type' => 'тип на файл',
        'files.*.data'      => 'данни на файл',
        'file_id'           => 'файл',
        'tour_id'           => 'изборен тур',
        'body'              => 'текст',
        'subject'           => 'заглавие',
        'new_name'          => 'наименование',
        'new_status'        => 'статус',
        'filters'           => 'филтър',
        'with_pagination'   => 'покажи на страници',
        'reg_number'        => 'номер на регистрация',
        'reg_date'          => 'дата на регистрация',
        'city'              => 'град',
        'status_date'       => 'дата на промяна на статус',
        'goals'             => 'цели',
        'tools'             => 'средства',
        'public_benefits'   => 'дейности в обществена полза',
        'only_main_fields'  => 'върни само основни данни',
    ],

];
