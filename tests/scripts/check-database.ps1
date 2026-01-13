# check-database.ps1
Write-Host "Checking database tables..." -ForegroundColor Yellow

$tables = @(
    "users",
    "organizers",
    "events",
    "tickets",
    "ticket_variants",
    "orders",
    "order_items",
    "payments",
    "issued_tickets"
)

foreach ($table in $tables) {
    $count = php artisan tinker --execute="echo DB::table('$table')->count();"
    Write-Host "  $table : $count rows" -ForegroundColor Cyan
}
