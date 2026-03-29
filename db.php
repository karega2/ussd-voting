<?php
$conn = new mysqli("localhost","root","","school_voting");

if ($conn->connect_error) {
    die("Connection failed");
}
?>