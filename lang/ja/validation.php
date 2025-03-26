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

    'accepted' => ':を承認してください。',
    'accepted_if' => 'The :attribute must be accepted when :other is :value.',
    'active_url' => ':は有効なURLではありません。',
    'after' => 'The :attribute must be a date after :date.',
    'after_or_equal' => ':attributeには:date以降もしくは同日時を指定してください。',
    'alpha' => ':attributeにはアルファベッドのみ使用できます。',
    'alpha_dash' => ":attributeには英数字('A-Z','a-z','0-9')とハイフンと下線('-','_')が使用できます。",
    'alpha_num' => ":attributeには英数字('A-Z','a-z','0-9')が使用できます。",
    'array' => ':attributeには配列を指定してください。',
    'before' => ':attributeには:date以前の日付を指定してください。',
    'before_or_equal' => ':attributeには:date以前もしくは同日時を指定してください。',
    'between' => [
        'numeric' => ':attributeには:minから:maxまでの数字を指定してください。',
        'file'    => ':attributeには:min KBから:max KBまでのサイズのファイルを指定してください。',
        'string'  => ':attributeは:min文字から:max文字にしてください。',
        'array'   => ':attributeの項目は:min個から:max個にしてください。',
    ],
    'boolean'              => ":attributeには'true'か'false'を指定してください。",
    'confirmed'            => ':attributeと:attribute確認が一致しません。',
    'current_password' => 'The password is incorrect.',
    'date' => ':attributeは正しい日付ではありません。',
    'date_equals' => 'The :attribute must be a date equal to :date.',
    'date_format' => ":attributeの形式は':format'と合いません。",
    'declined' => 'The :attribute must be declined.',
    'declined_if' => 'The :attribute must be declined when :other is :value.',
    'different' => ':attributeと:otherには異なるものを指定してください。',
    'digits' => ':attributeは:digits桁にしてください。',
    'digits_between' => ':attributeは:min桁から:max桁にしてください。',
    'dimensions' => ':attributeは正しい縦横比ではありません。',
    'distinct' => ':attributeに重複した値があります。',
    'email' => ':attributeは有効なメールアドレス形式で指定してください。',
    'ends_with' => 'The :attribute must end with one of the following: :values.',
    'enum' => 'The selected :attribute is invalid.',
    'exists' => '選択された:attributeは有効ではありません。',
    'file' => ':attributeはファイルでなければいけません。',
    'filled' => ':attributeは必須です。',
    'gt'                   => [
        'numeric' => ':attributeは:valueより大きくなければなりません。',
        'file'    => ':attributeは:value KBより大きくなければなりません。',
        'string'  => ':attributeは:value文字より大きくなければなりません。',
        'array'   => ':attributeの項目数は:value個より大きくなければなりません。',
    ],
    'gte'                  => [
        'numeric' => ':attributeは:value以上でなければなりません。',
        'file'    => ':attributeは:value KB以上でなければなりません。',
        'string'  => ':attributeは:value文字以上でなければなりません。',
        'array'   => ':attributeの項目数は:value個以上でなければなりません。',
    ],
    'image' => ':attributeには画像を指定してください。',
    'in' => '選択された:attributeは有効ではありません。',
    'in_array' => ':attributeは:otherに存在しません。',
    'integer' => ':attributeには整数を指定してください。',
    'ip' => ':attributeには有効なIPアドレスを指定してください。',
    'ipv4' => ':attributeはIPv4アドレスを指定してください。',
    'ipv6' => ':attributeはIPv6アドレスを指定してください。',
    'json' => ':attributeには有効なJSON文字列を指定してください。',
    'lt' => [
        'numeric' => ':attributeは:valueより小さくなければなりません。',
        'file' => 'The :attribute must be less than :value kilobytes.',
        'string' => 'The :attribute must be less than :value characters.',
        'array' => 'The :attribute must have less than :value items.',
    ],
    'lte' => [
        'numeric' => 'The :attribute must be less than or equal to :value.',
        'file' => 'The :attribute must be less than or equal to :value kilobytes.',
        'string' => 'The :attribute must be less than or equal to :value characters.',
        'array' => 'The :attribute must not have more than :value items.',
    ],
    'mac_address' => 'The :attribute must be a valid MAC address.',
    'max' => [
        'numeric' => ':attributeには:max以下の数字を指定してください。',
        'file'    => ':attributeには:max KB以下のファイルを指定してください。',
        'string'  => ':attributeは:max文字以下にしてください。',
        'array'   => ':attributeの項目は:max個以下にしてください。',
    ],
    'mimes' => ':attributeには:valuesタイプのファイルを指定してください。',
    'mimetypes' => ':attributeには:valuesタイプのファイルを指定してください。',
    'min' => [
        'numeric' => ':attributeには:min以上の数字を指定してください。',
        'file'    => ':attributeには:min KB以上のファイルを指定してください。',
        'string'  => ':attributeは:min文字以上にしてください。',
        'array'   => ':attributeの項目は:max個以上にしてください。',
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.',
    'not_in' => '選択された:attributeは有効ではありません。',
    'not_regex' => 'The :attribute format is invalid.',
    'numeric' => ':attributeには数字を指定してください。',
    'password' => 'パスワードを正確に入力してください。',
    'password_check' => 'パスワードを正確に入力してください。',
    'present' => ':attributeは必ず存在しなくてはいけません。',
    'prohibited' => 'The :attribute field is prohibited.',
    'prohibited_if' => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless' => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits' => 'The :attribute field prohibits :other from being present.',
    'regex' => ':attributeには有効な正規表現を指定してください。',
    'required' => ':attributeを入力してください。',
    'required_array_keys' => 'The :attribute field must contain entries for: :values.',
    'required_if' => ':otherが:valueの場合:attributeを指定してください。',
    'required_unless' =>':otherが:value以外の場合:attributeを指定してください。',
    'required_with' => ':valuesが指定されている場合:attributeも指定してください。',
    'required_with_all' => ':valuesが全て指定されている場合:attributeも指定してください。',
    'required_without' => ':valuesが指定されていない場合:attributeを指定してください。',
    'required_without_all' => ':valuesが全て指定されていない場合:attributeを指定してください。',
    'same' => ':attributeと:otherが一致しません。',
    'size' => [
        'numeric' => ':attributeには:sizeを指定してください。',
        'file'    => ':attributeには:size KBのファイルを指定してください。',
        'string'  => ':attributeは:size文字にしてください。',
        'array'   => ':attributeの項目は:size個にしてください。',
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values.',
    'string' => ':attributeには文字を指定してください。',
    'timezone' => ':attributeには有効なタイムゾーンを指定してください。',
    'unique' => '指定の:attributeは既に使用されています。',
    'uploaded' => ':attributeのアップロードに失敗しました。',
    'url' => ':attributeは有効なURL形式で指定してください。',
    'uuid' => 'The :attribute must be a valid UUID.',

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
        'email' => 'ログインＩＤ',
        'password' => 'パスワード',

        'product_name' => '案件名',
        'material' => '材質',
        'quantity' => '数量',
        'desired_delivery_date' => '希望納期',
        'reply_due_date' => '回答期日',
        'comment' => 'コメント',
        'memo' => 'メモ',
        'd2_file' => '2Dデータ',
        'd3_file' => '3Dデータ',
        'important' => '優先フラグ',
        'delivery_date' => '回答納期',
        'total_amount' => '総額',

        'order_file' => '注文書',
        'drawing_file' => '正式図面（zip）',
    ],

];
