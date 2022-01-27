<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>


    <div id="container">
        <img class="logo" src="/assets/img/logo.png" alt="Logo">
        <h1><?= $title ?></h1>

        <ul>
            <li><a href="<?= $app_root ?>">Home</a></li>
            <li><a href="<?= $app_root ?>corporate/">Corporate</a></li>
            <li><a href="<?= $app_root ?>contact">Contact</a></li>
        </ul>
        <div class="content">
            <div class="row">
                <div class="col">
                    <div class="user"><img src="/assets/upload/user.png" alt="Logo"></div>

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

    <script src="/assets/js/script.js"></script>
</body>

</html>