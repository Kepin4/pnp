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
                    <h6 class="font-weight-bolder mb-0">Shift Report</h6>
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
                            <form id="frmFilter" action="<?= base_url('/CTrans/ShiftReport') ?>" method="POST">
                                <?= csrf_field() ?>
                                <div class="filter-box">
                                    <h5>Filter Tanggal</h5>
                                    <div class="row">
                                        <div class="col-6">
                                            <h6>Dari Tanggal</h6>
                                            <input name="dtStart" value="<?= session('cache')->dtStart ?>" type="date" class="form-control sm filter-input" style="width: 100%">
                                        </div>
                                        <div class="col-6">
                                            <h6>Sampai Tanggal</h6>
                                            <input name="dtEnd" value="<?= session('cache')->dtEnd ?>" type="date" class="form-control sm filter-input" style="width: 100%">
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
                                    <h4 class="pt-4">Shift Report</h4>
                                    <button class="btn btn-danger" id="ExportPDF"><i class="fas fa-file-pdf"></i> Print PDF</button>
                                    <button class="btn" id="ExportExcel" style="background-color: #174e17; color: white"><i class="fas fa-file-excel"></i> Export Excel</button>
                                </div>
                            </div>
                        </div>



                        <div class="card-body px-0 pt-0 pb-2">
                            <div class="table-responsive p-0 datatable-minimal p-3">
                                <table class="table align-items-center mb-0 display" id="tblTrans">
                                    <thead>
                                        <tr>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">#</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">ID Shift</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total Periode</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Started By</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Started Date</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ended By</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ended Date</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total Amount</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">CashBack</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total Win</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">TotalComp</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php $i = 1;
                                        foreach ($dt as $q) { ?>
                                            <tr>
                                                <td class="align-middle text-center">
                                                    <p class="text-xs">
                                                        <?= $i++ ?>
                                                    </p>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <a href="<?= base_url('../CTrans/DetailShift/' . $q->IDShift) ?>">
                                                        <p class="text-s font-weight-bold" style="color: #17c1e8"><?= $q->IDShift ?></p>
                                                    </a>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <p class="text-xs"><?= $q->Periode ?></p>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <p class="text-xs"><?= $q->InputBy ?></p>
                                                </td>
                                                <td class="align-left text-left">
                                                    <p class="text-xs">
                                                        <?php $xDt = new DateTime($q->InputDate);
                                                        echo $xDt->format('Y-M-d') . "<br>";
                                                        echo $xDt->format('h:i:s A');
                                                        ?>
                                                    </p>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <p class="text-xs"><?= $q->CloseBy ?></p>
                                                </td>
                                                <td class="align-left text-left">
                                                    <p class="text-xs">
                                                        <?php $xDt = new DateTime($q->CloseDate);
                                                        echo $xDt->format('Y-M-d') . "<br>";
                                                        echo $xDt->format('h:i:s A');
                                                        ?>
                                                    </p>
                                                </td>
                                                <td class="align-middle text-right">
                                                    <p class="text-xs" style="color: <?= ($q->TotalPlacement >= 0 ? 'green' : 'red') ?>"><?= number_format($q->TotalPlacement, 2, ",", '.'); ?></p>
                                                </td>
                                                <!-- <td class="align-middle text-right">
                                                    <p class="text-xs" style="color: <?= ($q->NominalWin >= 0 ? 'green' : 'red') ?>"><?= number_format($q->NominalWin, 2, ",", '.'); ?></p>
                                                </td> -->
                                                <td class="align-middle text-right">
                                                    <p class="text-xs" style="color: <?= ($q->NominalCashback >= 0 ? 'green' : 'red') ?>"><?= number_format($q->NominalCashback, 2, ",", '.'); ?></p>
                                                </td>
                                                <td class="align-middle text-right">
                                                    <p class="text-xs" style="color: <?= ($q->TotalWin >= 0 ? 'green' : 'red') ?>"><?= number_format($q->TotalWin, 2, ",", '.'); ?></p>
                                                </td>
                                                <td class="align-middle text-right">
                                                    <p class="text-xs" style="color: <?= ($q->Total >= 0 ? 'green' : 'red') ?>"><?= number_format($q->Total, 2, ",", '.'); ?></p>
                                                </td>
                                                <td class="align-middle text-right">
                                                    <p class="text-xs" style="color: <?= ($q->TotalComp >= 0 ? 'green' : 'red') ?>"><?= number_format($q->TotalComp, 2, ",", '.'); ?></p>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="text-center">
                                            <th colspan="7">TOTAL</td>
                                                <?php
                                                $TotalPlacement = array_sum(array_column($dt, 'TotalPlacement'));
                                                // $NominalWin = array_sum(array_column($dt, 'NominalWin'));
                                                $NominalCashback = array_sum(array_column($dt, 'NominalCashback'));
                                                $TotalWin = array_sum(array_column($dt, 'TotalWin'));
                                                $Total = array_sum(array_column($dt, 'Total'));
                                                $Total = array_sum(array_column($dt, 'Total'));
                                                $TotalComp = array_sum(array_column($dt, 'TotalComp'));
                                                ?>

                                                <!-- <th style="color: ($NominalWin >= 0 ? 'green' : 'red') ?>">number_format($NominalWin, 2, ',', '.')?></td> -->
                                            <th style="color: <?= ($NominalCashback >= 0 ? 'green' : 'red') ?>"><?= number_format($NominalCashback, 2, ',', '.') ?></td>
                                            <th style="color: <?= ($TotalWin >= 0 ? 'green' : 'red') ?>"><?= number_format($TotalWin, 2, ',', '.') ?></td>
                                            <th style="color: <?= ($Total >= 0 ? 'green' : 'red') ?>"><?= number_format($Total, 2, ',', '.') ?></td>
                                            <th style="color: <?= ($TotalComp >= 0 ? 'green' : 'red') ?>"><?= number_format($TotalComp, 2, ',', '.')  ?></td>
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

<script>
    $(document).ready(function() {
        $('#tblTrans').DataTable({});

        const xForm = $('#frmFilter').clone();
        $('#ExportPDF').on('click', function() {
            SendReport(xForm, 'CExport/ShiftReport/PDF')
        });
        $('#ExportExcel').on('click', function() {
            SendReport(xForm, 'CExport/ShiftReport/Excel')
        });

        receiveSignal('ReportShift', function(data) {
            $('#frmFilter').submit();
        })
    })
</script>