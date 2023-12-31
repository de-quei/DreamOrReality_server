<?php
include('db_conn.php');

$stmt = $conn->prepare("SELECT * FROM user");
$stmt->execute();
$result = $stmt->get_result();

$rows = array();
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}

// 데이터를 JSON으로 반환
echo json_encode($rows);
?>