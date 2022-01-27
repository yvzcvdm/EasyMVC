<?php
$code = $_SERVER['REDIRECT_STATUS'];
$codes = array(
    400 => "Bad Request",
    401 => "Unauthorized",
    402 => "Payment Required",
    403 => "Forbidden",
    404 => "Not Found",
    405 => "Method Not Allowed",
    406 => "Not Acceptable",
    407 => "Proxy Authentication Required",
    408 => "Request Timeout",
    409 => "Conflict",
    410 => "Gone",
    411 => "Length Required",
    412 => "Precondition Failed",
    413 => "Request Entity Too Large",
    414 => "Request-URI Too Long",
    415 => "Unsupported Media Type",
    416 => "Requested Range Not Satisfiable",
    417 => "Expectation Failed",
    418 => "I'm a teapot",
    422 => "Unprocessable Entity",
    423 => "Locked",
    424 => "Method Failure",
    426 => "Upgrade Required",
    428 => "Precondition Required",
    429 => "Too Many Requests",
    431 => "Request Header Fields Too Large",
    444 => "No Response",
    451 => "Unavailable For Legal Reasons",
    499 => "Client Closed Request",
    500 => "Internal Server Error",
    501 => "Not Implemented",
    502 => "Bad Gateway",
    503 => "Service Unavailable",
    504 => "Gateway Timeout",
    505 => "HTTP Version Not Supported",
    506 => "Variant Also Negotiates",
    507 => "Insufficient Storage",
    508 => "Loop Detected",
    510 => "Not Extended",
    511 => "Network Authentication Required",
    599 => "Network connect timeout error"
);





$source_url = 'http' . ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error Page</title>
    <style>
        html,
        body {
            background: #ccc;
            color: #333;
        }

        .content {
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            right: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 10px;
            justify-content: center;
        }

        h1 {
            color: brown;
            font-size: 45px;
            margin-top:0px;
            margin-bottom:5px;
        }

        p {
            font-size: 18px;
        }
    </style>
</head>

<body>
    <div class="content">
        <?php if (array_key_exists($code, $codes) && is_numeric($code)) { ?>
            <h1><?php echo $code ?></h1>
            <p><?php echo $codes[$code] ?></p>
        <? } else { ?>
            <p>Tanımlanamayan Hata!</p>
        <? } ?>
    </div>
</body>

</html>