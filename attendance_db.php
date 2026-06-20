<?php

$conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "attendance_db"
);

if(!$conn){
    die("Connection Failed: " . mysqli_connect_error());
}

echo "Database Connected Successfully";

?>