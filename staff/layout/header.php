<?php include "../../settings/core.php"; ?>

<?php 
// Check user role
$userRoleID = getUserRoleID();
if ($userRoleID !== 2) {
    header("Location: ../../login/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>CRMS - Staff</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/assets/style.css">

    <!-- Heroicons -->
    <script src="https://unpkg.com/heroicons@2.0.18/dist/heroicons.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
    function runAutomaticUpdate() {
    fetch('../../functions/automate.php')
        .then(response => response.json())
        .then(data => {
            console.log('Resource/event status updated:', data);
        })
        .catch(err => console.error('Error updating status:', err));
    }

    // Run every minute
    setInterval(runAutomaticUpdate, 30000);

    // Optional: run immediately when popup opens/closes
    document.querySelectorAll('.popup-class').forEach(el => {
    el.addEventListener('click', () => {
        runAutomaticUpdate();
    });
    });
    </script>
</head>

<body class="bg-gray-50">

<header class="bg-white shadow-md px-6 py-3 flex justify-between items-center fixed top-0 w-full z-40">
    <button id="toggleSidebar" class="text-gray-600 hover:text-black text-2xl">
        ☰
    </button>

    <h1 class="text-xl font-semibold tracking-wide">
        Campus Resource Management System
    </h1>

    <div class="text-gray-500">
        Hello, <?= htmlspecialchars($USER_FULL_NAME) ?>
    </div>
</header>

<div class="h-16"></div>
