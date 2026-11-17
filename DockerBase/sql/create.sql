-- 何度実行しても作り直せるように、先に削除しておく
DROP TABLE IF EXISTS battle_log;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS jobs;

-- ジョブ
CREATE TABLE jobs (
  id   INT PRIMARY KEY,
  name VARCHAR(50) NOT NULL
);

-- ユーザー
CREATE TABLE users (
  id     INT PRIMARY KEY,
  name   VARCHAR(50) NOT NULL,
  level  INT NOT NULL,
  job_id INT NOT NULL
);

-- バトルログ（id は自動で番号が振られる）
CREATE TABLE battle_log (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  user_id     INT NOT NULL,
  result_win  BOOLEAN NOT NULL,
  create_date DATE NOT NULL
);

INSERT INTO jobs(id, name) VALUES
(1, 'knight'),
(2, 'wizard'),
(3, 'thief');

INSERT INTO users(id, name, level, job_id) VALUES
(1, 'abc', 45, 3),
(2, 'def', 21, 1),
(3, 'ghi', 37, 2),
(4, 'jkl', 26, 2),
(5, 'mno', 31, 3),
(6, 'pqr', 19, 1),
(7, 'stu', 42, 1),
(8, 'vwx', 29, 2),
(9, 'yz', 40, 3);

-- ↓ここにスプレッドシートの E22〜E94 を貼り付ける
-- INSERT INTO battle_log (user_id, result_win, create_date) VALUES
-- (8, TRUE, '2025-01-05'),
-- ...
-- (9, TRUE, '2025-01-08');

INSERT INTO battle_log (user_id, result_win, create_date) VALUES
(8, TRUE, '2025-01-05'),
(9, TRUE, '2025-01-05'),
(6, TRUE, '2025-01-05'),
(5, TRUE, '2025-01-05'),
(8, TRUE, '2025-01-05'),
(3, FALSE, '2025-01-05'),
(2, FALSE, '2025-01-05'),
(9, FALSE, '2025-01-05'),
(1, FALSE, '2025-01-05'),
(8, TRUE, '2025-01-05'),
(9, TRUE, '2025-01-05'),
(6, FALSE, '2025-01-05'),
(1, FALSE, '2025-01-06'),
(4, TRUE, '2025-01-06'),
(2, TRUE, '2025-01-06'),
(3, TRUE, '2025-01-06'),
(5, TRUE, '2025-01-06'),
(7, FALSE, '2025-01-06'),
(7, TRUE, '2025-01-06'),
(5, FALSE, '2025-01-06'),
(2, TRUE, '2025-01-06'),
(1, TRUE, '2025-01-06'),
(4, FALSE, '2025-01-06'),
(6, TRUE, '2025-01-06'),
(3, TRUE, '2025-01-06'),
(8, FALSE, '2025-01-06'),
(7, FALSE, '2025-01-06'),
(6, FALSE, '2025-01-06'),
(6, TRUE, '2025-01-06'),
(1, FALSE, '2025-01-06'),
(3, TRUE, '2025-01-06'),
(6, FALSE, '2025-01-06'),
(7, TRUE, '2025-01-06'),
(7, TRUE, '2025-01-06'),
(4, TRUE, '2025-01-06'),
(2, TRUE, '2025-01-06'),
(8, TRUE, '2025-01-06'),
(4, TRUE, '2025-01-06'),
(8, FALSE, '2025-01-07'),
(1, TRUE, '2025-01-07'),
(3, TRUE, '2025-01-07'),
(3, FALSE, '2025-01-07'),
(9, FALSE, '2025-01-07'),
(9, TRUE, '2025-01-07'),
(5, TRUE, '2025-01-07'),
(1, FALSE, '2025-01-07'),
(7, TRUE, '2025-01-07'),
(3, TRUE, '2025-01-07'),
(8, TRUE, '2025-01-07'),
(4, TRUE, '2025-01-07'),
(2, FALSE, '2025-01-07'),
(2, TRUE, '2025-01-07'),
(5, FALSE, '2025-01-07'),
(9, FALSE, '2025-01-07'),
(5, TRUE, '2025-01-08'),
(4, TRUE, '2025-01-08'),
(6, TRUE, '2025-01-08'),
(1, TRUE, '2025-01-08'),
(2, TRUE, '2025-01-08'),
(7, TRUE, '2025-01-08'),
(5, TRUE, '2025-01-08'),
(6, FALSE, '2025-01-08'),
(5, TRUE, '2025-01-08'),
(7, TRUE, '2025-01-08'),
(4, TRUE, '2025-01-08'),
(2, TRUE, '2025-01-08'),
(8, TRUE, '2025-01-08'),
(3, TRUE, '2025-01-08'),
(9, FALSE, '2025-01-08'),
(4, TRUE, '2025-01-08'),
(1, FALSE, '2025-01-08'),
(9, TRUE, '2025-01-08');