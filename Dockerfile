# Stage 1: Build Frontend
FROM node:20-alpine AS frontend-builder

WORKDIR /app

# Copy frontend package files
COPY frontend/package.json frontend/package-lock.json ./

# Install dependencies
RUN npm ci --prefer-offline --no-audit

# Copy frontend source code
COPY frontend/ ./

# Build frontend for production
RUN npm run build

# Stage 2: Backend PHP with Apache
FROM php:8.3-apache

# Install system dependencies and PHP extensions required for Laravel
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install \
        pdo \
        pdo_pgsql \
        mbstring \
        zip \
        bcmath \
        gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable mod_rewrite for Apache
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files first for better caching
COPY composer.json composer.lock ./

# Install PHP dependencies (as root, no permission issues)
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --no-interaction

# Copy backend source code (without chown, we're root)
COPY . .

# Configure git to avoid ownership issues
RUN git config --global --add safe.directory /var/www/html

# Create necessary directories BEFORE composer dump-autoload
RUN mkdir -p storage/framework/{sessions,views,cache} \
    && mkdir -p storage/logs \
    && mkdir -p bootstrap/cache

# Generate optimized autoloader (Laravel needs bootstrap/cache to exist)
RUN composer dump-autoload --optimize --no-dev

# Set permissions for Laravel directories
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Copy frontend build to public (after Laravel files are in place)
# This copies dist/* into public/, merging with existing files
RUN mkdir -p /tmp/frontend-build
COPY --from=frontend-builder /app/dist /tmp/frontend-build
RUN cp -r /tmp/frontend-build/* /var/www/html/public/ && rm -rf /tmp/frontend-build

# Configure Apache for Laravel API + Vue SPA
RUN echo '<VirtualHost *:80>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        Options Indexes FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    # API routes go to Laravel\n\
    <LocationMatch "^/(api|sanctum|storage)/">\n\
        FallbackResource /index.php\n\
    </LocationMatch>\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Create .htaccess to handle SPA + API routing
RUN echo 'RewriteEngine On\n\
# Handle API routes\n\
RewriteCond %{REQUEST_URI} ^/(api|sanctum|storage)/ [NC]\n\
RewriteRule ^ index.php [L]\n\
# Handle Frontend routes (SPA)\n\
RewriteCond %{REQUEST_FILENAME} !-f\n\
RewriteCond %{REQUEST_FILENAME} !-d\n\
RewriteRule ^ index.html [L]' > /var/www/html/public/.htaccess

# Copy and setup entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Set final permissions for Apache (base permissions)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 80
EXPOSE 80

# Use entrypoint script instead of direct CMD
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
