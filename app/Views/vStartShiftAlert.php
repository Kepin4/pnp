<div id="pnlShift" style="width: 100vw; height: 100vh; top: 0; left: 0; z-index: 99999; background-color: rgba(0, 0, 0, 0.5); position: fixed; justify-content: center; align-items: center; display: flex;">
    <form id="frmShift" action="<?= base_url('../../CNumber/StartShift') ?>" method="post">
        <div class="card" style="width: 250px; height: 250px;">
            <span id="btnClose" style="font-size: 35px; height: 15px; left: 15px; font-weight: bold; cursor: pointer; float: right; position: absolute; padding-top: 3px;">&times;</span>
            <div class="card-body p-5 align-content-center pt-6">
                <strong>
                    <span>
                        Shift belum dimulai, Mulai shift?
                    </span>
                </strong>
            </div>
            <button id="btnStart" type="submit" class="btn btn-lg btn-primary mb-0" style="border-radius:15px; border-top-right-radius: 0; border-top-left-radius: 0;">
                MULAI SHIFT
            </button>
        </div>
    </form>
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