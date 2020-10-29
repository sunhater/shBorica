<?php

/**  @desc Payment request for Borica EMV 3D Secure Payment
* @package shBorica
* @version 1.0
*  @author Pavel Tzonkov
* @license https://opensource.org/licenses/MIT MIT
*    @link https://github.com/sunhater/shBorica */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    require 'lib/Borica.php';

    $borica = new Borica(
        require('config/config.php'),
        require('config/defaults.php')
    );

    // FOR DEBUG
    // $data = $_POST;
    // $borica->collectRequestData($data);
    // var_dump($data); die;

    $borica->request($_POST);
    die;
}

$ORDER = rand(1, 9999);
$AMOUNT = rand(1000, 9999) / 100;

?><html>
<head>
    <title>Borica 3D Secure test request</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous" />
</head>
<body class="m-3">

<form action="request.php" method="post" class="text-right" style="width: 15rem; margin: 0 auto">

    <div class="input-group mb-3">
        <div class="input-group-prepend">
            <div class="input-group-text">ORDER</div>
        </div>
        <input name="ORDER" value="<?php echo $ORDER; ?>" type="number" min="1" max="999999" class="form-control text-right" />
    </div>

    <div class="input-group mb-3">
        <div class="input-group-prepend">
            <div class="input-group-text">AMOUNT</div>
        </div>
        <input name="AMOUNT" value="<?php echo $AMOUNT; ?>" type="number" min="1" max="99.99" step=".01" class="form-control text-right" />
    </div>

    <div class="input-group mb-3">
        <div class="input-group-prepend">
            <div class="input-group-text">DESC</div>
        </div>
        <input name="DESC" value="Order #<?php echo $ORDER; ?>" type="text" maxlength="50" class="form-control text-right" />
    </div>

    <button type="submit" class="btn btn-primary">
        Submit
    </button>

</form>

</body>
</html>