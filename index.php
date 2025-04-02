<?php
date_default_timezone_set('Asia/Kuala_Lumpur'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Tickets</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body>
    <div class="container mt-5">
        <h2 class="mb-4 text-center">Support Tickets</h2>

        <!-- Display Total Tickets -->
        <div class="text-center mt-3">
            <h5>Total Tickets: <span id="ticketCount">Loading...</span></h5>
        </div>
        <!-- Search Input -->
        <div class="row">
            <div class="col-md-6 mx-auto d-flex">
                <input type="text" id="search" class="form-control" placeholder="Search Contact, Name, Ticket No, Description, Category, Status...">
                <button class="btn btn-secondary ms-2" id="refreshTable">
                    <i class="bi bi-arrow-clockwise"></i> Refresh
                </button>
            </div>
        </div>

        <!-- Responsive Table -->
        <div class="table-responsive mt-3 table-sm">
            <div id="ticketTable"></div>
        </div>

        <!-- Pagination and Action Buttons -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-3">
            <div class="mb-2 mb-md-0">
                <button class="btn btn-primary me-2" id="prevPage">Previous</button>
                <span id="pageNumber"></span>
                <button class="btn btn-primary ms-2" id="nextPage">Next</button>
            </div>
            <button class="btn btn-success" id="applyChanges">Apply Changes</button>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Success</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Changes applied successfully!
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Modal -->
    <div class="modal fade" id="errorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Error</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Failed to apply changes.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- No Changes Modal -->
    <div class="modal fade" id="noChangesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">No Changes Detected</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    There are no changes to apply.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Refresh Modal -->
    <div class="modal fade" id="refreshModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Refresh Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Table has been refreshed successfully!
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/tickets.js"></script>
</body>

</html>