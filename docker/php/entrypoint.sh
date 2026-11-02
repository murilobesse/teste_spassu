#!/bin/bash
set -e

echo "🚀 Iniciando o projeto..."

mkdir -p /tmp/mpdf
chmod 777 /tmp/mpdf

# O Docker Compose já aguarda o healthcheck do banco (depends_on).
# Um pequeno sleep garante que a rede esteja 100% estabilizada.
sleep 3

echo "📦 Instalando dependências Composer..."
composer install --no-interaction --optimize-autoloader

if [ -f "package.json" ]; then
    echo "📦 Instalando dependências Node..."
    npm install
fi

echo "🗄️ Verificando banco de dados..."
php bin/console doctrine:database:create --if-not-exists --no-interaction || true

echo "🔄 Aplicando migrations..."
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

if [ "${APP_ENV:-dev}" != "prod" ]; then
    echo "📊 Carregando fixtures..."
    php bin/console doctrine:fixtures:load --no-interaction || true
fi

if [ -f "webpack.config.js" ]; then
    echo "🎨 Buildando assets..."
    npm run build || true
fi

echo "🧹 Limpando cache..."

rm -rf var/cache/*
rm -rf var/log/*
php bin/console cache:clear --no-warmup --no-interaction || true
php bin/console cache:warmup --no-interaction || true

echo "✅ Projeto pronto!"
echo "🌐 Acesse: http://localhost:8080"
echo ""

# Executa o comando padrão do container (php-fpm)
exec "$@"