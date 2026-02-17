<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>KHQR Payment</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f3f4f6; min-height: 100vh; padding: 2rem 1rem; }
        .container { max-width: 400px; margin: 0 auto; background: white; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); padding: 2rem; }
        h1 { text-align: center; margin-bottom: 1.5rem; color: #1f2937; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 4px; }
        input, select { width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 16px; }
        input:focus, select:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
        .btn { width: 100%; padding: 12px; border: none; border-radius: 6px; font-size: 16px; font-weight: 500; cursor: pointer; margin-top: 1rem; }
        .btn-primary { background: #3b82f6; color: white; }
        .btn-primary:hover { background: #2563eb; }
        .btn-success { background: #10b981; color: white; }
        .btn-success:hover { background: #059669; }
        .qr-result { text-align: center; margin-top: 1.5rem; }
        .qr-result img, .qr-result canvas { max-width: 256px; margin: 0 auto; }
        .qr-text { font-size: 12px; color: #6b7280; margin-top: 8px; word-break: break-all; padding: 8px; background: #f9fafb; border-radius: 4px; }
        .status { padding: 12px; border-radius: 6px; margin-top: 1rem; }
        .status-info { background: #dbeafe; color: #1e40af; }
        .status-success { background: #d1fae5; color: #065f46; }
        .status-error { background: #fee2e2; color: #991b1b; }
        .status-warning { background: #fef3c7; color: #92400e; }
        .hidden { display: none; }
    </style>
</head>
<body>
    <div class="container">
        <h1>KHQR Payment</h1>
        
        <form id="generateForm">
            <div class="form-group">
                <label>Amount</label>
                <input type="number" name="amount" step="0.01" min="0.01" required placeholder="0.00">
            </div>
            <div class="form-group">
                <label>Currency</label>
                <select name="currency">
                    <option value="USD">USD</option>
                    <option value="KHR">KHR</option>
                </select>
            </div>
            <div class="form-group">
                <label>Bill Number (Optional)</label>
                <input type="text" name="bill_number" placeholder="#12345">
            </div>
            <button type="submit" class="btn btn-primary">Generate QR Code</button>
        </form>

        <div id="qrResult" class="qr-result hidden">
            <div id="qrImage"></div>
            <p style="color: #6b7280; margin-top: 8px;">Scan with Bakong app to pay</p>
            <div id="qrText" class="qr-text hidden"></div>
            <button id="checkPayment" class="btn btn-success">Check Payment Status</button>
        </div>

        <div id="statusMessage" class="status hidden"></div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        let currentMd5 = null;
        let currentPaymentId = null;
        let qrcode = null;

        document.getElementById('generateForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData);

            showStatus('Generating QR code...', 'info');

            try {
                const response = await fetch('/api/payment/generate-qr', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();
                
                if (result.success && result.qr_code) {
                    currentMd5 = result.md5;
                    currentPaymentId = result.payment_id;
                    document.getElementById('qrResult').classList.remove('hidden');
                    
                    // Clear previous QR
                    const qrContainer = document.getElementById('qrImage');
                    qrContainer.innerHTML = '';
                    
                    // Generate new QR
                    qrcode = new QRCode(qrContainer, {
                        text: result.qr_code,
                        width: 256,
                        height: 256,
                        colorDark: "#000000",
                        colorLight: "#ffffff",
                        correctLevel: QRCode.CorrectLevel.M
                    });
                    
                    // Show QR string
                    document.getElementById('qrText').textContent = result.qr_code;
                    document.getElementById('qrText').classList.remove('hidden');
                    
                    const expiresAt = new Date(result.expires_at);
                    const expiresIn = Math.floor((expiresAt - new Date()) / 1000);
                    
                    showStatus(`✅ QR Code generated! Auto-checking payment... (Expires in ${Math.floor(expiresIn/60)} minutes)`, 'success');
                    startAutoCheck(expiresIn);
                } else {
                    showStatus(result.message || 'Failed to generate QR', 'error');
                }
            } catch (error) {
                console.error(error);
                showStatus('Error: ' + error.message, 'error');
            }
        });

        let checkInterval = null;
        let timeoutTimer = null;
        let isChecking = false;
        let timeRemaining = 1800; // 30 minutes default

        async function checkPaymentStatus() {
            if (!currentMd5 || isChecking) return;
            
            isChecking = true;

            try {
                const response = await fetch('/api/payment/check', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ md5: currentMd5 })
                });

                const result = await response.json();
                
                if (result.success && result.status === 'SUCCESS') {
                    showStatus('✅ Payment Successful! ' + (result.data.telegram_sent ? 'Notification sent to Telegram.' : 'Processing notification...'), 'success');
                    stopAutoCheck();
                    hideQRCode();
                } else if (result.status === 'EXPIRED') {
                    showStatus('⏰ Payment expired! Please generate a new QR code.', 'error');
                    stopAutoCheck();
                    hideQRCode();
                } else {
                    const mins = Math.floor(timeRemaining / 60);
                    const secs = timeRemaining % 60;
                    const attempts = result.data?.check_attempts || 0;
                    showStatus(`⏳ Checking... Time remaining: ${mins}:${secs < 10 ? '0' : ''}${secs} (${attempts} checks)`, 'warning');
                }
            } catch (error) {
                showStatus('Error: ' + error.message, 'error');
            }
            
            isChecking = false;
        }

        function startAutoCheck(expirySeconds = 1800) {
            if (checkInterval) clearInterval(checkInterval);
            if (timeoutTimer) clearInterval(timeoutTimer);
            
            timeRemaining = expirySeconds;
            
            // Check every 5 seconds (reduced frequency)
            checkInterval = setInterval(checkPaymentStatus, 5000);
            
            // Countdown timer
            timeoutTimer = setInterval(() => {
                timeRemaining--;
                if (timeRemaining <= 0) {
                    stopAutoCheck();
                    hideQRCode();
                    showStatus('⏰ QR Code expired! Please generate a new one.', 'error');
                }
            }, 1000);
            
            document.getElementById('checkPayment').textContent = 'Stop Auto-Check';
        }

        function stopAutoCheck() {
            if (checkInterval) {
                clearInterval(checkInterval);
                checkInterval = null;
            }
            if (timeoutTimer) {
                clearInterval(timeoutTimer);
                timeoutTimer = null;
            }
            document.getElementById('checkPayment').textContent = 'Start Auto-Check';
        }

        function hideQRCode() {
            document.getElementById('qrResult').classList.add('hidden');
            document.getElementById('qrImage').innerHTML = '';
            document.getElementById('qrText').classList.add('hidden');
            currentMd5 = null;
            currentPaymentId = null;
        }

        document.getElementById('checkPayment').addEventListener('click', () => {
            if (!currentMd5) {
                showStatus('No QR code generated yet', 'warning');
                return;
            }

            if (checkInterval) {
                stopAutoCheck();
                showStatus('Auto-check stopped', 'info');
            } else {
                startAutoCheck();
                showStatus('Auto-checking... 30 minutes timeout', 'info');
            }
        });

        function showStatus(message, type) {
            const el = document.getElementById('statusMessage');
            el.className = 'status status-' + type;
            el.textContent = message;
            el.classList.remove('hidden');
        }

        // Check payment status every 30 seconds even when browser is idle
        setInterval(() => {
            if (currentMd5 && !isChecking) {
                checkPaymentStatus();
            }
        }, 30000);
    </script>
</body>
</html>
