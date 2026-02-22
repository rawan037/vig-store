<?php
$conn = new mysqli("localhost","DB_USER","DB_PASS","invoices_db");

$result = $conn->query("SELECT * FROM invoices ORDER BY id DESC");
$invoices = [];

while($row = $result->fetch_assoc()){
  $invoices[] = $row;
}

echo json_encode($invoices);
$conn->close();
?>
