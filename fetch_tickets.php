<?php
include 'config/db.php';

date_default_timezone_set('Asia/Kuala_Lumpur');

$limit = 15;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Predefined list of assignees
$assignees = ["A", "B", "C"];

$query = "SELECT ticket_no, contact_details, issue_category, description, status, date_created, date_resolved, assigned_to FROM tickets";
$params = [];

if ($search) {
    $query .= " WHERE contact_details LIKE :search 
                OR ticket_no LIKE :search 
                OR description LIKE :search
                OR issue_category LIKE :search 
                OR status LIKE :search
                OR assigned_to LIKE :search";
    $params[':search'] = "%$search%";
}

$query .= " ORDER BY ticket_no DESC LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value, PDO::PARAM_STR);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

$countQuery = "SELECT COUNT(*) as total FROM tickets";
if ($search) {
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
$totalPages = ceil($totalRows / $limit);

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

foreach ($tickets as $ticket) {
    $dateResolved = $ticket['status'] === 'Resolved' ? $ticket['date_resolved'] : '-';
    $readonly = $ticket['status'] === 'Resolved' ? 'readonly' : '';

    $disabled = ($ticket['status'] === 'Resolved') ? 'disabled' : ''; // Only disable if resolved

    $assignedDropdown = "<select class='form-select form-select-sm assigned_to' data-ticket='{$ticket['ticket_no']}' $disabled>";
    $assignedDropdown .= "<option value='Unassigned'" . (empty($ticket['assigned_to']) || $ticket['assigned_to'] === 'Unassigned' ? ' selected' : '') . ">Unassigned</option>";

    foreach ($assignees as $assignee) {
        $selected = ($ticket['assigned_to'] === $assignee) ? 'selected' : '';
        $assignedDropdown .= "<option value='{$assignee}' {$selected}>{$assignee}</option>";
    }

    $assignedDropdown .= "</select>";

    $output .= "<tr>
        <td class='ticket_no'>{$ticket['ticket_no']}</td>
        <td>{$ticket['contact_details']}</td>
        <td>{$ticket['issue_category']}</td>
        <td>{$ticket['description']}</td>
        <td>
            <select class='form-select form-select-sm status' data-ticket='{$ticket['ticket_no']}' data-original='{$ticket['status']}' $readonly>
                <option value='Ongoing' " . ($ticket['status'] == 'Ongoing' ? 'selected' : '') . ">Ongoing</option>
                <option value='Resolved' " . ($ticket['status'] == 'Resolved' ? 'selected' : '') . ">Resolved</option>
            </select>
        </td>
        <td>{$ticket['date_created']}</td>
        <td class='date_resolved'>$dateResolved</td>
        <td>$assignedDropdown</td>
    </tr>";
}

$output .= '</tbody></table>';

echo json_encode(['html' => $output, 'total_pages' => $totalPages, 'total_records' => $totalRows]);
