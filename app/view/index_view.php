<div id="container">
    <img class="logo" src="<?= $app["root"] ?>public/images/logo.png" alt="Logo">
    <h1><?= $title ?></h1>
    <?php include LAYOUT . "/menu.php"; ?>
    <div class="content">
        <div class="d-flex">
            <div>

                <form action="<?= $app["uri"] ?>?get_1=data_1&get_2=data_2" method="post">
                    <table>
                        <tr>
                            <td colspan="2" style="text-align: center;">
                                <h3>Örnek Form</h3>
                            </td>
                        </tr>
                        <tr>
                            <td>Input 1</td>
                            <td><input type="text" name="input_1" value="<?= @$data["app"]["post"]["input_1"] ?>"></td>
                        </tr>
                        <tr>
                            <td>Input 2</td>
                            <td><input type="text" name="input_2" value="<?= @$data["app"]["post"]["input_2"] ?>"></td>
                        </tr>
                        <tr>
                            <td>Input 3</td>
                            <td><input type="text" name="input_3" value="<?= @$data["app"]["post"]["input_3"] ?>"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><input type="submit" value="Test Send"></td>
                        </tr>
                    </table>
                </form>
            </div>
            <div>
                <table>
                    <tr>
                        <td>Root : </td>
                        <td><?= $app["root"] ?></td>
                    </tr>
                    <tr>
                        <td>Folder : </td>
                        <td><?= $app["folder"] ?></td>
                    </tr>
                    <tr>
                        <td>File : </td>
                        <td><?= $app["file"] ?></td>
                    </tr>
                    <tr>
                        <td>Method : </td>
                        <td><?= $app["method"] ?></td>
                    </tr>
                    <tr>
                        <td>URI : </td>
                        <td><?= $app["uri"] ?></td>
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

    <?php include LAYOUT . "/debug.php"; ?>

</div>