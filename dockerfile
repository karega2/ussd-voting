FROM php:8.2-apache

# Enable mysqli
RUN docker-php-ext-install mysqli

# Copy project files
COPY . /var/www/html/

# Expose port 80
EXPOSE 80
