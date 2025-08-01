<style>
    .filter-box {
        padding: 10px;
        border: 1px solid #cb0c9f;
        border-radius: 5px;
        background-color: #f8f9fa;
        display: inline-block;
        margin-top: 10px;
    }

    .filter-input {
        width: 100%;
        /* border: 1px solid #1e1e1e; */
        /* border: 1px solid #cb0c9f;
        color: #cb0c9f */
    }
</style>

<body class="g-sidenav-show  bg-gray-100">
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg " style=" top: 25px;">
        <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
            <div class="container-fluid py-1 px-3">
                <nav aria-label="breadcrumb">
                    <h6 class="font-weight-bolder mb-0">Topup Request List</h6>
                </nav>
                <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
                    <div class="ms-md-auto pe-md-3 d-flex align-items-center">
                    </div>
                    <ul class="navbar-nav justify-content-end">
                        <li class="nav-item d-flex align-items-center">
                            <a href="<?= base_url('../../CBase/Profile') ?>" class="nav-link text-body font-weight-bold px-0">

                                <i class="fa fa-user me-sm-1"></i>
                                <span class="d-sm-inline"><?= session('username') ?></span>
                            </a>
                        </li>
                        <li class="nav-item ps-3 d-flex align-items-center">
                            <a href="<?= base_url('/CLogin/Logout') ?>" class="nav-link text-body font-weight-bold px-0">
                                <i class="fa fa-sign-out me-sm-1"></i>
                                <span class="d-sm-inline">Log Out</span>
                            </a>
                        </li>
                        <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                            <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                                <div class="sidenav-toggler-inner">
                                    <i class="sidenav-toggler-line"></i>
                                    <i class="sidenav-toggler-line"></i>
                                    <i class="sidenav-toggler-line"></i>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>


        <!-- Table Section -->
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header pt-3 pb-1">
                            <form id="frmFilter" action="<?= base_url('/CTrans/TopupRequest') ?>" method="POST">
                                <?= csrf_field() ?>
                                <div class="filter-box">
                                    <h5>Filter Date</h5>
                                    <div class="row">
                                        <div class="col-6">
                                            <h6>Start Date</h6>
                                            <input name="dtStart" value="<?= session('cache')->dtStart ?>" type="date" class="form-control sm filter-input" style="width: 100%">
                                        </div>
                                        <div class="col-6">
                                            <h6>End Date</h6>
                                            <input name="dtEnd" value="<?= session('cache')->dtEnd ?>" type="date" class="form-control sm filter-input" style="width: 100%">
                                        </div>
                                    </div>

                                    <div class="col pl-1 pt-3 pb-0">
                                        <input name="chkRequest" type="checkbox" <?= (session('cache')->chkRequest == "on" ? "checked" : "") ?>> Show All Requested
                                    </div>

                                    <div class="row pt-3">
                                        <div class="row m-0 p-3">
                                            <button class="btn btn-sm btn-outline-primary w-100">
                                                Refresh
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="row">
                                <div class="col-7">
                                    <h4 class="pt-4">Topup Request List</h4>
                                    <button class="btn btn-danger" id="ExportPDF"><i class="fas fa-file-pdf"></i> Print PDF</button>
                                    <button class="btn" id="ExportExcel" style="background-color: #174e17; color: white"><i class="fas fa-file-excel"></i> Export Excel</button>
                                </div>
                                <?php if (!(session('level') >= 1 && session('level') <= 3)) { ?>
                                    <div class="col-5 text-end pt-4">
                                        <a href="<?= base_url('/CBase/NewTopup') ?>">
                                            <button class="btn btn-sm btn-outline-primary">New</button>
                                        </a>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>



                        <div class="card-body px-0 pt-0 pb-2">
                            <div class="table-responsive datatable-minimal p-3">
                                <table class="table align-items-center mb-0 display" id="tblTrans">
                                    <thead>
                                        <tr class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            <th class="ps-2">#</th>
                                            <th>Kode Request</th>
                                            <th>Member</th>
                                            <th>Amount</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Topup By</th>
                                            <th>Refused By</th>
                                            <th>Update Time</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php

                                        use App\Controllers\CTools;

                                        $CTools = new CTools();

                                        $i = 1;
                                        foreach ($dtReqTopup as $q) { ?>
                                            <tr class="text-xs">
                                                <td><?= $i++ ?></td>
                                                <td><?= $q->kodereq ?></td>
                                                <td><?= $q->username ?></td>
                                                <td><?= number_format($q->amount, 2, ',', '.'); ?></td>
                                                <td><?php $xTgl = new Datetime($q->tanggal);
                                                    echo $xTgl->format('Y-M-d') . '<br>';
                                                    echo $xTgl->format('H:i:s');
                                                    ?></td>
                                                <td class="text-l text-center pt-2"><button class="btn btn-sm py-1 px-1 text-center text-l <?= $q->status == 5 ? 'btn-success' : ($q->status == 8 ? 'btn-danger' : 'btn-secondary')  ?>" style="border-radius: 2px; color: white" disabled><?= $q->status == 1 ? "<i class='fas fa-clock' style='font-size: x-large'></i>" : ($q->status == 5 ? "<i class='fas fa-square-check' style='font-size: x-large'></i>" : ($q->status == 8 ? "<i class='fas fa-square-xmark' style='font-size: x-large'></i>" : "Failed"))  ?></button></td>
                                                <td><?= ($q->status == 5 ? $CTools->getUsername($q->updateby) : "") ?></td>
                                                <td><?= ($q->status == 8 ? $CTools->getUsername($q->updateby) : "") ?></td>
                                                <td><?php if ($q->status == 5 ||  $q->status == 8) {
                                                        $xTgl = new Datetime($q->updatedate);
                                                        echo $xTgl->format('Y-M-d') . '<br>';
                                                        echo $xTgl->format('H:i:s');
                                                    } ?></td>
                                                <td>
                                                    <?php if (!(session('level') >= 1 && session('level') >= 3)) { ?>
                                                        <a href=" <?= base_url('../CTrans/ProcessReqTopup/' . '/' . $q->kodereq) ?>">
                                                            <button class="bg-transparent border-0 text-xs" <?= (($q->status == 5 || $q->status == 8) ? "disabled" : "") ?>>
                                                                TopUp
                                                            </button>
                                                        </a>
                                                        |
                                                        <a href=" <?= base_url('../CTrans/RefuseReqTopup/' . '/' . $q->kodereq) ?>">
                                                            <button class="bg-transparent border-0 text-xs" <?= (($q->status == 5 || $q->status == 8) ? "disabled" : "") ?>>
                                                                Refuse
                                                            </button>
                                                        </a>
                                                        <?= ($q->status == 5) ? "|"  : "" ?>
                                                    <?php } ?>
                                                    <?php if ($q->status == 5) { ?>
                                                        <a href="<?= base_url('../CTrans/DetailTransactionRef/' . '/' . $q->kodereq) ?>">
                                                            <button class="bg-transparent border-0 text-xs">
                                                                Detail
                                                            </button>
                                                        </a>
                                                    <?php } ?>
                                                <?php } ?>
                                                </td>
                                            </tr>
                                    </tbody>
                                    <tfoot>
                                        <?php
                                        $Amount = array_sum(array_column(array_filter($dtReqTopup, function ($x) {
                                            return $x->status == 5;
                                        }), 'amount'));
                                        ?>
                                        <tr>
                                            <th class="text-center" colspan="3">Total</th>
                                            <th class="text-right"><?= number_format($Amount, 2, ',', '.'); ?></th>
                                            <th colspan="6"></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>

<script>
    $(document).ready(function() {
        $('#tblTrans').DataTable({});

        const xForm = $('#frmFilter').clone();
        $('#ExportPDF').on('click', function() {
            SendReport(xForm, 'CExport/TopupRequest/PDF')
        });
        $('#ExportExcel').on('click', function() {
            SendReport(xForm, 'CExport/TopupRequest/Excel')
        });
    })
</script>