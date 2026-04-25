FROM dunglas/frankenphp:php8.4-bookworm

# ── Composer ─────────────────────────────────────────────────────────────────
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ── Node.js 20 LTS ───────────────────────────────────────────────────────────
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && node --version && npm --version

# ── Extensions PHP ────────────────────────────────────────────────────────────
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

# ── OPcache production ────────────────────────────────────────────────────────
RUN { \
    echo "opcache.enable=1"; \
    echo "opcache.memory_consumption=256"; \
    echo "opcache.max_accelerated_files=20000"; \
    echo "opcache.validate_timestamps=0"; \
    echo "opcache.jit_buffer_size=100M"; \
    echo "opcache.jit=1255"; \
} >> /usr/local/etc/php/conf.d/opcache.ini

WORKDIR /app

# ── Layer cache Composer ──────────────────────────────────────────────────────
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# ── Layer cache Node ──────────────────────────────────────────────────────────
COPY package.json package-lock.json ./
RUN npm ci

# ── Copie du projet ───────────────────────────────────────────────────────────
COPY . .

# ── Build assets frontend ─────────────────────────────────────────────────────
RUN npm run build

# ── Permissions storage ───────────────────────────────────────────────────────
RUN mkdir -p storage/framework/sessions \
             storage/framework/views \
             storage/framework/cache \
             storage/framework/testing \
             storage/logs \
             bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 8080

# ── Démarrage via start.sh (migrations, caches, serve) ───────────────────────
# NOTE : optimize:clear SUPPRIMÉ — il détruisait les caches buildés ci-dessus
# NOTE : admin:create DÉPLACÉ dans start.sh (une seule fois au boot, pas au build)
CMD ["bash", "start.sh"]
