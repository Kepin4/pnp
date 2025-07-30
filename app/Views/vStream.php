<style>
  #fs_btn {
    background-color: #1f1f1f;
    border: none;
    color: #ffffff;
    padding: 8px;
    font-size: 14px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }

  #fs_btn .fullscreen-icon {
    width: 16px;
    height: 16px;
    background-image: url('https://example.com/fullscreen-icon.svg');
    background-size: cover;
  }

  #fs_btn:hover {
    background-color: #0f0f0f;
  }
</style>


<body class="g-sidenav-show  bg-gray-100">
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg" style=" top: 25px;">
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
      <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
          <!-- <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a></li>
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Dashboard</li>
          </ol> -->
          <h6 class="font-weight-bolder mb-0">Stream</h6>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
          <div class="ms-md-auto pe-md-3 d-flex align-items-center">
          </div>
          <ul class="navbar-nav  justify-content-end">
            <li class="nav-item d-flex align-items-center">
              <a href="<?= base_url('../../CBase/Profile') ?>" class="nav-link text-body font-weight-bold px-0">

                <i class="fa fa-user me-sm-1"></i>
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
    <button id="fs_btn" class="ml-4">Fullscreen</button>
    <div style="height: 10px;"></div>
    <div id="twitch-embed" style="display: none"></div>
    <div style="padding: 50px;">
      <div style="display: none; background-color: #1e1e2d; width: 100%; height: 100px; border-radius: 15px; display: flex; justify-content: center; align-items: center; padding-top: 20px" id="offline">
        <p style="color: white; text-align: center; font-family: monospace;">Currently Offline...</p>
      </div>
    </div>


</body>





<script src="https://player.twitch.tv/js/embed/v1.js"></script>
<script type="text/javascript">
  var fsbtn = document.getElementById('fs_btn');
  var div = document.getElementById('twitch-embed');
  var player = new Twitch.Player("twitch-embed", {
    width: "100%",
    height: "750",
    autoplay: true,
    controls: false,
    channel: "ppnp8888",
  });

  fsbtn.addEventListener('click', function() {
    if (!document.fullscreenElement) {
      div.classList.add('fullscreen');
      if (div.requestFullscreen) {
        div.requestFullscreen();
      } else if (div.mozRequestFullScreen) { // Firefox
        div.mozRequestFullScreen();
      } else if (div.webkitRequestFullscreen) { // Chrome, Safari and Opera
        div.webkitRequestFullscreen();
      } else if (div.msRequestFullscreen) { // IE/Edge
        div.msRequestFullscreen();
      }
    } else {
      document.exitFullscreen();
      div.classList.remove('fullscreen');
    }
  });

  player.addEventListener(Twitch.Player.OFFLINE, function() {
    document.getElementById("twitch-embed").style.display = "none";
    document.getElementById("offline").style.display = "flex";
    fsbtn.style.display = "none";
  });

  player.addEventListener(Twitch.Player.ONLINE, function() {
    document.getElementById("twitch-embed").style.display = "block";
    document.getElementById("offline").style.display = "none";
    fsbtn.style.display = "block";
  });
</script>