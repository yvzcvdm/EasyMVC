<div id="container">
    <img class="logo" src="<?= $app["root"] ?>public/images/logo.png" alt="Logo">
    <h1><?= $title ?></h1>

    <ul>
        <li><a href="<?= $app["root"] ?>">Home</a></li>
        <li><a href="<?= $app["root"] ?>corporate/">Corporate</a></li>
        <li><a href="<?= $app["root"] ?>contact">Contact</a></li>
        <li><a href="<?= $app["root"] ?>upload">Upload</a></li>
    </ul>
    <ul>
        <li><a href="<?= $app["root"] ?>admin/">Admin</a></li>
        <li><a href="<?= $app["root"] ?>admin/users/">Users</a></li>
        <li><a href="<?= $app["root"] ?>admin/settings">Settings</a></li>
    </ul>
    <div class="content">
        <div class="row">
            <div class="col">

                <form action="<?= $app["uri"] ?>" method="post" enctype="multipart/form-data">
                    <table>
                        <tr>
                            <td>Dosya(lar) Seç</td>
                            <td><input type="file" name="file_input[]" multiple></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><input type="submit" value="Gönder"></td>
                        </tr>
                    </table>
                </form>
            </div>

        </div>

    </div>
    <div class="content">
        <h3>Yükleme Sonuçları</h3>

        <?php if (!empty($items)): ?>
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                <thead>
                    <tr style="background-color: #f5f5f5;">
                        <th style="border: 1px solid #ddd; padding: 5px 10px; text-align: left; font-weight: bold;">Dosya</th>
                        <th style="border: 1px solid #ddd; padding: 5px 10px; text-align: left; font-weight: bold;">Durum</th>
                        <th style="border: 1px solid #ddd; padding: 5px 10px; text-align: left; font-weight: bold;">Detay</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td style="border: 1px solid #ddd; padding: 12px;"><?= htmlspecialchars($item['file'] ?? 'N/A') ?></td>
                            <td style="border: 1px solid #ddd; padding: 12px;">
                                <?php if ($item['status'] === 'success'): ?>
                                    <span style="color: #4caf50; font-weight: bold;">✓ Başarılı</span>
                                <?php else: ?>
                                    <span style="color: #f44336; font-weight: bold;">❌ Hata</span>
                                <?php endif; ?>
                            </td>
                            <td style="border: 1px solid #ddd; padding: 12px;">
                                <?php if ($item['status'] === 'success'): ?>
                                    
                                        <?= htmlspecialchars($item['path']) ?>
                                    
                                <?php else: ?>
                                    <?= htmlspecialchars($item['message'] ?? '') ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="color: #999; text-align: center; padding: 20px;">Henüz dosya yüklenmedi.</p>
        <?php endif; ?>
    </div>
</div>
    </div>
</div>