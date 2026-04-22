FROM dunglas/frankenphp:php8.4-bookworm

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs

# Extensions PHP — pdo_pgsql ajouté pour PostgreSQL
RUN install-php-extensions \
    intl \
    zip \
    gd \
    pdo_mysql \
    pdo_pgsql \
    opcache \
    mbstring \
    xml \
    curl \
    bcmath

WORKDIR /app

# Dépendances Composer
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Dépendances Node
COPY package.json package-lock.json ./
RUN npm ci

# Tout le projet
COPY . .

# Build assets + structure dossiers
# IMPORTANT : migrate retiré ici — PostgreSQL non disponible au build
RUN npm run build \
    && mkdir -p storage/framework/sessions \
    && mkdir -p storage/framework/views \
    && mkdir -p storage/framework/cache \
    && mkdir -p storage/framework/testing \
    && mkdir -p storage/logs \
    && mkdir -p bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache

EXPOSE 8080

# Migrate au runtime — PostgreSQL disponible ici
CMD echo "=== DEBUT ===" \
    && php artisan migrate --force || true \
    && php artisan filament:upgrade || true \
    && php artisan optimize:clear || true \
    && php artisan storage:link --force || true \
    && php artisan vendor:publish --tag=livewire:assets --force || true \
    && php artisan admin:create || true \
    && echo "=== DEMARRAGE ===" \
    && php artisan serve --host=0.0.0.0 --port=8080