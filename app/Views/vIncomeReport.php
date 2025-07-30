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
    }
</style>

<?php

$myLevel = session('level');
function frmtTD(float $xVal, bool $reverse = false, bool $isNoColor = false)
{
    $xCol = ($isNoColor ? '' : ($xVal * ($reverse ? -1 : 1) >= 0 ? 'green' : 'red'));
    $xStrVal = number_format($xVal, 2, '.', ',');
    return "<td class='align-middle text-right' style='color: $xCol'> <p class='text-xs'> $xStrVal</p> </td>";
}

?>

<body class="g-sidenav-show  bg-gray-100">
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg " style=" top: 25px;">
        <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
            <div class="container-fluid py-1 px-3">
                <nav aria-label="breadcrumb">
                    <h6 class="font-weight-bolder mb-0">Laporan Income</h6>
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

                            <form id="frmFilter" action="<?= base_url('/CTrans/IncomeReport') ?>" method="POST">
                                <?= csrf_field() ?>
                                <div class="filter-box">
                                    <h5>Filter Tanggak</h5>
                                    <input id="txtID" name="txtID" value="<?= $fltrTrans->txtID ?>" type="hidden">
                                    <input id="txtIsNoAgent" name="txtIsNoAgent" value="<?= $fltrTrans->txtIsNoAgent ?>" type="hidden">
                                    <div class="row">
                                        <div class="col-6">
                                            <h6>Dari Tanggal</h6>
                                            <input name="dtStart" value="<?= $fltrTrans->dtStart ?>" type="date" class="form-control sm filter-input" style="width: 100%">
                                        </div>
                                        <div class="col-6">
                                            <h6>Sampai Tanggal</h6>
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
                                    <h4 class="pt-4">Laporan Income</h4>
                                    <button class="btn btn-danger" id="ExportPDF"><i class="fas fa-file-pdf"></i> Print PDF</button>
                                    <button class="btn" id="ExportExcel" style="background-color: #174e17; color: white"><i class="fas fa-file-excel"></i> Export Excel</button>
                                </div>
                            </div>
                        </div>



                        <div class="card-body px-0 pt-0 pb-2">
                            <div class="table-responsive p-0 datatable-minimal p-3">
                                <table class="table align-items-center mb-0 display" id="tblTrans">
                                    <thead>
                                        <?php if ($fltrTrans->Expand) { ?>
                                            <tr>
                                                <th rowspan="2" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">#</th>
                                                <th rowspan="2" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Member</th>
                                                <th colspan="4" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Agent</th>
                                                <th colspan="4" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Member</th>
                                                <th rowspan="2" class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total Comp</th>
                                            </tr>
                                            <tr>
                                                <th class="text-right text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Turnover</th>
                                                <!-- <th class="text-right text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nominal Play</th> -->
                                                <th class="text-right text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Win</th>
                                                <th class="text-right text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Cashback</th>
                                                <th class="text-right text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total</th>

                                                <th class="text-right text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Turnover</th>
                                                <!-- <th class="text-right text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nominal Play</th> -->
                                                <th class="text-right text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Win</th>
                                                <th class="text-right text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Cashback</th>
                                                <th class="text-right text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total</th>
                                            </tr>

                                        <?php } else { ?>
                                            <tr>
                                                <th class="text-right text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">#</th>
                                                <th class="text-right text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Member</th>
                                                <th class="text-right text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Turnover</th>
                                                <!-- <th class="text-right text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nominal Play</th> -->
                                                <th class="text-right text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Win</th>
                                                <th class="text-right text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Cashback</th>
                                                <th class="text-right text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total</th>

                                                <?php if ($myLevel >= 1 && $myLevel <= 3) { ?>
                                                    <th class="text-right text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total Comp</th>
                                                <?php } ?>
                                            </tr>
                                        <?php } ?>
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
                                                    <p class="text-s font-weight-bold" <?= $q->Level == 5 ? '' : 'style="color: #17c1e8" onclick="xViewDetail(\'' . $q->idUser . '\', ' . $q->isNoAgent . ')"' ?>><?= $q->Username ?></p>

                                                </td>
                                                <?= frmtTD($q->TurnOver) ?>
                                                <!-- frmtTD($q->NominalPlay) -->
                                                <?= frmtTD($q->Win) ?>
                                                <?= frmtTD($q->Cashback) ?>
                                                <?= frmtTD($q->Total) ?>


                                                <?php if ($fltrTrans->Expand) { ?>
                                                    <?= frmtTD($q->TurnOverD) ?>
                                                    <!-- frmtTD($q->NominalPlayD) ?> -->
                                                    <?= frmtTD($q->WinD) ?>
                                                    <?= frmtTD($q->CashbackD) ?>
                                                    <?= frmtTD($q->TotalD) ?>
                                                <?php } ?>

                                                <?php if ($myLevel >= 1 && $myLevel <= 3) { ?>
                                                    <?= frmtTD($q->TotalComp) ?>
                                                <?php } ?>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="text-center">
                                            <th colspan="2">TOTAL</th>
                                            <?php
                                            $TurnOver = array_sum(array_column($dt, 'TurnOver'));
                                            // $NominalPlay = array_sum(array_column($dt, 'NominalPlay'));
                                            $Win = array_sum(array_column($dt, 'Win'));
                                            $Cashback = array_sum(array_column($dt, 'Cashback'));
                                            $Total = array_sum(array_column($dt, 'Total'));
                                            $TurnOverD = array_sum(array_column($dt, 'TurnOverD'));
                                            // $NominalPlayD = array_sum(array_column($dt, 'NominalPlayD'));
                                            $WinD = array_sum(array_column($dt, 'WinD'));
                                            $CashbackD = array_sum(array_column($dt, 'CashbackD'));
                                            $TotalD = array_sum(array_column($dt, 'TotalD'));
                                            $Komisi = array_sum(array_column($dt, 'Komisi'));

                                            $TotalComp = array_sum(array_column($dt, 'TotalComp'));
                                            ?>

                                            <th style="color: <?= ($TurnOver >= 0 ? 'green' : 'red') ?> "><?= number_format($TurnOver, 2, '.', ',') ?></th>
                                            <!-- <th style="color: ($NominalPlay >= 0 ? 'green' : 'red') ?> ">number_format($NominalPlay, 2, '.', ',') ?></th> -->
                                            <th style="color: <?= ($Win >= 0 ? 'green' : 'red') ?> "><?= number_format($Win, 2, '.', ',') ?></th>
                                            <th style="color: <?= ($Cashback >= 0 ? 'green' : 'red') ?> "><?= number_format($Cashback, 2, '.', ',') ?></th>
                                            <th style="color: <?= ($Total >= 0 ? 'green' : 'red') ?> "><?= number_format($Total, 2, '.', ',') ?></th>
                                            <?php if ($fltrTrans->Expand) { ?>
                                                <th style="color: <?= ($TurnOverD >= 0 ? 'green' : 'red') ?> "><?= number_format($TurnOverD, 2, '.', ',') ?></th>
                                                <!-- <th style="color:($NominalPlayD >= 0 ? 'green' : 'red') ?> ">number_format($NominalPlayD, 2, '.', ',') ?></th> -->
                                                <th style="color: <?= ($WinD >= 0 ? 'green' : 'red') ?> "><?= number_format($WinD, 2, '.', ',') ?></th>
                                                <th style="color: <?= ($CashbackD >= 0 ? 'green' : 'red') ?> "><?= number_format($CashbackD, 2, '.', ',') ?></th>
                                                <th style="color: <?= ($TotalD >= 0 ? 'green' : 'red') ?> "><?= number_format($TotalD, 2, '.', ',') ?></th>
                                            <?php } ?>

                                            <?php if ($myLevel >= 1 && $myLevel <= 3) { ?>
                                                <th style="color: <?= ($TotalComp >= 0 ? 'green' : 'red') ?> "><?= number_format($TotalComp, 2, '.', ',') ?></th>
                                            <?php } ?>
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
            SendReport(xForm, 'CExport/IncomeReport/PDF')
        });
        $('#ExportExcel').on('click', function() {
            SendReport(xForm, 'CExport/IncomeReport/Excel')
        });
    })

    function xViewDetail(ID, Bool) {
        $('#txtID').val(ID);
        $('#txtIsNoAgent').val(Bool);
        $('#frmFilter').submit();
    }
</script>