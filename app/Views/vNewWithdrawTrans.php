<?php session()->set('AllowSave', true) ?>
<style>
    .input-container {
        display: flex;
        align-items: center;
    }

    .input-label {
        margin-left: 10px;
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

    /* Custom Modal Styles */
    .custom-modal {
        display: none;
        position: fixed;
        z-index: 10000000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-content {
        background-color: #fefefe;
        margin: 15% auto;
        padding: 20px;
        border: none;
        border-radius: 8px;
        width: 400px;
        max-width: 90%;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid #dee2e6;
    }

    .modal-title {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 600;
        color: #495057;
    }

    .modal-body {
        margin-bottom: 20px;
        color: #6c757d;
        line-height: 1.5;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .modal-btn {
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        transition: background-color 0.2s;
    }

    .modal-btn-cancel {
        background-color: #6c757d;
        color: white;
    }

    .modal-btn-cancel:hover {
        background-color: #5a6268;
    }

    .modal-btn-confirm {
        background-color: #dc3545;
        color: white;
    }

    .modal-btn-confirm:hover {
        background-color: #c82333;
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
                    <h6 class="font-weight-bolder mb-0">Input Transaction</h6>
                </nav>
            </div>
        </nav>


        <div class="content-wrapper">
            <div class="container-xxl flex-grow-1 container-p-y">
                <div class="card">
                    <div class="card-header">
                        <form action="<?= base_url('../CTrans/SaveTransaction') ?>" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="txtKodeReq" value="<?= session('cache')['KodeReq'] ?? "" ?>">
                            <div class="mb-3">
                                <label for="account-select" class="form-label">ID Member</label>
                                <select id="selAccount" name="selAccount" class="form-select select-supply" autofocus required>
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
                                <select id="selJenisTrans" name="selJenisTrans" class="form-select select-supply" aria-label="" required>
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
                                <input id="txtDesc" name="txtDesc" type="text" class="form-control" placeholder="Description" />
                            </div>

                            <div class="mb-3">
                                <label for="" class="form-label">Nominal</label>
                                <input id="txtNominal" name="txtNominal" type="number" class="form-control" placeholder="Amount" value=<?= session('cache')['Nominal'] ?? 0 ?> min=0 required />
                            </div>

                            <div id="WarningTopup" class="mt-6 mb-3 d-none">
                                <b class="text-danger">
                                    Masih terdapat Topup yang belum diproses pada user ini.
                                </b>
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a class="text-dark" href="<?= base_url('/CTrans/Transaction') ?>">
                                    <button type="button" class="btn btn-secondary">Back</button>
                                </a>
                            </div>
                        </form>

                        <!-- Custom Confirmation Modal -->
                        <div id="confirmationModal" class="custom-modal">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Confirmation Required</h5>
                                </div>
                                <div class="modal-body">
                                    Terdapat Payment Topup yang belum selesai pada user ini. Apakah Anda yakin ingin melanjutkan?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="modal-btn modal-btn-cancel" id="modalCancel">Batal</button>
                                    <button type="button" class="modal-btn modal-btn-confirm" id="modalConfirm">Lanjutkan</button>
                                </div>
                            </div>
                        </div>
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

        $('#txtNominal').on('change', function() {
            let xvalue = $(this).val().toString();
            xvalue = xvalue.replace(/[^0-9]/g, '');
            let intValue = parseInt(xvalue);
            if (isNaN(intValue)) {
                intValue = 0;
            }

            $(this).val(intValue);
        });

        $('#txtCashback').on('change', function() {
            let xvalue = $(this).val().toString();
            xvalue = xvalue.replace(/[^0-9]/g, '');
            let intValue = parseInt(xvalue);
            if (isNaN(intValue)) {
                intValue = 0;
            } else if (intValue > 100) {
                intValue = 100;
            }

            $(this).val(intValue);
        });

        // Form submission handler with confirmation modal
        $('form').on('submit', function(e) {
            // Check if WarningTopup is visible (doesn't have d-none class)
            if (!$('#WarningTopup').hasClass('d-none')) {
                e.preventDefault(); // Prevent form submission
                $('#confirmationModal').show(); // Show confirmation modal
            }
            // If WarningTopup is not visible, allow normal form submission
        });

        // Modal Cancel button handler
        $('#modalCancel').on('click', function() {
            $('#confirmationModal').hide(); // Hide modal and do nothing
        });

        // Modal Confirm button handler
        $('#modalConfirm').on('click', function() {
            $('#confirmationModal').hide(); // Hide modal
            // Submit the form programmatically
            $('form')[0].submit();
        });

        // Close modal when clicking outside of it
        $('#confirmationModal').on('click', function(e) {
            if (e.target === this) {
                $(this).hide();
            }
        });

        cekTopupPending();
    })


    function cekTopupPending() {
        $.ajax({
            url: '<?= base_url('/CTrans/CekTopupPending') ?>',
            type: 'POST',
            data: {
                userId: <?= $u->id ?>
            },
            dataType: 'json',
            success: function(response) {
                console.log(response);
                if (response.hasPending) {
                    $('#WarningTopup').removeClass('d-none');
                } else {
                    $('#WarningTopup').addClass('d-none');
                }
            },
            error: function() {
                console.error('Error checking topup pending status.');
            }
        });
    }
</script>
