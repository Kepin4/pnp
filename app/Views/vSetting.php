<body class="g-sidenav-show bg-gray-100">
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg" style=" top: 25px;">
        <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
            <div class="container-fluid py-1 px-3">
                <nav aria-label="breadcrumb">
                    <h6 class="font-weight-bolder mb-0">Setting</h6>
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

        <div class="container-fluid py-4 d-flex">
            <form action="<?= base_url('../CAdmin/SaveSetting') ?>" method="POST">
                <div class="card p-4" style="width: auto; max-width: 450px;">
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex mb-2 pl-2">
                                <img src="<?= base_url('../../img/' . session('Comp')->logo) ?>" alt="" style="width: 100px; height: 100px;border: 1px solid gray; border-radius: 5px;" class="p-3">
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="d-flex mt-4 mb-2">
                                <input type="text" name="txtnama" id="txtnama" class="form-control ml-2 w-100" value="<?= (!session('cache') ? session('Comp')->nama ?? '' : session('cache')->Nama ?? ''); ?>" placeholder="Nama WEB" required <?= !(session('level') >= 1 && session('level') <= 2) ? 'disabled' : '' ?>>
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="txtnohp" style="color: red; font-size: 10px">* Format +62</label>
                            <div class="d-flex mb-2">
                                <input type="text" name="txtnohp" id="txtnohp" class="form-control ml-2 w-100" value="<?= (!session('cache') ? session('Comp')->nohp ?? '' : session('cache')->NoHp) ?? '' ?>" placeholder="Nomor Telp ADMIN" required <?= !(session('level') >= 1 && session('level') <= 2) ? 'disabled' : '' ?>>
                            </div>
                        </div>

                        <?php if (session('idUser') == 1) { ?>
                            <div class="col-12 pl-3 pt-2">
                                <input name="chkWizard" type="checkbox" <?= (session('Comp')->isWizard ?? false) ? "checked" : "" ?>> Wizard User
                            </div>

                            <div class="col-12 pl-3">
                                <input name="UserWizard" list="dtUser" type="text" value="<?= (session('Comp')->userWizard ?? 0) ?>" class="form-control" placeholder="Wizard User">
                            </div>

                            <datalist id="dtUser">
                                <?php foreach ($dtUser as $qUser) {
                                    echo '<option value="' . $qUser->username . '">' . $qUser->id . '</option>';
                                } ?>
                            </datalist>
                        <?php } ?>


                        <div class="col-12">
                            <label class="mt-2" style="font-size: 10px">Default Confirmation Timer</label>
                            <div class="d-flex mb-2">
                                <input type="text" name="txtDefTimer" id="txtDefTimer" class="form-control ml-2 w-100" value="<?= (!session('cache') ? session('Comp')->defTimer : session('cache')->defTimer); ?>" placeholder="Default Timer" required <?= !(session('level') >= 1 && session('level') <= 2) ? 'disabled' : '' ?>> <span class="p-2 pt-1">Second's</span>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="mt-2" style="font-size: 10px">Default Placement Time</label>
                            <div class="d-flex mb-2">
                                <input type="text" name="txtPlacementTime" id="txtPlacementTime" class="form-control ml-2 w-100" value="<?= (!session('cache') ? session('Comp')->PlacementTime : session('cache')->PlacementTime); ?>" placeholder="Default Timer" required <?= !(session('level') >= 1 && session('level') <= 2) ? 'disabled' : '' ?>> <span class="p-2 pt-1">Second's</span>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="mt-2" style="font-size: 10px">Limit Saldo Placement</label>
                            <div class="d-flex mb-2">
                                <input type="text" name="txtLimitSaldo" id="txtLimitSaldo" class="form-control ml-2 w-100" value="<?= (!session('cache') ? session('Comp')->limitsaldo : session('cache')->limitsaldo); ?>" placeholder="Limit Saldo" required <?= !(session('level') >= 1 && session('level') <= 2) ? 'disabled' : '' ?>>
                            </div>
                        </div>

                        <?php if (session('level') == 1) { ?>
                            <div class="col-12 pl-3">
                                <a href="<?= base_url('../CAdmin/ResetLimitKomisi'); ?>"><button type="button" class="btn btn-sm mt-4 btn-outline-secondary">Reset Komisi Limit Bulanan</button></a>
                            </div>

                            <div class="col-12 pl-3">
                                <a href="#" onclick="confirmClearData()">
                                    <button id="btntruncate" type="button" class="btn btn-sm mt-4 btn-outline-danger">Clear Data</button>
                                </a>
                            </div>
                        <?php } ?>


                        <div class="col-5">
                            <label class="mt-4" style="font-size: 10px">Recommend Amount</label>
                            <div class="d-flex mb-2">
                                <label for="" class="mt-2 w-10" style="font-size: 10px;">1.</label>
                                <input type="text" name="txtRecNum1" id="txtRecNum1" class="form-control ml-2 w-50" value=<?= (!session('cache') ? $qRecNum[1] : session('cache')->RecNum[1]) ?> min=0 max=0>
                            </div>
                            <div class="d-flex mb-2">
                                <label for="" class="mt-2 w-10" style="font-size: 10px;">2.</label>
                                <input type="text" name="txtRecNum2" id="txtRecNum2" class="form-control ml-2 w-50" value=<?= (!session('cache') ? $qRecNum[2] : session('cache')->RecNum[2]) ?> min=0 max=0>
                            </div>
                            <div class="d-flex mb-2">
                                <label for="" class="mt-2 w-10" style="font-size: 10px;">3.</label>
                                <input type="text" name="txtRecNum3" id="txtRecNum3" class="form-control ml-2 w-50" value=<?= (!session('cache') ? $qRecNum[3] : session('cache')->RecNum[3]) ?> min=0 max=0>
                            </div>
                            <div class="d-flex mb-2">
                                <label for="" class="mt-2 w-10" style="font-size: 10px;">4.</label>
                                <input type="text" name="txtRecNum4" id="txtRecNum4" class="form-control ml-2 w-50" value=<?= (!session('cache') ? $qRecNum[4] : session('cache')->RecNum[4]) ?> min=0 max=0>
                            </div>
                            <div class="d-flex mb-2">
                                <label for="" class="mt-2 w-10" style="font-size: 10px;">5.</label>
                                <input type="text" name="txtRecNum5" id="txtRecNum5" class="form-control ml-2 w-50" value=<?= (!session('cache') ? $qRecNum[5] : session('cache')->RecNum[5]) ?> min=0 max=0>
                            </div>
                        </div>

                        <div class="col-5">
                            <label class="mt-4" style="font-size: 10px">Kode Depan Transaksi</label>
                            <div class="d-flex mb-2">
                                <label for="" class="mt-2 w-70" style="font-size: 10px;">Topup :</label>
                                <input type="text" name="txtKodeDepanTopup" id="txtKodeDepanTopup" class="form-control ml-2 w-50" placeholder="<?= $KodeDepan['Topup'] ?>" value='<?= (!session('cache') ? $KodeDepan['Topup'] : session('cache')->$KodeDepan['Topup']) ?>' <?= !(session('level') >= 1 && session('level') <= 2) ? 'disabled' : '' ?> required>
                            </div>
                            <div class="d-flex mb-2">
                                <label for="" class="mt-2 w-70" style="font-size: 10px;">Sales :</label>
                                <input type="text" name="txtKodeDepanSales" id="txtKodeDepanSales" class="form-control ml-2 w-50" placeholder="<?= $KodeDepan['Sales'] ?>" value='<?= (!session('cache') ? $KodeDepan['Sales'] : session('cache')->$KodeDepan['Sales']) ?>' <?= !(session('level') >= 1 && session('level') <= 2) ? 'disabled' : '' ?> required>
                            </div>
                            <div class="d-flex mb-2">
                                <label for="" class="mt-2 w-70" style="font-size: 10px;">Placement :</label>
                                <input type="text" name="txtKodeDepanPlacement" id="txtKodeDepanPlacement" class="form-control ml-2 w-50" placeholder="<?= $KodeDepan['Placement'] ?>" value='<?= (!session('cache') ? $KodeDepan['Placement'] : session('cache')->$KodeDepan['Placement']) ?>' <?= !(session('level') >= 1 && session('level') <= 2) ? 'disabled' : '' ?> required>
                            </div>
                            <div class="d-flex mb-2">
                                <label for="" class="mt-2 w-70" style="font-size: 10px;">Withdraw :</label>
                                <input type="text" name="txtKodeDepanWithdraw" id="txtKodeDepanWithdraw" class="form-control ml-2 w-50" placeholder="<?= $KodeDepan['Withdraw'] ?>" value='<?= (!session('cache') ? $KodeDepan['Withdraw'] : session('cache')->$KodeDepan['Withdraw']) ?>' <?= !(session('level') >= 1 && session('level') <= 2) ? 'disabled' : '' ?> required>
                            </div>
                            <div class="d-flex mb-2">
                                <label for="" class="mt-2 w-70" style="font-size: 10px;">CheckIn :</label>
                                <input type="text" name="txtKodeDepanCheckIn" id="txtKodeDepanCheckIn" class="form-control ml-2 w-50" placeholder="<?= $KodeDepan['CheckIn'] ?>" value='<?= (!session('cache') ? $KodeDepan['CheckIn'] : session('cache')->$KodeDepan['CheckIn']) ?>' <?= !(session('level') >= 1 && session('level') <= 2) ? 'disabled' : '' ?> required>
                            </div>
                            <div class="d-flex mb-2">
                                <label for="" class="mt-2 w-70" style="font-size: 10px;">Prize :</label>
                                <input type="text" name="txtKodeDepanPrize" id="txtKodeDepanPrize" class="form-control ml-2 w-50" placeholder="<?= $KodeDepan['Prize'] ?>" value='<?= (!session('cache') ? $KodeDepan['Prize'] : session('cache')->$KodeDepan['Prize']) ?>' <?= !(session('level') >= 1 && session('level') <= 2) ? 'disabled' : '' ?> required>
                            </div>
                            <div class="d-flex mb-2">
                                <label for="" class="mt-2 w-70" style="font-size: 10px;">Commission :</label>
                                <input type="text" name="txtKodeDepanCommission" id="txtKodeDepanCommission" class="form-control ml-2 w-50" placeholder="<?= $KodeDepan['Commission'] ?>" value='<?= (!session('cache') ? $KodeDepan['Commission'] : session('cache')->$KodeDepan['Commission']) ?>' <?= !(session('level') >= 1 && session('level') <= 2) ? 'disabled' : '' ?> required>
                            </div>
                        </div>


                        <div class="col-12">
                            <div class="d-flex mt-4 mb-2">
                                <button class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div>
                <form action="<?= base_url('../CAdmin/SaveInformasi') ?>" method="POST">
                    <div class="card p-4 ml-5" style="width: auto; max-width: 450px;">
                        <div class="row p-2">
                            <label for="meInformasi" class="p-0">Informasi</label>
                            <textarea name="meInformasi" id="meInformasi" class="form-control mt-0" style="min-height: 150px;"><?= $Informasi ?></textarea>
                        </div>
                        <div class="row p-2">
                            <label for="meCaraMain" class="p-0">Cara Main</label>
                            <textarea name="meCaraMain" id="meCaraMain" class="form-control mt-0" style="min-height: 150px;"><?= $CaraMain ?></textarea>
                        </div>
                        <button class="btn btn-sm btn-primary w-30 p-2">Save</button>
                    </div>
                </form>

                <?php if (session('idUser') == 1) { ?>
                    <button id="btnRegenerate" class="btn btn-sm btn-primary w-max-[450px] mx-5 my-2" onclick="reGenerateCode(this)">Generate New Activation Key</button>
                <?php } ?>
            </div>
        </div>
    </main>
</body>

<script>
    function reGenerateCode(btn) {
        $(btn).prop('disabled', true)
        $.ajax({
            url: '<?= base_url('../CTools/regenerateActivationKey') ?>',
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.status == 200) {
                    fAlert(`5|Success Generate New Activation Key = ${res.data.key}`)
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                } else {
                    fAlert(`3|${res.message}`)
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                }
                $(btn).prop('disabled', false)
            },
            error: function(err) {
                console.error(err)
                fAlert(`3|${err}`)
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
                $(btn).prop('disabled', false)
            },

        })
    }

    function confirmClearData() {
        var userConfirmation = confirm("Are you sure you want to clear the data?");
        if (userConfirmation) {
            window.location.href = "<?= base_url('../CAdmin/ResetData'); ?>";
        }
    }
</script>