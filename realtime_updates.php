<?php
header("Content-Type: text/event-stream");
header("Cache-Control: no-cache");
header("Connection: keep-alive");
header("X-Accel-Buffering: no"); // Prevents Nginx from buffering output

include 'config/db.php';

@ini_set('max_execution_time', 0);
@ini_set('output_buffering', 'off');
@ini_set('zlib.output_compression', 0);
@ini_set('implicit_flush', 1);
while (ob_get_level()) {
    ob_end_flush();
}

$lastTotal = 0;

while (true) {
    // Fetch latest ticket count
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM tickets");
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Only send data if there's a change
    if ($total !== $lastTotal) {
        $lastTotal = $total;
        echo "data: " . json_encode(["total_records" => $total]) . "\n\n";
        ob_flush();
        flush(); // Send data immediately
    }

    sleep(2); // Check for updates every 2 seconds
}
