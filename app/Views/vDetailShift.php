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
                    <h6 class="font-weight-bolder mb-0">Detail Shift Report</h6>
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
                            <div class="row">
                                <div class="col-7">
                                    <h4 class="pt-4">Detail Shift Report</h4>
                                </div>
                            </div>
                        </div>



                        <div class="card-body px-0 pt-0 pb-2">
                            <div class="table-responsive p-0 datatable-minimal p-3">
                                <table class="table align-items-center mb-0 display" id="tblTrans">
                                    <thead>
                                        <tr>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">#</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Number</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Start By</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Start Date</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Update By</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Update Date</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total Placement</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total Win</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total Cashback</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php $i = 1;
                                        foreach ($dtDetail as $q) { ?>
                                            <tr>
                                                <td class="align-middle text-center">
                                                    <p class="text-xs"><?= $i++ ?></p>
                                                </td>

                                                <td class="align-middle text-center">
                                                    <p class="text-s font-weight-bold"><?= $q->Number ?></p>
                                                </td>

                                                <td class="align-middle text-center">
                                                    <p class="text-xs"><?= $q->StartBy ?></p>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <p class="text-xs">
                                                        <?php $xDt = new DateTime($q->StartDate);
                                                        echo $xDt->format('Y-M-d') . "<br>";
                                                        echo $xDt->format('h:i:s A');
                                                        ?>
                                                    </p>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <p class="text-xs"><?= $q->CloseBy ?></p>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <p class="text-xs">
                                                        <?php $xDt = new DateTime($q->CloseDate);
                                                        echo $xDt->format('Y-M-d') . "<br>";
                                                        echo $xDt->format('h:i:s A');
                                                        ?>
                                                    </p>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <p class="text-xs"><?= $q->TotalPlacement ?></p>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <p class="text-xs"><?= $q->TotalWin ?></p>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <p class="text-xs"><?= $q->TotalCashback ?></p>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <p class="text-xs"><?= $q->TotalPlacement - $q->TotalWin - $q->TotalCashback ?></p>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="text-center">
                                            <th colspan="6">TOTAL</td>
                                                <?php
                                                $TotalPlacement = array_sum(array_column($dtDetail, 'TotalPlacement'));
                                                $TotalWin = array_sum(array_column($dtDetail, 'TotalWin'));
                                                $TotalCashback = array_sum(array_column($dtDetail, 'TotalCashback'));
                                                $Total = array_sum(array_column($dtDetail, 'Total'));
                                                ?>
                                            <th style="color: <?= $TotalPlacement >= 0 ? 'green' : 'red' ?>"><?= number_format($TotalPlacement, 2, ',', '.') ?> </td>
                                            <th style="color: <?= $TotalWin >= 0 ? 'green' : 'red' ?>"><?= number_format($TotalWin, 2, ',', '.') ?> </td>
                                            <th style="color: <?= $TotalCashback >= 0 ? 'green' : 'red' ?>"><?= number_format($TotalCashback, 2, ',', '.') ?> </td>
                                            <th style="color: <?= $Total >= 0 ? 'green' : 'red' ?>"><?= number_format($Total, 2, ',', '.') ?> </td>
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
    })
</script>