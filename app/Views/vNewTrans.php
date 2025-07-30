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
                            <div class="mb-3 mt-3">
                                <button type="submit" class="btn btn-primary">Submit</button>
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
    });
</script>