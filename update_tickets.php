<?php
include 'config/db.php';

// Display and log PHP errors if something is wrong
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Ensure the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!empty($data['tickets'])) { // Ensure there are tickets to update
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("UPDATE tickets 
                SET status = :status, 
                    assigned_to = CASE WHEN :assigned_to IS NOT NULL AND :assigned_to != '' THEN :assigned_to ELSE assigned_to END, 
                    date_resolved = :date_resolved 
                WHERE ticket_no = :ticket_no");

            foreach ($data['tickets'] as $ticket) {
                $dateResolved = ($ticket['status'] === 'Resolved') ? date("Y-m-d H:i:s") : null;

                $stmt->execute([
                    ':status' => $ticket['status'],
                    ':assigned_to' => isset($ticket['assigned_to']) ? $ticket['assigned_to'] : null,
                    ':date_resolved' => $dateResolved,
                    ':ticket_no' => $ticket['ticket_no']
                ]);
            }

            $pdo->commit();
            echo json_encode(["success" => true]);
        } catch (Exception $e) {
            $pdo->rollBack();
            echo json_encode(["success" => false, "error" => $e->getMessage()]);
        }
    } else {
        echo json_encode(["success" => false, "error" => "No data received"]);
    }
}
