<?php
session_start();
include '../settings/core.php';

// Run the automatic update function
updateResourceAndEventStatus();

echo json_encode(['status' => 'success']);
