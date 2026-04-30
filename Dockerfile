FROM php:8.4-apache

# =========================
# System dependencies
# =========================
RUN apt-get update && apt-get install -y \
    git curl unzip zip libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# =========================
# Install Node.js (for Vite build)
# =========================
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# =========================
# Enable Apache rewrite
# =========================
RUN a2enmod rewrite

# =========================
# Set working directory
# =========================
WORKDIR /var/www/html

# =========================
# Copy project files
# =========================
COPY . .

# =========================
# Install frontend + build Vite assets
# =========================
RUN npm install && npm run build

# =========================
# Install Composer
# =========================
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --no-dev --optimize-autoloader

# =========================
# Permissions
# =========================
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# =========================
# Laravel public directory fix
# =========================
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

# =========================
# Render PORT fix
# =========================
RUN sed -i 's/80/${PORT}/g' /etc/apache2/ports.conf
RUN sed -i 's/:80/:${PORT}/g' /etc/apache2/sites-available/000-default.conf

# =========================
# Add entrypoint script
# =========================
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# =========================
# Expose port
# =========================
EXPOSE 10000

# =========================
# Start container using entrypoint
# =========================
CMD ["/entrypoint.sh"]