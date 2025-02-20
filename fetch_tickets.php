<?php
include 'config/db.php';

date_default_timezone_set('Asia/Kuala_Lumpur');

$limit = 10; //Number of tickets per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; //Get current page number
$offset = ($page - 1) * $limit; //Calculates where to start fetching tickets in the database.
$search = isset($_GET['search']) ? trim($_GET['search']) : ''; //Extracts search input (if any)

$query = "SELECT ticket_no, contact_details, issue_category, description, status, date_created, date_resolved, assigned_to FROM tickets";
$params = [];

//If there is a search query, applies a where filter to search
if ($search) {
    $query .= " WHERE contact_details LIKE :search 
                OR ticket_no LIKE :search 
                OR description LIKE :search
                OR issue_category LIKE :search 
                OR status LIKE :search
                OR assigned_to LIKE :search";
    $params[':search'] = "%$search%";
}

$query .= " ORDER BY ticket_no DESC LIMIT :limit OFFSET :offset"; //Latest ticket appears first, and Implements pagination using LIMIT and OFFSET

//Execute SQL query using PDO to prevent SQL injection
$stmt = $pdo->prepare($query); //prepare() = prepare(SQL QUERY) for execution
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value, PDO::PARAM_STR); //bindValue() = Binds a value to a parameter in the prepared statement.
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute(); //Sends the query to DB
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC); //Stores all the fetched tickets in the $tickets variable // fetchAll() = fetches all rows from result as an array

// Get total count 
$countQuery = "SELECT COUNT(*) as total FROM tickets";
if ($search) { //Counts only matching tickets when searching
    $countQuery .= " WHERE contact_details LIKE :search 
                     OR ticket_no LIKE :search 
                     OR issue_category LIKE :search 
                     OR status LIKE :search";
}
$totalStmt = $pdo->prepare($countQuery);
if ($search) {
    $totalStmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
}
$totalStmt->execute();
$totalRows = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalRows / $limit); //Calculates total pages

// Outputs a bootstrap-styled table with header only (No data)
$output = '<table class="table table-striped table-bordered table-sm">
    <thead class="table-dark">
        <tr>
            <th>Ticket No</th>
            <th>Contact</th>
            <th>Category</th>
            <th>Description</th>
            <th>Status</th>
            <th>Created</th>
            <th>Resolved</th>
            <th>Assigned</th>
        </tr>
    </thead>
    <tbody>';

//Populating each row with ticket data by looping through tickets
foreach ($tickets as $ticket) {
    $dateResolved = $ticket['status'] === 'Resolved' ? $ticket['date_resolved'] : '-'; //If status is resolved, date resolved is displayed, otherwise shows - 
    $readonly = $ticket['status'] === 'Resolved' ? 'readonly' : ''; //Status and assigned to becomes readonly if resolved

    $output .= "<tr>
    <td class='ticket_no'>{$ticket['ticket_no']}</td>
    <td>{$ticket['contact_details']}</td>
    <td>{$ticket['issue_category']}</td>
    <td>{$ticket['description']}</td>
    <td>
        <select class='form-select form-select-sm status' data-ticket='{$ticket['ticket_no']}' 
                data-original='{$ticket['status']}' $readonly>
            <option value='Ongoing' " . ($ticket['status'] == 'Ongoing' ? 'selected' : '') . ">Ongoing</option>
            <option value='Resolved' " . ($ticket['status'] == 'Resolved' ? 'selected' : '') . ">Resolved</option>
        </select>
    </td>
    <td>{$ticket['date_created']}</td>
    <td class='date_resolved'>$dateResolved</td>
    <td>
        <input type='text' class='form-control form-control-sm assigned_to' data-ticket='{$ticket['ticket_no']}'
               data-original='{$ticket['assigned_to']}' value='{$ticket['assigned_to']}' $readonly>
    </td>
</tr>";
}

$output .= '</tbody></table>';

// Return JSON with the html table, total pages, and total records
echo json_encode(['html' => $output, 'total_pages' => $totalPages, 'total_records' => $totalRows]);
