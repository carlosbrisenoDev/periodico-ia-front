<?php

/**

 * PayPal Setting & API Credentials

 * Created by Raza Mehdi .

 */



return [

    'mode'    => 'sandbox',
    'sandbox' => [
        'username'    => env('PAYPAL_SANDBOX_API_USERNAME', 'sb-njqjj309697_api1.business.example.com'),
        'password'    => env('PAYPAL_SANDBOX_API_PASSWORD', 'T6C2T84MZFY37RLR'),
        'secret'      => env('PAYPAL_SANDBOX_API_SECRET', 'AV6S5UDPOWj9DEBxAoYpAZio5IayABt1G7e6dVAqhSE5moJdDNUzdw.8'),
        'certificate' => env('PAYPAL_SANDBOX_API_CERTIFICATE', ''),
        'app_id'      => 'APP-80W284485P519543T',
    ],

    'live' => [
      'username'    => "",//david.valdivia_api1.gruposhirushi.com
      'password'    => "", //72TJU6XGX3925Z24
      'secret'      => "", // AtfDNy7A1neQ2JCFejq0eE6zrCtNAxJSueGG0mFXuHdpPHkHlAfe-.ze
      'certificate' => '',
      'app_id'      => '',

    ],
    'payment_action' => 'Sale',
    'currency'       => env('PAYPAL_CURRENCY', 'MXN'),
    'billing_type'   => 'MerchantInitiatedBilling',
    'notify_url'     => '',
    'locale'         => 'es_XC',
    'validate_ssl'   => false,

];
?>
