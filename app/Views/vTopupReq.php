<style>
    .filter-box {
        padding: 20px;
        border: 1px solid #cb0c9f;
        border-radius: 10px;
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        box-shadow: 0 4px 6px rgba(203, 12, 159, 0.1);
        margin-top: 15px;
        transition: all 0.3s ease;
    }

    .filter-box:hover {
        box-shadow: 0 6px 12px rgba(203, 12, 159, 0.15);
        transform: translateY(-2px);
    }

    .filter-input {
        width: 100%;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        padding: 8px 12px;
        transition: border-color 0.3s ease;
    }

    .filter-input:focus {
        border-color: #cb0c9f;
        box-shadow: 0 0 0 0.2rem rgba(203, 12, 159, 0.25);
    }

    .filter-section {
        margin-bottom: 15px;
    }

    .filter-title {
        color: #cb0c9f;
        font-weight: 600;
        margin-bottom: 15px;
        font-size: 1.1rem;
    }

    .filter-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 5px;
    }

    .btn-filter {
        background: linear-gradient(135deg, #cb0c9f 0%, #e91e63 100%);
        border: none;
        border-radius: 6px;
        padding: 10px 20px;
        color: white;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-filter:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(203, 12, 159, 0.3);
        color: white;
    }

    .btn-clear {
        background: #6c757d;
        border: none;
        border-radius: 6px;
        padding: 10px 20px;
        color: white;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-clear:hover {
        background: #5a6268;
        transform: translateY(-1px);
        color: white;
    }

    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        border-radius: 10px;
    }

    .spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #cb0c9f;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .table-container {
        position: relative;
    }

    .empty-state {
        text-align: center;
        padding: 40px;
        color: #6c757d;
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 15px;
        color: #dee2e6;
    }
</style>

<body class="g-sidenav-show  bg-gray-100">
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg " style=" top: 25px;">
        <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
            <div class="container-fluid py-1 px-3">
                <nav aria-label="breadcrumb">
                    <h6 class="font-weight-bolder mb-0">Topup Request List</h6>
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
                            <form id="frmFilter">
                                <?= csrf_field() ?>
                                <div class="filter-box">
                                    <h5 class="filter-title"><i class="fas fa-filter me-2"></i>Filter Box</h5>
                                    
                                    <!-- Filter Inputs -->
                                    <div class="filter-section">
                                        <div class="row align-items-end">
                                            <div class="col-md-3">
                                                <h6 class="filter-label">Start Date</h6>
                                                <input id="dtStart" name="dtStart" value="<?= session('cache')->dtStart ?? date('Y-m-01') ?>" type="date" class="form-control filter-input">
                                            </div>
                                            <div class="col-md-3">
                                                <h6 class="filter-label">End Date</h6>
                                                <input name="dtEnd" value="<?= session('cache')->dtEnd ?? '' ?>" type="date" class="form-control filter-input">
                                            </div>
                                            <div class="col-md-3">
                                                <h6 class="filter-label">Status</h6>
                                                <select name="status" class="form-control filter-input">
                                                    <option value="">All Statuses</option>
                                                    <option value="1">Pending</option>
                                                    <option value="5">Approved</option>
                                                    <option value="8">Rejected</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-check mb-2">
                                                    <input name="chkRequest" type="checkbox" class="form-check-input" id="chkRequest" <?= (session('cache')->chkRequest == "on" ? "checked" : "") ?>>
                                                    <label class="form-check-label" for="chkRequest">
                                                        Show All Requested
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="filter-section">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <button type="submit" class="btn btn-filter w-100">
                                                    <i class="fas fa-search me-2"></i>Apply Filters
                                                </button>
                                            </div>
                                            <div class="col-md-6">
                                                <button type="button" class="btn btn-clear w-100" id="btnClearFilter">
                                                    <i class="fas fa-times me-2"></i>Clear Filters
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="row">
                                <div class="col-7">
                                    <h4 class="pt-4">Topup Request List</h4>
                                    <button class="btn btn-danger" id="ExportPDF"><i class="fas fa-file-pdf"></i> Print PDF</button>
                                    <button class="btn" id="ExportExcel" style="background-color: #174e17; color: white"><i class="fas fa-file-excel"></i> Export Excel</button>
                                </div>
                                <?php if (!(session('level') >= 1 && session('level') <= 3)) { ?>
                                    <div class="col-5 text-end pt-4">
                                        <a href="<?= base_url('/CBase/NewTopup') ?>">
                                            <button class="btn btn-sm btn-outline-primary">New</button>
                                        </a>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>



                        <div class="card-body px-0 pt-0 pb-2">
                            <div class="table-responsive datatable-minimal p-3 table-container">
                                <div class="loading-overlay" id="loadingOverlay" style="display: none;">
                                    <div class="spinner"></div>
                                </div>
                                <table class="table align-items-center mb-0 display" id="tblTrans">
                                    <thead>
                                        <tr class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            <th class="ps-2">#</th>
                                            <th>Kode Request</th>
                                            <th>Member</th>
                                            <th>Amount</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Topup By</th>
                                            <th>Refused By</th>
                                            <th>Update Time</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="topup-requests-body">
                                        <!-- Data will be loaded via AJAX -->
                                    </tbody>
                                    <tfoot id="summary-row">
                                        <!-- Summary will be loaded via AJAX -->
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>

<script>
    let dataTable;
    const baseUrl = '<?= base_url() ?>';
    const userLevel = <?= session('level') ?? 0 ?>;

    $(document).ready(function() {
        // Initialize DataTable
        dataTable = $('#tblTrans').DataTable({
            paging: false,
            searching: false,
            info: false,
            ordering: true,
            columnDefs: [
                { orderable: false, targets: [9] } // Actions column
            ]
        });

        // Load initial data
        loadTopupRequests();

        // Form submission handler
        $('#frmFilter').on('submit', function(e) {
            e.preventDefault();
            loadTopupRequests();
        });

        // Clear filter handler
        $('#btnClearFilter').on('click', function() {
            clearFilters();
        });

        // Export handlers
        $('#ExportPDF').on('click', function() {
            exportData('PDF');
        });

        $('#ExportExcel').on('click', function() {
            exportData('Excel');
        });
    });

    function loadTopupRequests() {
        showLoading(true);
        
        const formData = $('#frmFilter').serialize();
        
        $.ajax({
            url: baseUrl + '/CTrans/ajaxTopupRequests',
            type: 'GET',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    renderTable(response.data);
                    renderSummary(response.summary);
                } else {
                    showError('Failed to load data: ' + (response.message || 'Unknown error'));
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                showError('Failed to load data. Please try again.');
            },
            complete: function() {
                showLoading(false);
            }
        });
    }

    function renderTable(data) {
        // Clear existing data
        dataTable.clear();
        
        if (data.length === 0) {
            $('#topup-requests-body').html(`
                <tr>
                    <td colspan="10" class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h5>No data found</h5>
                        <p>Try adjusting your filters to see more results.</p>
                    </td>
                </tr>
            `);
            return;
        }

        // Add rows to DataTable
        data.forEach(function(item, index) {
            const row = [
                index + 1,
                item.kodereq,
                item.username,
                formatAmount(item.amount),
                formatDate(item.tanggal),
                renderStatus(item.status),
                item.topup_by || '',
                item.refused_by || '',
                item.update_time ? formatDate(item.update_time) : '',
                renderActions(item)
            ];
            dataTable.row.add(row);
        });

        dataTable.draw();
    }

    function renderStatus(status) {
        let btnClass, icon, statusText;
        
        switch(parseInt(status)) {
            case 1:
                btnClass = 'btn-secondary';
                icon = 'fas fa-clock';
                statusText = 'Pending';
                break;
            case 5:
                btnClass = 'btn-success';
                icon = 'fas fa-square-check';
                statusText = 'Approved';
                break;
            case 8:
                btnClass = 'btn-danger';
                icon = 'fas fa-square-xmark';
                statusText = 'Rejected';
                break;
            default:
                btnClass = 'btn-warning';
                icon = 'fas fa-exclamation';
                statusText = 'Unknown';
        }

        return `<button class="btn btn-sm py-1 px-1 text-center ${btnClass}" style="border-radius: 2px; color: white" disabled title="${statusText}">
                    <i class="${icon}" style="font-size: large"></i>
                </button>`;
    }

    function renderActions(item) {
        let actions = '';
        const isProcessed = (item.status == 5 || item.status == 8);
        
        // Check user level permissions
        if (!(userLevel >= 1 && userLevel <= 3)) {
            if (!isProcessed) {
                actions += `<a href="${baseUrl}/CTrans/ProcessReqTopup/${item.kodereq}">
                                <button class="bg-transparent border-0 text-xs">TopUp</button>
                            </a> | 
                            <a href="${baseUrl}/CTrans/RefuseReqTopup/${item.kodereq}">
                                <button class="bg-transparent border-0 text-xs">Refuse</button>
                            </a>`;
            }
        }
        
        if (item.status == 5) {
            if (actions) actions += ' | ';
            actions += `<a href="${baseUrl}/CTrans/DetailTransactionRef/${item.kodereq}">
                            <button class="bg-transparent border-0 text-xs">Detail</button>
                        </a>`;
        }
        
        return actions || '-';
    }

    function renderSummary(summary) {
        const summaryHtml = `
            <tr>
                <th class="text-center" colspan="3">Total</th>
                <th class="text-right">${formatAmount(summary.total_amount || 0)}</th>
                <th colspan="6"></th>
            </tr>
        `;
        $('#summary-row').html(summaryHtml);
    }

    function formatAmount(amount) {
        return new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(amount);
    }

    function formatDate(dateString) {
        if (!dateString) return '';
        
        const date = new Date(dateString);
        const dateStr = date.toLocaleDateString('en-CA'); // YYYY-MM-DD format
        const timeStr = date.toLocaleTimeString('en-GB', { hour12: false }); // HH:mm:ss format
        
        return `${dateStr}<br>${timeStr}`;
    }

    function clearFilters() {
        $('#frmFilter')[0].reset();
        const xDate = '<?= date('Y-m-01') ?>';
        $('#dtStart').val('<?= date('Y-m-01') ?>');
        loadTopupRequests();
    }

    function showLoading(show) {
        if (show) {
            $('#loadingOverlay').show();
        } else {
            $('#loadingOverlay').hide();
        }
    }

    function showError(message) {
        // You can implement a toast notification or alert here
        alert(message);
    }

    function exportData(type) {
        const formData = $('#frmFilter').serialize();
        const url = `${baseUrl}/CExport/TopupRequest/${type}?${formData}`;
        window.open(url, '_blank');
    }
</script>
