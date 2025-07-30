<body class="g-sidenav-show bg-gray-100">
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg" style=" top: 25px;">
        <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
            <div class="container-fluid py-1 px-3">
                <nav aria-label="breadcrumb">
                    <h6 class="font-weight-bolder mb-0">Detail Placement</h6>
                </nav>

                <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
                    <div class="ms-md-auto pe-md-3 d-flex align-items-center"></div>
                    <ul class="navbar-nav  justify-content-end">
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

        <div class="container-fluid py-4">
            <div class="card p-4">
                <h4><?= $qTrns->notrans ?></h4>
                <?php if (!$qTrns->noref == '') { ?>
                    <tr>
                        <td style="font-weight: bold;">No Referensi</td>
                        <td class="pl-4 pr-1" style="font-weight: bold;">:</td>
                        <td><?= $qTrns->noref ?></td>
                    </tr>
                <?php } ?>

                <?php if ($qStatus == 5) { ?>
                    <a href="<?= base_url('../CTrans/PrintTransaction/' . '/' . $qTrns->notrans) ?>" style="display: inline-block; width:fit-content;">
                        <button class="btn btn-sm btn-danger mt-2">
                            <i class="fa fa-file-pdf"></i> Print PDF
                        </button>
                    </a>
                <?php } ?>



                <div>
                    <table border="0">
                        <tr>
                            <td style="font-weight: bold;">Date</td>
                            <td class="pl-4 pr-1" style="font-weight: bold;">:</td>
                            <td><?php $xTgl = new Datetime($qTrns->tanggal);
                                echo $xTgl->format('Y M d'); ?></td>
                        </tr>
                        <?php if ($qTrns->jenistrans == '3' || $qTrns->jenistrans == '6') { ?>
                            <tr>
                                <td style="font-weight: bold;">Shift-Sesi</td>
                                <td class="pl-4 pr-1" style="font-weight: bold;">:</td>
                                <td><?= "$IDShift - $Periode" ?></td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td style="font-weight: bold;">Member</td>
                            <td class="pl-4 pr-1" style="font-weight: bold;">:</td>
                            <td><?= $qTrns->username ?></td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;">Description</td>
                            <td class="pl-4 pr-1" style="font-weight: bold;">:</td>
                            <td><?= $qTrns->keterangan ?></td>
                        </tr>
                        <?php if ($qTrns->jenistrans == '3') { ?>
                            <tr>
                                <td style="font-weight: bold;">Win Number</td>
                                <td class="pl-4 pr-1" style="font-weight: bold;">:</td>
                                <td><?= $WinNum ?></td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold;">Status</td>
                                <td class="pl-4 pr-1" style="font-weight: bold;">:</td>
                                <td><?= ($qStatus == 5 ? ($qNotransWin == '' ? 'Lose' : 'Win') : 'On Placement') ?></td>
                            </tr>

                            <?php if ($qNotransWin != '') { ?>
                                <tr>
                                    <td style="font-weight: bold;">Notrans Win</td>
                                    <td class="pl-4 pr-1" style="font-weight: bold;">:</td>
                                    <td>
                                        <a href="<?= base_url('../CTrans/DetailTransaction/' . '/' . $qNotransWin) ?>" style="font-weight: bold; color: blue;">
                                            <?= ($qStatus == 5 ? $qNotransWin : '') ?>
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } ?>

                        <tr>
                            <td style="font-weight: bold;">Total Amount</td>
                            <td class="pl-4 pr-1" style="font-weight: bold;">:</td>
                            <td><?= number_format($TotalSaldo, 2, '.', ',') ?></td>
                        </tr>
                        <tr>
                            <?php
                            $i = 1;

                            use App\Controllers\CTools;

                            $cntrl = new CTools;
                            $Saldo = $cntrl->getSaldo($qTrns->iduser, $xTgl->format('Y-m-d H:i:s'));
                            $SaldoAwal = $Saldo;
                            ?>
                            <td style="font-weight: bold;">Saldo Awal</td>
                            <td class="pl-4 pr-1" style="font-weight: bold;">:</td>
                            <td><?= number_format($Saldo, 2, '.', ',') ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="table-responsive mt-4">
                <caption>
                    <h5 style="border-bottom: 1px solid black">Number</h5>
                </caption>
                <table id="tblSaldo" class="table">
                    <thead>
                        <tr>
                            <td class="text-center">Number</td>
                            <td>Nominal</td>
                            <td>Win</td>
                            <td>Cashback</td>
                            <td>Total</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dtNumber as $q) { ?>
                            <tr>
                                <td class="text-center"><?= $q->Number ?></td>
                                <td><?= number_format($q->Nominal, 2, '.', ',') ?></td>
                                <td style="color: <?= ($q->Win > 0 ? 'green' : 'red') ?>;"><?= number_format($q->Win, 2, '.', ',') ?></td>
                                <td style="color: <?= ($q->Cashback > 0 ? 'green' : 'red') ?>;"><?= number_format($q->Cashback, 2, '.', ',') ?></td>
                                <td style="color: <?= ($q->Total > 0 ? 'green' : 'red') ?>;"><?= number_format($q->Total, 2, '.', ',') ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <?php
                        $Nominal = array_sum(array_column($dtNumber, 'Nominal'));
                        $Win = array_sum(array_column($dtNumber, 'Win'));
                        $Cashback = array_sum(array_column($dtNumber, 'Cashback'));
                        $Total = array_sum(array_column($dtNumber, 'Total'));
                        ?>
                        <th>TOTAL</td>
                        <th style="color : <?= $Nominal >= 0 ? 'green' : 'red' ?>"><?= number_format($Nominal, 2, '.', ',') ?></td>
                        <th style="color : <?= $Win >= 0 ? 'green' : 'red' ?>"><?= number_format($Win, 2, '.', ',') ?></td>
                        <th style="color : <?= $Cashback >= 0 ? 'green' : 'red' ?>"><?= number_format($Cashback, 2, '.', ',') ?></td>
                        <th style="color : <?= $Total >= 0 ? 'green' : 'red' ?>"><?= number_format($Total, 2, '.', ',') ?></td>
                            </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </main>
</body>

<script>
    $(document).ready(function() {
        $('#tblSaldo').DataTable({});
    })
</script>