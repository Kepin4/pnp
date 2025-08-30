<style>
  .fixed-alert-container {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 99999;
    box-sizing: border-box;
  }
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 " id="sidenav-main">
  <div class="sidenav-header">
    <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
    <a class="navbar-brand m-0" href="#">
      <img src="<?= base_url('../../img/' . session('Comp')->logo) ?>" class="navbar-brand-img h-100" alt="main_logo">
      <span class="ms-1 font-weight-bold"><?= session('Comp')->nama ?></span>
    </a>
  </div>
  <hr class="horizontal dark mt-0">
  <div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main" style="height: calc(100vh - 175px);">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link " href="<?= base_url('/CBase') ?>">
          <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            <svg width="12px" height="12px" viewBox="0 0 45 40" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
              <title>shop </title>
              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <g transform="translate(-1716.000000, -439.000000)" fill="#FFFFFF" fill-rule="nonzero">
                  <g transform="translate(1716.000000, 291.000000)">
                    <g transform="translate(0.000000, 148.000000)">
                      <path class="color-background opacity-6" d="M46.7199583,10.7414583 L40.8449583,0.949791667 C40.4909749,0.360605034 39.8540131,0 39.1666667,0 L7.83333333,0 C7.1459869,0 6.50902508,0.360605034 6.15504167,0.949791667 L0.280041667,10.7414583 C0.0969176761,11.0460037 -1.23209662e-05,11.3946378 -1.23209662e-05,11.75 C-0.00758042603,16.0663731 3.48367543,19.5725301 7.80004167,19.5833333 L7.81570833,19.5833333 C9.75003686,19.5882688 11.6168794,18.8726691 13.0522917,17.5760417 C16.0171492,20.2556967 20.5292675,20.2556967 23.494125,17.5760417 C26.4604562,20.2616016 30.9794188,20.2616016 33.94575,17.5760417 C36.2421905,19.6477597 39.5441143,20.1708521 42.3684437,18.9103691 C45.1927731,17.649886 47.0084685,14.8428276 47.0000295,11.75 C47.0000295,11.3946378 46.9030823,11.0460037 46.7199583,10.7414583 Z"></path>
                      <path class="color-background" d="M39.198,22.4912623 C37.3776246,22.4928106 35.5817531,22.0149171 33.951625,21.0951667 L33.92225,21.1107282 C31.1430221,22.6838032 27.9255001,22.9318916 24.9844167,21.7998837 C24.4750389,21.605469 23.9777983,21.3722567 23.4960833,21.1018359 L23.4745417,21.1129513 C20.6961809,22.6871153 17.4786145,22.9344611 14.5386667,21.7998837 C14.029926,21.6054643 13.533337,21.3722507 13.0522917,21.1018359 C11.4250962,22.0190609 9.63246555,22.4947009 7.81570833,22.4912623 C7.16510551,22.4842162 6.51607673,22.4173045 5.875,22.2911849 L5.875,44.7220845 C5.875,45.9498589 6.7517757,46.9451667 7.83333333,46.9451667 L19.5833333,46.9451667 L19.5833333,33.6066734 L27.4166667,33.6066734 L27.4166667,46.9451667 L39.1666667,46.9451667 C40.2482243,46.9451667 41.125,45.9498589 41.125,44.7220845 L41.125,22.2822926 C40.4887822,22.4116582 39.8442868,22.4815492 39.198,22.4912623 Z"></path>
                    </g>
                  </g>
                </g>
              </g>
            </svg>
          </div>
          <span class="nav-link-text ms-1">Dashboard</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link " href="<?= base_url('/CBase/Stream') ?>">
          <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="100" height="100" viewBox="0 0 50 50">
              <path d="M 8.0136719 7.0292969 A 1.0001 1.0001 0 0 0 7.3222656 7.3222656 C 2.7995047 11.846068 -1.1842379e-15 18.102792 0 25 C 0 31.897208 2.7995047 38.153932 7.3222656 42.677734 A 1.0001 1.0001 0 0 0 8.7363281 42.677734 L 11.570312 39.84375 A 1.0001 1.0001 0 0 0 11.570312 38.429688 C 8.1286602 34.987084 6 30.242812 6 25 C 6 19.757188 8.1297921 15.013788 11.572266 11.572266 A 1.0001 1.0001 0 0 0 11.572266 10.158203 L 8.7363281 7.3222656 A 1.0001 1.0001 0 0 0 8.0136719 7.0292969 z M 41.957031 7.0292969 A 1.0001 1.0001 0 0 0 41.263672 7.3222656 L 38.427734 10.158203 A 1.0001 1.0001 0 0 0 38.427734 11.572266 C 41.870208 15.013788 44 19.757188 44 25 C 44 30.242812 41.870208 34.986212 38.427734 38.427734 A 1.0001 1.0001 0 0 0 38.427734 39.841797 L 41.263672 42.677734 A 1.0001 1.0001 0 0 0 42.677734 42.677734 C 47.201645 38.154865 50 31.897208 50 25 C 50 18.102792 47.200495 11.846068 42.677734 7.3222656 A 1.0001 1.0001 0 0 0 41.957031 7.0292969 z M 8.0976562 9.5117188 L 9.5195312 10.933594 C 6.1269359 14.664061 4 19.575176 4 25 C 4 30.424712 6.1260807 35.337173 9.5175781 39.068359 L 8.0976562 40.488281 C 4.3450168 36.394537 2 30.995061 2 25 C 2 19.004939 4.3450168 13.605463 8.0976562 9.5117188 z M 41.902344 9.5117188 C 45.654983 13.605463 48 19.004939 48 25 C 48 30.995061 45.655695 36.395442 41.902344 40.488281 L 40.480469 39.066406 C 43.873064 35.335939 46 30.424824 46 25 C 46 19.575176 43.873064 14.664061 40.480469 10.933594 L 41.902344 9.5117188 z M 14.382812 13.398438 A 1.0001 1.0001 0 0 0 13.691406 13.691406 C 10.796092 16.587786 9 20.593819 9 25 C 9 29.406181 10.796092 33.412214 13.691406 36.308594 A 1.0001 1.0001 0 0 0 15.105469 36.308594 L 17.931641 33.482422 A 1.0001 1.0001 0 0 0 17.931641 32.068359 C 16.119902 30.255711 15 27.761761 15 25 C 15 22.238239 16.119902 19.744289 17.931641 17.931641 A 1.0001 1.0001 0 0 0 17.931641 16.517578 L 15.105469 13.691406 A 1.0001 1.0001 0 0 0 14.382812 13.398438 z M 35.587891 13.398438 A 1.0001 1.0001 0 0 0 34.894531 13.691406 L 32.068359 16.517578 A 1.0001 1.0001 0 0 0 32.068359 17.931641 C 33.880098 19.744289 35 22.238239 35 25 C 35 27.761761 33.880098 30.255711 32.068359 32.068359 A 1.0001 1.0001 0 0 0 32.068359 33.482422 L 34.894531 36.308594 A 1.0001 1.0001 0 0 0 36.308594 36.308594 C 39.203908 33.412214 41 29.406181 41 25 C 41 20.593819 39.203908 16.587786 36.308594 13.691406 A 1.0001 1.0001 0 0 0 35.587891 13.398438 z M 14.466797 15.880859 L 15.947266 17.361328 C 14.184764 19.450917 13 22.061346 13 25 C 13 27.938654 14.184764 30.549083 15.947266 32.638672 L 14.466797 34.119141 C 12.335969 31.66133 11 28.50273 11 25 C 11 21.49727 12.335969 18.33867 14.466797 15.880859 z M 35.533203 15.880859 C 37.664031 18.33867 39 21.49727 39 25 C 39 28.50273 37.664031 31.66133 35.533203 34.119141 L 34.052734 32.638672 C 35.815236 30.549083 37 27.938654 37 25 C 37 22.061346 35.815236 19.450917 34.052734 17.361328 L 35.533203 15.880859 z M 25 18 C 21.134 18 18 21.134 18 25 C 18 28.866 21.134 32 25 32 C 28.866 32 32 28.866 32 25 C 32 21.134 28.866 18 25 18 z M 25 20 C 27.757 20 30 22.243 30 25 C 30 27.757 27.757 30 25 30 C 22.243 30 20 27.757 20 25 C 20 22.243 22.243 20 25 20 z"></path>
            </svg>
          </div>
          <span class="nav-link-text ms-1">Stream</span>
        </a>
      </li>

      <?php if (session('level') > 3) { ?>
        <li class="nav-item">
          <a class="nav-link" href="<?= base_url('/CPlay/Play') ?>">
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="fa fa-circle-dot" style="color: #3a416f;"></i>

            </div>
            <span class="nav-link-text ms-1">Placement</span>
          </a>
        </li>
      <?php } ?>



      <?php if (session('level') >= 1 && session('level') <= 3) { ?>
        <li class="nav-item">
          <a class="nav-link" href="<?= base_url('/CNumber') ?>">
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center" style="color: #3a416f">
              <i class="fa fa-circle-dot" style="color: #3a416f;"></i>
            </div>
            <span class="nav-link-text ms-1">Number</span>
          </a>
        </li>
      <?php } ?>

      <?php if (session('level') == 1) { ?>
        <li class="nav-item">
          <a class="nav-link" href="<?= base_url('/CTools/webSocketTest') ?>">
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center" style="color: #3a416f">
              <i class="fas fa-plug" style="color: #3a416f;"></i>
            </div>
            <span class="nav-link-text ms-1">WebSocket Test</span>
          </a>
        </li>
      <?php } ?>
      <li class="nav-item mt-3">
        <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Transaction</h6>
      </li>
      <?php if (session('level') >= 1 && session('level') <= 3) { ?>
        <li class="nav-item">
          <a class="nav-link  " href="<?= base_url('/CTrans/NewTransaction') ?>">
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
              <svg width="12px" height="12px" viewBox="0 0 42 42" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                <title>office</title>
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                  <g transform="translate(-1869.000000, -293.000000)" fill="#FFFFFF" fill-rule="nonzero">
                    <g transform="translate(1716.000000, 291.000000)">
                      <g id="office" transform="translate(153.000000, 2.000000)">
                        <path class="color-background opacity-6" d="M12.25,17.5 L8.75,17.5 L8.75,1.75 C8.75,0.78225 9.53225,0 10.5,0 L31.5,0 C32.46775,0 33.25,0.78225 33.25,1.75 L33.25,12.25 L29.75,12.25 L29.75,3.5 L12.25,3.5 L12.25,17.5 Z"></path>
                        <path class="color-background" d="M40.25,14 L24.5,14 C23.53225,14 22.75,14.78225 22.75,15.75 L22.75,38.5 L19.25,38.5 L19.25,22.75 C19.25,21.78225 18.46775,21 17.5,21 L1.75,21 C0.78225,21 0,21.78225 0,22.75 L0,40.25 C0,41.21775 0.78225,42 1.75,42 L40.25,42 C41.21775,42 42,41.21775 42,40.25 L42,15.75 C42,14.78225 41.21775,14 40.25,14 Z M12.25,36.75 L7,36.75 L7,33.25 L12.25,33.25 L12.25,36.75 Z M12.25,29.75 L7,29.75 L7,26.25 L12.25,26.25 L12.25,29.75 Z M35,36.75 L29.75,36.75 L29.75,33.25 L35,33.25 L35,36.75 Z M35,29.75 L29.75,29.75 L29.75,26.25 L35,26.25 L35,29.75 Z M35,22.75 L29.75,22.75 L29.75,19.25 L35,19.25 L35,22.75 Z"></path>
                      </g>
                    </g>
                  </g>
                </g>
              </svg>
            </div>
            <span class="nav-link-text ms-1">Transaksi</span>
          </a>
        </li>

        <li class="nav-item mt-3">
          <h6 class="ps-4 ms-2 text-uppercase text-xxs font-weight-bold opacity-6">Laporan</h6>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= base_url('../CTrans/ListNumber') ?>" id="selectReportDate">
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="fa fa-list-ol" style="color: #3a416f;"></i>
            </div>
            <span class="nav-link-text ms-1">Total List Number</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= base_url('../CTrans/ShiftReport') ?>" id="selectReportDate">
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
              <svg width="12px" height="12px" viewBox="0 0 43 36" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                <title>credit-card</title>
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                  <g transform="translate(-2169.000000, -745.000000)" fill="#FFFFFF" fill-rule="nonzero">
                    <g transform="translate(1716.000000, 291.000000)">
                      <g transform="translate(453.000000, 454.000000)">
                        <path class="color-background opacity-6" d="M43,10.7482083 L43,3.58333333 C43,1.60354167 41.3964583,0 39.4166667,0 L3.58333333,0 C1.60354167,0 0,1.60354167 0,3.58333333 L0,10.7482083 L43,10.7482083 Z"></path>
                        <path class="color-background" d="M0,16.125 L0,32.25 C0,34.2297917 1.60354167,35.8333333 3.58333333,35.8333333 L39.4166667,35.8333333 C41.3964583,35.8333333 43,34.2297917 43,32.25 L43,16.125 L0,16.125 Z M19.7083333,26.875 L7.16666667,26.875 L7.16666667,23.2916667 L19.7083333,23.2916667 L19.7083333,26.875 Z M35.8333333,26.875 L28.6666667,26.875 L28.6666667,23.2916667 L35.8333333,23.2916667 L35.8333333,26.875 Z"></path>
                      </g>
                    </g>
                  </g>
                </g>
              </svg>
            </div>
            <span class="nav-link-text ms-1">Laporan Shift Placement</span>
          </a>
        </li>
      <?php } ?>
      <li class="nav-item">
        <a class="nav-link" href="<?= base_url('../CTrans/PlacementReport') ?>" id="selectReportDate">
          <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="fas fa-clipboard" style="color: #3a416f;"></i>
          </div>
          <span class="nav-link-text ms-1">Laporan Placement</span>
        </a>
      </li>


      <li class="nav-item">
        <a class="nav-link" href="<?= base_url('../CTrans/IncomeReport') ?>" id="selectReportDate">
          <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="fas fa-clipboard" style="color: #3a416f;"></i>
          </div>
          <span class="nav-link-text ms-1">Laporan Income</span>
        </a>
      </li>

      <?php if (session('level') >= 1 && session('level') <= 4) { ?>
        <li class="nav-item">
          <a class="nav-link" href="<?= base_url('../CTrans/CommissionReport') ?>" id="selectReportDate">
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="fas fa-clipboard" style="color: #3a416f;"></i>
            </div>
            <span class="nav-link-text ms-1">Laporan Komisi</span>
          </a>
        </li>
      <?php } ?>
      <li class="nav-item">
        <a class="nav-link" href="/CTrans/Transaction" id="selectReportDate">
          <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            <svg width="12px" height="12px" viewBox="0 0 43 36" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
              <title>credit-card</title>
              <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <g transform="translate(-2169.000000, -745.000000)" fill="#FFFFFF" fill-rule="nonzero">
                  <g transform="translate(1716.000000, 291.000000)">
                    <g transform="translate(453.000000, 454.000000)">
                      <path class="color-background opacity-6" d="M43,10.7482083 L43,3.58333333 C43,1.60354167 41.3964583,0 39.4166667,0 L3.58333333,0 C1.60354167,0 0,1.60354167 0,3.58333333 L0,10.7482083 L43,10.7482083 Z"></path>
                      <path class="color-background" d="M0,16.125 L0,32.25 C0,34.2297917 1.60354167,35.8333333 3.58333333,35.8333333 L39.4166667,35.8333333 C41.3964583,35.8333333 43,34.2297917 43,32.25 L43,16.125 L0,16.125 Z M19.7083333,26.875 L7.16666667,26.875 L7.16666667,23.2916667 L19.7083333,23.2916667 L19.7083333,26.875 Z M35.8333333,26.875 L28.6666667,26.875 L28.6666667,23.2916667 L35.8333333,23.2916667 L35.8333333,26.875 Z"></path>
                    </g>
                  </g>
                </g>
              </g>
            </svg>
          </div>
          <span class="nav-link-text ms-1">Laporan Invoice</span>
        </a>
      </li>


      <li class="nav-item mt-3">
        <h6 class="ps-4 ms-2 text-uppercase text-xxs font-weight-bold opacity-6">Permintaan</h6>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?= base_url('/CTrans/TopupRequest') ?>">
          <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="fa fa-envelope" style="color: #3a416f;"></i>
          </div>
          <span class="nav-link-text ms-1"><span id="alertTopUp" style="font-size: 15px; color: red;"></span>Permintaan TopUp</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="<?= base_url('/CTrans/WithdrawRequest') ?>">
          <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            <i class="fa fa-envelope" style="color: #3a416f;"></i>
          </div>
          <span class="nav-link-text ms-1"><span id="alertWithdraw" style="font-size: 15px; color: red;"></span>Permintaan Withdraw</span>
        </a>
      </li>

      <?php if (session('level') >= 1 && session('level') <= 4) { ?>
        <li class="nav-item mt-3">
          <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Data</h6>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= base_url('/CData/User') ?>">
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
              <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="bi bi-people-fill" viewBox="0 0 16 16" style="color: #3a416f">
                <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5" />
              </svg>
            </div>
            <span class="nav-link-text ms-1">List User</span>
          </a>
        </li>
        <?php if (session('level') >= 1 && session('level') <= 3) { ?>
          <li class="nav-item">
            <a class="nav-link" href="<?= base_url('/CAdmin/Setting') ?>">
              <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="bi bi-people-fill" viewBox="0 0 16 16" style="color: #3a416f">
                  <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5" />
                </svg>
              </div>
              <span class="nav-link-text ms-1">Setting</span>
            </a>
          </li>
        <?php } ?>
      <?php } ?>
    </ul>
  </div>
  <div class="sidenav-footer mx-3" style="position: fixed;">
    <div class="container-fluid">
      <div class="row align-items-center justify-content-lg-between">
        <div class="col-lg-12 mb-lg-0 mb-4">
          <div class="copyright text-center text-sm text-muted text-lg-center">
            ©made by <a href="https://smartappscare.com/" class="font-weight-bold" target="_blank">RLG</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</aside>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>


<body class="g-sidenav-show bg-gray-100">
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg" style=" top: 25px; z-index: 999999;">
    <div class="container-fluid">
      <div id="AlertBox" class="card mb-4 p-3" style="background-color: #d0342c; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); width: 80%; display: none;">
        <p id="AlertText" style="font-family: 'monospace', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif; color: white; font-size: 16px; margin: 0;">
        </p>
      </div>
    </div>
  </main>
</body>



<script>
  $(document).ready(function() {
    const xSesAlert = '<?= session()->getFlashdata('alert') ?>';
    if (xSesAlert) {
      fAlert(xSesAlert);
    }


    // WebSocket Signal Trigger
    const xSesSignal = <?= json_encode(session()->getFlashdata('signal')) ?> || [];
    if (xSesSignal.length > 0) {
      setTimeout(() => {
        xSesSignal.forEach((x) => {
          sendSignal(x);
        });
      }, 500);
    }

    // Check Request
    receiveSignal('SignalRequest', function(data) {
      reCheckRequest();
    });

    reCheckRequest();
  });


  function reCheckRequest() {
    $.ajax({
      url: '<?= base_url('/CTools/AjaxGetNotif') ?>',
      method: 'GET',
      dataType: 'json',
      success: function(response) {
        $('#alertTopUp').text(response.Topup ? ' * ' : '');
        $('#alertWithdraw').text(response.Withdraw ? ' * ' : '');
      },
      error: function(xhr, status, error) {
        console.error('AJAX error:', status, error);
      }
    });
  }



  function fAlert(Text) {
    const AlertBox = $('#AlertBox');
    const AlertText = $('#AlertText');
    const xStatus = {
      1: '⏰ | Pending',
      2: '⚠️ | Warning',
      3: ' ❗ | Failed',
      5: '✅ | Success'
    };

    const xColorArr = {
      1: 'darkgray',
      2: '#e7cb02',
      3: '#d0342c',
      5: '#24a148'
    };

    var xIsi = ""
    var xColor = ""
    const xStr = Text.split('|');
    if (xStr.length === 2) {
      const xStatText = parseInt(xStr[0], 10);
      const xStatIcon = xStatus[xStatText] || '?';
      const xText = xStr[1];
      xColor = xColorArr[xStatText]
      xIsi = "<strong>" + xStatIcon + ", </strong> <span>" + xText + '</span>';
    } else {
      console.log("Invalid input format.");
    }

    AlertText.html(xIsi);
    AlertBox.css('background-color', xColor);
    AlertBox.show();
    setTimeout(function() {
      AlertBox.hide();
    }, 5000);
  };

  // WebSocket Client Implementation
  class WebSocketClient {
    constructor() {
      this.socket = null;
      this.isConnected = false;
      this.clientId = null;
      this.reconnectAttempts = 0;
      this.maxReconnectAttempts = 5;
      this.reconnectDelay = 3000;
    }

    connect() {
      try {
        // Determine WebSocket URL based on current location
        const protocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:';
        let hostname = window.location.hostname;

        // Handle cases where hostname might be empty or localhost variations
        if (!hostname || hostname === '') {
          hostname = 'localhost';
        }

        const wsUrl = `${protocol}//${hostname}:8081`;

        console.log('Connecting to WebSocket server:', wsUrl);
        this.socket = new WebSocket(wsUrl);

        this.socket.onopen = () => {
          console.log('WebSocket connected');
          this.isConnected = true;
          this.reconnectAttempts = 0;
        };

        this.socket.onmessage = (event) => {
          try {
            const message = JSON.parse(event.data);
            this.handleMessage(message);
          } catch (error) {
            console.error('Error parsing WebSocket message:', error);
          }
        };

        this.socket.onclose = () => {
          console.log('WebSocket disconnected');
          this.isConnected = false;
          this.clientId = null;
          this.attemptReconnect();
        };

        this.socket.onerror = (error) => {
          console.error('WebSocket error:', error);
        };

      } catch (error) {
        console.error('Failed to create WebSocket connection:', error);
        this.attemptReconnect();
      }
    }

    handleMessage(message) {
      console.log('Received WebSocket message:', message);

      switch (message.type) {
        case 'welcome':
          this.clientId = message.clientId;
          console.log('WebSocket client ID:', this.clientId);
          break;

        case 'signal':
          // Trigger signal event for other pages to listen
          window.dispatchEvent(new CustomEvent('websocket-signal', {
            detail: {
              signal: message.signal,
              data: message.data || {},
              timestamp: message.timestamp,
              fromClientId: message.fromClientId
            }
          }));
          console.log('Signal received:', message.signal);
          break;

        case 'error':
          console.error('WebSocket server error:', message.message);
          break;

        default:
          console.log('Unknown message type:', message.type);
      }
    }

    sendSignal(signalType, data = {}) {
      if (!this.isConnected || !this.socket) {
        console.warn('WebSocket not connected. Attempting to connect...');
        this.connect();
        return false;
      }

      const message = {
        type: 'signal',
        signal: signalType,
        data: data
      };

      try {
        this.socket.send(JSON.stringify(message));
        console.log('Signal sent:', signalType, data);
        return true;
      } catch (error) {
        console.error('Failed to send signal:', error);
        return false;
      }
    }

    attemptReconnect() {
      if (this.reconnectAttempts < this.maxReconnectAttempts) {
        this.reconnectAttempts++;
        console.log(`Attempting to reconnect... (${this.reconnectAttempts}/${this.maxReconnectAttempts})`);

        setTimeout(() => {
          this.connect();
        }, this.reconnectDelay);
      } else {
        console.error('Max reconnection attempts reached. Please refresh the page.');
      }
    }

    disconnect() {
      if (this.socket) {
        this.socket.close();
      }
    }
  }

  // Global WebSocket client instance
  let wsClient = null;

  // Initialize WebSocket connection when page loads
  $(document).ready(function() {
    wsClient = new WebSocketClient();
    wsClient.connect();
  });

  // Global function to send signals (as requested)
  function sendSignal(signalType, data = {}) {
    if (wsClient) {
      return wsClient.sendSignal(signalType, data);
    } else {
      console.error('WebSocket client not initialized');
      return false;
    }
  }

  // Clean up WebSocket connection when page unloads
  $(window).on('beforeunload', function() {
    if (wsClient) {
      wsClient.disconnect();
    }
  });
</script>