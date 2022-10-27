FROM ubuntu:18.04

# Setup required environment variables

ENV APACHE_DOCUMENT_ROOT /var/www/html
ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOG_DIR /var/log/apache2
ENV APACHE_LOCK_DIR /var/lock/apache2
ENV APACHE_PID_FILE /var/run/apache2.pid

# Install required tools

RUN apt-get update && apt-get -y upgrade && DEBIAN_FRONTEND=noninteractive
RUN apt-get update && apt-get install -y --fix-missing curl
RUN apt-get update && apt-get install -y vim
RUN apt-get update && apt-get install -y git

# Install Apache First

RUN apt-get update && apt-get -y install apache2


# Install PHP
RUN apt-get update && apt-get -y upgrade
RUN apt-get install -y software-properties-common
# RUN locale-gen en_US.UTF-8
# RUN export LANG=en_US.UTF-8
RUN LC_ALL=C.UTF-8 add-apt-repository ppa:ondrej/php
RUN apt-get update
RUN apt-get -y -f install mysql-client

RUN ln -snf /usr/share/zoneinfo/$CONTAINER_TIMEZONE /etc/localtime && echo $CONTAINER_TIMEZONE > /etc/timezone

# Install dependencies:
RUN apt-get update && apt-get install -y tzdata

# Set timezone:
RUN ln -snf /usr/share/zoneinfo/$CONTAINER_TIMEZONE /etc/localtime && echo $CONTAINER_TIMEZONE > /etc/timezone
RUN apt-get update && apt-get install -y --fix-missing php7.4 libapache2-mod-php7.4 php7.4 php7.4-common php7.4-gd php7.4-mysql

RUN apt-get update && apt-get install -y --fix-missing  php7.4-mcrypt php7.4-curl php7.4-intl php7.4-xsl php7.4-mbstring php7.4-zip php7.4-bcmath \
    php7.4-iconv php7.4-soap php7.4-redis redis

# Configure PHP
#COPY php.ini /etc/php/7.4/apache2/php.ini

# Configure apache with ENV variables
ADD apache-config.conf /etc/apache2/sites-enabled/000-default.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN a2enmod php7.4
RUN a2enmod rewrite

COPY src/ /var/www/html
CMD /usr/sbin/apache2ctl -D FOREGROUND




