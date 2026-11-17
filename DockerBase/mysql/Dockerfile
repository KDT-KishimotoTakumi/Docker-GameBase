
# 使用するMySQLのイメージを指定する
FROM mysql:8.0.36-debian

# MySQL用の古いパッケージリストを削除し、
# パッケージ情報を更新して日本語ロケールをインストールする
RUN rm -f /etc/apt/sources.list.d/mysql.list && \
    apt-get update && apt-get install -y locales

# 日本語のUTF-8ロケールを有効にして生成する
RUN sed -i 's/# ja_JP.UTF-8 UTF-8/ja_JP.UTF-8 UTF-8/' /etc/locale.gen && \
    locale-gen

# 使用する言語を日本語に設定する
ENV LANG ja_JP.UTF-8
ENV LC_ALL ja_JP.UTF-8

# タイムゾーンを日本時間に設定する
ENV TZ Asia/Tokyo