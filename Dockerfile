FROM php:8.2-apache-bullseye

RUN docker-php-ext-install mysqli \
    && rm -rf /var/cache/apk/* /usr/share/doc /usr/share/man /usr/share/locale

# Enable Apache modules required for .htaccess
RUN a2enmod headers rewrite

# V13 Fix: Disable X-Powered-By globally in PHP
RUN echo "expose_php=Off" > /usr/local/etc/php/conf.d/security.ini

# V13 Fix: Hide Apache version globally
RUN sed -i 's/ServerTokens OS/ServerTokens Prod/g' /etc/apache2/conf-available/security.conf
RUN sed -i 's/ServerSignature On/ServerSignature Off/g' /etc/apache2/conf-available/security.conf
RUN a2enconf security
