FROM php:8.3-apache

# 必要なPHP拡張モジュールをインストール
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Apacheのモジュールを有効化（必要に応じて）
# RUN a2enmod rewrite

# 作業ディレクトリを設定
WORKDIR /var/www/html/

# ポート80を公開
EXPOSE 80