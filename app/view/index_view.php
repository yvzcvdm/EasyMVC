<div id="container">
    <img class="logo" src="<?= $app["root"] ?>public/images/logo.png" alt="Logo">
    <h1><?= $title ?></h1>
    <?php include LAYOUT . "/menu.php"; ?>
    <div class="content">
        <div class="row">
            <div class="col">

                <form action="<?= $app["uri"] ?>?get_1=data_1&get_2=data_2" method="post">
                    <table>
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
            <div class="col">
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

    <div class="content">
        <h3 style="margin-top: 30px; color: #333;">Debug Bilgileri</h3>
        <p style="color: #666; font-size: 14px; margin-bottom: 15px;">
            Bu tablo, uygulamaya gelen tüm verileri göstermektedir. Başlıklar (GET, POST, SESSION vb.)
            ve diğer sistem bilgilerini burada izleyebilirsiniz.
        </p>

        <table style="width: 100%; border-collapse: collapse; background: #fff;">
            <tr style="background: #f0f0f0; font-weight: bold;">
                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Key</th>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Value</th>
            </tr>
            <?php
            function renderData($data, $level = 0)
            {
                foreach ($data as $key => $value): ?>
                    <tr style="background: <?= $level > 0 ? '#f5f5f5' : '#fff' ?>;">
                        <td style="border: 1px solid #ddd; padding: 8px; font-weight: bold; background: #fafafa; padding-left: <?= (8 + $level * 15) ?>px;">
                            <?= htmlspecialchars($key) ?>
                        </td>
                        <td style="border: 1px solid #ddd; padding: 8px;">
                            <?php if (is_array($value)): ?>
                                <table style="width: 100%; border-collapse: collapse; margin: 5px 0;">
                                    <?php renderData($value, $level + 1); ?>
                                </table>
                            <?php else: ?>
                                <?= htmlspecialchars($value) ?>
                            <?php endif; ?>
                        </td>
                    </tr>
            <?php endforeach;
            }
            renderData($data);
            ?>
        </table>
    </div>
</div>