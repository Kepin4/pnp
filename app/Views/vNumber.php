<style>
    input[type="number"]::-webkit-outer-spin-button,
    input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
</style>


<body class="g-sidenav-show bg-gray-100">
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg" style=" top: 25px;">
        <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
            <div class="container-fluid py-1 px-3">
                <nav aria-label="breadcrumb">
                    <h6 class="font-weight-bolder mb-0">Number</h6>
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
            <div class="row mt-4">
                <div class="row">
                    <div class="col-12">
                        <div class="card mb-4">
                            <div class="card-header m-1 row">
                                <div class="col-6 m-0">
                                    <h5>History Winning</h5>
                                </div>
                            </div>
                            <div class="card-body px-0 pt-0 pb-2">
                                <div class="table-responsive p-0">
                                    <table class="table align-items-center mb-0  text-center">
                                        <thead>
                                            <tr>
                                                <th class="text-uppercase text-primary text-xxs font-weight-bolder opacity-7 w-5">#</th>
                                                <?php for ($i = 1; $i <= 10; $i++) { ?>
                                                    <th class="text-uppercase text-primary text-xxs font-weight-bolder opacity-7"><?= $i ?></th>
                                                <?php } ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php for ($i = 5; $i > 0; $i--) { ?>
                                                <tr>
                                                    <td class="text-uppercase text-primary text-xxs"><?= 6 - $i ?></td>
                                                    <?php
                                                    for ($ii = 1; $ii <= 10; $ii++) {
                                                        $dt = $histWin[($i * 10 - $ii + 1)]; ?>
                                                        <td class="text-uppercase text-secondary font-weight-bolder text-s">
                                                            <span style="display: inline-block; width: 30px; height: 30px;border-radius: 50%; background-color: <?= $dt['isLast'] ? 'red' : 'transparent' ?>; color: <?= $dt['isLast'] ? 'white' : 'black' ?>; text-align: center; line-height: 30px; font-size: 14px;">
                                                                <?= $dt['Val'] ?>
                                                            </span>
                                                        </td>
                                                    <?php } ?>
                                                </tr>
                                            <?php }; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (session('level') >= 1 && session('level') <= 3) { ?>
                <div class="flex flex-row gap-2">
                    <a href="<?= base_url('../CNumber/SetNumber') ?>">
                        <button class="btn btn-lg btn-primary">
                            SET ANGKA
                        </button>
                    </a>
                </div>
            <?php } ?>

            <div class="row mt-1">
                <div class="card m-2 p-3">
                    <div class="table-responsive p-0 datatable-minimal ">
                        <table class="table align-items-center mb-0 display" id="tblTrans">
                            <thead>
                                <tr>
                                    <td>#</td>
                                    <td>Tanggal</td>
                                    <td>Shift</td>
                                    <td>Periode</td>
                                    <td>Number</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1;
                                $xLastID = 0;
                                $Periode = 0;
                                foreach ($dtNum as $q) {
                                    if ($xLastID <> $q->idshift) {
                                        $Periode++;
                                    }
                                ?>
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <td><?= $q->jamselesai ?></td>
                                        <td><?= $q->idshift ?></td>
                                        <td><?= $Periode ?></td>
                                        <td><?= $q->number ?></td>
                                    </tr>
                                <?php } ?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>