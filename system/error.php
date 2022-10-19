<?php
header('HTTP/1.0 404 Not Found', true, 404);
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
header('HTTP/1.0 ' . $code . ' ' . $codes[$code], true, $code);
$source_url = 'http' . ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$domain = 'http' . ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? 's' : '') . '://' . $_SERVER['HTTP_HOST'];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" sizes="16x16" href="data:image/png;base64,
iVBORw0KGgoAAAANSUhEUgAAABAAAAAQBAMAAADt3eJSAAAAMFBMVEU0OkArMjhobHEoPUPFEBIu
O0L+AAC2FBZ2JyuNICOfGx7xAwTjCAlCNTvVDA1aLzQ3COjMAAAAVUlEQVQI12NgwAaCDSA0888G
CItjn0szWGBJTVoGSCjWs8TleQCQYV95evdxkFT8Kpe0PLDi5WfKd4LUsN5zS1sKFolt8bwAZrCa
GqNYJAgFDEpQAAAzmxafI4vZWwAAAABJRU5ErkJggg==" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error!</title>
    <style>
        html,
        body {
            background: #eee;
            color: #aaa;
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
            color: #e1c500;
            font-size: 45px;
            margin-top: 0px;
            margin-bottom: 5px;
        }

        p {
            font-size: 18px;
        }

        .code {
            padding: 10px 15px;
            border: 1px solid #000;
            background: #333;
            font-size: 14px;
            border-radius: 5px;
        }

        a {
            padding: 10px 15px;
            display: block;
            background: #e1c500;
            margin-top: 20px;
            color: #333;
            font-weight: bold;
            text-decoration: none;
            border-radius: 5px;
        }

        a:hover {
            background: #bda500;
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
        <p class="code"><?php echo $source_url; ?></p>
        <a href="<?php echo $domain ?>">Home Page</a>
    </div>
</body>

</html>