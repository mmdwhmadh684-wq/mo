FROM php:8.2-cli
WORKDIR /var/www/html
COPY . /var/www/html
CMD [ "php", "Nexora.php" ]
