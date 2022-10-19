<?php
$code = $_SERVER['REDIRECT_STATUS'];
$codes = array(
    null => "Page Not Found",
    200 => "Page Not Found",
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
<html lang="tr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?php echo $codes[$code]?></title>

    <style>
        * {
            -webkit-box-sizing: border-box;
            box-sizing: border-box
        }

        body {
            padding: 0;
            margin: 0
        }

        #notfound {
            position: relative;
            height: 100vh
        }

        #notfound .notfound {
            position: absolute;
            left: 50%;
            top: 50%;
            -webkit-transform: translate(-50%, -50%);
            -ms-transform: translate(-50%, -50%);
            transform: translate(-50%, -50%)
        }

        .notfound {
            max-width: 460px;
            width: 100%;
            text-align: center;
            line-height: 1.4
        }

        .notfound .notfound-404 {
            position: relative;
            width: 180px;
            height: 180px;
            margin: 0 auto 50px
        }

        .notfound .notfound-404>div:first-child {
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            bottom: 0;
            background: #ffa200;
            -webkit-transform: rotate(45deg);
            -ms-transform: rotate(45deg);
            transform: rotate(45deg);
            border: 5px dashed #000;
            border-radius: 5px
        }

        .notfound .notfound-404>div:first-child:before {
            content: '';
            position: absolute;
            left: -5px;
            right: -5px;
            bottom: -5px;
            top: -5px;
            -webkit-box-shadow: 0 0 0 5px rgba(0, 0, 0, .1) inset;
            box-shadow: 0 0 0 5px rgba(0, 0, 0, .1) inset;
            border-radius: 5px
        }

        .notfound .notfound-404 h1 {
            font-family: cabin, sans-serif;
            color: #000;
            font-weight: 700;
            margin: 0;
            font-size: 90px;
            position: absolute;
            top: 50%;
            -webkit-transform: translate(-50%, -50%);
            -ms-transform: translate(-50%, -50%);
            transform: translate(-50%, -50%);
            left: 50%;
            text-align: center;
            height: 40px;
            line-height: 40px
        }

        .notfound h2 {
            font-family: cabin, sans-serif;
            font-size: 33px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px
        }

        .notfound p {
            font-family: cabin, sans-serif;
            font-size: 16px;
            color: #000;
            font-weight: 400
        }

        .notfound a {
            font-family: cabin, sans-serif;
            display: inline-block;
            padding: 10px 25px;
            background-color: #ffa200;
            border: none;
            border-radius: 40px;
            color: #333;
            border:1px solid #000;
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            text-decoration: none;
            -webkit-transition: .2s all;
            transition: .2s all
        }

        .notfound a:hover {
            background-color: #2c2c2c;
            color:#fff;
        }
    </style>

    <!--[if lt IE 9]>
		  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
    <meta name="robots" content="noindex, follow">
</head>

<body>
    
    <div id="notfound">
        <div class="notfound">
            <div class="notfound-404">
                <div></div>
                <h1><?php echo ($code == 200 || empty($code))?404:$code ?></h1>
            </div>
            <h2><?php echo $codes[$code]?></h2>
            <p>The page you are looking for might have been removed had its name changed or is temporarily unavailable.</p>
            <a href="<?php echo $domain ?>">Home Page</a>
        </div>
    </div>
</body>

</html>