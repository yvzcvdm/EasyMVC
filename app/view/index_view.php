<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <style type="text/css">
        ::selection {
            background-color: #E13300;
            color: white;
        }

        ::-moz-selection {
            background-color: #E13300;
            color: white;
        }

        body {
            background-color: #efefef;
            margin: 40px;
            font: 13px/20px normal Helvetica, Arial, sans-serif;
            color: #4F5155;
        }

        a {
            color: #003399;
            background-color: transparent;
            font-weight: normal;
        }

        h1 {
            color: #444;
            background-color: transparent;
            border-bottom: 1px solid #D0D0D0;
            font-size: 24px;
            font-weight: normal;
            margin: 0;
            padding: 20px;
        }

        code,
        pre {
            font-family: Consolas, Monaco, Courier New, Courier, monospace;
            font-size: 12px;
            background-color: #f9f9f9;
            border: 1px solid #D0D0D0;
            color: #002166;
            display: block;
            margin: 14px 0 14px 0;
            padding: 12px 10px 12px 10px;
            overflow: hidden;
        }

        #container {
            max-width: 1000px;
            background: #fff;
            margin: 2% auto;
            border: 1px solid #D0D0D0;
            box-shadow: 0 0 8px #D0D0D0;
        }

        p {
            margin: 12px 15px 12px 15px;
        }

        ul {
            list-style: none;
            margin: 0;
            padding: 0;
            border-bottom: 1px solid #D0D0D0;
            background: #f5f5f5;
            overflow: auto;
        }

        ul li {
            display: block;
            float: left;
            border-right: 1px solid #D0D0D0;
        }

        ul li a {
            display: block;
            padding: 10px 20px;
            text-decoration: none;
            color: #333;
        }

        ul li a:hover {
            background: #eaeaea;
        }

        div.content {
            padding: 20px;
        }

        table {
            width: 100%;
        }

        table td:first-child {
            text-align: right;
            width: 15%;
            min-width: 100px;
            color: #000;
            font-weight: bold;
        }

        table td {
            padding: 5px;
        }

        form {
            max-width: 300px;
        }

        form input {
            display: block;

        }

        div.row {
            padding:2%;
            display: flex;
            flex-direction: row;
            justify-content: space-around;
        }
        div.col {width:100%;}
    </style>
</head>

<body>
    <div id="container">
        <h1><?= $title ?></h1>
        <ul>
            <li><a href="<?= $app_root ?>">Home</a></li>
            <li><a href="<?= $app_root ?>corporate/">Corporate</a></li>
            <li><a href="<?= $app_root ?>contact">Contact</a></li>            
        </ul>        
        <div class="content">
            <div class="row">
                <div class="col">
                    <form action="<?= $app_uri ?>?get_1=data_1&get_2=data_2" method="post">
                        <table>
                            <tr>
                                <td>User Name</td>
                                <td><input type="text" name="user_name" value="<?= @$data["app_post"]["user_name"] ?>"></td>
                            </tr>
                            <tr>
                                <td>Password</td>
                                <td><input type="text" name="user_pass" value="<?= @$data["app_post"]["user_pass"] ?>"></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><input type="submit" value="Test Send"></td>
                            </tr>
                        </table>
                    </form>
                </div>
                <div class="col">
                    <table>
                        <tr>
                            <td>Root : </td>
                            <td><?= $app_root ?></td>
                        </tr>
                        <tr>
                            <td>Path : </td>
                            <td><?= $app_path ?></td>
                        </tr>
                        <tr>
                            <td>File : </td>
                            <td><?= $app_file ?></td>
                        </tr>
                        <tr>
                            <td>Function : </td>
                            <td><?= $app_function ?></td>
                        </tr>
                        <tr>
                            <td>URI : </td>
                            <td><?= $app_uri ?></td>
                        </tr>
                        <tr>
                            <td>Title : </td>
                            <td><?= $title ?></td>
                        </tr>
                        <tr>
                            <td>Random Code : </td>
                            <td><?= $text_code ?></td>
                        </tr>
                    </table>
                </div>
            </div>

        </div>

        <div class="content"><? highlight_string("<?php\n\$data =\n" . var_export($data, true) . ";\n?>"); ?></div>
    </div>
</body>

</html>