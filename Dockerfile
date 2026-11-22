FROM php:8.2-apache

# Copy the PHP application into Apache's web directory
COPY . /var/www/html/

# Enable Apache rewrite module
RUN a2enmod rewrite

# Set the working directory
WORKDIR /var/www/html

# Expose Apache port
EXPOSE 80