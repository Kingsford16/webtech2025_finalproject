<?php

$SERVER = "localhost";
$USERNAME = "root";
$PASSWD = "";
$DATABASE = "crms";



$conn = new mysqli($SERVER, $USERNAME, $PASSWD, $DATABASE) or die ("Could not connect database");

// Check if connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


