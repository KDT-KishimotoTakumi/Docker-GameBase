<?php
// ==================================================
// データベース接続と共通の関数
// ==================================================

// ログイン機能はまだないので、ユーザーID 1 として動かす
const USER_ID = 1;

// MySQLに接続する（ホスト名は docker-compose.yml のサービス名「mysql」）
function getDb(): PDO
{
    $dsn = 'mysql:host=mysql;dbname=card_game;charset=utf8mb4';
    return new PDO($dsn, 'root', 'p@ssword', [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // エラー時に例外を出す
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // 結果を連想配列で受け取る
    ]);
}

// HTMLに表示する文字をエスケープする（XSS対策）
function h($str): string
{
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}

// ページの上の部分
function pageHeader(string $title): void
{
    echo <<<HTML
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>{$title}</title>
<style>
  body  { font-family: sans-serif; margin: 30px; }
  table { border-collapse: collapse; margin: 10px 0; }
  th, td { border: 1px solid #999; padding: 6px 14px; }
  th    { background: #eee; }
  .msg  { color: #c00; }
  .new  { color: #06c; font-weight: bold; }
</style>
</head>
<body>
<h1>{$title}</h1>
HTML;
}

// ページの下の部分
function pageFooter(): void
{
    echo "</body>\n</html>\n";
}