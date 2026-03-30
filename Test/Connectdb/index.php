<?php
include('database.php');

$sql = "SELECT * FROM user";

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "ID: " . $row["id"] . "<br>";
        echo "Username:" . $row["user"] . "<br>";
        echo "Password: " . $row["password"] . "<br>";
        echo "Date Created: " . $row["reg_date"] . "<br>";
    }
}


mysqli_close($conn);
