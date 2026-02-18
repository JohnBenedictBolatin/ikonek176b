#!/bin/bash

# Railway Setup Script for Laravel Application
# This script helps with initial Railway deployment setup

echo "🚂 Railway Laravel Deployment Setup"
echo "===================================="
echo ""

# Check if Railway CLI is installed
if ! command -v railway &> /dev/null; then
    echo "📦 Installing Railway CLI..."
    npm install -g @railway/cli
    echo "✅ Railway CLI installed"
else
    echo "✅ Railway CLI already installed"
fi

echo ""
echo "🔐 Login to Railway..."
railway login

echo ""
echo "🔗 Linking project to Railway..."
railway link

echo ""
echo "🔑 Generating application key..."
APP_KEY=$(railway run php artisan key:generate --show)
echo "Generated APP_KEY: $APP_KEY"
echo ""
echo "⚠️  IMPORTANT: Copy the APP_KEY above and add it to Railway Dashboard → Variables → APP_KEY"
echo ""

echo ""
echo "📊 Running database migrations..."
railway run php artisan migrate --force

echo ""
echo "🔗 Creating storage link..."
railway run php artisan storage:link

echo ""
echo "✨ Setup complete!"
echo ""
echo "Next steps:"
echo "1. Add APP_KEY to Railway environment variables"
echo "2. Configure other environment variables (see RAILWAY_DEPLOYMENT.md)"
echo "3. Push your code to trigger deployment: git push"
echo "4. Visit your Railway URL to test"
