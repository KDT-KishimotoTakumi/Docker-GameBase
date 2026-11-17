<?php
// ==================================================
// データベース接続と共通の関数
// ==================================================

// ログイン機能はまだないので、ユーザーID 1 として動かす
const USER_ID = 1;

// MySQLに接続する（ホスト名は docker-compose.yml のサービス名「mysql」）
function getDb(): PDO
{
    $dsn = 'mysql:host=mysql;dbname=forge_game;charset=utf8mb4';
    return new PDO($dsn, 'root', 'p@ssword', [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
}

// HTMLに表示する文字をエスケープする（XSS対策）
function h($str): string
{
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}

// 強化に使えるインスタンスを取得する（自分のもの・チーム編成に入っていないもの）
// $excludeId を指定すると、そのインスタンスを除く
function getUsableInstances(PDO $db, int $itemId, int $excludeId = 0): array
{
    $sql = <<<SQL
    SELECT ui.id, ui.item_id, i.name, ui.created_at
    FROM user_items AS ui
    INNER JOIN items AS i ON ui.item_id = i.id
    WHERE ui.user_id = :user_id
      AND ui.item_id = :item_id
      AND ui.id <> :exclude_id
      AND ui.id NOT IN (SELECT user_item_id FROM user_decks WHERE user_id = :user_id2)
    ORDER BY ui.id
    SQL;
    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':user_id'    => USER_ID,
        ':item_id'    => $itemId,
        ':exclude_id' => $excludeId,
        ':user_id2'   => USER_ID,
    ]);
    return $stmt->fetchAll();
}

// ページの上の部分
function pageHeader(string $title): void
{
    $title = h($title);
    echo <<<HTML
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>{$title}</title>
<style>
  body  { font-family: sans-serif; margin: 20px; }
  table { border-collapse: collapse; width: 100%; margin: 16px 0; }
  th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
  th    { background: #f2f2f2; }
  .msg  { color: #c00; }
</style>
</head>
<body>
HTML;
}

// ページの下の部分
function pageFooter(): void
{
    echo "</body>\n</html>\n";
}
