-- ==================================================
-- カード強化サイト用 データベース
-- 実行方法（MySQLの中で）：source /root/sql/card_game.sql;
-- ==================================================
SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS card_game DEFAULT CHARACTER SET utf8mb4;
USE card_game;

-- 何度実行しても作り直せるように、先に削除しておく
DROP TABLE IF EXISTS user_decks;
DROP TABLE IF EXISTS user_cards;
DROP TABLE IF EXISTS cards;

-- --------------------------------------------------
-- cards：カードのマスタデータ（最初に準備しておくデータ）
-- next_card_id：強化したときに変わる先のカードID（NULL = これ以上強化できない）
-- --------------------------------------------------
CREATE TABLE cards (
  id           INT PRIMARY KEY,
  name         VARCHAR(50) NOT NULL,
  rarity       INT NOT NULL,
  next_card_id INT NULL
);

-- --------------------------------------------------
-- user_cards：ユーザーが持っているカード（トランザクションデータ）
-- 同じカードを2枚持っていれば、card_id が同じ行が2行できる
-- --------------------------------------------------
CREATE TABLE user_cards (
  id      INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  card_id INT NOT NULL
);

-- --------------------------------------------------
-- user_decks：ユーザーが組んだデッキ（チーム編成）
-- ここに入っているカードは素材として使えない
-- --------------------------------------------------
CREATE TABLE user_decks (
  user_id      INT NOT NULL,
  deck_id      INT NOT NULL,
  user_card_id INT NOT NULL
);

-- --------------------------------------------------
-- サンプルデータ
-- --------------------------------------------------
INSERT INTO cards (id, name, rarity, next_card_id) VALUES
(1, 'スライム',             1, 2),
(2, 'キングスライム',       2, 3),
(3, 'ゴッドスライム',       3, NULL),
(4, 'ゴブリン',             1, 5),
(5, 'ゴブリンロード',       2, NULL),
(6, 'ドラゴン',             2, 7),
(7, 'エンシェントドラゴン', 3, NULL),
(8, 'ナイト',               1, 9),
(9, 'パラディン',           2, NULL);

INSERT INTO user_cards (user_id, card_id) VALUES
(1, 1),  -- id 1  スライム
(1, 1),  -- id 2  スライム
(1, 1),  -- id 3  スライム（デッキに入っている）
(1, 4),  -- id 4  ゴブリン
(1, 4),  -- id 5  ゴブリン
(1, 6),  -- id 6  ドラゴン（デッキに入っている）
(1, 6),  -- id 7  ドラゴン（デッキに入っている）
(1, 8),  -- id 8  ナイト（1枚だけなので画面①に出ない）
(1, 3),  -- id 9  ゴッドスライム（1枚だけなので画面①に出ない）
(2, 1),  -- id 10 ユーザー2のスライム
(2, 1);  -- id 11 ユーザー2のスライム

INSERT INTO user_decks (user_id, deck_id, user_card_id) VALUES
(1, 1, 3),
(1, 1, 6),
(1, 1, 7);