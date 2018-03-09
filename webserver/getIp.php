<?php
$code = $_GET["id"];
$conn = new mysqli('filipkin.com', 'a2a', 'password', 'a2a');
$sql = "SELECT `ip` FROM `ips` WHERE `code` = '$code'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo $row["ip"];
    }
} else {
    echo "0";
}
$conn->close();
?>
