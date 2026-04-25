#!/bin/bash
# ═══════════════════════════════════════════════════════════════════════════
# start.sh — Script de démarrage Railway — DiscovTrip (version corrigée)
# ═══════════════════════════════════════════════════════════════════════════
set -e

PORT="${PORT:-8080}"
APP_ENV="${APP_ENV:-production}"

echo ""
echo "╔══════════════════════════════════════════╗"
echo "║     🌍 DiscovTrip — Démarrage Railway    ║"
echo "╚══════════════════════════════════════════╝"
echo "  PORT     : $PORT"
echo "  ENV      : $APP_ENV"
echo "  APP_URL  : ${APP_URL:-⚠️ NON DÉFINI}"
echo "  DB       : ${DB_CONNECTION:-sqlite}"
echo ""

# ── 1. Base de données SQLite (si driver = sqlite) ────────────────────────
if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    DB_PATH="${DB_DATABASE:-/app/database/database.sqlite}"
    if [ ! -f "$DB_PATH" ]; then
        echo "📦 Création SQLite : $DB_PATH"
        mkdir -p "$(dirname "$DB_PATH")"
        touch "$DB_PATH"
    fi
fi

# ── 2. Vérification des assets compilés ──────────────────────────────────
if [ ! -f "public/build/manifest.json" ]; then
    echo "⚠️  public/build/manifest.json absent — rebuild en cours..."
    npm ci --silent && npm run build
    echo "✅ Assets reconstruits."
else
    echo "✅ Assets OK (manifest.json présent)"
fi

# ── 3. Storage link ───────────────────────────────────────────────────────
echo "🔗 Storage link..."
php artisan storage:link --force 2>/dev/null || true

# ── 4. Migrations ─────────────────────────────────────────────────────────
echo "🗄️  Migrations en cours..."
if php artisan migrate --force --no-interaction; then
    echo "✅ Migrations OK"
else
    echo "❌ ERREUR MIGRATION — Affichage du pretend pour debug :"
    php artisan migrate --pretend --force 2>&1 | head -50 || true
    exit 1
fi

# ── 5. Création du compte admin (idempotent) ──────────────────────────────
if [ -n "$ADMIN_EMAIL" ] && [ -n "$ADMIN_PASSWORD" ]; then
    echo "👤 Vérification compte admin..."
    php artisan admin:create 2>/dev/null && echo "✅ Admin OK" || echo "ℹ️  Admin déjà existant ou ignoré"
fi

# ── 6. Caches production ──────────────────────────────────────────────────
# IMPORTANT : NE PAS faire optimize:clear ici — les caches du build Docker sont déjà bons
echo "⚡ Régénération caches..."
php artisan config:cache  2>/dev/null && echo "  ✅ config" || echo "  ⚠️  config:cache échoué (non bloquant)"
php artisan route:cache   2>/dev/null && echo "  ✅ routes" || echo "  ⚠️  route:cache échoué (non bloquant)"
php artisan view:cache    2>/dev/null && echo "  ✅ vues"   || echo "  ⚠️  view:cache échoué (non bloquant)"

# ── 7. Permissions finales ────────────────────────────────────────────────
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# ── 8. Démarrage serveur ──────────────────────────────────────────────────
echo ""
echo "╔══════════════════════════════════════════╗"
echo "║  🚀 Serveur démarré sur le port $PORT    ║"
echo "╚══════════════════════════════════════════╝"
echo ""

exec php artisan serve --host=0.0.0.0 --port="$PORT"
