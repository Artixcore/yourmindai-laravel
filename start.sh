#!/bin/bash
set -e

echo "🚀 Starting YourMindAI Laravel Backend..."

# Check required environment variables
REQUIRED_VARS=("MONGODB_URI" "JWT_SECRET" "OPENAI_API_KEY" "CORS_ORIGIN")
MISSING_VARS=()

for var in "${REQUIRED_VARS[@]}"; do
    if [ -z "${!var}" ]; then
        MISSING_VARS+=("$var")
    fi
done

if [ ${#MISSING_VARS[@]} -ne 0 ]; then
    echo "❌ Error: Missing required environment variables:"
    printf '   - %s\n' "${MISSING_VARS[@]}"
    exit 1
fi

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ]; then
    echo "⚠️  APP_KEY not set, generating..."
    php artisan key:generate --force
    echo "✅ APP_KEY generated"
fi

# Clear and cache config for production
echo "📦 Optimizing for production..."
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Get port from environment (DigitalOcean sets PORT automatically)
PORT=${PORT:-8000}

echo "✅ Starting server on port $PORT"
echo "🌐 Health check available at: http://localhost:$PORT/api/health"

# Start Laravel server
exec php artisan serve --host=0.0.0.0 --port=$PORT
