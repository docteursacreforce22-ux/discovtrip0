#!/bin/bash
# start.sh — Script de démarrage Railway pour DiscovTrip
# Placé à la racine du projet

set -e

echo "🚀 DiscovTrip — démarrage Railway..."

# ── 1. Créer le fichier SQLite s'il n'existe pas ──
DB_PATH="${DB_DATABASE:-/app/database/database.sqlite}"
if [ ! -f "$DB_PATH" ]; then
    echo "📦 Création de la base SQLite : $DB_PATH"
    mkdir -p "$(dirname "$DB_PATH")"
    touch "$DB_PATH"
fi

# ── 2. Migrations ──────────────────────────────────
echo "🗄️  Migrations..."
if ! php artisan migrate --force --no-interaction; then
    echo "⚠️  Migration error — tentative avec --pretend pour diagnostiquer..."
    php artisan migrate --pretend --force 2>&1 | head -40 || true
    echo "❌ Migration échouée. Vérifier les logs ci-dessus."
    exit 1
fi

# ── 3. Storage link ────────────────────────────────
echo "🔗 Storage link..."
php artisan storage:link 2>/dev/null || true

# ── 4. Caches production ───────────────────────────
echo "⚡ Caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ── 5. Permissions ────────────────────────────────
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

echo "✅ Prêt. Démarrage du serveur sur port ${PORT:-8080}..."

# ── 6. Démarrage ──────────────────────────────────
exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"