<?php
include 'config/db.php';

//Display and PHP errors if something is wrong
ini_set('display_errors', 1);
error_reporting(E_ALL);

//Checks if its a POST request, only running script when a POST request is made
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"), true); //Reads the raw JSON msg send from ticket.js

    if (!empty($data['tickets'])) { //Ensure that there are actual tickets to update
        try {
            $pdo->beginTransaction(); //Starts the DB transaction
            $stmt = $pdo->prepare("UPDATE tickets SET status = :status, assigned_to = :assigned_to, date_resolved = :date_resolved WHERE ticket_no = :ticket_no"); //prepare the statement // Update tickets based on specific fields

            //Loops through each ticket and update the database
            foreach ($data['tickets'] as $ticket) {
                $dateResolved = ($ticket['status'] === 'Resolved') ? date("Y-m-d H:i:s") : null; //If resolved, set date resolved to current timestamp, NULL if not

                //Execute the query
                $stmt->execute([
                    ':status' => $ticket['status'],
                    ':assigned_to' => $ticket['assigned_to'],
                    ':date_resolved' => $dateResolved,
                    ':ticket_no' => $ticket['ticket_no']
                ]);
            }

            $pdo->commit();
            echo json_encode(["success" => true]);
        }
        //Handles error if something goes wrong and rollback changes
        catch (Exception $e) {
            $pdo->rollBack();
            echo json_encode(["success" => false, "error" => $e->getMessage()]);
        }
    }
    //Handles cases where no data is received
    else {
        echo json_encode(["success" => false, "error" => "No data received"]);
    }
}
