# test-rate-limit.ps1
Write-Host "Testing Checkout Rate Limit (5 per minute)..." -ForegroundColor Yellow

for ($i = 1; $i -le 7; $i++) {
    Write-Host "`nRequest #$i" -ForegroundColor Cyan

    try {
        $response = Invoke-WebRequest `
            -Uri "http://localhost:8000/checkout/test-event-slug" `
            -Method GET `
            -UseBasicParsing `
            -ErrorAction Stop

        Write-Host "  Status: $($response.StatusCode) - OK" -ForegroundColor Green
    } catch {
        $statusCode = $_.Exception.Response.StatusCode.value__

        if ($statusCode -eq 429) {
            Write-Host "  Status: 429 - Too Many Requests (Rate limit hit!)" -ForegroundColor Red
        } else {
            Write-Host "  Status: $statusCode" -ForegroundColor Yellow
        }
    }

    Start-Sleep -Milliseconds 500
}

Write-Host "`nRate limit test completed." -ForegroundColor Green
