<?php

/**  @desc Responce from Borica EMV 3D Secure Payment
* @package shBorica
* @version 1.0
*  @author Pavel Tzonkov
* @license https://opensource.org/licenses/MIT MIT
*    @link https://github.com/sunhater/shBorica */

require 'lib/Borica.php';

$borica = new Borica(
    require('config/config.php'),
    require('config/defaults.php')
);

if ($_SERVER['REQUEST_METHOD'] !== 'POST')
    die('Request method is not POST');

// FOR DEBUG
// var_dump($_POST); die;

foreach (['TERMINAL', 'TRTYPE', 'ORDER', 'AMOUNT', 'CURRENCY', 'ACTION', 'NONCE', 'TIMESTAMP', 'P_SIGN'] as $key)
    if (!isset($_POST[$key]) || !is_string($_POST[$key]))
        die('Missing requered data');

if (($_POST['TRTYPE'] !== '1') ||
    ($_POST['TERMINAL'] !== $borica->getDefault('TERMINAL')) ||
    ($_POST['CURRENCY'] !== $borica->getDefault('CURRENCY'))
)
    die('Data missmatch');

if (!$borica->verify())
    die('Response is not verified');

if (($_POST['ACTION'] !== '0') ||
    (
        isset($_POST['RC']) &&
        ($_POST['RC'] !== '00')
    )
)
    die('Payment failed');

// TODO: CHECK TIMESTAMP, ORDER & AMOUNT

echo 'Successfully paid';