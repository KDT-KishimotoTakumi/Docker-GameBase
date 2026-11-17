# Ubuntuの最新イメージを使用する
FROM ubuntu:latest

# パッケージ情報を更新する
RUN apt-get update

# vimをインストールする
RUN apt-get install -y vim

# コンテナ起動時にbashを使用する
CMD ["/bin/bash"]