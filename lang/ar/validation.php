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

    'name_ar' => 'الاسم بالعربية مطلوب',
    'name_en' => 'الاسم بالإنجليزية مطلوب',
    'email' => 'البريد الالكتروني مطلوب',
    'password' => 'كلمه السر مطلوبه',
    'email_unique' => 'البريد الالكتروني موجود بالفعل',
    'password_min' => 'كلمه السر يجب ان تكون اكبر من 8 احرف',
    'image' => 'خطا في رفع الصوره',
    'status' => 'من فضلك اختر الحاله',
    'question_en' => 'السؤال بالإنجليزية مطلوب',
    'question_ar' => 'السؤال بالعربية مطلوب',
    'answer_en' => 'الاجابه بالإنجليزية مطلوب',
    'answer_ar' => 'الاجابه بالعربية مطلوب',


    ////////////////////////////
    //Alert Messages
    'deleted' => 'تم الحذف بنجاح',
    'success' => 'تم الاضافه بنجاح',
    'edited' => 'تم التعديل بنجاح',
    ////////////////////////////
    'accepted' => 'يجب قبول  :attribute',
    'accepted_if' => ' :attribute مقبول في حال ما إذا كان :other يساوي :value.',
    'active_url' => ' :attribute لا يُمثّل رابطًا صحيحًا',
    'after' => 'يجب على  :attribute ان يكون تاريخًا لاحقًا للتاريخ :date.',
    'after_or_equal' => ' :attribute يجب ان يكون تاريخاً لاحقاً او مطابقاً للتاريخ :date.',
    'alpha' => 'يجب ان لا يحتوي  :attribute سوى على حروف',
    'alpha_dash' => 'يجب ان لا يحتوي  :attribute على حروف، ارقام ومطّات.',
    'alpha_num' => 'يجب ان يحتوي :attribute على حروفٍ وارقامٍ فقط',
    'array' => 'يجب ان يكون  :attribute ًمصفوفة',
    'before' => 'يجب على  :attribute ان يكون تاريخًا سابقًا للتاريخ :date.',
    'before_or_equal' => ' :attribute يجب ان يكون تاريخا سابقا او مطابقا للتاريخ :date',
    'between' => [
        'array' => 'يجب ان يحتوي :attribute على عدد من العناصر بين :min و :max',
        'file' => 'يجب ان يكون حجم الملف :attribute بين :min و :max كيلوبايت.',
        'numeric' => 'يجب ان تكون قيمة :attribute بين :min و :max.',
        'string' => 'يجب ان يكون عدد حروف النّص :attribute بين :min و :max',
    ],
    'boolean' => 'يجب ان تكون قيمة  :attribute إما true او false ',
    'confirmed' => 'حقل التاكيد غير مُطابق للحقل :attribute',
    'current_password' => 'كلمة المرور غير صحيحة',
    'date' => ' :attribute ليس تاريخًا صحيحًا',
    'date_equals' => 'لا يساوي  :attribute مع :date.',
    'date_format' => 'لا يتوافق  :attribute مع الشكل :format.',
    'declined' => 'يجب رفض  :attribute',
    'declined_if' => ' :attribute مرفوض في حال ما إذا كان :other يساوي :value.',
    'different' => 'يجب ان يكون ان :attribute و :other مُختلفان',
    'digits' => 'يجب ان يحتوي  :attribute على :digits رقمًا/ارقام',
    'digits_between' => 'يجب ان يحتوي  :attribute بين :min و :max رقمًا/ارقام',
    'dimensions' => 'الـ :attribute يحتوي على ابعاد صورة غير صالحة.',
    'distinct' => 'للحقل :attribute قيمة مُكرّرة.',
    'doesnt_end_with' => ' :attribute يجب الا ينتهي بواحدة من القيم التالية: :values.',
    'doesnt_start_with' => ' :attribute يجب الا يبدا بواحدة من القيم التالية: :values.',
    // 'email' => 'يجب ان يكون :attribute عنوان بريد إلكتروني صحيح البُنية',
    'ends_with' => 'الـ :attribute يجب ان ينتهي باحد القيم التالية :value.',
    'enum' => ' :attribute غير صحيح',
    'exists' => ' :attribute غير موجود',
    'file' => 'الـ :attribute يجب ان يكون من ملفا.',
    'filled' => ' :attribute إجباري',
    'gt' => [
        'array' => 'الـ :attribute يجب ان يحتوي علي اكثر من :value عناصر/عنصر.',
        'file' => 'الـ :attribute يجب ان يكون اكبر من :value كيلو بايت.',
        'numeric' => 'الـ :attribute يجب ان يكون اكبر من :value.',
        'string' => 'الـ :attribute يجب ان يكون اكبر من :value حروفٍ/حرفًا.',
    ],
    'gte' => [
        'array' => 'الـ :attribute يجب ان يحتوي علي :value عناصر/عنصر او اكثر.',
        'file' => 'الـ :attribute يجب ان يكون اكبر من او يساوي :value كيلو بايت.',
        'numeric' => 'الـ :attribute يجب ان يكون اكبر من او يساوي :value.',
        'string' => 'الـ :attribute يجب ان يكون اكبر من او يساوي :value حروفٍ/حرفًا.',
    ],
    // 'image' => 'يجب ان يكون  :attribute صورةً',
    'in' => ' :attribute لاغٍ',
    'in_array' => ' :attribute غير موجود في :other.',
    'integer' => 'يجب ان يكون  :attribute عددًا صحيحًا',
    'ip' => 'يجب ان يكون  :attribute عنوان IP ذا بُنية صحيحة',
    'ipv4' => 'يجب ان يكون  :attribute عنوان IPv4 ذا بنية صحيحة.',
    'ipv6' => 'يجب ان يكون  :attribute عنوان IPv6 ذا بنية صحيحة.',
    'json' => 'يجب ان يكون  :attribute نصا من نوع JSON.',
    'lowercase' => ' :attribute يجب ان يتكون من حروف صغيرة',
    'lt' => [
        'array' => 'الـ :attribute يجب ان يحتوي علي اقل من :value عناصر/عنصر.',
        'file' => 'الـ :attribute يجب ان يكون اقل من :value كيلو بايت.',
        'numeric' => 'الـ :attribute يجب ان يكون اقل من :value.',
        'string' => 'الـ :attribute يجب ان يكون اقل من :value حروفٍ/حرفًا.',
    ],
    'lte' => [
        'array' => 'الـ :attribute يجب ان يحتوي علي اكثر من :value عناصر/عنصر.',
        'file' => 'الـ :attribute يجب ان يكون اقل من او يساوي :value كيلو بايت.',
        'numeric' => 'الـ :attribute يجب ان يكون اقل من او يساوي :value.',
        'string' => 'الـ :attribute يجب ان يكون اقل من او يساوي :value حروفٍ/حرفًا.',
    ],
    'mac_address' => 'يجب ان يكون  :attribute عنوان MAC ذا بنية صحيحة.',
    'max' => [
        'array' => 'يجب ان لا يحتوي  :attribute على اكثر من :max عناصر/عنصر.',
        'file' => 'يجب ان لا يتجاوز حجم الملف :attribute :max كيلوبايت',
        'numeric' => 'يجب ان تكون قيمة  :attribute مساوية او اصغر لـ :max.',
        'string' => 'يجب ان لا يتجاوز طول نص :attribute :max حروفٍ/حرفًا',
    ],
    'max_digits' => ' :attribute يجب الا يحتوي اكثر من :max ارقام.',
    'mimes' => 'يجب ان يكون  ملفًا من نوع : :values.',
    'mimetypes' => 'يجب ان يكون  ملفًا من نوع : :values.',
    'min' => [
        'array' => 'يجب ان يحتوي  :attribute على الاقل على :min عُنصرًا/عناصر',
        'file' => 'يجب ان يكون حجم الملف :attribute على الاقل :min كيلوبايت',
        'numeric' => 'يجب ان تكون قيمة  :attribute مساوية او اكبر لـ :min.',
        'string' => 'يجب ان يكون طول نص :attribute على الاقل :min حروفٍ/حرفًا',
    ],
    'min_digits' => ' :attribute يجب ان يحتوي :min ارقام على الاقل.',
    'multiple_of' => ' :attribute يجب ان يكون من مضاعفات :value.',
    'not_in' => ' :attribute لاغٍ',
    'not_regex' => ' :attribute نوعه لاغٍ',
    'numeric' => 'يجب على  :attribute ان يكون رقمًا',
    // 'password' => [
    //     'letters' => 'يجب ان يشمل حقل :attribute على حرف واحد على الاقل.',
    //     'mixed' => 'يجب ان يشمل حقل :attribute على حرف واحد بصيغة كبيرة على الاقل وحرف اخر بصيغة صغيرة.',
    //     'numbers' => 'يجب ان يشمل حقل :attribute على رقم واحد على الاقل.',
    //     'symbols' => 'يجب ان يشمل حقل :attribute على رمز واحد على الاقل.',
    //     'uncompromised' => 'حقل :attribute تبدو غير آمنة. الرجاء اختيار قيمة اخرى.',
    // ],
    'present' => 'يجب تقديم  :attribute',
    'prohibited' => ' :attribute محظور',
    'prohibited_if' => ' :attribute محظور في حال ما إذا كان :other يساوي :value.',
    'prohibited_unless' => ' :attribute محظور في حال ما لم يكون :other يساوي :value.',
    'prohibits' => ' :attribute يحظر :other من اي يكون موجود',
    'regex' => 'صيغة  :attribute .غير صحيحة',
    'required' => ' :attribute مطلوب.',
    'required_array_keys' => ' :attribute يجب ان يحتوي علي مدخلات للقيم التالية :values.',
    'required_if' => ' :attribute مطلوب في حال ما إذا كان :other يساوي :value.',
    'required_if_accepted' => 'The :attribute field is required when :other is accepted.',
    'required_unless' => ' :attribute مطلوب في حال ما لم يكن :other يساوي :values.',
    'required_with' => ' :attribute إذا توفّر :values.',
    'required_with_all' => ' :attribute إذا توفّر :values.',
    'required_without' => ' :attribute إذا لم يتوفّر :values.',
    'required_without_all' => ' :attribute إذا لم يتوفّر :values.',
    'same' => 'يجب ان يتطابق  :attribute مع :other',
    'size' => [
        'array' => 'يجب ان يحتوي  :attribute على :size عنصرٍ/عناصر بالظبط',
        'file' => 'يجب ان يكون حجم الملف :attribute :size كيلوبايت',
        'numeric' => 'يجب ان تكون قيمة  :attribute مساوية لـ :size',
        'string' => 'يجب ان يحتوي النص :attribute على :size حروفٍ/حرفًا بالظبط',
    ],
    'starts_with' => ' :attribute يجب ان يبدا باحد القيم التالية: :values.',
    'string' => 'يجب ان يكون  :attribute نصآ.',
    'timezone' => 'يجب ان يكون :attribute نطاقًا زمنيًا صحيحًا',
    'unique' => 'قيمة  :attribute مُستخدمة من قبل',
    'uploaded' => 'فشل في تحميل الـ :attribute',
    'uppercase' => 'The :attribute must be uppercase.',
    'url' => 'صيغة  :attribute غير صحيحة',
    'uuid' => ' :attribute يجب ان ايكون رقم UUID صحيح.',

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
        'sender_phone' => [
            'saudi_format' => 'يجب أن يبدأ رقم الهاتف بـ 05 متبوعًا بـ 8 أرقام (مثال: 0512345678).',
        ],
        'selected_receivers' => [
            'invalid_json'   => 'بيانات المستلمين غير صالحة.',
            'empty'          => 'يرجى إضافة مستلم واحد على الأقل.',
            'missing_fields' => 'يجب أن يحتوي كل مستلم على رقم معرف أو اسم.',
            'required'       => 'المستلمون مطلوبون.',
        ],
        'receivers_count' => [
            'min' => 'يجب أن يكون لديك مستلم واحد على الأقل.',
        ],
        'cod_price_per_receiver' => [
            'required_if' => 'رسوم الدفع عند الاستلام لكل مستلم مطلوبة عند اختيار الدفع عند الاستلام.',
        ],
        'extra_kg' => [
            'mismatch' => 'الوزن الإضافي لا يطابق العملية الحسابية (المدخل - الحد الأقصى).',
        ],
        'accept_terms' => [
            'accepted' => 'يجب عليك قبول الشروط والأحكام.',
        ],
        'shipment_image' => [
            'image' => 'يجب أن تكون صورة الشحنة ملف صورة.',
            'mimes' => 'يجب أن تكون صورة الشحنة من نوع: jpg, jpeg, png, webp.',
            'max'   => 'يجب ألا تزيد صورة الشحنة عن 2 ميغابايت.',
        ],

    ],

    'there_were_errors' => 'يرجى تصحيح الأخطاء التالية:',


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
        'phone' => 'رقم الهاتف',
        'password' => 'كلمة المرور',
        'email' => 'البريد الالكترونى',
        'otp' => 'كود التحقق',
        'password_confirmation' => 'تأكيد كلمة المرور',
        'name' => 'الاسم',
        'image' => 'الصورة',
        'app_name' => 'اسم التطبيق',
        'app_name_ar' => 'اسم التطبيق بالعربية',
        'app_name_en' => 'اسم التطبيق بالانجليزية',
        'whatsapp' => 'رقم واتس اب',
        'facebook' => 'رابط فيس بوك',
        'twitter' => 'رابط اكس',
        'instagram' => 'رابط انستجرام',
        'youtube' => 'رابطيوتيوب',
        'snapchat' => 'رابط سناب شات',
        'tiktok' => 'رابط تيك توك',
        'logo' => 'لوجو',
        'fav_icon' => 'الايقونة',
        'app_store_url' => 'لينك تطبيق الايفون',
        'google_play_url' => 'لينك تطبيق الاندرويد',
        'permission_id' => 'الصلاحية',
        'permission_id.*' => 'الصلاحية',
        'role' => 'الدور',
        'title' => 'العنوان',
        'description' => 'الوصف',
        'content' => 'المحتوى',
        'subject' => 'موضوع الرسالة',
        'message' => 'الرسالة',
        'type' => 'النوع',
        'source' => 'المصدر',
        'payment_method' => 'وسيلة الدفع',
        'notes' => 'ملاحظات',
        'company' => 'الشركة',
        'interactions' => 'التفاعلات',
        'payment_status' => 'حالة الدفع',
        'job_title' => 'المسمى الوظيفى',
        'job_title_ar' => 'المسمى الوظيفى بالعربية',
        'job_title_en' => 'المسمى الوظيفى بالانجليزية',
        'comment' => 'الرأى',
        'comment_ar' => 'الرأى بالعربية',
        'comment_en' => 'الرأى بالانجليزية',
        'name_ar' => 'الاسم بالعربية',
        'name_en' => 'الاسم بالانجليزية',
        'address_ar' => 'العنوان بالعربية',
        'address_en' => 'العنوان بالانجليزية',
        'question' => 'السؤال',
        'question_ar' => 'السؤال بالعربية',
        'question_en' => 'السؤال بالانجليزية',
        'answer' => 'الاجابة',
        'answer_ar' => 'الاجابة بالعربية',
        'answer_en' => 'الاجابة بالانجليزية',
        'message_sent_successfully' => 'تم الارسال بنجاح',
        'contact' => 'الرسالة',
        'bio_ar' => 'نبذة تعريفية بالعربية',
        'bio_en' => 'نبذة تعريفية بالانجليزية',
        'description_ar' => 'الوصف بالعربية',
        'description_en' => 'الوصف بالانجليزية',
        'attachment' => 'المرفق',
        'amount' => 'المبلغ',
        'bank' => 'البنك',
        'bank_id' => 'البنك',
        'banks_id' => 'البنك',
        'international_price' => 'السعر الدولي',
        'local_price' => 'السعر المحلي',
        'company_id' => 'معرف الشركة',
        'company_name_ar' => 'اسم الشركة بالعربية',
        'company_name_en' => 'اسم الشركة بالانجليزية',
        'title_ar' => 'العنوان بالعربية',
        'title_en' => 'العنوان بالانجليزية',
        'review_ar' => 'الآراء بالعربية',
        'review_en' => 'الآراء بالانجليزية',
        'rate' => 'التقييم',
        'first_name' => 'الاسم الاول',
        'last_name' => 'الاسم الاخير',
        'token' => 'الرمز المميز',
        'new_password' => 'كلمة المرور الجديدة',
        'additional_phone' => 'رقم الهاتف الإضافي',
        'postal_code' => 'الرمز البريدي',
        'dateFrom' => 'التاريخ من',
        'dateTo' => 'التاريخ إلى',
        'isCod' => 'الدفع عند الاستلام',
        'shippingCompanyId' => 'شركة الشحن',
        'method' => 'الطريقة',
        'status' => 'الحالة',
        'search' => 'البحث',
        'receiverName' => 'اسم المستلم',
        'receiverPhone' => 'هاتف المستلم',
        'shipping_company_id' => 'شركة الشحن',
        'shipping_method' => 'طريقة الشحن',
        'sender_name' => 'اسم المرسل',
        'sender_phone' => 'هاتف المرسل',
        'sender_email' => 'بريد المرسل الإلكتروني',
        'sender_address' => 'عنوان المرسل',
        'sender_country_id' => 'دولة المرسل',
        'sender_country_name' => 'اسم دولة المرسل',
        'sender_state_id' => 'محافظة المرسل',
        'sender_state_name' => 'اسم محافظة المرسل',
        'sender_city_id' => 'مدينة المرسل',
        'sender_city_name' => 'اسم مدينة المرسل',
        'sender_postal_code' => 'الرمز البريدي للمرسل',
        'selected_receivers' => 'المستلمون المحددون',
        'receivers_count' => 'عدد المستلمين',
        'package_type' => 'نوع الطرد',
        'package_number' => 'رقم الطرد',
        'length' => 'الطول',
        'width' => 'العرض',
        'height' => 'الارتفاع',
        'weight' => 'الوزن',
        'cod_amount' => 'مبلغ الدفع عند الاستلام',
        'package_description' => 'وصف الطرد',
        'shipping_price_per_receiver' => 'سعر الشحن لكل مستلم',
        'extra_weight_per_receiver' => 'الوزن الإضافي لكل مستلم',
        'cod_price_per_receiver' => 'سعر الدفع عند الاستلام لكل مستلم',
        'total_per_receiver' => 'المجموع لكل مستلم',
        'total_amount' => 'المبلغ الإجمالي',
        'currency' => 'العملة',
        'max_weight' => 'الوزن الأقصى',
        'entered_weight' => 'الوزن المدخل',
        'extra_kg' => 'الكيلو الإضافي',
        'accept_terms' => 'قبول الشروط',
        'shipment_image' => 'صورة الشحنة',
        'code' => 'الكود',
        'date_from' => 'التاريخ من',
        'date_to' => 'التاريخ إلى',
        'trans_type' => 'نوع المعاملة',
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Values
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our value placeholder
    | with something more reader friendly such as "محلي" instead
    | of "local". This simply helps us make our message more expressive.
    |
    */

    'values' => [
        'transaction_created' => 'تم إنشاء المعاملة',
        'transaction_rejected' => 'تم رفض المعاملة',
        'transaction_accepted' => 'تم قبول المعاملة',
        'newshipment' => 'شحنة جديدة',
        'balance_deposited' => 'تم إيداع الرصيد',
        'balance_deduction' => 'خصم من الرصيد',
        'accepted' => 'مقبول',
        'pending' => 'معلق',
        'rejected' => 'مرفوض',
        'shippment' => 'شحنة',
        'transaction' => 'معاملة',
        'edit_balance' => 'تعديل الرصيد',
        'other' => 'أخرى',
        'deposit' => 'إيداع',
        'deduct' => 'خصم',
        'local' => 'محلي',
        'international' => 'دولي',
        'box' => 'صندوق',
        'document' => 'مستند',
        'wallet' => 'المحفظة',
        'cod' => 'الدفع عند الاستلام',
        'true' => 'صحيح',
        'false' => 'خطأ',
        'processing' => 'قيد المعالجة',
        'failed' => 'فشل',
        'cancelRequest' => 'طلب إلغاء',
        'canceled' => 'ملغي',
        'returned' => 'مرتجع',
        'delivered' => 'تم التسليم',
    ],

];
