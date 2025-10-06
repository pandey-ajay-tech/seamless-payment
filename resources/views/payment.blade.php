<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Seamless Payment Demo</title>
    <link href="{{asset('assets/css/bootstrap.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('assets/css/payments.css')}}">
</head>

<body>
    <div class="loader-wrapper" id="loaderWrapper">
        <div class="loader"></div>
    </div>
    <div class="container py-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <h3>Deposit</h3>
                <div class="mb-3">
                    <label for="amountInput" class="form-label">Enter Amount</label>
                    <input type="number" id="amountInput" class="form-control" value="{{ $amount ?? 100 }}" min="1">
                </div>
                <p>Amount: <strong id="amountDisplay">₹{{ $amount ?? 100 }}</strong></p>
                <button id="payBtn" class="btn btn-primary">Pay / Create Transaction</button>
                <div id="error" class="alert alert-danger mt-3" style="display:none;"></div>

                <div id="deposit" class="mt-4" style="display:none;">
                    <h5>Scan QR / UPI Link</h5>
                    <img id="qrImg" class="qr-img mb-3" src="" alt="QR code">
                    <div class="input-group mb-2">
                        <input id="upiLink" type="text" class="form-control" readonly>
                        <button id="copyBtn" class="btn btn-outline-secondary">Copy</button>
                        <a id="openUpi" class="btn btn-success" href="#" target="_blank">Open</a>
                    </div>
                    <p>Amount: <strong id="displayAmount"></strong></p>
                    <div class="mb-3">
                        Status: <span id="statusBadge" class="badge status-badge pending">Pending</span>
                        <button id="checkStatus" class="btn btn-link">Check status</button>
                    </div>
                </div>

                <div class="success-message" id="successMsg">
                    Payment Successful! Redirecting...
                </div>
            </div>
        </div>
    </div>
    <script src="{{asset('assets/jQuery.min.js')}}"></script>
   <script src="{{asset('assets/js/payment.js')}}"></script>
</body>
</html>