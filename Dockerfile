FROM php:8.4-apache

# =========================
# System dependencies
# =========================
RUN apt-get update && apt-get install -y \
    git curl unzip zip libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# =========================
# Install Node.js (for Vite)
# =========================
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# =========================
# Enable Apache rewrite
# =========================
RUN a2enmod rewrite

# =========================
# Working directory
# =========================
WORKDIR /var/www/html

# =========================
# Copy project
# =========================
COPY . .

# =========================
# Install Composer
# =========================
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# =========================
# Install PHP dependencies
# =========================
RUN composer install --no-dev --optimize-autoloader

# =========================
# Install frontend dependencies + build Vite
# =========================
RUN npm install
RUN npm run build

# =========================
# Laravel optimizations
# =========================
RUN php artisan config:clear \
    && php artisan route:clear \
    && php artisan view:clear \
    && php artisan cache:clear

# =========================
# Permissions fix
# =========================
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# =========================
# Apache document root fix
# =========================
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

# =========================
# Render PORT fix (IMPORTANT)
# =========================
RUN sed -i 's/80/${PORT}/g' /etc/apache2/ports.conf
RUN sed -i 's/:80/:${PORT}/g' /etc/apache2/sites-available/000-default.conf

# =========================
# Expose port for Render
# =========================
EXPOSE 10000

# =========================
# Start Apache
# =========================
CMD ["apache2-foreground"]