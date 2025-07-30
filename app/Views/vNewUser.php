<body class="g-sidenav-show  bg-gray-100">
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
            <div class="container-fluid py-1 px-3">
                <nav aria-label="breadcrumb">
                    <h6 class="font-weight-bolder mb-0">New User</h6>
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


        <div class="content-wrapper">
            <div class="container-xxl flex-grow-1 container-p-y">
                <div class="card">
                    <div class="card-header">
                        <form action="<?= base_url('../CData/SaveUser') ?>" method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="member-name" class="form-label">Member Name</label>
                                <input type="text" name="txtUsername" id="txtUsername" class="form-control" placeholder="Member Name" required autocomplete="new-username" />
                            </div>

                            <div class="mb-3">
                                <label for="member-name" class="form-label">Password</label>
                                <input type="password" name="txtPassword" class="form-control" placeholder="Password" required autocomplete="new-password" />
                            </div>

                            <div class="mb-3">
                                <label for="member-name" class="form-label">Level</label>
                                <select name="selLevel" id="selLevel" class="form-control">
                                    <?= (session('level') == 1 ? "<option value='1'>Maintenance</option>" : "") ?>
                                    <?= (session('level') == 1 ? "<option value='2'>Admin</option>" : "") ?>
                                    <?= (session('level') >= 1 && session('level') <= 2 ? "<option value='3'>Kasir</option>" : "") ?>
                                    <?= (session('level') >= 1 && session('level') <= 3 ? "<option value='4'>Agent</option>" : "") ?>
                                    <option value='5' selected>VIP</option>
                                </select>
                            </div>

                            <div class="row pl-3">
                                <div class="mb-3 col-lg-2 pl-0">
                                    <label for="txtCashback" class="form-label">Cashback (%)</label>
                                    <div class="input-container">
                                        <input id="txtCashback" name="txtCashback" type="text" class="form-control w-50" placeholder="0" value="0" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\..*/g, '$1');" />
                                    </div>
                                </div>

                                <div id="pnlMaxCashback" class="mb-3 col-lg-2 pl-0" style="display: none">
                                    <label for="txtMaxCashback" class="form-label"> Max Cashback (%)</label>
                                    <div class="input-container">
                                        <input id="txtMaxCashback" name="txtMaxCashback" type="text" class="form-control w-50" placeholder="0" value="0" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\..*/g, '$1');" />
                                    </div>
                                </div>

                                <?php if (session('level') >= 1 && session('level') <= 3) { ?>
                                    <div id="pnlKomisi" class="mb-3 col-lg-2 pl-0" style="display: none">
                                        <label for="txtKomisi" class="form-label">Komisi (%)</label>
                                        <div class="input-container">
                                            <input id="txtKomisi" name="txtKomisi" type="text" class="form-control w-50" placeholder="0" value="0" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\..*/g, '$1');" />
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>

                            <div class="mb-3 mt-3">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a class="text-dark" href="<?= base_url('/CData/User') ?>">
                                    <button type="button" class="btn btn-secondary">Back</button>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    $('#selLevel').on('change', function() {
        if ($(this).val() == 4) {
            $('#pnlKomisi').css('display', 'block')
            $('#pnlMaxCashback').css('display', 'block')
        } else {
            $('#pnlKomisi').css('display', 'none')
            $('#pnlMaxCashback').css('display', 'none')
        }
    })

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
</script>