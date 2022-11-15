FROM php:7.1-apache-jessie

RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libpng-dev \
    && docker-php-ext-install -j$(nproc) iconv mcrypt \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd

RUN docker-php-ext-install pdo_mysql mbstring
RUN echo 'error_reporting = E_ALL' >> /usr/local/etc/php/conf.d/99_myconf.ini
RUN echo 'date.timezone = Asia/Tokyo' >> /usr/local/etc/php/conf.d/99_myconf.ini
RUN pecl install redis
RUN pecl install xdebug
RUN docker-php-ext-enable redis
ADD ./docker-php-ext-xdebug.ini /usr/local/etc/php/conf.d/
ADD ./composer.phar /usr/local/bin/composer

RUN apt-get install -y vim
RUN apt-get install zip unzip
RUN apt-get install -y git
RUN apt-get install -y wget
# treeコマンドで階層表示
RUN apt-get install tree
# PHPUnit実行時のnot found: notify-sendが出ないように
RUN apt-get install -y libnotify-bin

RUN groupadd -g 2000 yyyyy
RUN useradd -g yyyyy -G sudo -m -s /bin/bash aki
RUN echo 'aki:password' | chpasswd

#RUN apt-get install -y rsyslog
#RUN /etc/init.d/rsyslog restart

RUN a2enmod
