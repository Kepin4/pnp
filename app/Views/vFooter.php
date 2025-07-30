<style>
  .custom-button {
    position: fixed;
    bottom: 0;
    right: 0;
    margin: 15px;
    z-index: 999999;
  }

  .custom-button button {
    width: 150px;
    opacity: 1 !important;
  }

  .custom-button button.text-left {
    text-align: left;
    font-weight: bold;
  }
</style>

<?php

use App\Controllers\CPlay;

$cntrl = new CPlay;
$Saldo = number_format($cntrl->getSaldo(), 2, ',', '.');
if (!(session('level') >= 1 && session('level') <= 3)) {
  echo "<div class='custom-button'>
          <button disabled class='btn btn-success text-left'>Saldo: {$Saldo}</button>
        </div>";
}

?>


<script>
  function formatNumber(value) {
    return value.toLocaleString('pt-BR', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    });
  }

  function getTimeDifference(xTime1, xTime2, xFormat) {
    let date1 = new Date(xTime1);
    let date2 = new Date(xTime2);

    if (date1 > date2) {
      return '00:00';
    }

    let diff = Math.abs(date2 - date1);
    let seconds = Math.floor(diff / 1000) % 60;
    let minutes = Math.floor(diff / 60000) % 60;
    let hours = Math.floor(diff / 3600000);

    let result = '';
    if (xFormat.includes('h') || xFormat.includes(':')) result += String(hours).padStart(2, '0') + ':';
    if (xFormat.includes('m') || xFormat.includes(':')) result += String(minutes).padStart(2, '0') + ':';
    if (xFormat.includes('s') || xFormat.includes(':')) result += String(seconds).padStart(2, '0');
    return result.endsWith(':') && !xFormat.includes('s') ? result.slice(0, -1) : result;
  }
</script>

<script>
  let idleTime = 0;
  let focusTime = 0;
  const interval = setInterval(() => {
    idleTime++;
    focusTime++;

    if (idleTime >= 10 * 12) {
      window.location.href = '../../CLogin/Logout';
    }

    if (focusTime >= 10 * 12) {
      window.location.href = '../../CLogin/Logout';
    }
  }, 5000);

  const resetIdleTime = () => {
    idleTime = 0;
  };

  const resetFocusTime = () => {
    focusTime = 0;
  };

  window.onload = resetIdleTime;
  document.onmousemove = resetIdleTime;
  document.onkeypress = resetIdleTime;
  document.onscroll = resetIdleTime;

  window.onfocus = resetFocusTime;
  window.onblur = () => {
    focusTime = 0;
  };
</script>


<script>
  document.addEventListener('keydown', function(e) {
    const myLevel = <?= session('level'); ?>;
    if (myLevel === 1) {
      return;
    }
    if (e.key === 'F12' || (e.ctrlKey && e.shiftKey && e.key === 'I') || (e.ctrlKey && e.key === 'u')) {
      e.preventDefault();
    }
  });
</script>

<script>
  function SendReport(xForm, xPath) {
    let xRealPath = '<?= base_url("../") ?>' + '/' + xPath
    xForm.attr('action', xRealPath);
    xForm.attr('target', '_BLANK');
    $('body').append(xForm);
    xForm.submit();
    xForm.remove();
  }
</script>


<footer class="footer pt-3  ">

</footer>


<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/2.1.5/js/dataTables.js"></script>


<!--   Core JS Files   -->
<script src="<?= base_url('template/assets/js/core/popper.min.js') ?>"></script>
<script src="<?= base_url('template/assets/js/core/bootstrap.min.js') ?>"></script>
<script src="<?= base_url('template/assets/js/plugins/perfect-scrollbar.min.js') ?>"></script>
<script src="<?= base_url('template/assets/js/plugins/smooth-scrollbar.min.js') ?>"></script>
<script src="<?= base_url('template/assets/js/plugins/chartjs.min.js') ?>"></script>

<script type="text/javascript">
  $(document).ready(function() {
    $('#authors-table').DataTable();
  });
</script>
<script>
  var win = navigator.platform.indexOf('Win') > -1;
  if (win && document.querySelector('#sidenav-scrollbar')) {
    var options = {
      damping: '0.5'
    }
    Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
  }
</script>
<script src="<?= base_url('template/assets/js/soft-ui-dashboard.min.js?v=1.0.7') ?>"></script>
</body>

</html>