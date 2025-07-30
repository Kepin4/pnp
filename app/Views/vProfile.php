<style>
    .overlay {
        width: 100vw;
        height: 100vh;
        top: 0;
        left: 0;
        z-index: 99999;
        background-color: rgba(0, 0, 0, 0.5);
        position: fixed;
        display: none;
        justify-content: center;
        align-items: center;
    }

    .modal-card {
        width: 5%;
        max-width: 300px;
    }

    .close-btn {
        font-size: 24px;
        font-weight: bold;
        cursor: pointer;
        float: right;
    }

    .input-field {
        width: 100%;
        padding: 10px;
        margin-top: 10px;
        margin-bottom: 10px;
        box-sizing: border-box;
    }
</style>

<body class="g-sidenav-show bg-gray-100">
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg" style=" top: 25px;">
        <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
            <div class="container-fluid py-1 px-3">
                <nav aria-label="breadcrumb">
                    <h6 class="font-weight-bolder mb-0">Profile</h6>
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
                <div class="col-lg-5 mb-lg-0 mb-4">
                    <div class="card z-index-2">
                        <div class="card-body p-3">
                            <h6>Summary</h6>
                            <div class="bg-gradient-dark border-radius-lg py-3 pe-1 mb-3">
                                <div class="chart">
                                    <canvas id="chart-bars" class="chart-canvas" height="170"></canvas>
                                </div>
                            </div>
                            <h6 class="ms-2 mt-4 mb-0">Total</h6>
                            <div class="container border-radius-lg">
                                <div class="row">
                                    <div class="col-6 py-3 ps-0">
                                        <div class="d-flex mb-2">
                                            <p class="text-xs mt-1 mb-0 font-weight-bold">Saldo</p>
                                        </div>
                                        <h4 class="font-weight-bolder"><?= number_format($saldo, 2, ',', '.') ?></h4>
                                    </div>
                                    <div class="col-6 py-3 ps-0">
                                        <div class="d-flex mb-2">
                                            <p class="text-xs mt-1 mb-0 font-weight-bold">Placement</p>
                                        </div>
                                        <h4 class="font-weight-bolder"><?= number_format($play, 2, ',', '.') ?></h4>
                                    </div>
                                    <div class="col-6 py-3 ps-0">
                                        <div class="d-flex mb-2">
                                            <p class="text-xs mt-1 mb-0 font-weight-bold">CashBack</p>
                                        </div>
                                        <h4 class="font-weight-bolder"><?= number_format($cashback, 2, ',', '.') ?></h4>
                                    </div>
                                    <div class="col-6 py-3 ps-0">
                                        <div class="d-flex mb-2">
                                            <p class="text-xs mt-1 mb-0 font-weight-bold">Top Up</p>
                                        </div>
                                        <h4 class="font-weight-bolder"><?= number_format($topup, 2, ',', '.') ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-lg-0 mb-4">
                    <div class="card z-index-2">
                        <div class="card-body p-3">
                            <h6> <?= (session('level') >= 1 && session('level') <= 3) ? 'Profile User,' : 'Welcome,' ?> <b><?= $name ?></b></h6>
                            <div class="container border-radius-lg">
                                <div class="row">
                                    <div class="col-8">
                                        <div class="d-flex mb-2">
                                            <p class="text-xs mt-1 mb-0 font-weight-bold">Saldo</p>
                                        </div>
                                        <h4 class="font-weight-bolder"><?= number_format($saldo, 2, ',', '.') ?></h4>
                                    </div>
                                    <div class="col-4 pt-4 text-end">
                                        <button id="btnReqWithdraw" class="btn btn-sm btn-outline-primary w-100 px-1" <?= (session('level') >= 1 && session('level') <= 3) ? 'disabled' : '' ?>>Withdraw</button>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-8 ">
                                        <div class="d-flex mb-2">
                                            <p class="text-xs mt-1 mb-0 font-weight-bold">Placement</p>
                                        </div>
                                        <h4 class="font-weight-bolder"><?= number_format($play, 2, ',', '.') ?></h4>
                                    </div>
                                    <div class="col-4 pt-4 text-end">
                                        <button id="btnReqTopup" class="btn btn-sm btn-outline-primary w-100 px-1" <?= (session('level') >= 1 && session('level') <= 3) ? 'disabled' : '' ?>>Topup</button>
                                    </div>
                                </div>

                                <div class="row mt-5">
                                    <div class="col-6">
                                        <a href="<?= base_url("/CPlay/Play") ?>">
                                            <button class="btn btn-sm btn-primary w-100 pl-2 pr-2" <?= (session('level') >= 1 && session('level') <= 3) ? 'disabled' : '' ?>>Placement</button>
                                        </a>
                                    </div>
                                    <div class="col-6">
                                        <button class="btn btn-sm btn-outline-primary w-100 pl-2 pr-2" onclick="changePass(<?= $account_id ?>)">Change Pass</button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>

<div id="pnlReqTopup" class="overlay">
    <div class="card w-50 modal-card">
        <div class="card-body p-3">
            <form id="frmReqTopup" action="<?= base_url('/CTrans/RequestTopUp') ?>" method="POST">
                <div class="modal-content">
                    <span id="btnCloseReqTopup" class="close-btn">&times;</span>
                    <h5 id="lblKeterangan">Enter a Topup Nominal</h5>
                    <input type="number" id="txtNominal" name="txtNominal" placeholder="Enter value" class="input-field" value=0 min=0>
                    <div id="xKetWD" style="display: none">
                        <label for="txtNoref">Keterangan</label>
                        <input type="text" id="txtNoref" name="txtNoref" placeholder="Nama Bank - No Rek" class="input-field mt-0">
                    </div>
                    <button class="btn btn-outline-primary w-503" style="align-self : flex-end;">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="pnlChangePass" class="overlay">
    <div class="card w-50 modal-card">
        <div class="card-body p-3">
            <form id="frmChangePass" action="<?= base_url('../CData/ChangePass') ?>" method="POST">
                <input type="hidden" id="txtID" name="txtID">
                <div class="modal-content">
                    <span id="btnCloseChangePass" class="close-btn">&times;</span>
                    <h5>Enter New Password</h5>
                    <input type="password" id="txtPass" name="txtPass" placeholder="Password" class="input-field">
                    <button class="btn btn-outline-primary w-503" style="align-self : flex-end;">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {
        const btnReqTopup = $('#btnReqTopup');
        const btnCloseReqTopup = $('#btnCloseReqTopup');
        const pnlReqTopup = $('#pnlReqTopup');

        btnReqTopup.on('click', function() {
            pnlReqTopup.css('display', 'flex');
            $('#xKetWD').css('display', 'none');
            $('#lblKeterangan').text("Enter Topup Nominal");
            $('#frmReqTopup').attr('action', "<?= base_url('/CTrans/RequestTopUp') ?>");
        });

        $('#btnReqWithdraw').on('click', function() {
            pnlReqTopup.css('display', 'flex');
            $('#xKetWD').css('display', 'block');
            $('#lblKeterangan').text("Enter Withdraw Nominal");
            $('#frmReqTopup').attr('action', "<?= base_url('/CTrans/RequestWithdraw') ?>");
        });

        $('#btnCloseChangePass').on('click', function() {
            $('#pnlChangePass').css('display', 'none');
        })

        btnCloseReqTopup.on('click', function() {
            pnlReqTopup.hide();
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

        const xReqTopup = '<?= session()->getFlashdata('ReqTopup') ?>';
        if (xReqTopup) {
            window.open(xReqTopup, '_blank');
        }


        <?php if (session()->has('newWithDraw')) { ?>
            pnlReqTopup.css('display', 'flex');
            $('#lblKeterangan').text("Enter Withdraw Nominal");
            $('#frmReqTopup').attr('action', "<?= base_url('/CTrans/RequestWithdraw') ?>");
        <?php } ?>


        <?php if (session()->has('newTopup')) { ?>
            pnlReqTopup.css('display', 'flex');
            $('#lblKeterangan').text("Enter Topup Nominal");
            $('#frmReqTopup').attr('action', "<?= base_url('/CTrans/RequestTopUp') ?>");
        <?php } ?>

    });





    function changePass($id) {
        $('#txtID').val($id);
        $("#pnlChangePass").css('display', 'flex');
    }
</script>

<script src="<?= base_url('template/assets/js/plugins/chartjs.min.js') ?>"></script>
<script>
    var ctx = document.getElementById("chart-bars").getContext("2d");
    new Chart(ctx, {
        type: "bar",
        data: {
            labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
            datasets: [{
                label: "Sales",
                tension: 0.4,
                borderWidth: 0,
                borderRadius: 4,
                borderSkipped: false,
                backgroundColor: "#fff",
                data: [<?php echo implode(',', $win); ?>],
                maxBarThickness: 6
            }, ],
        },

        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                }
            },
            interaction: {
                intersect: false,
                mode: 'index',
            },
            scales: {
                y: {
                    grid: {
                        drawBorder: false,
                        display: false,
                        drawOnChartArea: false,
                        drawTicks: false,
                    },
                    ticks: {
                        suggestedMin: 0,
                        suggestedMax: 500,
                        beginAtZero: true,
                        padding: 15,
                        font: {
                            size: 14,
                            family: "Open Sans",
                            style: 'normal',
                            lineHeight: 2
                        },
                        color: "#fff"
                    },
                },
                x: {
                    grid: {
                        drawBorder: false,
                        display: false,
                        drawOnChartArea: false,
                        drawTicks: false
                    },
                    ticks: {
                        display: false
                    },
                },
            },
        },
    });
</script>