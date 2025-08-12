<?php session()->set('AllowSave', true) ?>
<style>
    .input-container {
        display: flex;
        align-items: center;
        /* Align items vertically */
    }

    .input-label {
        margin-left: 10px;
        /* Adjust the spacing between label and input */
        font-weight: 800;
    }

    .container {
        margin: 20px;
    }

    label {
        display: block;
        margin-bottom: 5px;
    }

    input[type="text"] {
        width: 100%;
        padding: 5px;
        margin-bottom: 10px;
    }

    ul {
        list-style-type: none;
        padding: 0;
        margin: 0;
    }

    li {
        padding: 8px 10px;
        cursor: pointer;
    }

    li:hover {
        background-color: #f0f0f0;
    }
</style>

<body class="g-sidenav-show  bg-gray-100">
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
            <div class="container-fluid py-1 px-3">
                <nav aria-label="breadcrumb">
                    <!-- <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a></li>
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Transaction</li>
          </ol> -->
                    <h6 class="font-weight-bolder mb-0">Input Topup Transaction</h6>
                </nav>
            </div>
        </nav>


        <div class="content-wrapper">
            <div class="container-xxl flex-grow-1 container-p-y">
                <div class="card">
                    <div class="card-header">
                        <form action="<?= base_url('../CTrans/SaveMultiPaymentTopUp') ?>" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="txtKodeReq" value="<?= session('cache')['KodeReq'] ?? "" ?>">
                            <input type="hidden" name="txtTotalTopupAmount" id="txtTotalTopupAmount" value="<?= session('cache')['Nominal'] ?? 0 ?>">
                            <div class="mb-3">
                                <label for="account-select" class="form-label">ID Member</label>
                                <select id="selAccount" name="selAccount" class="form-select select-supply" autofocus required disabled>
                                    <option disabled selected value="">Select Account ID</option>
                                    <?php foreach ($dtUser as $u) { ?>
                                        <option data-name="<?= $u->username ?>" value="<?= $u->id ?>" <?= $u->id == (session('cache')['idUser'] ?? 0) ? 'selected' : '' ?>>
                                            <?= $u->id ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="member-name" class="form-label">Nama Member</label>
                                <input type="text" name="desc" id="member-name" class="form-control" placeholder="Member Name" value="<?= session('cache')['Username'] ?? '' ?>" disabled />
                            </div>

                            <div class="mb-3">
                                <label for="" class="form-label">Tipe Transaksi</label>
                                <select id="selJenisTrans" name="selJenisTrans" class="form-select select-supply" aria-label="" required disabled>
                                    <option disabled selected value="">Select Transaction Type</option>
                                    <?php foreach ($dtJenisTrans as $t) { ?>
                                        <option value="<?= $t->id ?>" <?= $t->id == (session('cache')['JenisTrans'] ?? 0) ? 'selected' : '' ?>>
                                            <?= $t->jenis ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="" class="form-label">Deskripsi</label>
                                <input id="txtDesc" name="txtDesc" type="text" class="form-control" placeholder="Description" value="Topup Request <?= session('cache')['KodeReq'] ?? "" ?>" />
                            </div>

                            <div class="mb-3">
                                <label for="" class="form-label">Nominal Topup</label>
                                <input id="txtNominal" name="txtNominal" type="text" class="form-control" placeholder="0" value="<?= number_format(session('cache')['Nominal'] ?? 0, 0, ',', '.') ?>" required disabled />
                            </div>

                            <hr>
                            <h5>Multi-Payment Details</h5>
                            <div id="payment-container">
                                <!-- Payment rows will be added here -->
                            </div>
                            <button type="button" class="btn btn-info btn-sm mt-2" id="add-payment-row">Add Payment</button>
                            <div class="mb-3 mt-3">
                                <label for="total-paid" class="form-label">Total Paid</label>
                                <input type="text" id="total-paid" class="form-control" value="0" disabled>
                            </div>
                            <div class="mb-3 mt-3">
                                <label for="remaining-amount" class="form-label">Remaining Amount</label>
                                <input type="text" id="remaining-amount" class="form-control text-danger fw-bold" value="0" disabled>
                            </div>
                            <div class="mb-3 mt-3">
                                <button type="submit" class="btn btn-primary">Submit Payment</button>
                                <a class="text-dark" href="<?= base_url('/CTrans/Transaction') ?>">
                                    <button type="button" class="btn btn-secondary">Back</button>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>


<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />

<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.0/dist/jquery.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>

<script>
    $(document).ready(function() {
        $('#selAccount').on('change', function() {
            var xSel = $(this).find('option:selected');
            var xName = xSel.data('name');
            $('#member-name').val(xName);
        });

        $('#txtNominal').on('input', function() {
            let xvalue = $(this).val().toString();
            // Remove all non-numeric characters including existing dots (thousands separators)
            let cleanValue = xvalue.replace(/\./g, '').replace(/[^0-9]/g, '');

            // Use Number() for more robust conversion and handle empty string
            let numValue = Number(cleanValue);
            if (isNaN(numValue) || cleanValue === '') {
                numValue = 0;
            }

            // Format with dot as thousands separator (Indonesian locale)
            let formattedValue = numValue.toLocaleString('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
            $(this).val(formattedValue);
        });

        // Multi-payment logic
        let paymentCount = 0;

        function addPaymentRow(amount = 0, isCash = false) {
            paymentCount++;
            const formattedAmount = amount > 0 ? amount.toLocaleString('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }) : '';
            console.log(formattedAmount)
            const paymentRow = `
                <div class="row mb-2 payment-row" id="payment-row-${paymentCount}">
                    <div class="col-md-5">
                        <label for="payment-method-${paymentCount}" class="form-label">Payment Method</label>
                        <select name="payment_method[]" id="payment-method-${paymentCount}" class="form-select payment-method" required>
                            <option value="1" ${isCash ? 'selected' : ''}>Cash</option>
                            <option value="2">Bank Transfer</option>
                            <!-- Add more payment methods as needed -->
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label for="payment-amount-${paymentCount}" class="form-label">Amount</label>
                        <input type="text" name="payment_amount[]" id="payment-amount-${paymentCount}" class="form-control payment-amount" value="${formattedAmount}" placeholder="0" required>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-sm remove-payment-row" data-id="${paymentCount}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            $('#payment-container').append(paymentRow);
            calculateTotalPaid();
        }

        function calculateTotalPaid() {
            let totalPaid = 0;
            $('.payment-amount').each(function() {
                let cleanValue = $(this).val().replace(/\./g, '').replace(/[^0-9]/g, '');
                totalPaid += parseInt(cleanValue) || 0;
            });
            let formattedTotal = totalPaid.toLocaleString('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
            $('#total-paid').val(formattedTotal);

            // Calculate remaining amount
            const totalTopupAmount = parseInt($('#txtTotalTopupAmount').val()) || 0;
            const remainingAmount = totalTopupAmount - totalPaid;
            let formattedRemaining = remainingAmount.toLocaleString('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
            
            // Update remaining amount field and styling
            const remainingField = $('#remaining-amount');
            remainingField.val(formattedRemaining);
            
            // Change color based on remaining amount
            if (remainingAmount > 0) {
                remainingField.removeClass('text-success').addClass('text-danger fw-bold');
            } else if (remainingAmount === 0) {
                remainingField.removeClass('text-danger').addClass('text-success fw-bold');
            } else {
                remainingField.removeClass('text-success text-danger').addClass('text-warning fw-bold');
            }
        }

        // Add initial payment row with auto-filled data
        const initialNominal = <?= (session('cache')['Nominal'] ?? 0) ?> || 0;
        addPaymentRow(initialNominal, true);

        $('#add-payment-row').on('click', function() {
            addPaymentRow();
        });

        $(document).on('click', '.remove-payment-row', function() {
            const rowId = $(this).data('id');
            $(`#payment-row-${rowId}`).remove();
            calculateTotalPaid();
        });

        $(document).on('input', '.payment-amount', function() {
            let xvalue = $(this).val().toString();
            // Remove all non-numeric characters including existing dots (thousands separators)
            let cleanValue = xvalue.replace(/\./g, '').replace(/[^0-9]/g, '');

            // Use Number() for more robust conversion and handle empty string
            let numValue = Number(cleanValue);
            if (isNaN(numValue) || cleanValue === '') {
                numValue = 0;
            }

            // Format with dot as thousands separator (Indonesian locale)
            let formattedValue = numValue.toLocaleString('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
            $(this).val(formattedValue);
            calculateTotalPaid();
        });

        // Form submission validation
        $('form').on('submit', function(e) {
            // Clean formatted values before submission
            $('.payment-amount').each(function() {
                let cleanValue = $(this).val().replace(/\./g, ''); // Remove dots (thousands separators)
                $(this).val(cleanValue);
            });

            const totalTopupAmount = parseInt($('#txtTotalTopupAmount').val());
            let totalPaid = 0;
            $('.payment-amount').each(function() {
                totalPaid += parseInt($(this).val()) || 0;
            });

            if (totalPaid === 0) {
                alert('Please add at least one payment.');
                e.preventDefault();
                return;
            }

            // Status logic will be handled server-side:
            // Status 4: Unfulfilled payment (totalPaid < totalTopupAmount)
            // Status 5: Fully paid (totalPaid >= totalTopupAmount)
            // Partial payments are allowed and will be marked as status 4
            
            // Optional: Show confirmation for partial payments
            if (totalPaid < totalTopupAmount) {
                const remaining = totalTopupAmount - totalPaid;
                const formattedRemaining = remaining.toLocaleString('id-ID');
                if (!confirm(`This is a partial payment. Remaining amount: ${formattedRemaining}. Continue?`)) {
                    e.preventDefault();
                    return;
                }
            }
        });
    });
</script>
