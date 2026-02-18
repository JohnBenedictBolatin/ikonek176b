# Railway Setup Script for Laravel Application (PowerShell)
# This script helps with initial Railway deployment setup

Write-Host "🚂 Railway Laravel Deployment Setup" -ForegroundColor Cyan
Write-Host "====================================" -ForegroundColor Cyan
Write-Host ""

# Check if Railway CLI is installed
$railwayInstalled = Get-Command railway -ErrorAction SilentlyContinue

if (-not $railwayInstalled) {
    Write-Host "📦 Installing Railway CLI..." -ForegroundColor Yellow
    npm install -g @railway/cli
    Write-Host "✅ Railway CLI installed" -ForegroundColor Green
} else {
    Write-Host "✅ Railway CLI already installed" -ForegroundColor Green
}

Write-Host ""
Write-Host "🔐 Login to Railway..." -ForegroundColor Yellow
railway login

Write-Host ""
Write-Host "🔗 Linking project to Railway..." -ForegroundColor Yellow
railway link

Write-Host ""
Write-Host "🔑 Generating application key..." -ForegroundColor Yellow
$appKey = railway run php artisan key:generate --show
Write-Host "Generated APP_KEY: $appKey" -ForegroundColor Cyan
Write-Host ""
Write-Host "⚠️  IMPORTANT: Copy the APP_KEY above and add it to Railway Dashboard → Variables → APP_KEY" -ForegroundColor Red
Write-Host ""

Write-Host ""
Write-Host "📊 Running database migrations..." -ForegroundColor Yellow
railway run php artisan migrate --force

Write-Host ""
Write-Host "🔗 Creating storage link..." -ForegroundColor Yellow
railway run php artisan storage:link

Write-Host ""
Write-Host "✨ Setup complete!" -ForegroundColor Green
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Cyan
Write-Host "1. Add APP_KEY to Railway environment variables"
Write-Host "2. Configure other environment variables (see RAILWAY_DEPLOYMENT.md)"
Write-Host "3. Push your code to trigger deployment: git push"
Write-Host "4. Visit your Railway URL to test"
