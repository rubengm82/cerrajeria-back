<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Línies d'idioma de validació
    |--------------------------------------------------------------------------
    |
    | Les línies següents contenen els missatges d'error predeterminats
    | utilitzats pel validador. Algunes regles tenen diverses versions,
    | com les regles de mida. Pots ajustar aquests missatges quan calgui.
    |
    */

    'accepted' => 'El camp :attribute ha de ser acceptat.',
    'accepted_if' => 'El camp :attribute ha de ser acceptat quan :other és :value.',
    'active_url' => 'El camp :attribute ha de ser una URL vàlida.',
    'after' => 'El camp :attribute ha de ser una data posterior a :date.',
    'after_or_equal' => 'El camp :attribute ha de ser una data posterior o igual a :date.',
    'alpha' => 'El camp :attribute només pot contenir lletres.',
    'alpha_dash' => 'El camp :attribute només pot contenir lletres, números, guions i guions baixos.',
    'alpha_num' => 'El camp :attribute només pot contenir lletres i números.',
    'any_of' => 'El camp :attribute no és vàlid.',
    'array' => 'El camp :attribute ha de ser un array.',
    'ascii' => 'El camp :attribute només pot contenir caràcters alfanumèrics i símbols d\'un sol byte.',
    'before' => 'El camp :attribute ha de ser una data anterior a :date.',
    'before_or_equal' => 'El camp :attribute ha de ser una data anterior o igual a :date.',
    'between' => [
        'array' => 'El camp :attribute ha de tenir entre :min i :max elements.',
        'file' => 'El camp :attribute ha de tenir entre :min i :max kilobytes.',
        'numeric' => 'El camp :attribute ha d\'estar entre :min i :max.',
        'string' => 'El camp :attribute ha de tenir entre :min i :max caràcters.',
    ],
    'boolean' => 'El camp :attribute ha de ser vertader o fals.',
    'can' => 'El camp :attribute conté un valor no autoritzat.',
    'confirmed' => 'La confirmació del camp :attribute no coincideix.',
    'contains' => 'Al camp :attribute li falta un valor obligatori.',
    'current_password' => 'La contrasenya és incorrecta.',
    'date' => 'El camp :attribute ha de ser una data vàlida.',
    'date_equals' => 'El camp :attribute ha de ser una data igual a :date.',
    'date_format' => 'El camp :attribute ha de coincidir amb el format :format.',
    'decimal' => 'El camp :attribute ha de tenir :decimal decimals.',
    'declined' => 'El camp :attribute ha de ser rebutjat.',
    'declined_if' => 'El camp :attribute ha de ser rebutjat quan :other és :value.',
    'different' => 'El camp :attribute i :other han de ser diferents.',
    'digits' => 'El camp :attribute ha de tenir :digits dígits.',
    'digits_between' => 'El camp :attribute ha de tenir entre :min i :max dígits.',
    'dimensions' => 'El camp :attribute té dimensions d\'imatge no vàlides.',
    'distinct' => 'El camp :attribute té un valor duplicat.',
    'doesnt_contain' => 'El camp :attribute no ha de contenir cap dels valors següents: :values.',
    'doesnt_end_with' => 'El camp :attribute no ha d\'acabar amb cap dels valors següents: :values.',
    'doesnt_start_with' => 'El camp :attribute no ha de començar amb cap dels valors següents: :values.',
    'email' => 'El camp :attribute ha de ser una adreça de correu electrònic vàlida.',
    'encoding' => 'El camp :attribute ha d\'estar codificat en :encoding.',
    'ends_with' => 'El camp :attribute ha d\'acabar amb un dels valors següents: :values.',
    'enum' => 'El camp :attribute seleccionat no és vàlid.',
    'exists' => 'El camp :attribute seleccionat no és vàlid.',
    'extensions' => 'El camp :attribute ha de tenir una de les extensions següents: :values.',
    'file' => 'El camp :attribute ha de ser un fitxer.',
    'filled' => 'El camp :attribute ha de tenir un valor.',
    'gt' => [
        'array' => 'El camp :attribute ha de tenir més de :value elements.',
        'file' => 'El camp :attribute ha de ser superior a :value kilobytes.',
        'numeric' => 'El camp :attribute ha de ser superior a :value.',
        'string' => 'El camp :attribute ha de tenir més de :value caràcters.',
    ],
    'gte' => [
        'array' => 'El camp :attribute ha de tenir :value elements o més.',
        'file' => 'El camp :attribute ha de ser superior o igual a :value kilobytes.',
        'numeric' => 'El camp :attribute ha de ser superior o igual a :value.',
        'string' => 'El camp :attribute ha de tenir :value caràcters o més.',
    ],
    'hex_color' => 'El camp :attribute ha de ser un color hexadecimal vàlid.',
    'image' => 'El camp :attribute ha de ser una imatge.',
    'in' => 'El camp :attribute seleccionat no és vàlid.',
    'in_array' => 'El camp :attribute ha d\'existir a :other.',
    'in_array_keys' => 'El camp :attribute ha de contenir almenys una de les claus següents: :values.',
    'integer' => 'El camp :attribute ha de ser un nombre enter.',
    'ip' => 'El camp :attribute ha de ser una adreça IP vàlida.',
    'ipv4' => 'El camp :attribute ha de ser una adreça IPv4 vàlida.',
    'ipv6' => 'El camp :attribute ha de ser una adreça IPv6 vàlida.',
    'json' => 'El camp :attribute ha de ser una cadena JSON vàlida.',
    'list' => 'El camp :attribute ha de ser una llista.',
    'lowercase' => 'El camp :attribute ha d\'estar en minúscules.',
    'lt' => [
        'array' => 'El camp :attribute ha de tenir menys de :value elements.',
        'file' => 'El camp :attribute ha de ser inferior a :value kilobytes.',
        'numeric' => 'El camp :attribute ha de ser inferior a :value.',
        'string' => 'El camp :attribute ha de tenir menys de :value caràcters.',
    ],
    'lte' => [
        'array' => 'El camp :attribute no ha de tenir més de :value elements.',
        'file' => 'El camp :attribute ha de ser inferior o igual a :value kilobytes.',
        'numeric' => 'El camp :attribute ha de ser inferior o igual a :value.',
        'string' => 'El camp :attribute ha de tenir :value caràcters o menys.',
    ],
    'mac_address' => 'El camp :attribute ha de ser una adreça MAC vàlida.',
    'max' => [
        'array' => 'El camp :attribute no ha de tenir més de :max elements.',
        'file' => 'El camp :attribute no ha de ser superior a :max kilobytes.',
        'numeric' => 'El camp :attribute no ha de ser superior a :max.',
        'string' => 'El camp :attribute no ha de tenir més de :max caràcters.',
    ],
    'max_digits' => 'El camp :attribute no ha de tenir més de :max dígits.',
    'mimes' => 'El camp :attribute ha de ser un fitxer de tipus: :values.',
    'mimetypes' => 'El camp :attribute ha de ser un fitxer de tipus: :values.',
    'min' => [
        'array' => 'El camp :attribute ha de tenir almenys :min elements.',
        'file' => 'El camp :attribute ha de tenir almenys :min kilobytes.',
        'numeric' => 'El camp :attribute ha de ser almenys :min.',
        'string' => 'El camp :attribute ha de tenir almenys :min caràcters.',
    ],
    'min_digits' => 'El camp :attribute ha de tenir almenys :min dígits.',
    'missing' => 'El camp :attribute ha d\'estar absent.',
    'missing_if' => 'El camp :attribute ha d\'estar absent quan :other és :value.',
    'missing_unless' => 'El camp :attribute ha d\'estar absent tret que :other sigui :value.',
    'missing_with' => 'El camp :attribute ha d\'estar absent quan :values és present.',
    'missing_with_all' => 'El camp :attribute ha d\'estar absent quan :values són presents.',
    'multiple_of' => 'El camp :attribute ha de ser múltiple de :value.',
    'not_in' => 'El camp :attribute seleccionat no és vàlid.',
    'not_regex' => 'El format del camp :attribute no és vàlid.',
    'numeric' => 'El camp :attribute ha de ser un número.',
    'password' => [
        'letters' => 'El camp :attribute ha de contenir almenys una lletra.',
        'mixed' => 'El camp :attribute ha de contenir almenys una majúscula i una minúscula.',
        'numbers' => 'El camp :attribute ha de contenir almenys un número.',
        'symbols' => 'El camp :attribute ha de contenir almenys un símbol.',
        'uncompromised' => 'El valor indicat per a :attribute ha aparegut en una filtració de dades. Tria un altre valor.',
    ],
    'present' => 'El camp :attribute ha d\'estar present.',
    'present_if' => 'El camp :attribute ha d\'estar present quan :other és :value.',
    'present_unless' => 'El camp :attribute ha d\'estar present tret que :other sigui :value.',
    'present_with' => 'El camp :attribute ha d\'estar present quan :values és present.',
    'present_with_all' => 'El camp :attribute ha d\'estar present quan :values són presents.',
    'prohibited' => 'El camp :attribute està prohibit.',
    'prohibited_if' => 'El camp :attribute està prohibit quan :other és :value.',
    'prohibited_if_accepted' => 'El camp :attribute està prohibit quan :other és acceptat.',
    'prohibited_if_declined' => 'El camp :attribute està prohibit quan :other és rebutjat.',
    'prohibited_unless' => 'El camp :attribute està prohibit tret que :other estigui a :values.',
    'prohibits' => 'El camp :attribute impedeix que :other sigui present.',
    'regex' => 'El format del camp :attribute no és vàlid.',
    'required' => 'El camp :attribute és obligatori.',
    'required_array_keys' => 'El camp :attribute ha de contenir entrades per a: :values.',
    'required_if' => 'El camp :attribute és obligatori quan :other és :value.',
    'required_if_accepted' => 'El camp :attribute és obligatori quan :other és acceptat.',
    'required_if_declined' => 'El camp :attribute és obligatori quan :other és rebutjat.',
    'required_unless' => 'El camp :attribute és obligatori tret que :other estigui a :values.',
    'required_with' => 'El camp :attribute és obligatori quan :values és present.',
    'required_with_all' => 'El camp :attribute és obligatori quan :values són presents.',
    'required_without' => 'El camp :attribute és obligatori quan :values no és present.',
    'required_without_all' => 'El camp :attribute és obligatori quan cap de :values és present.',
    'same' => 'El camp :attribute ha de coincidir amb :other.',
    'size' => [
        'array' => 'El camp :attribute ha de contenir :size elements.',
        'file' => 'El camp :attribute ha de tenir :size kilobytes.',
        'numeric' => 'El camp :attribute ha de ser :size.',
        'string' => 'El camp :attribute ha de tenir :size caràcters.',
    ],
    'starts_with' => 'El camp :attribute ha de començar amb un dels valors següents: :values.',
    'string' => 'El camp :attribute ha de ser una cadena de text.',
    'timezone' => 'El camp :attribute ha de ser una zona horària vàlida.',
    'unique' => 'El camp :attribute ja està en ús.',
    'uploaded' => 'El camp :attribute no s\'ha pogut pujar.',
    'uppercase' => 'El camp :attribute ha d\'estar en majúscules.',
    'url' => 'El camp :attribute ha de ser una URL vàlida.',
    'ulid' => 'El camp :attribute ha de ser un ULID vàlid.',
    'uuid' => 'El camp :attribute ha de ser un UUID vàlid.',

    /*
    |--------------------------------------------------------------------------
    | Línies personalitzades de validació
    |--------------------------------------------------------------------------
    |
    | Aquí pots especificar missatges personalitzats per a atributs fent servir
    | la convenció "atribut.regla".
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'missatge-personalitzat',
        ],

        'conflict_ranges' => 'El :attribute entra en conflicte amb un rang de dates existent.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Atributs personalitzats de validació
    |--------------------------------------------------------------------------
    |
    | Les línies següents substitueixen el marcador :attribute per noms
    | més llegibles, com "adreça de correu electrònic" en lloc d'"email".
    |
    */

    'attributes' => [
        'address' => 'adreça',
        'billing_address' => 'adreça de facturació',
        'billing_country' => 'país de facturació',
        'billing_province' => 'província de facturació',
        'billing_zip_code' => 'codi postal de facturació',
        'code' => 'codi',
        'customer.address' => 'adreça del client',
        'customer.email' => 'correu electrònic del client',
        'customer.name' => 'nom del client',
        'customer.phone' => 'telèfon del client',
        'customer.zip_code' => 'codi postal del client',
        'description' => 'descripció',
        'discount' => 'descompte',
        'email' => 'correu electrònic',
        'file' => 'fitxer',
        'image' => 'imatge',
        'images' => 'imatges',
        'installation_address' => 'adreça d\'instal·lació',
        'last_name_one' => 'primer cognom',
        'name' => 'nom',
        'order.installation_address' => 'adreça d\'instal·lació',
        'order.shipping_address' => 'adreça d\'enviament',
        'password' => 'contrasenya',
        'phone' => 'telèfon',
        'price' => 'preu',
        'quantity' => 'quantitat',
        'shipping_address' => 'adreça d\'enviament',
        'shipping_country' => 'país d\'enviament',
        'shipping_province' => 'província d\'enviament',
        'shipping_zip_code' => 'codi postal d\'enviament',
        'stock' => 'estoc',
        'title' => 'títol',
        'token' => 'token',
    ],

];
