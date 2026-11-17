# ============================================================
# Dockerfile
# PHP 8.2 + Apache
# ============================================================

FROM php:8.2-apache


# ============================================================
# PHP設定ファイル
# ============================================================

COPY ./php.ini /usr/local/etc/php/


# ============================================================
# 必要なLinuxパッケージをインストール
# ============================================================

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        # GD用
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        \
        # mbstring用
        libonig-dev \
        \
        # memcached用
        libmemcached-dev \
        zlib1g-dev \
        \
        # PECLビルド用
        autoconf \
        build-essential \
        pkg-config \
        \
        # その他
        vim \
        default-mysql-client \
        openssh-server \
    && rm -rf /var/lib/apt/lists/*


# ============================================================
# GDの設定
# ============================================================

RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg


# ============================================================
# PHP拡張機能をインストール
# ============================================================

RUN docker-php-ext-install \
    pdo_mysql \
    mysqli \
    mbstring \
    gd


# ============================================================
# Memcached
# ============================================================

RUN pecl install memcached \
    && docker-php-ext-enable memcached


# ============================================================
# Redis
# ============================================================

RUN pecl install redis \
    && docker-php-ext-enable redis


# ============================================================
# Apache設定
# ============================================================

COPY ./php_apache2.conf /etc/apache2/mods-enabled/


# ============================================================
# WebアプリをApache公開ディレクトリへコピー
# ============================================================

COPY . /var/www/html/