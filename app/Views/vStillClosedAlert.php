<div id="pnlShift" style="width: 100vw; height: 100vh; top: 0; left: 0; z-index: 99999; background-color: rgba(0, 0, 0, 0.5); position: fixed; justify-content: center; align-items: center; display: flex;">
    <div class="card" style="width: 250px; height: 350px;">
        <span id="btnClose" style="font-size: 35px; height: 15px; left: 15px; font-weight: bold; cursor: pointer; float: right; position: absolute; padding-top: 3px;">&times;</span>
        <div class="card-body p-5 align-content-center pt-6">
            <strong>
                <span>
                    Belum dapat melakukan Placement, Status masih Offline. Silahkan coba lagi nanti!
                </span>
            </strong>
        </div>
        <a href="<?= base_url('../CNumber/Number') ?>">
            <button id="btnStart" type="submit" class="btn btn-lg btn-primary mb-0 w-100" style="border-radius:15px; border-top-right-radius: 0; border-top-left-radius: 0;">
                BACK
            </button>
        </a>
    </div>
</div>



<script>
    $(document).ready(function() {
        $('#frmShift').on('submit', function() {
            $('#btnStart').prop('disabled', true);
        });

        $('#btnClose').on('click', function() {
            window.location.href = "<?= base_url('../CNumber/Number'); ?>"
        });
    });
</script>