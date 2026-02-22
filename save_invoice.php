<?php
$conn = new mysqli("localhost","DB_USER","DB_PASS","invoices_db");

$data = json_decode(file_get_contents("php://input"), true);

$stmt = $conn->prepare("INSERT INTO invoices 
(invoice_number,customer_name,customer_phone,total,items) 
VALUES (?,?,?,?,?)");

$items = json_encode($data['items']);

$stmt->bind_param("sssds",
$data['invoice_number'],
$data['customer_name'],
$data['customer_phone'],
$data['total'],
$items
);

$stmt->execute();
$conn->close();
?>
