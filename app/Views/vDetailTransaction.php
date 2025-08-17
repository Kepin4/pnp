<body class="g-sidenav-show bg-gray-100">
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg" style=" top: 25px;">
        <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
            <div class="container-fluid py-1 px-3">
                <nav aria-label="breadcrumb">
                    <h6 class="font-weight-bolder mb-0">Detail Transaction</h6>
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
                <?php 
                // Check if we have multiple transaction numbers from session
                $multiNotrans = session()->getFlashdata('multi_notrans');
                if ($multiNotrans) {
                    // Display multiple transaction numbers
                    echo '<h4>' . $multiNotrans . '</h4>';
                } else {
                    // Display single transaction number
                    echo '<h4>' . $qTrns->notrans . '</h4>';
                }
                ?>
                <?php if (!$qTrns->noref == '') { ?>
                    <tr>
                        <td style="font-weight: bold;">No Referensi</td>
                        <td class="pl-4 pr-1" style="font-weight: bold;">:</td>
                        <td><?= $qTrns->noref ?></td>
                    </tr>
                <?php } ?>

                <a href="<?= base_url('../CTrans/PrintTransaction/' . '/' . $qTrns->notrans) ?>" style="display: inline-block; width:fit-content;">
                    <button class="btn btn-sm btn-danger mt-2">
                        <i class="fa fa-file-pdf"></i> Print PDF
                    </button>
                </a>


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
                            $Saldo = $cntrl->getSaldo($idUser, $xTgl->format('Y-m-d H:i:s'));
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
                    <h5 style="border-bottom: 1px solid black">Saldo</h5>
                </caption>
                <table id="tblSaldo" class="table">
                    <thead>
                        <tr>
                            <td style="width: 25px">#</td>
                            <td>Income</td>
                            <td>Outcome</td>
                            <td>Balance</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($dtSaldo as $q) {
                            $xVal = (float) $q->amount;
                            $SaldoAwal += $xVal  ?>
                            <tr>
                                <td style="width: 25px"><?= $i++ ?></td>
                                <td><?= ($q->amount > 0 ? number_format($xVal, 2, '.', ',') : 0) ?></td>
                                <td><?= ($q->amount < 0 ? number_format($xVal, 2, '.', ',') : 0) ?></td>
                                <td><?= number_format($SaldoAwal, 2, '.', ',') ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
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
