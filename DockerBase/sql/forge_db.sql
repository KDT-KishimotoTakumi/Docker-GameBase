-- ==================================================
-- アイテム強化サイト用 データベース
-- 実行方法（MySQLの中で）：source /root/sql/forge.sql;
-- ==================================================
SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS forge_game DEFAULT CHARACTER SET utf8mb4;
USE forge_game;

-- 何度実行しても作り直せるように、先に削除しておく
DROP TABLE IF EXISTS user_decks;
DROP TABLE IF EXISTS user_items;
DROP TABLE IF EXISTS items;

-- --------------------------------------------------
-- items：アイテムのマスタデータ
-- next_item_id：強化すると手に入るアイテムのID（NULL = これ以上強化できない）
-- --------------------------------------------------
CREATE TABLE items (
  id           INT PRIMARY KEY,
  name         VARCHAR(50) NOT NULL,
  next_item_id INT NULL
);

-- --------------------------------------------------
-- user_items：ユーザーが持っているアイテム（1行 = 1個 = インスタンス）
-- --------------------------------------------------
CREATE TABLE user_items (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  user_id    INT NOT NULL,
  item_id    INT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- --------------------------------------------------
-- user_decks：チーム編成（ここに入っているアイテムは強化に使えない）
-- --------------------------------------------------
CREATE TABLE user_decks (
  user_id      INT NOT NULL,
  deck_id      INT NOT NULL,
  user_item_id INT NOT NULL
);

-- --------------------------------------------------
-- サンプルデータ
-- --------------------------------------------------
INSERT INTO items (id, name, next_item_id) VALUES
(1, 'レアアイテム',     2),
(2, '超レアアイテム',   3),
(3, '神レアアイテム',   NULL),
(4, 'ポーション',       5),
(5, 'ハイポーション',   NULL);

INSERT INTO user_items (user_id, item_id) VALUES
(1, 2),  -- id 1 超レアアイテム
(1, 2),  -- id 2 超レアアイテム
(1, 2),  -- id 3 超レアアイテム
(1, 1),  -- id 4 レアアイテム（1個だけなので強化できない）
(1, 4),  -- id 5 ポーション
(1, 4),  -- id 6 ポーション（チーム編成中なので使えない）
(2, 2),  -- id 7 ユーザー2の超レアアイテム
(2, 2);  -- id 8 ユーザー2の超レアアイテム

INSERT INTO user_decks (user_id, deck_id, user_item_id) VALUES
(1, 1, 6);