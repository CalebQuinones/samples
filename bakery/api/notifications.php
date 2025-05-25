<?php
session_start();
require_once '../config.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($method) {
    case 'GET':
        // Fetch notifications
        $since = isset($_GET['since']) ? intval($_GET['since']/1000) : 0;
        $sql = "SELECT * FROM notifications WHERE created_at > FROM_UNIXTIME(?) ORDER BY created_at DESC LIMIT 10";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $since);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $notifications = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $notifications[] = [
                'id' => $row['id'],
                'title' => $row['title'],
                'message' => $row['message'],
                'time' => $row['created_at'],
                'read' => $row['is_read'] == 1
            ];
        }
        echo json_encode(['notifications' => $notifications]);
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'add':
                    $sql = "INSERT INTO notifications (title, message) VALUES (?, ?)";
                    $stmt = mysqli_prepare($conn, $sql);
                    mysqli_stmt_bind_param($stmt, "ss", $data['title'], $data['message']);
                    mysqli_stmt_execute($stmt);
                    echo json_encode([
                        'id' => mysqli_insert_id($conn),
                        'title' => $data['title'],
                        'message' => $data['message'],
                        'time' => 'Just now',
                        'read' => false
                    ]);
                    break;
                    
                case 'read':
                    $sql = "UPDATE notifications SET is_read = 1 WHERE id = ?";
                    $stmt = mysqli_prepare($conn, $sql);
                    mysqli_stmt_bind_param($stmt, "i", $data['id']);
                    mysqli_stmt_execute($stmt);
                    echo json_encode(['success' => true]);
                    break;
                    
                case 'read-all':
                    mysqli_query($conn, "UPDATE notifications SET is_read = 1");
                    echo json_encode(['success' => true]);
                    break;
            }
        }
        break;
}
?>
