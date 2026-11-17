<?php
// ==================================================
// 画面② 素材カード選択
// ・強化対象カードと同じカードIDのものだけ表示（強化対象そのものは除く）
// ・同じカードIDを複数持っている場合は、複数行表示
// ・デッキ（チーム編成）に入っているカードは素材にできないので除く
// ・カード名をリンクにして、押したら画面③へ
// ==================================================
require_once __DIR__ . '/db.php';

$db = getDb();
$targetId = (int)($_GET['target'] ?? 0);

// 強化対象カードの情報を取得（自分のカードかどうかも確認）
$sql = <<<SQL
SELECT uc.id AS user_card_id, uc.card_id, c.name, c.rarity
FROM user_cards AS uc
INNER JOIN cards AS c ON uc.card_id = c.id
WHERE uc.id = :target AND uc.user_id = :user_id
SQL;
$stmt = $db->prepare($sql);
$stmt->execute([':target' => $targetId, ':user_id' => USER_ID]);
$target = $stmt->fetch();

pageHeader('画面② 素材カード選択');

if (!$target) {
    echo '<p class="msg">強化対象のカードが見つかりません。</p>';
    echo '<p><a href="index.php">画面①へ戻る</a></p>';
    pageFooter();
    exit;
}

// 素材にできるカードを取得
$sql = <<<SQL
SELECT uc.id AS user_card_id, uc.card_id, c.name, c.rarity
FROM user_cards AS uc
INNER JOIN cards AS c ON uc.card_id = c.id
WHERE uc.user_id = :user_id
  AND uc.card_id = :card_id
  AND uc.id <> :target
  AND uc.id NOT IN (
      SELECT user_card_id FROM user_decks WHERE user_id = :user_id2
  )
ORDER BY uc.id
SQL;
$stmt = $db->prepare($sql);
$stmt->execute([
    ':user_id'  => USER_ID,
    ':card_id'  => $target['card_id'],
    ':target'   => $targetId,
    ':user_id2' => USER_ID,
]);
$materials = $stmt->fetchAll();
?>
<h2>強化対象カード</h2>
<table>
  <tr><th>ユーザーカードID</th><th>カードID</th><th>カード名</th><th>レアリティ</th></tr>
  <tr>
    <td><?= h($target['user_card_id']) ?></td>
    <td><?= h($target['card_id']) ?></td>
    <td><?= h($target['name']) ?></td>
    <td><?= h($target['rarity']) ?></td>
  </tr>
</table>

<h2>素材カードを選んでください</h2>
<?php if (count($materials) === 0): ?>
  <p class="msg">素材にできるカードがありません（デッキに入っているカードは素材にできません）。</p>
<?php else: ?>
  <table>
    <tr><th>ユーザーカードID</th><th>カードID</th><th>カード名</th><th>レアリティ</th></tr>
    <?php foreach ($materials as $m): ?>
    <tr>
      <td><?= h($m['user_card_id']) ?></td>
      <td><?= h($m['card_id']) ?></td>
      <td><a href="result.php?target=<?= h($targetId) ?>&amp;material=<?= h($m['user_card_id']) ?>"><?= h($m['name']) ?></a></td>
      <td><?= h($m['rarity']) ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
<?php endif; ?>

<p><a href="index.php">画面①へ戻る</a></p>
<?php pageFooter(); ?>