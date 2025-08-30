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
                    <h6 class="font-weight-bolder mb-0">Laporan Invoice</h6>
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

                            <form id="frmFilter" action="<?= base_url('/CTrans/Transaction') ?>" method="POST">
                                <?= csrf_field() ?>
                                <div class="filter-box">
                                    <h5>Filter Tanggal</h5>
                                    <div class="row">
                                        <div class="col-6">
                                            <h6>Dari Tanggal</h6>
                                            <input name="dtStart" value="<?= $fltrTrans->dtStart ?>" type="date" class="form-control sm filter-input" style="width: 100%">
                                        </div>
                                        <div class="col-6">
                                            <h6>Sampai Taggal</h6>
                                            <input name="dtEnd" value="<?= $fltrTrans->dtEnd ?>" type="date" class="form-control sm filter-input" style="width: 100%">
                                        </div>
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
                                    <h4 class="pt-4">Laporan Invoice</h4>
                                    <button class="btn btn-danger" id="ExportPDF"><i class="fas fa-file-pdf"></i> Print PDF</button>
                                    <button class="btn" id="ExportExcel" style="background-color: #174e17; color: white"><i class="fas fa-file-excel"></i> Export Excel</button>
                                </div>
                                <?php if ((session('level') >= 1 && session('level') <= 3)) { ?>
                                    <div class="col-5 text-end pt-4">
                                        <a href="<?= base_url('/CTrans/NewTransaction') ?>">
                                            <button class="btn btn-sm btn-outline-primary">New</button>
                                        </a>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>



                        <div class="card-body px-0 pt-0 pb-2">
                            <div class="table-responsive p-0 datatable-minimal p-3">
                                <table class="table align-items-center mb-0 display" id="tblTrans">
                                    <thead>
                                        <tr>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">#</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Notrans</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Date</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Member</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Type</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Description</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Amount</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Cashback</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total Amount</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php $i = 1;
                                        foreach ($dtTrans as $q) { ?>
                                            <tr>
                                                <td class="align-middle text-center">
                                                    <p class="text-xs">
                                                        <?= $i++ ?>
                                                    </p>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <a href="<?= base_url('../CTrans/DetailTransaction/' . $q->notrans) ?>">
                                                        <p class="text-s font-weight-bold" style="color: #17c1e8"><?= $q->notrans ?></p>
                                                    </a>
                                                </td>
                                                <td class="align-left text-left">
                                                    <p class="text-xs" style=" font-weight: bold;">
                                                        <?php $xDt = new DateTime($q->tanggal);
                                                        echo $xDt->format('Y-M-d') . "<br>";
                                                        echo $xDt->format('h:i:s A');
                                                        ?>
                                                    </p>
                                                </td>
                                                <td class="align-middle">
                                                    <p class="text-xs"><?= $q->username ?></p>
                                                </td>
                                                <td class="align-middle">
                                                    <p class="text-xs"><?= $q->jenis ?></p>
                                                </td>
                                                <td class="align-middle">
                                                    <p class="text-xs"><?= $q->keterangan ?></p>
                                                </td>
                                                <td class="align-middle">
                                                    <?php $xAmount = $q->jenis == 'Topup' || $q->jenis == 'Withdraw' ? abs($q->amount) : $q->amount ?>
                                                    <p class="text-xs" style="color: <?= ($q->jenis == 'Topup' || $q->jenis == 'Withdraw' ? '' : ($q->amount >= 0 ? 'green' : 'red')) ?>"><?= number_format($xAmount, 2, '.', ',') ?></p>
                                                </td>
                                                <td class="align-middle">
                                                    <p class="text-xs" style="color: <?= ($q->cashback >= 0 ? 'green' : 'red') ?>"><?= number_format($q->cashback, 2, '.', ',') ?></p>
                                                </td>
                                                <td class="align-middle">
                                                    <?php $xTotal = $q->jenis == 'Topup' || $q->jenis == 'Withdraw' ? abs($q->total) : $q->total ?>
                                                    <p class="text-xs" style="color: <?= ($q->jenis == 'Topup' || $q->jenis == 'Withdraw' ? '' : ($q->total >= 0 ? 'green' : 'red')) ?>"><?= number_format($xTotal, 2, '.', ',') ?></p>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="6">TOTAL</td>
                                                <?php
                                                $tWONetral = array_filter($dtTrans, function ($q) {
                                                    return !($q->jenis == "Topup" || $q->jenis == 'Withdraw');
                                                });

                                                $amount = array_sum(array_column($dtTrans, 'amount'));
                                                $amountwonetral = array_sum(array_column($tWONetral, 'amount'));
                                                $cashback = array_sum(array_column($dtTrans, 'cashback'));
                                                $total = array_sum(array_column($dtTrans, 'total'));
                                                $totalwonetral = array_sum(array_column($tWONetral, 'total'));
                                                ?>
                                            <th style="color: <?= ($amountwonetral >= 0 ? 'green' : 'red')  ?>"><?= number_format($amountwonetral, 2, '.', ',') ?></td>
                                            <th style="color: <?= ($cashback >= 0 ? 'green' : 'red')  ?>"><?= number_format($cashback, 2, '.', ',') ?></td>
                                            <th style="color: <?= ($totalwonetral >= 0 ? 'green' : 'red')  ?>"><?= number_format($totalwonetral, 2, '.', ',') ?></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</body>

<div class="modal fade text-left" id="inlineForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel33">Details</h4>
                <button type="button" class="close btn btn-danger" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa fa-close"></i>
                </button>
            </div>
            <form action="#">
                <div class="modal-body">
                    <label for="">Input By: </label>
                    <div class="form-group">
                        <input readonly id="inputBy" type="text" class="form-control">
                    </div>
                    <label for="">Input Date: </label>
                    <div class="form-group">
                        <input readonly id="inputDate" type="text" class="form-control">
                    </div>
                    <label for="">Update By: </label>
                    <div class="form-group">
                        <input readonly id="updateBy" type="text" class="form-control">
                    </div>
                    <label for="">Update Date: </label>
                    <div class="form-group">
                        <input readonly id="updateDate" type="text" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Delete Confirmation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a id="deleteLink" href="#">
                    <button type="button" class="btn btn-danger">Delete</button>
                </a>
            </div>
        </div>
    </div>
</div>




<script>
    $(document).ready(function() {
        $('#tblTrans').DataTable({});

        const xForm = $('#frmFilter').clone();
        $('#ExportPDF').on('click', function() {
            SendReport(xForm, 'CExport/TransactionReport/PDF')
        });
        $('#ExportExcel').on('click', function() {
            SendReport(xForm, 'CExport/TransactionReport/Excel')
        });


        receiveSignal('ReportTransaction', function(data) {
            $('#frmFilter').submit();
        });
    })
</script>