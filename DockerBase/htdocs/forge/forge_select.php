<?php
// ==================================================
// 画面② ベースアイテムインスタンス選択
// 選んだアイテムのインスタンス（1個ずつ）を一覧表示し、ベースを1つ選ぶ
// ==================================================
require_once __DIR__ . '/forge_db.php';

$db = getDb();
$targetItemId = (int)($_GET['target_item_id'] ?? 0);

$instances = getUsableInstances($db, $targetItemId);

pageHeader('ベースアイテムインスタンス選択');
?>
<h1>ベースアイテムインスタンス選択 (アイテムID: <?= h($targetItemId) ?>)</h1>
<p>強化の<strong>ベース（土台）</strong>となるアイテムインスタンスを1つ選択してください。</p>

<?php if (count($instances) < 2): ?>
  <p class="msg">このアイテムは強化に必要な数（2個）を持っていません。</p>
<?php else: ?>
  <table>
    <tr>
      <th>インスタンスID</th>
      <th>アイテム名</th>
      <th>取得日時</th>
      <th>ベースとして選択</th>
    </tr>
    <?php foreach ($instances as $ins): ?>
    <tr>
      <td><?= h($ins['id']) ?></td>
      <td><?= h($ins['name']) ?></td>
      <td><?= h($ins['created_at']) ?></td>
      <td><a href="forge_select_material.php?target_item_type_id=<?= h($targetItemId) ?>&amp;base_user_item_id=<?= h($ins['id']) ?>">ベースに決定</a></td>
    </tr>
    <?php endforeach; ?>
  </table>
<?php endif; ?>

<p><a href="forge_entrance.php">強化対象アイテム選択に戻る</a></p>
<?php pageFooter(); ?>
