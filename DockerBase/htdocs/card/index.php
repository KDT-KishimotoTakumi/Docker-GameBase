<?php
// ==================================================
// 画面① 強化対象カード選択
// ・ユーザーカード一覧を表示
// ・同じカードIDを2枚以上持っているものだけ表示
// ・カード名をリンクにして、押したら画面②へ
// ==================================================
require_once __DIR__ . '/db.php';

$db = getDb();

// サブクエリで「2枚以上持っている card_id」を探し、その card_id のカードだけ取り出す
// next_card_id が NULL のカードはこれ以上強化できないので出さない
$sql = <<<SQL
SELECT uc.id AS user_card_id, uc.card_id, c.name, c.rarity
FROM user_cards AS uc
INNER JOIN cards AS c ON uc.card_id = c.id
WHERE uc.user_id = :user_id
  AND c.next_card_id IS NOT NULL
  AND uc.card_id IN (
      SELECT card_id
      FROM user_cards
      WHERE user_id = :user_id2
      GROUP BY card_id
      HAVING COUNT(*) >= 2
  )
ORDER BY uc.card_id, uc.id
SQL;

$stmt = $db->prepare($sql);
$stmt->execute([':user_id' => USER_ID, ':user_id2' => USER_ID]);
$cards = $stmt->fetchAll();

pageHeader('画面① 強化対象カード選択');
?>
<p>強化したいカードを選んでください。</p>

<?php if (count($cards) === 0): ?>
  <p class="msg">強化できるカードがありません（同じカードを2枚以上持っていません）。</p>
<?php else: ?>
  <table>
    <tr>
      <th>ユーザーカードID</th>
      <th>カードID</th>
      <th>カード名</th>
      <th>レアリティ</th>
    </tr>
    <?php foreach ($cards as $card): ?>
    <tr>
      <td><?= h($card['user_card_id']) ?></td>
      <td><?= h($card['card_id']) ?></td>
      <td><a href="material.php?target=<?= h($card['user_card_id']) ?>"><?= h($card['name']) ?></a></td>
      <td><?= h($card['rarity']) ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
<?php endif; ?>

<?php pageFooter(); ?>