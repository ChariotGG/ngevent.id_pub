# check-files.ps1
$requiredFiles = @(
    "app\Http\Middleware\EnsureUserIsAdmin.php",
    "app\Http\Middleware\EnsureUserIsOrganizer.php",
    "app\Providers\RouteServiceProvider.php",
    "routes\web.php",
    "routes\auth.php",
    "routes\organizer.php",
    "routes\admin.php",
    "routes\webhook.php",
    "bootstrap\app.php"
)

Write-Host "Checking required files..." -ForegroundColor Yellow

foreach ($file in $requiredFiles) {
    if (Test-Path $file) {
        Write-Host "✓ $file" -ForegroundColor Green
    } else {
        Write-Host "✗ $file - MISSING!" -ForegroundColor Red
    }
}
