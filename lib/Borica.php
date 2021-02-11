<?php

/**  @desc Base class for Borica EMV 3D Secure Payment
* @package shBorica
* @version 1.0
*  @author Pavel Tzonkov
* @license https://opensource.org/licenses/MIT MIT
*    @link https://github.com/sunhater/shBorica */


class Borica {

    const URL = [
        'test' => 'https://3dsgate-dev.borica.bg/cgi-bin/cgi_link',
        'live' => 'https://3dsgate.borica.bg/cgi-bin/cgi_link'
    ];

    const TRANSACTION_TYPES = [

        1 => [
            'name' => 'Payment',
            'fields' => ['AMOUNT', 'CURRENCY', 'DESC', 'TERMINAL', 'MERCH_NAME', 'MERCH_URL', 'MERCHANT', 'EMAIL', 'TRTYPE', 'ORDER', 'AD.CUST_BOR_ORDER_ID', 'COUNTRY', 'TIMESTAMP', 'MERCH_GMT', 'NONCE', 'ADDENDUM'],
            'sign_request_fields' => ['TERMINAL', 'TRTYPE', 'AMOUNT', 'CURRENCY', 'ORDER', 'MERCHANT', 'TIMESTAMP', 'NONCE'],
            'sign_response_fields' => ['ACTION', 'RC', 'APPROVAL', 'TERMINAL', 'TRTYPE', 'AMOUNT', 'CURRENCY', 'ORDER', 'RRN', 'INT_REF', 'PARES_STATUS', 'ECI', 'TIMESTAMP', 'NONCE']
        ],

        12 => [
            'name' => 'Initial Authorization',
            'fields' => ['AMOUNT', 'CURRENCY', 'DESC', 'TERMINAL', 'MERCH_NAME', 'MERCH_URL', 'MERCHANT', 'EMAIL', 'TRTYPE', 'ORDER', 'AD.CUST_BOR_ORDER_ID', 'COUNTRY', 'TIMESTAMP', 'MERCH_GMT', 'NONCE', 'ADDENDUM'],
            'sign_request_fields' => ['TERMINAL', 'TRTYPE', 'AMOUNT', 'CURRENCY', 'ORDER', 'MERCHANT', 'TIMESTAMP', 'NONCE'],
            'sign_response_fields' => ['ACTION', 'RC', 'APPROVAL', 'TERMINAL', 'TRTYPE', 'AMOUNT', 'CURRENCY', 'ORDER', 'RRN', 'INT_REF', 'PARES_STATUS', 'ECI', 'TIMESTAMP', 'NONCE']
        ],

        21 => [
            'name' => 'Complete Initial Authorization',
            'fields' => ['AMOUNT', 'CURRENCY', 'RRN', 'INT_REF', 'DESC', 'TERMINAL', 'MERCH_NAME', 'MERCH_URL', 'MERCHANT ', 'EMAIL', 'TRTYPE', 'ORDER', 'AD.CUST_BOR_ORDER_ID', 'COUNTRY', 'TIMESTAMP', 'MERCH_GMT', 'NONCE', 'ADDENDUM'],
            'sign_request_fields' => ['TERMINAL', 'TRTYPE', 'AMOUNT', 'CURRENCY', 'ORDER', 'MERCHANT', 'TIMESTAMP', 'NONCE'],
            'sign_response_fields' => ['ACTION', 'RC', 'APPROVAL', 'TERMINAL', 'TRTYPE', 'AMOUNT', 'CURRENCY', 'ORDER', 'RRN', 'INT_REF', 'PARES_STATUS', 'ECI', 'TIMESTAMP', 'NONCE']
        ],

        22 => [
            'name' => 'Cancel Initial Authorization',
            'fields' => ['AMOUNT', 'CURRENCY', 'RRN', 'INT_REF', 'DESC', 'TERMINAL', 'MERCH_NAME', 'MERCH_URL', 'MERCHANT ', 'EMAIL', 'TRTYPE', 'ORDER', 'AD.CUST_BOR_ORDER_ID', 'COUNTRY', 'TIMESTAMP', 'MERCH_GMT', 'NONCE', 'ADDENDUM'],
            'sign_request_fields' => ['TERMINAL', 'TRTYPE', 'AMOUNT', 'CURRENCY', 'ORDER', 'MERCHANT', 'TIMESTAMP', 'NONCE'],
            'sign_response_fields' => ['ACTION', 'RC', 'APPROVAL', 'TERMINAL', 'TRTYPE', 'AMOUNT', 'CURRENCY', 'ORDER', 'RRN', 'INT_REF', 'PARES_STATUS', 'ECI', 'TIMESTAMP', 'NONCE']
        ],

        24 => [
            'name' => 'Cancel Payment',
            'fields' => ['AMOUNT', 'CURRENCY', 'RRN', 'INT_REF', 'DESC', 'TERMINAL', 'MERCH_NAME', 'MERCH_URL', 'MERCHANT ', 'EMAIL', 'TRTYPE', 'ORDER', 'AD.CUST_BOR_ORDER_ID', 'COUNTRY', 'TIMESTAMP', 'MERCH_GMT', 'NONCE', 'ADDENDUM'],
            'sign_request_fields' => ['TERMINAL', 'TRTYPE', 'AMOUNT', 'CURRENCY', 'ORDER', 'MERCHANT', 'TIMESTAMP', 'NONCE'],
            'sign_response_fields' => ['ACTION', 'RC', 'APPROVAL', 'TERMINAL', 'TRTYPE', 'AMOUNT', 'CURRENCY', 'ORDER', 'RRN', 'INT_REF', 'PARES_STATUS', 'ECI', 'TIMESTAMP', 'NONCE']
        ],

        90 => [
            'name' => 'Check Transaction Status',
            'fields' => ['RRN', 'INT_REF', 'TERMINAL', 'TRTYPE', 'ORDER', 'NONCE', 'TRAN_TRTYPE', 'P_SIGN'],
            'sign_request_fields' => ['TERMINAL', 'TRTYPE', 'ORDER', 'NONCE'],
            'sign_response_fields' => ['ACTION', 'RC', 'APPROVAL', 'TERMINAL', 'TRTYPE', 'AMOUNT', 'CURRENCY', 'ORDER', 'RRN', 'INT_REF', 'PARES_STATUS', 'ECI', 'TIMESTAMP', 'NONCE']
        ]

    ];

    const ACTION_CODES = [
        0 => 'Successfully completed transaction',
        1 => 'Duplicated  transaction',
        2 => 'Canceled transaction',
        3 => 'Error processing transaction'
    ];

    const RC_CODES = [
        '00' => 'Successfully processed transaction',
        '-19' => 'Unsuccessful transaction',
        '-25' => 'Transaction confirmation is interrupted by the user',
        '-31' => 'The transaction is processed by the issuer',
        '-33' => 'Authentication by the user',
        '-39' => 'User confirmation request',
        '-40' => 'User transaction form request'
    ];

    const STATUSMSG_CODES = [
        'AS_FAIL' => 'Canceled upon 3DS authentication',
        'AS_OTP_ERROR' => 'Unsuccessful authentication with disposable password',
        'AS_RND_ERROR' => 'Unsuccessful authentication with random sum'
    ];

    protected $config = [
        'test_mode' => true,
        'suffix' => 'MYSHOP',
    //  'private_key' => '',
    //  'private_key_password' => '',
    //  'certificate' => '',
    ];

    protected $defaults = [
    //  'MERCHANT' => '9876543210',
    //  'TERMINAL' => 'V1234567',
        'ADDENDUM' => 'AD,TD',
        'CURRENCY' => 'BGN',
        'COUNTRY' => 'BG',
        'MERCH_GMT' => '+02',
    //  'MERCH_NAME' => '',
    //  'MERCH_URL' => '',
    //  'BACKREF' => '',
    //  'EMAIL' => '',
    ];

    public function __construct(array $config=null, array $defaults=null) {

        if ($config !== null)
            $this->config = array_replace($this->config, $config);

        if ($defaults !== null)
            $this->defaults = array_replace($this->defaults, $defaults);
    }

    public function getDefault($key=null) {

        if ($key === null)
            return $this->defaults;

        return isset($this->defaults[$key]) ? $this->defaults[$key] : null;
    }

    public function url() {
        return self::URL[$this->config['test_mode'] ? 'test' : 'live'];
    }

    public function collectRequestData(array &$data) {
        $types = self::TRANSACTION_TYPES;

        if (!isset($data['TRTYPE']) ||
            !is_scalar($data['TRTYPE']) ||
            !isset($types[$data['TRTYPE']])
        )
            $data['TRTYPE'] = '1';

        $type = $types[$data['TRTYPE']];
        $fields = array_flip($type['fields']);

        foreach ($data as $key => $val)
            if (!isset($fields[$key]) || !is_scalar($val))
                unset($data[$key]);
            else
                $data[$key] = (string) $val;

        $generated = ['TIMESTAMP', 'NONCE', 'AD.CUST_BOR_ORDER_ID'];
        $modified = ['ORDER', 'AMOUNT'];

        foreach (array_keys($fields) as $field) {

            if ($field == 'TRTYPE')
                continue;

            $isGenerated = in_array($field, $generated);
            $isModified = in_array($field, $modified);
            $isSet = isset($data[$field]);
            $hasDefault = isset($this->defaults[$field]);

            if (($isSet && (!$isModified || $isGenerated)) ||
                (!$isSet && !$isGenerated && !$hasDefault)
            )
                continue;

            if ($hasDefault && !$isSet)
                $data[$field] = $this->defaults[$field];

            if ($isModified && isset($data[$field])) {

                if ($field == 'ORDER') {
                    $order = $data['ORDER'];
                    $data['ORDER'] = substr(str_pad(abs($data['ORDER']), 6, '0', STR_PAD_LEFT), -6);
                }

                elseif ($field == 'AMOUNT')
                    $data['AMOUNT'] = number_format($data['AMOUNT'], 2, '.', '');

                continue;
            }

            if ($isGenerated) {

                if ($field == 'TIMESTAMP')
                    $data['TIMESTAMP'] = gmdate("YmdHis");

                elseif ($field == 'NONCE')
                    $data['NONCE'] = strtoupper(bin2hex(openssl_random_pseudo_bytes(16)));

                elseif (($field == 'AD.CUST_BOR_ORDER_ID') && isset($order))
                    $data['AD.CUST_BOR_ORDER_ID'] = $data['ORDER'] . str_pad($this->config['suffix'], 16, '-', STR_PAD_LEFT);

                continue;
            }
        }

        $this->sign($data);
    }

    public function request(array $data) {
        echo $this->getRequestForm($data);
    }

    public function getRequestForm(array $data) {

        $this->collectRequestData($data);

        $form = '<form action="' . $this->url() . '" id="pay-with-borica" method="post">';

        foreach ($data as $key => $val) {
            $key = htmlspecialchars($key, ENT_QUOTES, 'UTF-8');
            $val = htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
            $form .= '<input type="hidden" name="' . $key . '" value="' . $val . '" />';
        }

        $form .= '</form><script>document.getElementById("pay-with-borica").submit();</script>';

        return $form;
    }

    public function sign(array &$data) {
        $types = self::TRANSACTION_TYPES;

        if (!isset($data['TRTYPE']) || !isset($types[$data['TRTYPE']])) {
            $data['P_SIGN'] = '';
            return;
        }

        $sign = '';
        foreach($types[$data['TRTYPE']]['sign_request_fields'] as $key) {
            $sign .= isset($data[$key]) && is_scalar($data[$key]) && strlen($data[$key])
                ? strlen($data[$key]) . $data[$key] : '-';
        }

        $key = openssl_get_privatekey(
            $this->config["private_key"],
            $this->config["private_key_password"]
        );
        openssl_sign($sign, $signature, $key, OPENSSL_ALGO_SHA256);
        openssl_free_key($key);

        $data['P_SIGN'] = strtoupper(bin2hex($signature));
    }

    public function verify(array $data=null) {

        if ($data === null)
            $data = $_POST;

        if (!isset($data['P_SIGN']))
            return false;

        $types = self::TRANSACTION_TYPES;

        if (!isset($data['TRTYPE']) || !isset($types[$data['TRTYPE']]))
            return false;

        $sign = '';
        foreach ($types[$data['TRTYPE']]['sign_response_fields'] as $key)
            $sign .= isset($data[$key]) && is_scalar($data[$key]) && strlen($data[$key])
                ? strlen($data[$key]) . $data[$key] : '-';
        $sign = rtrim($sign, '-');

        $p_sign = hex2bin($data['P_SIGN']);
        $key = openssl_get_publickey($this->config["certificate"]);
        $verified = openssl_verify($sign, $p_sign, $key, OPENSSL_ALGO_SHA256);
        openssl_free_key($key);

        return ($verified === 1);
    }

}
