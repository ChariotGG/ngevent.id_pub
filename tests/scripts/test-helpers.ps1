# test-helpers.ps1 - PowerShell helper functions

function Test-Route {
    param (
        [string]$Uri,
        [string]$Method = "GET"
    )

    try {
        $response = Invoke-WebRequest -Uri $Uri -Method $Method -UseBasicParsing
        Write-Host "✓ $Uri - Status: $($response.StatusCode)" -ForegroundColor Green
    } catch {
        Write-Host "✗ $Uri - Status: $($_.Exception.Response.StatusCode.value__)" -ForegroundColor Red
    }
}

function Clear-LaravelCache {
    Write-Host "Clearing Laravel caches..." -ForegroundColor Yellow
    php artisan config:clear
    php artisan route:clear
    php artisan cache:clear
    php artisan view:clear
    Write-Host "✓ All caches cleared" -ForegroundColor Green
}

function Show-Routes {
    param (
        [string]$Filter = ""
    )

    if ($Filter) {
        php artisan route:list | Select-String $Filter
    } else {
        php artisan route:list
    }
}

function Test-WebhookSignature {
    param (
        [string]$Token = "test_webhook_token_123"
    )

    $headers = @{
        "x-callback-token" = $Token
        "Content-Type" = "application/json"
    }

    $body = '{"id":"test","status":"PAID"}'

    try {
        $response = Invoke-WebRequest `
            -Uri "http://localhost:8000/webhook/xendit/invoice" `
            -Method POST `
            -Headers $headers `
            -Body $body `
            -UseBasicParsing

        Write-Host "✓ Webhook signature valid" -ForegroundColor Green
        return $true
    } catch {
        Write-Host "✗ Webhook signature invalid" -ForegroundColor Red
        return $false
    }
}

# Export functions
Export-ModuleMember -Function Test-Route, Clear-LaravelCache, Show-Routes, Test-WebhookSignature
