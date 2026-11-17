<?php
// ==================================================
// 画面④ 強化結果
// ・ベースと素材の2つを消費（DELETE）する
// ・強化後のアイテム（items.next_item_id）を新しく1つ追加（INSERT）する
// ==================================================
require_once __DIR__ . '/forge_db.php';

$db = getDb();
$baseId     = (int)($_GET['base_id'] ?? 0);
$materialId = (int)($_GET['material_id'] ?? 0);

// インスタンス1つ分の情報を取得する（自分のもの・チーム編成に入っていないものだけ）
function findInstance(PDO $db, int $id): array|false
{
    $sql = <<<SQL
    SELECT ui.id, ui.item_id, i.name, i.next_item_id
    FROM user_items AS ui
    INNER JOIN items AS i ON ui.item_id = i.id
    WHERE ui.id = :id
      AND ui.user_id = :user_id
      AND ui.id NOT IN (SELECT user_item_id FROM user_decks WHERE user_id = :user_id2)
    SQL;
    $stmt = $db->prepare($sql);
    $stmt->execute([':id' => $id, ':user_id' => USER_ID, ':user_id2' => USER_ID]);
    return $stmt->fetch();
}

$error = '';
try {
    // 途中で失敗したら全部取り消せるように、トランザクションを開始する
    $db->beginTransaction();

    $base     = findInstance($db, $baseId);
    $material = findInstance($db, $materialId);

    // チェック（URLを書き換えられたときや、ページを更新したときのため）
    if (!$base || !$material) {
        throw new RuntimeException('アイテムが見つかりません（すでに強化済みの可能性があります）。');
    }
    if ($baseId === $materialId) {
        throw new RuntimeException('ベースと同じアイテムは素材にできません。');
    }
    if ($base['item_id'] !== $material['item_id']) {
        throw new RuntimeException('素材はベースと同じアイテムを選んでください。');
    }
    if ($base['next_item_id'] === null) {
        throw new RuntimeException('このアイテムはこれ以上強化できません。');
    }

    // ① ベースと素材を消費する
    $stmt = $db->prepare('DELETE FROM user_items WHERE id IN (:base, :material) AND user_id = :user_id');
    $stmt->execute([':base' => $baseId, ':material' => $materialId, ':user_id' => USER_ID]);

    // ② 強化後のアイテムを追加する
    $stmt = $db->prepare('INSERT INTO user_items (user_id, item_id) VALUES (:user_id, :item_id)');
    $stmt->execute([':user_id' => USER_ID, ':item_id' => $base['next_item_id']]);
    $newInstanceId = (int)$db->lastInsertId();

    // 強化後のアイテム名を取得
    $stmt = $db->prepare('SELECT id, name FROM items WHERE id = :id');
    $stmt->execute([':id' => $base['next_item_id']]);
    $newItem = $stmt->fetch();

    // 全部成功したので確定する
    $db->commit();
} catch (RuntimeException $e) {
    $db->rollBack();
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>強化結果</title>
<style>
  body    { font-family: sans-serif; margin: 40px; text-align: center; }
  .success { display: inline-block; border: 1px solid green; color: green; padding: 20px 30px; font-size: 1.5em; font-weight: bold; }
  .name   { color: darkblue; font-weight: bold; font-size: 1.1em; }
  .new    { color: darkblue; font-weight: bold; font-size: 1.6em; margin: 20px 0 10px; }
  .newid  { color: darkred; }
  .msg    { color: #c00; }
  hr      { margin: 20px 0; }
  ul      { list-style-position: inside; padding: 0; }
  li      { margin: 6px 0; }
</style>
</head>
<body>

<?php if ($error !== ''): ?>
  <p class="msg"><?= h($error) ?></p>
<?php else: ?>
  <div class="success">🎉 強化成功！ 🎉</div>

  <p>以下のアイテムを消費しました:</p>
  <ul>
    <li>ベース: <span class="name"><?= h($base['name']) ?></span> (インスタンスID: <?= h($baseId) ?>)</li>
    <li>素材: <span class="name"><?= h($material['name']) ?></span> (インスタンスID: <?= h($materialId) ?>)</li>
  </ul>

  <hr>

  <p>新しいアイテムを獲得しました:</p>
  <div class="new"><?= h($newItem['name']) ?></div>
  <p class="newid">(新しいアイテムID: <?= h($newItem['id']) ?> / インスタンスID: <?= h($newInstanceId) ?>)</p>
<?php endif; ?>

<p style="margin-top: 40px;"><a href="forge_entrance.php">続けて強化を行う</a></p>

</body>
</html>
