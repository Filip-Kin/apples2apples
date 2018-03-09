<?php
$id = $_GET["id"];
$conn = new mysqli('filipkin.com', 'a2a', 'password', 'a2a');
$sql = "DELETE FROM `ips` WHERE `code` = '$id'";
echo $conn->query($sql);
?>
