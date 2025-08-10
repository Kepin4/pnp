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
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
            <div class="container-fluid py-1 px-3">
                <nav aria-label="breadcrumb">
                    <h6 class="font-weight-bolder mb-0">User List</h6>
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
                                    <h4 class="pt-4">User List</h4>
                                </div>
                                <?php if ((session('level') >= 1 && session('level') <= 4)) { ?>
                                    <div class="col-5 text-end pt-4">
                                        <a href="<?= base_url('../CData/NewUser') ?>">
                                            <button class="btn btn-sm btn-outline-primary">New</button>
                                        </a>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="row mt-2">
                                <div class="col-12">
                                    <div class="filter-box">
                                        <label for="levelFilter" class="form-label mb-1" style="font-size: 12px; font-weight: bold; color: #cb0c9f;">Filter by Level:</label>
                                        <select id="levelFilter" class="form-control filter-input" style="font-size: 12px; padding: 5px;">
                                            <option value="">All Levels</option>
                                            <option value="Maintenance">Maintenance</option>
                                            <option value="Admin">Admin</option>
                                            <option value="Kasir">Kasir</option>
                                            <option value="Agent">Agent</option>
                                            <option value="VIP">VIP</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <div class="card-body px-0 pt-0 pb-2">
                            <div class="table-responsive p-0 datatable-minimal p-3">
                                <table class="table align-items-center mb-0 display" id="tblTrans">
                                    <thead>
                                        <tr>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">#</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Username</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Level</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Atasan</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Saldo</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">CashBack</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Komisi</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Bank</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No. Rek</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama Rek</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i = 1;
                                        foreach ($User as $q) { ?>
                                            <tr>
                                                <td class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2"><?= $i++ ?></td>
                                                <td class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2"><?= $q->username ?></td>
                                                <td class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2"><?= $q->LevelString ?></td>
                                                <td class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2"><?= ($q->level == 5 ? $q->Atasan : '') ?></td>
                                                <td class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2"><?= ($q->status == 5 ? 'Active' : 'Disabled') ?></td>
                                                <td class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2"><?= number_format($q->saldo, '2', ',', '.') ?></td>
                                                <td class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2"><?= $q->cashback ?>%</td>
                                                <td class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2"><?= $q->komisi ?>%</td>
                                                <td class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2"><?= $q->namabank ?: '-' ?></td>
                                                <td class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2"><?= $q->norek ?: '-' ?></td>
                                                <td class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2"><?= $q->namarek ?: '-' ?></td>
                                                <td class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                    <button onclick="ShowDetail(<?= $q->id  ?>)" class="btn btn-outline-primary text-xxs p-2" <?= ((int)session('level') >= (int) $q->level ? 'disabled' : '') ?>>Detail User</button>
                                                    <?php if (!($q->level >= 1 && $q->level <= 3)) { ?>
                                                        <a href="<?= base_url('../CBase/Profile/' . '/' . $q->id) ?> "><button class="btn btn-outline-primary text-xxs p-2">Profile</button></a>
                                                        <?php if (!((int) $q->level == 1)) { ?>
                                                            <?php if ((int) $q->status == 5) { ?>
                                                                <a href="<?= base_url('../CAdmin/DisableUser/' . '/' . $q->id) ?> "><button class="btn btn-outline-primary text-xxs p-2">Disable</button></a>
                                                            <?php } else { ?>
                                                                <a href="<?= base_url('../CAdmin/ReActiveUser/' . '/' . $q->id) ?> "><button class="btn btn-outline-primary text-xxs p-2">Re-Active</button></a>
                                                            <?php } ?>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</body>



<div id="pnlChangePass" style="width: 100vw; height: 100vh; top: 0; left: 0; z-index: 999999; background-color: rgba(0, 0, 0, 0.5); position: fixed; justify-content: center; align-items: center; display: none;">
    <form id="frmChangePass" action="<?= base_url('../CData/ChangePass') ?>" method="post">
        <div class=" card" style="width: 250px; height: 350px;">
            <span id="btnClose" style="font-size: 35px; height: 15px; left: 15px; font-weight: bold; cursor: pointer; float: right; position: absolute; padding-top: 3px;">&times;</span>
            <div class="card-body p-5 align-content-center pt-6">
                <input type="hidden" id="txtIDChangePass" name="txtIDChangePass">

                <div id="pnlActivationKey" style="<?= (session('level') == 1 ? 'display: none;' : '') ?>">
                    <label for="txtActivationKey">Activation Key</label>
                    <input type="text" id="txtActivationKey" name="txtActivationKey" class="form-control mb-2" <?= (session('level') == 1 ? '' : 'required') ?>>
                </div>

                <label for="txtPass">Password</label>
                <input type="password" id="txtPass" name="txtPass" class="form-control mb-2" required autocomplete="new-password">
                <label id="lblPassConf" for="txtPass">Password Confirmation</label>
                <input type="password" id="txtPassConf" name="txtPassConf" class="form-control mb-2" required autocomplete="new-password">
            </div>

            <button id="btnSubmitChangePass" type="submit" class="btn btn-lg btn-primary mb-0 w-100" style="border-radius:15px; border-top-right-radius: 0; border-top-left-radius: 0;" <?= (session('level') >= 1 && session('leve') <= 3 ? "" : "disabled") ?>>
                Save
            </button>
        </div>
    </form>
</div>


<div id="pnlDetailUser" style="width: 100vw; height: 100vh; top: 0; left: 0; z-index: 99999; background-color: rgba(0, 0, 0, 0.5); position: fixed; justify-content: center; align-items: center; display: none;">
    <form id="frmDetailUser" action="<?= base_url('../CData/UpdateUser') ?>" method="post">
        <div id="cardHeight" class="card h-auto" style="width: 350px;">
            <span id="btnClose2" style="font-size: 35px; height: 15px; left: 15px; font-weight: bold; cursor: pointer; float: right; position: absolute; padding-top: 3px;">&times;</span>
            <div class="card-body p-5 align-content-center pt-5">
                <input type="hidden" id="txtID" name="txtID">

                <input type="text" id="txtNamaUser" name="txtNamaUser" class="form-control mb-0" placeholder="Nama" required>
                <p id="lblSaldoUser" class="mb-1 ml-1 mt-0">Saldo : 0</p>
                <div class="row">
                    <div class="col">
                        <label for="cbLevel">Level</label>
                        <select name="cbLevel" id="cbLevel" class="form-control mb-2" required>
                            <?= (session('level') == 1 ? "<option value='1'>Maintenance</option>" : "") ?>
                            <?= (session('level') == 1 ? "<option value='2'>Admin</option>" : "") ?>
                            <?= (session('level') >= 1  && session('level') <= 2 ? "<option value='3'>Kasir</option>" : "") ?>
                            <?= (session('level') >= 1 && session('level') <= 3 ? "<option value='4'>Agent</option>" : "") ?>
                            <option value='5'>VIP</option>"
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="position-relative">
                        <label id="lblPassword" for="txtPassword">Password</label>
                        <input type="password" id="txtPassword" name="txtPassword" class="form-control mb-2 w-100" autocomplete="false" value="********" disabled>
                        <i class="fa-solid fa-eye toggle-password"
                            id="togglePassword"
                            style="position: absolute; top: 40px; right: 20px; cursor: pointer;"></i>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        <label id="lblCashback" for="txtCashback">Cashback <span>%</span></label>
                        <input type="number" id="txtCashback" name="txtCashback" class="form-control mb-2 w-100" val="0" step="any">
                    </div>
                </div>

                <div class="row" id="pnlKomisi" style="display: none;">
                    <div class="col-6">
                        <label id="lblKomisi" for="txtKomisi">Komisi <span>%</span></label>
                        <input type="number" id="txtKomisi" name="txtKomisi" class="form-control mb-2 w-100" val="0" step="any">
                    </div>
                    <div class="col-6">
                        <label id="lblMaxCashback" for="txtMaxCashback">Max Cashback <span>%</span></label>
                        <input type="number" id="txtMaxCashback" name="txtMaxCashback" class="form-control mb-2 w-100" val="0" step="any">
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <label for="selBankDetail" class="form-label">Nama Bank</label>
                        <select name="selBankDetail" id="selBankDetail" class="form-control mb-2">
                            <option value="">-- Pilih Bank --</option>
                            <?php if (isset($banks) && is_array($banks)): ?>
                                <?php foreach ($banks as $bank): ?>
                                    <option value="<?= $bank['kode'] ?>"><?= $bank['nama'] ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <label for="txtNoRekDetail" class="form-label">No. Rekening</label>
                        <input type="text" name="txtNoRekDetail" id="txtNoRekDetail" class="form-control mb-2" placeholder="No. Rekening" />
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <label for="txtNamaRekDetail" class="form-label">Nama Rekening</label>
                        <input type="text" name="txtNamaRekDetail" id="txtNamaRekDetail" class="form-control mb-2" placeholder="Nama Rekening" />
                    </div>
                </div>

            </div>
            <div style="display: flex;">
                <button id="btnChangePass" onclick="$('#pnlChangePass').css('display', 'flex');" type="button" class="btn btn-lg btn-primary m-0 px-1 w-50" style="border-radius: 0; border-bottom-left-radius: 15px; border-right: 0.5px solid white;">
                    Change Pass
                </button>
                <button id="btnSubmit" type="submit" class="btn btn-lg btn-primary m-0 px-1 w-50" style="border-radius: 0; border-bottom-right-radius: 15px; border-left: 0.5px solid white;">
                    Save
                </button>
            </div>
        </div>
    </form>
</div>



<script>
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('txtPassword');

    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        const myLevel = '<?= session('level') ?>';
        if (type == 'text') {

            const xKey = prompt('Activation Key');
            if (!xKey) {
                return;
            }

            $.ajax({
                url: '<?= base_url('../CTools/getUserPassword/') ?>',
                type: 'GET',
                data: {
                    iduser: $('#txtID').val(),
                    key: xKey
                },
                dataType: 'json',
                success: function(res) {
                    console.log(res)
                    if (res.status != 200) {
                        fAlert(`3|${res.message}`);
                        window.scrollTo({
                            top: 0,
                            behavior: 'smooth'
                        });
                        return
                    }

                    $('#txtPassword').val(res.data.password)
                    passwordInput.setAttribute('type', type);
                    this.classList.toggle('fa-eye');
                    this.classList.toggle('fa-eye-slash');
                },
                error: function(err) {
                    console.error(err)
                    fAlert(`3|${err}`);
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                    return
                }
            })
        } else {
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        }
    });
</script>

<script>
    let myLevel = '<?= session('level'); ?>'
    $(document).ready(function() {
        var table = $('#tblTrans').DataTable({});

        // Level filter functionality
        $('#levelFilter').on('change', function() {
            var selectedLevel = $(this).val();
            table.column(2).search(selectedLevel).draw();
        });

        $('#frmDetailUser').on('submit', function(e) {
            $('#btnSubmit').prop('disabled', true);


            let xPass1 = $('#txtPass').val();
            let xPass2 = $('#txtPassConf').val();
            if (xPass1 != xPass2) {
                e.preventDefault();
                fAlert("3|Password not Match")
                $('#lblPassConf').css('color', 'red');
                $('#btnSubmit').prop('disabled', false);
            }
        });

        $('#frmChangePass').on('submit', function(e) {
            e.preventDefault();
            $('#btnSubmitChangePass').prop('disabled', true);

            let xPass1 = $('#txtPass').val();
            let xPass2 = $('#txtPassConf').val();
            let myLevel = '<?= session('level') ?>';
            let xActivationKey = $('#txtActivationKey').val();

            // Check password match
            if (xPass1 != xPass2) {
                fAlert("3|Password not Match");
                $('#lblPassConf').css('color', 'red');
                $('#btnSubmitChangePass').prop('disabled', false);
                return;
            }

            // Reset label color
            $('#lblPassConf').css('color', '');

            // If level is 1, skip activation key validation
            if (myLevel == '1') {
                // Submit form directly
                submitChangePasswordForm();
                return;
            }

            // Validate activation key for other levels
            if (!xActivationKey) {
                fAlert("3|Activation Key is required");
                $('#btnSubmitChangePass').prop('disabled', false);
                return;
            }

            // Validate activation key via AJAX
            $.ajax({
                url: '<?= base_url('../CTools/getUserPassword/') ?>',
                type: 'GET',
                data: {
                    iduser: $('#txtIDChangePass').val(),
                    key: xActivationKey
                },
                dataType: 'json',
                success: function(res) {
                    if (res.status != 200) {
                        fAlert(`3|${res.message}`);
                        $('#btnSubmitChangePass').prop('disabled', false);
                        return;
                    }
                    // Activation key is valid, submit form
                    submitChangePasswordForm();
                },
                error: function(err) {
                    console.error(err);
                    fAlert("3|Error validating activation key");
                    $('#btnSubmitChangePass').prop('disabled', false);
                }
            });
        });

        function submitChangePasswordForm() {
            // Create form data
            let formData = new FormData();
            formData.append('txtIDChangePass', $('#txtIDChangePass').val());
            formData.append('txtPass', $('#txtPass').val());
            formData.append('txtPassConf', $('#txtPassConf').val());

            // Submit via AJAX or allow normal form submission
            $('#frmChangePass')[0].submit();
        }
        $('#btnClose2 ').on('click ', function() {
            $('#pnlDetailUser').css('display', 'none')
        });

        $('#btnClose ').on('click ', function() {
            $('#pnlChangePass').css('display', 'none');
            // Clear form fields for security
            $('#txtActivationKey').val('');
            $('#txtPass').val('');
            $('#txtPassConf').val('');
            $('#lblPassConf').css('color', '');
            $('#btnSubmitChangePass').prop('disabled', false);
        });

        $('#txtCashback').on('change', function(x) {
            let xMaxCash = '<?= session('maxCashback'); ?>'
            let myLevel = '<?= session('level'); ?>'
            if ((xMaxCash == 0) && (myLevel >= 1 && myLevel <= 3)) {
                return;
            }

            if ($(this).val() >= xMaxCash) {
                $(this).val(xMaxCash);
            }

        })

        $('#cbLevel').on('change', function(x) {
            if ($(this).val() == "4") {
                $('#pnlKomisi').css('display', 'flex')
                $('#cardHeight').css('height', '400px')
            } else {
                $('#pnlKomisi').css('display', 'none')
                $('#cardHeight').css('height', '350px')
            }
        })
    });

    function ShowDetail($ID) {
        $('#txtID').val($ID);
        txtID.value = $ID;
        txtIDChangePass.value = $ID;
        $.ajax({
            url: '<?= base_url('../CTools/getDataUser/') ?>' + '/' + $ID,
            type: 'GET',
            dataType: 'json',
            success: function(qData) {
                if (!qData) {
                    fAlert("3|Data User Tidak diTemukan!");
                    return;
                }
                $('#txtNamaUser').val(qData.username);
                $('#lblSaldoUser').text("Saldo : " + formatNumber(qData.Saldo));
                $('#cbLevel').val(qData.level);
                $('#txtCashback').val(qData.cashback);
                $('#txtKomisi').val(qData.komisi);
                $('#txtMaxCashback').val(qData.maxcashback);
                
                // Update bank information fields
                $('#selBankDetail').val(qData.kodebank); // Assuming qData.kodebank holds the bank code
                $('#txtNoRekDetail').val(qData.norek);
                $('#txtNamaRekDetail').val(qData.namarek);
                if (qData.level == "4" && (myLevel >= 1 && myLevel <= 3)) {
                    $('#pnlKomisi').css('display', 'flex')
                    $('#cardHeight').css('height', '400px')
                } else {
                    $('#pnlKomisi').css('display', 'none')
                    $('#cardHeight').css('height', '350px')
                }
                $('#pnlDetailUser').css('display', 'flex')
            },
            error: function(xhr, status, error) {
                console.log('Error User data:', error);
                isAllowed = true;
                setBtnUpdNum()
            }
        });
    }
</script>
