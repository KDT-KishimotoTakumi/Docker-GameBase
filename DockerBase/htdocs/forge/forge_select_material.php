<?php
// ==================================================
// 画面③ 素材アイテムインスタンス選択
// ベースと同じアイテムのインスタンスから、ベース以外を表示して素材を1つ選ぶ
// ==================================================
require_once __DIR__ . '/forge_db.php';

$db = getDb();
$targetItemId = (int)($_GET['target_item_type_id'] ?? 0);
$baseId       = (int)($_GET['base_user_item_id'] ?? 0);

// ベースが自分の使えるインスタンスか確認する
$baseOk = false;
foreach (getUsableInstances($db, $targetItemId) as $ins) {
    if ((int)$ins['id'] === $baseId) {
        $baseOk = true;
    }
}

// ベース以外のインスタンス = 素材候補
$materials = getUsableInstances($db, $targetItemId, $baseId);

pageHeader('素材アイテムインスタンス選択');
?>
<h1>素材アイテムインスタンス選択 (アイテムID: <?= h($targetItemId) ?>)</h1>

<?php if (!$baseOk): ?>
  <p class="msg">ベースのアイテムが見つかりません。</p>
<?php else: ?>
  <p>ベースアイテムインスタンスID: <strong><?= h($baseId) ?></strong></p>
  <p>強化の<strong>素材</strong>として利用するアイテムインスタンスを1つ選択してください。</p>

  <?php if (count($materials) === 0): ?>
    <p class="msg">素材にできるアイテムがありません。</p>
  <?php else: ?>
    <table>
      <tr>
        <th>インスタンスID</th>
        <th>アイテム名 (素材ID: <?= h($targetItemId) ?>)</th>
        <th>取得日時</th>
        <th>選択</th>
      </tr>
      <?php foreach ($materials as $m): ?>
      <tr>
        <td><?= h($m['id']) ?></td>
        <td><?= h($m['name']) ?></td>
        <td><?= h($m['created_at']) ?></td>
        <td><a href="forge_result.php?base_id=<?= h($baseId) ?>&amp;material_id=<?= h($m['id']) ?>">素材に決定し強化開始</a></td>
      </tr>
      <?php endforeach; ?>
    </table>
  <?php endif; ?>
<?php endif; ?>

<p><a href="forge_entrance.php">最初に戻る</a></p>
<?php pageFooter(); ?>
