<div class="content">
    <h3 class="debug-title">Debug Bilgileri</h3>
    <table class="debug-table">
        <tr class="debug-header">
            <th class="debug-th">Key</th>
            <th class="debug-th">Value</th>
        </tr>
        <?php
        function renderData($data, $level = 0)
        {
            foreach ($data as $key => $value): ?>
                <tr class="debug-row<?= $level > 0 ? ' debug-row-nested' : '' ?>">
                    <td class="debug-key<?= $level > 0 ? ' debug-key-nested' : '' ?>" style="padding-left: <?= (6 + $level * 12) ?>px;">
                        <?= htmlspecialchars((string) $key, ENT_QUOTES, 'UTF-8') ?>
                    </td>
                    <td class="debug-value">
                        <?php if (is_array($value)): ?>
                            <table class="debug-table debug-table-nested">
                                <?php renderData($value, $level + 1); ?>
                            </table>
                        <?php else: ?>
                            <?= htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8') ?>
                        <?php endif; ?>
                    </td>
                </tr>
        <?php endforeach;
        }
        renderData($data);
        ?>
    </table>
</div>