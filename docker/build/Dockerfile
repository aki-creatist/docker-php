FROM php:8.3-apache

# 必要なパッケージのインストール
RUN apt-get update && \
    apt-get install -y zip unzip git libzip-dev && \
    docker-php-ext-install zip mysqli pdo pdo_mysql && \
    rm -rf /var/lib/apt/lists/*

# Composerのインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /product/docker-php
EXPOSE 80
