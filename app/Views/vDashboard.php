<!DOCTYPE html>
<html lang="en">
<style>
  .table-container .btn {
    position: absolute;
    bottom: 10px;
    right: 10px;
  }
</style>
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

<body class="g-sidenav-show  bg-gray-100">
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg " style=" top: 25px;">
    <!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
      <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
          <!-- <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a></li>
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Dashboard</li>
          </ol> -->
          <h6 class="font-weight-bolder mb-0">Dashboard</h6>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
          <div class="ms-md-auto pe-md-3 d-flex align-items-center">
          </div>
          <ul class="navbar-nav  justify-content-end">
            <li class="nav-item d-flex align-items-center">
              <a href="<?= base_url('../../CBase/Profile') ?>" class="nav-link text-body font-weight-bold px-0">

                <i class="fa fa-user me-sm-1"></i>
                <span class="d-sm-inline"><?= $name ?></span>
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
    <!-- End Navbar -->

    <div class="container">
      <button id="btnReqTopup" class="btn btn-sm btn-primary" style="display: <?= (session('level') >= 1 && session('level') <= 3) ?  'none' : '' ?>">Topup</button>
      <button id="btnReqWithdraw" class="btn btn-sm btn-secondary text-white" style="display: <?= (session('level') >= 1 && session('level') <= 3) ? 'none' : '' ?>">Withdraw</button>
      <div class="row">
        <div class="col-6">
          <div class="card p-2">
            <div class="card-header pb-0">
              <b>Informasi</b>
            </div>
            <div class="card-body pt-0 pl-0 d-flex">
              <p><?php
                  $string = preg_replace("/^\s*- /m", "<li>", $Informasi);
                  $string = "<ul>\n" . $string . "\n</ul>";
                  echo nl2br($string);
                  ?></p>
            </div>
          </div>
        </div>
        <div class="col-6">
          <div class="card p-2">
            <div class="card-header pb-0">
              <b>Cara Main</b>
            </div>
            <div class="card-body pt-0 pl-0 d-flex">
              <p><?php
                  $string = preg_replace("/^\s*- /m", "<li>", $CaraMain);
                  $string = "<ul>\n" . $string . "\n</ul>";
                  echo nl2br($string);
                  ?></p>
            </div>
          </div>

        </div>
      </div>
    </div>
</body>

<div id="pnlReqTopup" class="overlay" style="z-index: 99999; display: none;">
  <div class="card w-50 modal-card">
    <div class="card-body p-3">
      <form id="frmReqTopup" action="<?= base_url('/CTrans/RequestTopUp') ?>" method="POST">
        <div class="modal-content">
          <span id="btnCloseReqTopup" class="close-btn">&times;</span>
          <h5 id="lblKeterangan">Enter a Topup Nominal</h5>
          <input type="text" id="txtNominal" name="txtNominal" placeholder="Enter value" class="input-field" value=0 min=0>
          <div id="xKetWD" style="display: none">
            <label for="txtNoref">Keterangan</label>
            <input type="text" id="txtNoref" name="txtNoref" placeholder="Nama Bank - No Rek" class="input-field mt-0" value="<?= session('namaBank') ?? '' ?> - <?= session('noRek') ?? '' ?> - <?= session('namaRek') ?? '' ?>">
          </div>
          <button class="btn btn-outline-primary w-503" style="align-self : flex-end;">Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>

</html>




<script>
  $(document).ready(function() {
    const btnReqTopup = $('#btnReqTopup');
    const btnCloseReqTopup = $('#btnCloseReqTopup');
    const pnlReqTopup = $('#pnlReqTopup');

    btnReqTopup.on('click', function() {
      console.log(pnlReqTopup)
      pnlReqTopup.css('display', 'flex');
      $('#xKetWD').css('display', 'none');
      $('#lblKeterangan').text("Enter Topup Nominal");
      $('#frmReqTopup').attr('action', "<?= base_url('/CTrans/RequestTopUp') ?>");
      console.log("a")
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

    $('#txtNominal').on('input', function() {
      let xvalue = $(this).val().toString();
      // Remove all non-numeric characters including existing dots (thousands separators)
      let cleanValue = xvalue.replace(/\./g, '').replace(/[^0-9]/g, '');
      
      // Use Number() for more robust conversion and handle empty string
      let numValue = Number(cleanValue);
      if (isNaN(numValue) || cleanValue === '') {
        numValue = 0;
      }
      
      // Format with dot as thousands separator (Indonesian locale)
      let formattedValue = numValue.toLocaleString('id-ID', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
      });
      $(this).val(formattedValue);
    });

    // Clean the value before form submission
    $('#frmReqTopup').on('submit', function() {
      let nominalInput = $('#txtNominal');
      let cleanValue = nominalInput.val().replace(/\./g, ''); // Remove dots (thousands separators)
      nominalInput.val(cleanValue);
    });

  });
</script>
