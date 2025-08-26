<style>
    input[type="number"]::-webkit-outer-spin-button,
    input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
</style>

<?php session()->set('xAllowSave', true); ?>

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
                                <div class="col-6 m-0" style="text-align : right;">
                                    <a href="<?= base_url('../../CNumber/Number') ?>">
                                        <button class="btn btn-sm btn-outline-primary">View More...</button>
                                    </a>
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
        </div>


        <div class="container-fluid p-4 pt-1" style="font-size:30px; font-family:monospace">
            <div id="pnlSession" class="card p-3 w-100" style="max-width: 400px;">
                <div class="p-3">
                    <h4>ID Shift : <span><?= $idShift ?></span></h4>
                    <h4>Periode : <span><?= $SesiCount ?></span></h4>
                    <h4>Timer : <span id="lblTimer">00:00</span></h4>
                    <div class="d-flex flex-col gap-2">
                        <button id="btnUpdNum" class="btn btn-lg btn-primary mt-4 w-55 px-2" disabled>UPDATE NUMBER</button>
                        <button class="btn btn-lg btn-danger mt-4 w-55 px-2" onclick="undoLastNumber()">UNDO</button>
                    </div>
                    <a href="<?= base_url('../../CNumber/CloseShift') ?>"><button id="btnCloseShift" class="btn btn-lg btn-primary mt-4 w-44 px-3" hidden>Close Shift</button></a>
                </div>
            </div>
        </div>
    </main>
</body>

<div id="pnlUpdNum" style="width: 100vw; height: 100vh; top: 0; left: 0; z-index: 99999; background-color: rgba(0, 0, 0, 0.5); position: fixed; justify-content: center; align-items: center; display: none;">
    <form id="frmUpdNum" action="<?= base_url('../../CNumber/UpdateNum') ?>" method="post">
        <input type="hidden" id="chkIsLast" name="chkIsLast" value='false'>
        <div class="card" style="width: 250px; height: 250px;">
            <span id="btnCloseUpdNum" style="font-size: 35px; height: 15px; left: 15px; font-weight: bold; cursor: pointer; float: right; position: absolute; padding-top: 3px;">&times;</span>
            <span id="Timer15s" style="font-size: 35px; height: 15px; right: 15px; font-weight: bold; cursor: pointer; float: left; position: absolute; padding-top: 3px;">
                <?= session('Comp')->defTimer ?>
            </span>
            <div class="card-body p-5 align-content-center pt-6">
                <strong>
                    <input id="txtUpdNum" name="txtUpdNum" type="number" value=0 min=1 max=24 class="form-control text-align-center text-center" style="font-size: 50px;" required>
                </strong>
            </div>
            <div style="display: flex;">
                <button id="btnSubmitUpdNum" type="submit" class="btn btn-lg btn-primary m-0 px-1 w-50" style="border-radius: 0; border-bottom-left-radius: 15px; border-right: 0.5px solid white;">
                    UPDATE
                </button>
                <button id="btnSubmitLastNum" type="button" class="btn btn-lg btn-primary m-0 px-1 w-50" style="border-radius: 0; border-bottom-right-radius: 15px; border-left: 0.5px solid white;">
                    Last Number
                </button>
            </div>
        </div>
    </form>
</div>

<div id="pnlReConfirm" style="width: 100vw; height: 100vh; top: 0; left: 0; z-index: 99999; background-color: rgba(0, 0, 0, 0.5); position: fixed; justify-content: center; align-items: center; display: none;">
    <div class="card" style="width: 250px; height: 250px;">
        <div class="card-body p-5 align-content-center">
            <strong>
                <label for="" style="font-size: 20px;">LAST NUMBER?</label>
            </strong>
        </div>
        <div style="display: flex;">
            <button id="btnConfirm" type="submit" class="btn btn-lg btn-primary m-0 px-1 w-50" style="border-radius: 0; border-bottom-left-radius: 15px; border-right: 0.5px solid white;">
                CONFIRM
            </button>
            <button id="btnUnConfrim" type="button" class="btn btn-lg btn-primary m-0 px-1 w-50" style="border-radius: 0; border-bottom-right-radius: 15px; border-left: 0.5px solid white; background-color: red;">
                CANCEL
            </button>
        </div>
    </div>
</div>



<script>
    const lblTimer = $('#lblTimer')
    let isAllowed = true;
    let xCounting = false;
    let xCD;
    let intervalId;
    let xNow, xEnd = new Date();
    let xDefTimer = <?= session('Comp')->defTimer ?>;
    let xTimer = xDefTimer;

    document.addEventListener("visibilitychange", () => {
        if (document.visibilityState === 'visible') {
            console.log("Welcome back!");
            readTimer();
        }
    });

    $(document).ready(function() {
        setBtnUpdNum()

        $('#btnUpdNum').on('click', function() {
            $('#pnlUpdNum').css('display', 'flex');
        });
        $('#btnCloseUpdNum').on('click', function() {
            $('#pnlUpdNum').css('display', 'none');
        });

        readTimer();


        $('#btnSubmitUpdNum').on('click', function(e) {
            e.preventDefault();

            if (!isAllowed) {
                $('html, body').animate({
                    scrollTop: 0
                }, 'fast')
                fAlert("3|Tidak dapat Update Number!");
                return;
            }

            if ($('#txtUpdNum').val() < 0 || $('#txtUpdNum').val() > 24) {
                $('html, body').animate({
                    scrollTop: 0
                }, 'fast')
                fAlert("3|Mohon isi Valid Number (1-24) !");
                return;
            }

            if (!xCounting) {
                xCounting = true;

                $('#txtUpdNum').prop('disabled', true);
                $('#btnSubmitUpdNum').css('background-color', 'red');
                $('#btnSubmitUpdNum').text('UNDO');
                clearInterval(xCD);
                xCD = setInterval(CountDownUpd, 1000);
            } else {

                clearInterval(xCD);
                xCounting = false;
                $('#txtUpdNum').prop('disabled', false);
                $('#btnSubmitUpdNum').css('background-color', '');
                $('#btnSubmitUpdNum').text('UPDATE');
                $('#pnlUpdNum').css('display', 'none');
                $('#chkIsLast').val('false');
                xTimer = xDefTimer;
                CountDownUpd()

                $('html, body').animate({
                    scrollTop: 0
                }, 'fast')
                fAlert("2|Berhasil Undo UPDATE NUMBER!");
            }
        });

        $('#btnSubmitLastNum').on('click', function(e) {
            e.preventDefault();
            $('#pnlReConfirm').css('display', 'flex');
        })

        $('#btnConfirm').on('click', function(e) {
            $('#chkIsLast').val('true');
            $('#pnlReConfirm').css('display', 'none');
            $('#btnSubmitUpdNum').click();
        })

        $('#btnUnConfrim').on('click', function() {
            $('#pnlReConfirm').css('display', 'none');
            $('#chkIsLast').val('false');
        });

        const form = document.querySelector('form');
        form.addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
            }
        });

    });

    function readTimer() {
        $.ajax({
            url: '<?= base_url('../CPlay/getTimer') ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status !== 'NO SESSION') {
                    xNow = new Date(response.JamSekarang);
                    xEnd = new Date(response.JamSelesai);
                    clearInterval(intervalId);
                    intervalId = setInterval(updateTimer, 1000);
                } else {
                    isAllowed = true;
                    setBtnUpdNum()
                }
            },
            error: function(xhr, status, error) {
                console.log('Error retrieving timer data:', error);
                isAllowed = true;
                setBtnUpdNum()
            }
        });
    }

    function CountDownUpd() {
        $('#Timer15s').text(xTimer--);
        if (xTimer < 0) {
            clearInterval(xCD);
            $('#Timer15s').text(0);
            $('#btnSubmitUpdNum').prop('disabled', true);
            $('#btnSubmitLastNum').prop('disabled', true);
            $('#txtUpdNum').prop('disabled', false);
            $('#frmUpdNum').submit();
        }
    }

    function setBtnUpdNum() {
        $('#btnUpdNum').prop('disabled', !isAllowed);
        if (!isAllowed) {
            $('#btnUpdNum').text('🚫 UPDATE NUMBER 🚫');
        } else {
            $('#btnUpdNum').text('UPDATE NUMBER');
        }
    }

    function setTimer() {
        if (xNow > xEnd) {
            isAllowed = true;
            lblTimer.text('00:00');
            lblTimer.css('color', 'red');
            // $('#btnUpdNum').css('background-color', 'red');
        } else {
            isAllowed = false;
            lblTimer.text(getTimeDifference(xNow, xEnd, "ms"));
            lblTimer.css('color', '#67748E');
            // $('#btnUpdNum').css('background-color', '');
        }
        setBtnUpdNum()
    }

    function updateTimer() {
        let dateNow = new Date(xNow);
        dateNow.setSeconds(dateNow.getSeconds() + 1);
        xNow = dateNow;
        setTimer();
    }
</script>




<script>
    function undoLastNumber() {
        if (confirm('Apakah Anda yakin ingin undo nomor terakhir?')) {
            $.ajax({
                url: '<?= base_url('../CNumber/UndoLastNumber') ?>',
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    if (data.success) {
                        fAlert('5|Nomor terakhir berhasil dihapus.');
                        $('html, body').animate({
                            scrollTop: 0
                        }, 'fast')

                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    } else {
                        fAlert('3|Gagal menghapus nomor terakhir: ' + data.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    fAlert('3|Terjadi kesalahan saat menghapus nomor terakhir.');
                }
            });
        }
    }
</script>