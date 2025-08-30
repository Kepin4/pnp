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
    }
</style>

<body class="g-sidenav-show  bg-gray-100">
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg " style=" top: 25px;">
        <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
            <div class="container-fluid py-1 px-3">
                <nav aria-label="breadcrumb">
                    <h6 class="font-weight-bolder mb-0">List Number</h6>
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

                            <form id="frmFilter" action="<?= base_url('/CTrans/ListNumber') ?>" method="POST">
                                <?= csrf_field() ?>
                                <div class="filter-box">
                                    <h5>Filter Tanggal</h5>
                                    <div class="row">
                                        <div class="col-6">
                                            <h6>Dari Tanggal</h6>
                                            <input name="dtStart" value="<?= $fltr->dtStart ?>" type="date" class="form-control sm filter-input" style="width: 100%">
                                        </div>
                                        <div class="col-6">
                                            <h6>Sampai Tanggal</h6>
                                            <input name="dtEnd" value="<?= $fltr->dtEnd ?>" type="date" class="form-control sm filter-input" style="width: 100%">
                                        </div>
                                    </div>

                                    <div class="row pt-3">
                                        <div class="row m-0 p-3">
                                            <button class="btn btn-sm btn-outline-primary w-100">
                                                Refresh
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <div class="row">
                                <div class="col-7">
                                    <h4 class="pt-4">List Number</h4>
                                </div>
                            </div>
                        </div>



                        <div class="card-body px-0 pt-0 pb-2">
                            <div class="table-responsive p-0 datatable-minimal p-3">
                                <table class="table align-items-center mb-0 display" id="tblTrans">
                                    <thead>
                                        <tr>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Shift</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Number</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Total Placement</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total Nominal Placement</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php $i = 1;
                                        foreach ($dtNum as $q) { ?>
                                            <tr>
                                                <td class="align-middle text-center">
                                                    <p class="text-xs"><?= $q->Shift ?></p>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <p class="text-xs"><?= $q->Number ?></p>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <p class="text-xs"><?= $q->TotalPasang ?></p>
                                                </td>
                                                <td class="align-middle text-right">
                                                    <p class="text-xs" style="color: <?= ($q->TotalNominal >= 0 ? 'green' : 'red') ?>"><?= number_format($q->TotalNominal, 2, '.', ',') ?></p>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="text-center">
                                            <th colspan="2">TOTAL</td>
                                                <?php $TotalNominal = array_sum(array_column($dtNum, 'TotalNominal'))  ?>
                                            <th class="text-center"><?= array_sum(array_column($dtNum, 'TotalPasang')) ?></td>
                                            <th style="color: <?= ($TotalNominal >= 0 ? 'green' :  'red') ?>"><?= number_format($TotalNominal, 2, '.', ',') ?></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</body>

<div class="modal fade text-left" id="inlineForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel33">Details</h4>
                <button type="button" class="close btn btn-danger" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa fa-close"></i>
                </button>
            </div>
            <form action="#">
                <div class="modal-body">
                    <label for="">Input By: </label>
                    <div class="form-group">
                        <input readonly id="inputBy" type="text" class="form-control">
                    </div>
                    <label for="">Input Date: </label>
                    <div class="form-group">
                        <input readonly id="inputDate" type="text" class="form-control">
                    </div>
                    <label for="">Update By: </label>
                    <div class="form-group">
                        <input readonly id="updateBy" type="text" class="form-control">
                    </div>
                    <label for="">Update Date: </label>
                    <div class="form-group">
                        <input readonly id="updateDate" type="text" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Delete Confirmation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a id="deleteLink" href="#">
                    <button type="button" class="btn btn-danger">Delete</button>
                </a>
            </div>
        </div>
    </div>
</div>




<script>
    $(document).ready(function() {
        $('#tblTrans').DataTable({});

        receiveSignal('ReportListNumber', function(data) {
            $('#frmFilter').submit();
        });
    })
</script>