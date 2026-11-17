<?php
// ==================================================
// 画面① 強化対象アイテム選択
// 同じアイテムを2個以上持っている（強化に使える）アイテムだけを、アイテムごとに1行で表示
// ==================================================
require_once __DIR__ . '/forge_db.php';

$db = getDb();

// アイテムごとに GROUP BY して数え、2個以上のものだけ HAVING で残す
// ・チーム編成に入っているものは数えない
// ・next_item_id が NULL（これ以上強化できない）のアイテムは出さない
$sql = <<<SQL
SELECT i.id, i.name, COUNT(ui.id) AS item_count
FROM user_items AS ui
INNER JOIN items AS i ON ui.item_id = i.id
WHERE ui.user_id = :user_id
  AND i.next_item_id IS NOT NULL
  AND ui.id NOT IN (SELECT user_item_id FROM user_decks WHERE user_id = :user_id2)
GROUP BY i.id, i.name
HAVING COUNT(ui.id) >= 2
ORDER BY i.id
SQL;
$stmt = $db->prepare($sql);
$stmt->execute([':user_id' => USER_ID, ':user_id2' => USER_ID]);
$items = $stmt->fetchAll();

pageHeader('強化対象アイテム選択');
?>
<h1>強化対象アイテム選択</h1>
<p>強化のベースとして利用するアイテムを選択してください。</p>

<?php if (count($items) === 0): ?>
  <p class="msg">強化できるアイテムがありません（同じアイテムを2個以上持っていません）。</p>
<?php else: ?>
  <table>
    <tr>
      <th>アイテムID</th>
      <th>アイテム名</th>
      <th>所持数</th>
      <th>選択</th>
    </tr>
    <?php foreach ($items as $item): ?>
    <tr>
      <td><?= h($item['id']) ?></td>
      <td><?= h($item['name']) ?></td>
      <td><?= h($item['item_count']) ?></td>
      <td><a href="forge_select.php?target_item_id=<?= h($item['id']) ?>">強化する</a></td>
    </tr>
    <?php endforeach; ?>
  </table>
<?php endif; ?>

<?php pageFooter(); ?>
