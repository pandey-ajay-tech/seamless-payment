$(document).ready(function() {
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    let currentToken = null;

    function showError(msg) {
        $('#error').show().text(msg);
    }

    function hideError() {
        $('#error').hide().text('');
    }

    function showLoader() {
        $('#loaderWrapper').fadeIn(200);
    }

    function hideLoader() {
        $('#loaderWrapper').fadeOut(200);
    }

    $('#amountInput').on('input', function() {
        let val = parseFloat($(this).val());
        if (val < 1) val = 1;
        $('#amountDisplay').text('₹' + val.toFixed(2));
    });

    $('#payBtn').on('click', function() {
        hideError();
        showLoader();
        const $btn = $(this);
        $btn.prop('disabled', true);

        const amount = parseFloat($('#amountInput').val());
        if (amount < 1) {
            showError('Amount must be greater than 0');
            hideLoader();
            $btn.prop('disabled', false);
            return;
        }

        $.ajax({
            url: '/create-transaction',
            method: 'POST',
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            data: JSON.stringify({
                amount: amount
            }),
            success: function(json) {
                if (!json.status) {
                    showError(json.error_message || 'Unable to create transaction');
                    return;
                }
                currentToken = json.data.token;

                $.ajax({
                    url: '/get-deposit-details',
                    method: 'POST',
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: JSON.stringify({
                        token: currentToken,
                        amount: amount
                    }),
                    success: function(depositJson) {
                        if (!depositJson.status) {
                            showError(depositJson.error_message || 'Unable to fetch deposit details');
                            return;
                        }

                        const data = depositJson.data;
                        $('#qrImg').attr('src', 'data:image/png;base64,' + data.qr);
                        $('#upiLink').val(data.link);
                        $('#openUpi').attr('href', data.link);
                        $('#displayAmount').text('₹' + data.amount);
                        $('#deposit').fadeIn(300);
                        $('#statusBadge').text('Pending').attr('class', 'badge status-badge pending');

                        checkStatus(); // initial status check
                    },
                    error: function() {
                        showError('Unable to fetch deposit details');
                    },
                    complete: hideLoader
                });
            },
            error: function() {
                showError('Unable to create transaction');
            },
            complete: function() {
                hideLoader();
                $btn.prop('disabled', false);
            }
        });
    });

    $('#copyBtn').on('click', function() {
        const link = $('#upiLink').val();
        navigator.clipboard.writeText(link).then(() => {
            $('#copyBtn').text('Copied!');
            setTimeout(() => $('#copyBtn').text('Copy'), 1500);
        }).catch((e) => {
            showError('Copy failed: ' + e.message);
        });
    });

    $('#checkStatus').on('click', checkStatus);

    function checkStatus() {
        if (!currentToken) return;
        hideError();
        showLoader();

        $.ajax({
            url: '/validate-transaction',
            method: 'POST',
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            data: JSON.stringify({
                token: currentToken
            }),
            success: function(json) {
                if (!json.status) {
                    showError(json.error_message || 'Unable to validate transaction');
                    return;
                }

                const status = json.transaction_status || 'Pending';
                $('#statusBadge').text(status);

                if (status.toLowerCase() === 'pending') {
                    $('#statusBadge').attr('class', 'badge status-badge pending');
                } else if (status.toLowerCase() === 'completed' || status.toLowerCase() === 'success') {
                    $('#statusBadge').attr('class', 'badge status-badge success');
                    $('#successMsg').fadeIn(400);
                    setTimeout(() => {
                        $('#successMsg').fadeOut();
                        location.reload();
                    }, 3000);
                } else {
                    $('#statusBadge').attr('class', 'badge status-badge failed');
                }
            },
            error: function() {
                showError('Unable to validate transaction');
            },
            complete: hideLoader
        });
    }

    $('#openUpi').on('click', function(e) {
        e.preventDefault();
        const link = $('#upiLink').val();
        if (link.startsWith('upi:')) {
            alert('This UPI link can only be opened on mobile devices with UPI apps.');
            return;
        }
        window.open(link, '_blank');
    });
});


document.addEventListener('DOMContentLoaded', function() {
    const loaderWrapper = document.getElementById('loaderWrapper');

    // Hide loader after full page is loaded
    window.addEventListener('load', () => {
        loaderWrapper.style.display = 'none';
    });

    // Optional: Helper functions if you want to show/hide during API calls
    window.showLoader = function() {
        loaderWrapper.style.display = 'flex';
    }

    window.hideLoader = function() {
        loaderWrapper.style.display = 'none';
    }
});