FROM php:8.2-cli
WORKDIR /var/www/html
COPY . /var/www/html
CMD ["sh", "-c", "php -S 0.0.0.0:$PORT Nexora.php"]

