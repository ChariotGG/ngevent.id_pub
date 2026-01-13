# test-webhook.ps1
$webhookToken = "test_webhook_token_123" # Ganti dengan token dari .env

$headers = @{
    "Content-Type" = "application/json"
    "x-callback-token" = $webhookToken
}

$body = @{
    id = "invoice_123"
    status = "PAID"
    paid_amount = 150000
    external_id = "order-NGE20260113ABC123"
    payment_method = "BANK_TRANSFER"
    payment_channel = "BCA"
} | ConvertTo-Json

try {
    $response = Invoke-WebRequest `
        -Uri "http://localhost:8000/webhook/xendit/invoice" `
        -Method POST `
        -Headers $headers `
        -Body $body `
        -UseBasicParsing

    Write-Host "Response Status: $($response.StatusCode)" -ForegroundColor Green
    Write-Host "Response Body: $($response.Content)" -ForegroundColor Cyan
} catch {
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host "Response: $($_.Exception.Response)" -ForegroundColor Yellow
}
