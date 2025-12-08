<div class="content">
    <h3 style="margin-top: 30px; color: #333;">Debug Bilgileri</h3>
    <p style="color: #666; font-size: 14px; margin-bottom: 15px;">
        Bu tablo, uygulamaya gelen tüm verileri göstermektedir. Başlıklar (GET, POST, SESSION vb.)
        ve diğer sistem bilgilerini burada izleyebilirsiniz.
    </p>

    <table style="width: 100%; border-collapse: collapse; background: #ffffff;">
        <tr style="background: #2c3e50; font-weight: bold; color: #ffffff;">
            <th style="border: 1px solid #27394bff; padding: 6px 8px; text-align: left;">Key</th>
            <th style="border: 1px solid #27394bff; padding: 6px 8px; text-align: left;">Value</th>
        </tr>
        <?php
        function renderData($data, $level = 0)
        {
            foreach ($data as $key => $value): ?>
                <tr style="background: <?= $level > 0 ? '#ecf0f1' : '#ffffff' ?>; border-bottom: 1px solid #999;">
                    <td style="border: 1px solid #223344ff; padding: 1px 6px 1px <?= (6 + $level * 12) ?>px; font-weight: bold; background: <?= $level > 0 ? '#d5dbdb' : '#34495e' ?>; color: <?= $level > 0 ? '#2c3e50' : '#ffffff' ?>;">
                        <?= htmlspecialchars($key) ?>
                    </td>
                    <td style="border: 1px solid #34495e; padding: 1px 6px; color: #2c3e50;">
                        <?php if (is_array($value)): ?>
                            <table style="width: 100%; border-collapse: collapse; margin: 1px 0;">
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