<style>
    .overlay {
        width: 100vw;
        height: 100vh;
        top: 0;
        left: 0;
        z-index: 99999;
        background-color: rgba(0, 0, 0, 0.5);
        position: fixed;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .modal-card {
        width: 75%;
        max-width: 600px;
    }

    .close-btn {
        font-size: 24px;
        font-weight: bold;
        cursor: pointer;
        float: right;
    }
</style>
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
                    <h6 class="font-weight-bolder mb-0">Play</h6>
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

            <div class="row mt-4">
                <div class="card ml-2 px-4 py-3 text-align-center align-items-center w-auto" style="color: white; background-color: #161616" id="pnlTimer">
                    <span style="font-size:30px; font-family: monospace; font-weight: bold;" id="lblTimer">
                        00:00
                    </span>
                </div>

                <button class="my-0 ml-2 px-4 py-2 btn btn-primary w-auto" onclick="window.location.reload();">
                    <span style="color: white; font-weight:bold; font-family: monospace; font-size: 30px;">
                        <i class="fa fa-refresh"></i>
                    </span>
                </button>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <div class="tab-content">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="manual-tab" data-toggle="tab" href="#manual" role="tab" aria-controls="manual" aria-selected="true">Manual</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="group-tab" data-toggle="tab" href="#group" role="tab" aria-controls="group" aria-selected="false">Group</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="group-tab" data-toggle="tab" href="#banding1" role="tab" aria-controls="banding1" aria-selected="false">1 : 1</a>
                            </li>
                        </ul>

                        <div class="tab-pane fade show active" id="manual" role="tabpanel" aria-labelledby="manual-tab">
                            <div class="card mb-4" style="border-top-left-radius: 0px;">
                                <div class="card-header m-1 row">
                                </div>
                                <div class="card-body px-0 pt-0 pb-2">
                                    <form id="FormPlacement" action="<?= base_url('/CPlay/SavePlay') ?>" method="POST">
                                        <div class="table-responsive p-0">
                                            <table class="table align-items-center mb-0  text-center">
                                                <thead>
                                                    <tr>
                                                        <th class="text-uppercase text-primary text-xxs font-weight-bolder opacity-7 w-15">Number</th>
                                                        <th class="text-uppercase text-primary text-xxs font-weight-bolder opacity-7 w-25" style="text-align: left;">Nominal</th>
                                                        <th class="text-uppercase text-primary text-xxs font-weight-bolder opacity-7" colspan="5"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php for ($i = 1; $i <= 24; $i++) { ?>
                                                        <tr>
                                                            <td class="text-uppercase text-secondary font-weight-bolder text-s"> <?= $i ?> </td>
                                                            <td class="text-uppercase text-secondary font-weight-bolder text-s">
                                                                <input id="txtVal<?= $i ?>" name="txtVal<?= $i ?>" type="number" class="form-control form-control-sm" style="" min="0" value="" step="1">
                                                            </td>
                                                            <?php for ($ii = 1; $ii <= 5; $ii++) { ?>
                                                                <td class="text-uppercase text-secondary font-weight-bolder text-s">
                                                                    <div class="form-check" style="text-align: left;">
                                                                        <input type="radio" class="form-check-input" id="radio<?= $i ?>" name="optradio<?= $i ?>" data-id="<?= $i ?>" value="<?= $Req[$ii] ?>" inputmode="numeric">
                                                                        <label class="form-check-label" id="radio<?= $i ?>"><?= $Req[$ii] ?></label>
                                                                    </div>
                                                                </td>
                                                            <?php } ?>
                                                        </tr>
                                                    <?php }; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <button type="button" id="btnSubmit" class="btn btn-sm btn-outline-primary mt-3 ml-5">Submit</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="group" role="tabpanel" aria-labelledby="group-tab">
                            <div class="card mb-4" style="border-top-left-radius: 0px;">
                                <div class="card-header m-1 row">
                                </div>
                                <div class="card-body px-0 pt-0 pb-2">
                                    <form id="frmPlacementGroup" action="<?= base_url('/CPlay/SavePlayGroup') ?>" method="POST">
                                        <div class="table-responsive p-0">
                                            <table class="table align-items-center mb-0  text-center">
                                                <thead>
                                                    <tr>
                                                        <th class="text-uppercase text-primary text-xxs font-weight-bolder opacity-7 w-15">Number</th>
                                                        <th class="text-uppercase text-primary text-xxs font-weight-bolder opacity-7 w-25" style="text-align: left;">Nominal</th>
                                                        <th class="text-uppercase text-primary text-xxs font-weight-bolder opacity-7" colspan="5"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach (['Ganjil', 'Genap', 'Kecil', 'Besar'] as $q) { ?>
                                                        <tr>
                                                            <td class="text-uppercase text-secondary font-weight-bolder text-s"> <?= $q ?> </td>
                                                            <td class="text-uppercase text-secondary font-weight-bolder text-s">
                                                                <input id="txtVal<?= $q ?>" name="txtVal<?= $q ?>" type="number" class="form-control form-control-sm" style="" min="0" step="1" value="">
                                                            </td>
                                                            <?php for ($ii = 1; $ii <= 5; $ii++) { ?>
                                                                <td class="text-uppercase text-secondary font-weight-bolder text-s">
                                                                    <div class="form-check" style="text-align: left;">
                                                                        <input type="radio" class="form-check-input" id="radio<?= $q ?>" name="optradio<?= $q ?>" data-id="<?= $q ?>" value="<?= $Req[$ii] ?>" inputmode="numeric">
                                                                        <label class="form-check-label" id="radio<?= $q ?>"><?= $Req[$ii] ?></label>
                                                                    </div>
                                                                </td>
                                                            <?php } ?>
                                                        </tr>
                                                    <?php }; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <button type="button" id="btnSubmitGroup" class="btn btn-sm btn-outline-primary mt-3 ml-5">Submit</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="banding1" role="tabbanding1" aria-labelledby="group-tab">
                            <div class="card mb-4" style="border-top-left-radius: 0px;">
                                <div class="card-header m-1 row">
                                </div>
                                <div class="card-body px-0 pt-0 pb-2">
                                    <form id="frmPlacementBanding" action="<?= base_url('/CPlay/SavePlayBanding') ?>" method="POST">
                                        <div class="table-responsive p-0">
                                            <table class="table align-items-center mb-0  text-center">
                                                <thead>
                                                    <tr>
                                                        <th class="text-uppercase text-primary text-xxs font-weight-bolder opacity-7 w-15">Number</th>
                                                        <th class="text-uppercase text-primary text-xxs font-weight-bolder opacity-7 w-25" style="text-align: left;">Nominal</th>
                                                        <th class="text-uppercase text-primary text-xxs font-weight-bolder opacity-7" colspan="5"></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach (['GANJIL', 'GENAP', 'KECIL', 'BESAR'] as $q) { ?>
                                                        <tr>
                                                            <td class="text-uppercase text-secondary font-weight-bolder text-s"> <?= $q ?> </td>
                                                            <td class="text-uppercase text-secondary font-weight-bolder text-s">
                                                                <input id="txtVal<?= $q ?>" name="txtVal<?= $q ?>" type="number" class="form-control form-control-sm" style="" min="0" step="1" value="" data-banding="true">
                                                            </td>
                                                            <?php for ($ii = 1; $ii <= 5; $ii++) { ?>
                                                                <td class="text-uppercase text-secondary font-weight-bolder text-s">
                                                                    <div class="form-check" style="text-align: left;">
                                                                        <input type="radio" class="form-check-input" id="radio<?= $q ?>" name="optradio2<?= $q ?>" data-id="<?= $q ?>" value="<?= $Req[$ii] ?>" inputmode="numeric">
                                                                        <label class="form-check-label" id="radio<?= $q ?>"><?= $Req[$ii] ?></label>
                                                                    </div>
                                                                </td>
                                                            <?php } ?>
                                                        </tr>
                                                    <?php }; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <button type="button" id="btnSubmitBanding" class="btn btn-sm btn-outline-primary mt-3 ml-5">Submit</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>



<?php if (session('refused')) { ?>
    <div id="pnlRefused" class="overlay">
        <div class="card modal-card">
            <div class="card-body p-3">
                <div class="modal-content">
                    <span id="btnCloseRefused" class="close-btn">&times;</span>
                    <h4>Placement TerTolak!</h4>
                    <table>
                        <thead>
                            <tr>
                                <td>#</td>
                                <td>Keterangan</td>
                                <td>Number</td>
                                <td>Nominal</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1;
                            foreach (session('refused') as $q) { ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= $q->keterangan ?></td>
                                    <td><?= $q->value->Num ?></td>
                                    <td><?= $q->value->Nominal ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>

                    <button id="btnCloseRef" class="btn btn-primary w-50 mt-5" style="align-self : flex-end;">Close</button>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<div id="pnlConf" class="overlay" style="display: none">
    <div class="card modal-card">
        <div class="card-body p-3">
            <div class="modal-content">
                <span id="btnCloseConf" class="close-btn">&times;</span>
                <h4>Confirmation!</h4>
                <div class="table-container" style="max-height: 300px; overflow-y: auto;">
                    <table class="table align-items-center mb-0 display">
                        <thead>
                            <tr>
                                <td>#</td>
                                <td>Number</td>
                                <td class="text-right">Nominal</td>
                            </tr>
                        </thead>
                        <tbody id="tBodyConf">
                        </tbody>
                        <tfoot>
                            <th class="text-left" colspan="2">Total</th>
                            <th class="text-right"><span id="txtTotalConf"></span></th>
                        </tfoot>
                    </table>
                </div>
                <div class="button-container" style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                    <button id="btnCloseConf2" class="btn btn-primary w-50" style="background-color: red;">Close</button>
                    <button id="btnConfirmConf" class="btn btn-primary w-50">Confirm</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        let xTujuan = "Manual";
        const tBodyConf = $(' #tBodyConf');
        let xTr = "";

        $('#btnSubmit').on('click', function() {
            xTujuan = "Manual";
            tBodyConf.empty();
            let i = 1;
            let xVal = 0;
            for (let ix = 1; ix <= 24; ix++) {
                const inputVal = parseInt($(`#txtVal${ix}`).val(), 10);
                if (inputVal >= 10) {
                    xVal += inputVal;
                    $('#txtTotalConf').text(xVal.toLocaleString(undefined, {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }));
                    const formattedValue = inputVal.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).replace('.', ',');

                    const newRow = `
                        <tr>
                            <td>${i++}</td>
                            <td>${ix}</td>
                            <td class="text-right">${formattedValue}</td>
                        </tr>
                    `;
                    tBodyConf.append(newRow);
                }
            }
            $('#btnConfirmConf').prop('disabled', false);
        });

        $('#btnSubmitGroup').on('click', function() {
            xTujuan = "Group";
            tBodyConf.empty();
            let i = 1;
            let xVal = 0;
            ['Ganjil', 'Genap', 'Kecil', 'Besar'].forEach(function(ix) {
                const inputVal = parseInt($(`#txtVal${ix}`).val(), 10);
                if (inputVal >= 10) {
                    xVal += inputVal * 12;
                    $('#txtTotalConf').text(xVal.toLocaleString(undefined, {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }));
                    const formattedValue = inputVal.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).replace('.', ',');

                    const newRow = `
                        <tr>
                            <td>${i++}</td>
                            <td>${ix}</td>
                            <td class="text-right">${formattedValue}</td>
                        </tr>
                    `;
                    tBodyConf.append(newRow);
                }
            })
            $('#btnConfirmConf').prop('disabled', false);
        });

        $('#btnSubmitBanding').on('click', function() {
            xTujuan = "Banding";
            tBodyConf.empty();
            let i = 1;
            let xVal = 0;
            ['GANJIL', 'GENAP', 'KECIL', 'BESAR'].forEach(function(ix) {
                const inputVal = parseInt($(`#txtVal${ix}`).val(), 10);
                if (inputVal >= 10) {
                    xVal += inputVal;
                    $('#txtTotalConf').text(xVal.toLocaleString(undefined, {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }));
                    const formattedValue = inputVal.toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).replace('.', ',');

                    const newRow = `
                        <tr>
                            <td>${i++}</td>
                            <td>${ix}</td>
                            <td class="text-right">${formattedValue} <span style="font-size: 10px">/12</span></td>
                        </tr>
                    `;
                    tBodyConf.append(newRow);
                }
            })
            $('#btnConfirmConf').prop('disabled', false);
        });


        $('#btnConfirmConf').on('click', function() {
            $('#btnSubmit').attr('type', 'submit');
            $('#btnSubmitGroup').attr('type', 'submit');
            $('#btnSubmitBanding').attr('type', 'submit');

            if (xTujuan === "Manual") {
                $('#btnSubmit').click();
            } else if (xTujuan === "Group") {
                $('#btnSubmitGroup').click();
            } else if (xTujuan === "Banding") {
                $('#btnSubmitBanding').click();
            }

            $(this).prop('disabled', true);
        });

        $('#btnCloseConf2').on('click', function() {
            $('#pnlConf').css('display', 'none');
        })
        $('#btnCloseConf').on('click', function() {
            $('#pnlConf').css('display', 'none');
        })

        $('#pnlConf').on('change', function() {
            if ($(this).css('display') === 'none') {
                $('#btnSubmit').attr('type', 'button');
                $('#btnSubmitGroup').attr('type', 'button');
            } else if ($(this).css('display') === 'flex') {
                $('#btnSubmit').attr('type', 'submit');
                $('#btnSubmitGroup').attr('type', 'submit');
            }
        });

    });
</script>



<script>
    $(document).ready(function() {
        const lblTimer = $('#lblTimer');
        const pnlTimer = $('#pnlTimer');
        const btnSubmit = $('#btnSubmit');
        const FormPlacement = $('#FormPlacement');
        let xNow, xEnd = new Date();
        let intervalId;
        let xintId;
        let isAllowed, RefreshMe = false;

        // Event
        getTimer()


        document.addEventListener("visibilitychange", () => {
            if (document.visibilityState === 'visible') {
                console.log("Welcome back!");
                getTimer();
            }
        });

        FormPlacement.on('submit', function(e) {
            if (!isAllowed) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: 0
                }, 'fast')
                fAlert("3|Waktu Placement sudah habis, Silahkan menunggu sesi berikutnya!");
            }
        });

        $('#frmPlacementGroup').on('submit', function(e) {
            if (!isAllowed) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: 0
                }, 'fast')
                fAlert("3|Waktu Placement sudah habis, Silahkan menunggu sesi berikutnya!");
            }
        });

        btnSubmit.on('click', function() {
            $('#pnlConf').css('display', 'flex');
            // btnSubmit.disabled = true;
        });

        $('#btnSubmitGroup').on('click', function() {
            $('#pnlConf').css('display', 'flex');
        });

        $('#btnSubmitBanding').on('click', function() {
            $('#pnlConf').css('display', 'flex');
        });


        $('#btnCloseRef').on('click', function() {
            $('#pnlRefused').css('display', 'none');
        });

        $('#btnCloseRefused').on('click', function() {
            $('#pnlRefused').css('display', 'none');
        });



        // Function
        function setTimer() {
            if (xNow > xEnd) {
                isAllowed = false;
                lblTimer.text('00:00');
                pnlTimer.css('background-color', 'red');
                clearInterval(intervalId);
                xintId = setInterval(waitRespond, 3000);
            } else {
                if (RefreshMe) {
                    window.location.reload();
                }
                isAllowed = true;
                lblTimer.text(getTimeDifference(xNow, xEnd, "ms"));
                pnlTimer.css('background-color', '#161616');
            }
        }

        function waitRespond() {
            RefreshMe = true
            getTimer();
        }

        function updateTimer() {
            let dateNow = new Date(xNow);
            dateNow.setSeconds(dateNow.getSeconds() + 1);
            xNow = dateNow;
            setTimer();
        }

        function getTimer() {

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
                        clearInterval(xintId);
                        isAllowed = true;
                    } else {
                        fAlert('3|Placement belum dibuka!');
                        isAllowed = false;
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error retrieving timer data:', error);
                    // alert('Error retrieving timer data');
                    isAllowed = false;
                }
            });
        }
    });
</script>






<script>
    const radios = document.querySelectorAll('input[type="radio"]');
    const txtVals = document.querySelectorAll('input[id^="txtVal"]');
    txtVals.forEach(txtVal => {
        txtVal.addEventListener('change', () => {
            checkTxt(txtVal);
        });
    });

    function checkTxt(txtVal) {
        let xvalue = txtVal.value.toString();
        xvalue = xvalue.replace(/[^0-9]/g, '');
        let intValue = parseInt(xvalue, 10);

        if (isNaN(intValue)) {
            intValue = "";
        }

        let xMin = 10;
        // if (txtVal.dataset.banding === "true") {
        //     xMin = 120;
        // }


        let xLimit = <?= (session('Comp')->limitsaldo ?? 0);  ?>;
        if (intValue < xMin) {
            intValue = "";
        } else if (intValue > xLimit) {
            intValue = xLimit;
        }

        txtVal.value = intValue;
    }

    const lastChecked = {};
    radios.forEach(radio => {
        radio.addEventListener('change', () => {
            const idVal = radio.getAttribute('data-id');
            const txtVal = document.getElementById('txtVal' + idVal);
            txtVal.value = radio.value.toString();
            checkTxt(txtVal);
        });

        radio.addEventListener('click', (event) => {
            const idVal = radio.getAttribute('data-id');
            const value = radio.value;
            const txtVal = document.getElementById('txtVal' + idVal);
            if (!lastChecked[idVal]) {
                lastChecked[idVal] = {};
            }

            if (lastChecked[idVal][value] === true) {
                radio.checked = false;
                lastChecked[idVal][value] = false;
                txtVal.value = "";
            } else {
                Object.keys(lastChecked[idVal]).forEach(val => {
                    lastChecked[idVal][val] = false;
                    const otherRadio = document.querySelector(`input[type="radio"][data-id="${idVal}"][value="${val}"]`);
                    if (otherRadio) otherRadio.checked = false;
                });

                radio.checked = true;
                lastChecked[idVal][value] = true;
                txtVal.value = value;
                checkTxt(txtVal);
            }
        });
    });
</script>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<?php
if (!isset($_SESSION['xAllowSave'])) {
    session()->set('xAllowSave', true);
    echo "<script>console.log('Success')</script>";
} else {
    echo "<script>console.log('Session variable already set.')</script>";
}

?>