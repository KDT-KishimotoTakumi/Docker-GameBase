<?php
// ==================================================
// 画面③ 強化結果
// ・素材カードを削除する
// ・強化対象カードの card_id を、cards.next_card_id（強化後のカードID）に変える
// ・カードが新しいIDに変わったことがわかる情報を表示
// ==================================================
require_once __DIR__ . '/db.php';

$db = getDb();
$targetId   = (int)($_GET['target'] ?? 0);
$materialId = (int)($_GET['material'] ?? 0);

pageHeader('画面③ 強化結果');

// エラーを表示して終わる
function fail(string $message): void
{
    echo '<p class="msg">' . h($message) . '</p>';
    echo '<p><a href="index.php">画面①へ戻る</a></p>';
    pageFooter();
    exit;
}

// カード1枚分の情報を取得する（自分のカードだけ）
function findUserCard(PDO $db, int $userCardId): array|false
{
    $sql = <<<SQL
    SELECT uc.id AS user_card_id, uc.card_id, c.name, c.rarity, c.next_card_id
    FROM user_cards AS uc
    INNER JOIN cards AS c ON uc.card_id = c.id
    WHERE uc.id = :id AND uc.user_id = :user_id
    SQL;
    $stmt = $db->prepare($sql);
    $stmt->execute([':id' => $userCardId, ':user_id' => USER_ID]);
    return $stmt->fetch();
}

// ---------- ここから強化処理 ----------
try {
    // 途中で失敗したら全部取り消せるように、トランザクションを開始する
    $db->beginTransaction();

    $before   = findUserCard($db, $targetId);
    $material = findUserCard($db, $materialId);

    // チェック（URLを書き換えられたときや、ページを更新したときのため）
    if (!$before || !$material) {
        throw new RuntimeException('カードが見つかりません（すでに強化済みの可能性があります）。');
    }
    if ($targetId === $materialId) {
        throw new RuntimeException('強化対象と同じカードは素材にできません。');
    }
    if ($before['card_id'] !== $material['card_id']) {
        throw new RuntimeException('素材は強化対象と同じカードを選んでください。');
    }
    if ($before['next_card_id'] === null) {
        throw new RuntimeException('このカードはこれ以上強化できません。');
    }
    $stmt = $db->prepare('SELECT COUNT(*) FROM user_decks WHERE user_id = :user_id AND user_card_id = :id');
    $stmt->execute([':user_id' => USER_ID, ':id' => $materialId]);
    if ($stmt->fetchColumn() > 0) {
        throw new RuntimeException('デッキに入っているカードは素材にできません。');
    }

    // ① 素材カードを削除する
    $stmt = $db->prepare('DELETE FROM user_cards WHERE id = :id AND user_id = :user_id');
    $stmt->execute([':id' => $materialId, ':user_id' => USER_ID]);

    // ② 強化対象カードを、新しいカードIDに変える
    $stmt = $db->prepare('UPDATE user_cards SET card_id = :next WHERE id = :id AND user_id = :user_id');
    $stmt->execute([':next' => $before['next_card_id'], ':id' => $targetId, ':user_id' => USER_ID]);

    // 全部成功したので確定する
    $db->commit();
} catch (RuntimeException $e) {
    $db->rollBack();
    fail($e->getMessage());
}

// 強化後のカード情報を取得
$after = findUserCard($db, $targetId);
?>
<p class="new">強化に成功しました！</p>

<table>
  <tr><th></th><th>カードID</th><th>カード名</th><th>レアリティ</th></tr>
  <tr>
    <th>強化前</th>
    <td><?= h($before['card_id']) ?></td>
    <td><?= h($before['name']) ?></td>
    <td><?= h($before['rarity']) ?></td>
  </tr>
  <tr>
    <th>強化後</th>
    <td class="new"><?= h($after['card_id']) ?></td>
    <td class="new"><?= h($after['name']) ?></td>
    <td class="new"><?= h($after['rarity']) ?></td>
  </tr>
</table>

<p>ユーザーカードID <?= h($targetId) ?> のカードが、
  カードID <?= h($before['card_id']) ?>（<?= h($before['name']) ?>）から
  カードID <span class="new"><?= h($after['card_id']) ?>（<?= h($after['name']) ?>）</span> に変わりました。</p>
<p>素材にしたユーザーカードID <?= h($materialId) ?>（<?= h($material['name']) ?>）は消費されました。</p>

<p><a href="index.php">画面①へ戻る</a></p>
<?php pageFooter(); ?>