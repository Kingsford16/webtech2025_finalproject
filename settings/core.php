<?php
session_start();
include 'connection.php';

if (!isLoggedIn()) {
    header("Location: ../login/login.php");
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to get the user's role ID
function getUserRoleID() {
    if (isset($_SESSION['role_id'])) {
        return $_SESSION['role_id'];
    } else {
        return false;
    }
}

if (isset($_GET['get_times']) && isset($_GET['hour_period'])) {

    $hp = intval($_GET['hour_period']);

    $sql = "SELECT eventimes_id, time FROM Eventimes WHERE hour_period = $hp";
    $result = mysqli_query($conn, $sql);

    $times = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $times[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($times);
    exit;
}

function get_all_durations() {
    global $conn;
    $sql = "SELECT dur_id, dur_category FROM durations ORDER BY dur_category ASC";
    $result = $conn->query($sql);
    $data = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    return $data;
}

function get_eventimes_by_hour_period($hour_period) {
    global $conn;
    $sql = "SELECT eventimes_id, time, hour_period FROM eventimes WHERE hour_period = ? ORDER BY eventimes_id ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $hour_period);
    $stmt->execute();
    $res = $stmt->get_result();
    $data = [];
    while ($row = $res->fetch_assoc()) {
        $data[] = $row;
    }
    $stmt->close();
    return $data;
}

// Function to get all resources
function get_all_resources() {
    global $conn;

    $sql = "
        SELECT
            r.res_id,
            r.res_name,
            r.res_img,
            r.res_status,
            l.loc_name,
            cs.cs_status

        FROM resources r
        LEFT JOIN locations l        ON r.loc_id = l.loc_id
        LEFT JOIN currentstatus cs   ON r.res_status = cs.cs_id
        LEFT JOIN resmanagers rm     ON r.res_id = rm.res_id
        LEFT JOIN users u            ON rm.user_id = u.user_id
        LEFT JOIN departments d      ON rm.dep_id = d.dep_id

        GROUP BY r.res_id
        ORDER BY r.res_name
    ";

    $result = $conn->query($sql);

    if (!$result) {
        return []; 
    }

    $resources = [];
    while ($row = $result->fetch_assoc()) {
        $resources[] = $row;
    }
    return $resources;
}

function get_all_resources_assigned_to_each_manager() {
    global $conn;

    // Ensure the logged-in manager exists
    if (!isset($_SESSION['user_id'])) {
        return [];
    }

    $manager_id = $_SESSION['user_id'];

    $sql = "
        SELECT
            r.res_id,
            r.res_name,
            r.res_img,
            r.res_status,

            l.loc_name,
            cs.cs_status,

            rm.rm_id,
            u.fname,
            u.lname,
            d.dep_name

        FROM resources r
        LEFT JOIN locations l        ON r.loc_id = l.loc_id
        LEFT JOIN currentstatus cs   ON r.res_status = cs.cs_id
        LEFT JOIN resmanagers rm     ON r.res_id = rm.res_id
        LEFT JOIN users u            ON rm.user_id = u.user_id
        LEFT JOIN departments d      ON rm.dep_id = d.dep_id

        WHERE rm.user_id = ?
        ORDER BY r.res_name ASC
    ";

    // PREPARE STATEMENT
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("SQL prepare error: " . $conn->error);
    }

    $stmt->bind_param("i", $manager_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $resources = [];

    while ($row = $result->fetch_assoc()) {
        $resources[] = $row;
    }

    return $resources;
}

/**
 * Count "My Events" for the logged-in student.
 * Conditions: app_id = 1, pro_id = 2, event_date >= today
 */
function count_my_events_for_student() {
    global $conn;

    if (!isset($_SESSION['user_id'])) {
        return 0;
    }

    $student_id = intval($_SESSION['user_id']);
    $today = date("Y-m-d");

    $sql = "
        SELECT COUNT(*) AS total
        FROM bookings b
        WHERE b.user_id = ?
          AND b.app_id = 1
          AND b.pro_id = 2
          AND b.event_date >= ?
    ";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return 0;
    }

    $stmt->bind_param("is", $student_id, $today);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return (int) ($res['total'] ?? 0);
}

function get_upcoming_events_for_admin() {
    global $conn;

    if (!isset($_SESSION['user_id'])) {
        return [];
    }

    $today = date("Y-m-d");
    $current_time = date("H:i:s"); // Current time in H:i:s format

    $sql = "
        SELECT
            b.booking_id,
            b.user_id,
            b.rm_id,
            b.res_id,
            b.app_id,
            b.pro_id,
            b.et_id,
            b.event_date,
            b.capacity,
            b.purpose,
            b.datetime_of_booking,

            -- resource info
            r.res_name,
            r.res_img,
            r.res_status,

            -- resource status label
            cs.cs_status,

            -- event time (Eventimes)
            et.times AS event_time,
            et.eventimes_id,
            CONCAT(b.event_date, ' @', et.times) AS datetime,

            -- resource manager record (resmanagers)
            rm.rm_id AS resmanager_id,
            rm.dep_id AS manager_dep_id,

            -- manager user info
            mu.user_id AS manager_user_id,
            CONCAT(mu.fname, ' ', mu.lname) AS manager_fullname,
            mu.email AS manager_email,
            mu.phone AS manager_phone,
            mu.user_img AS manager_img,

            -- department name (if available)
            d.dep_name AS manager_dep_name,

            -- student info (user_role = 3)
            su.user_id AS student_user_id,
            CONCAT(su.fname, ' ', su.lname) AS student_fullname,
            su.email AS student_email,
            su.phone AS student_phone,
            su.user_img AS student_img

        FROM bookings b
        LEFT JOIN resources r    ON b.res_id = r.res_id
        LEFT JOIN currentstatus cs ON r.res_status = cs.cs_id
        LEFT JOIN eventimes et   ON b.et_id = et.eventimes_id
        -- resource manager for that resource
        LEFT JOIN resmanagers rm ON b.res_id = rm.res_id
        LEFT JOIN users mu       ON rm.user_id = mu.user_id
        LEFT JOIN departments d  ON rm.dep_id = d.dep_id
        -- student (user with user_role = 3)
        LEFT JOIN users su       ON b.user_id = su.user_id AND su.user_role = 3

        WHERE b.app_id = 1
          AND b.pro_id = 2
          AND b.event_date >= ?
          AND et.times NOT LIKE CONCAT('% - ', ?)
        ORDER BY b.event_date ASC, et.eventimes_id ASC
    ";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return [];
    }

    $stmt->bind_param("ss", $today, $current_time);
    $stmt->execute();
    $result = $stmt->get_result();

    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }

    $stmt->close();
    return $rows;
}

/**
 * Get "My Events" for the logged-in student with resource + manager contact.
 * Conditions: app_id = 1, pro_id = 2, event_date >= today
 * ORDER BY event_date ASC (earliest first)
 */
function get_my_events_for_student() {
    global $conn;

    if (!isset($_SESSION['user_id'])) {
        return [];
    }

    $student_id = intval($_SESSION['user_id']);
    $today = date("Y-m-d");

    $sql = "
        SELECT
            b.booking_id,
            b.user_id,
            b.rm_id,
            b.res_id,
            b.app_id,
            b.pro_id,
            b.et_id,
            b.event_date,
            b.capacity,
            b.purpose,
            b.datetime_of_booking,

            -- resource info
            r.res_name,
            r.res_img,
            r.res_status,

            -- resource status label
            cs.cs_status,

            -- event time (Eventimes)
            et.times AS event_time,

            -- resource manager record (resmanagers)
            rm.rm_id AS resmanager_id,
            rm.dep_id AS manager_dep_id,

            -- manager user info
            mu.user_id AS manager_user_id,
            mu.fname AS manager_fname,
            mu.lname AS manager_lname,
            mu.email AS manager_email,
            mu.phone AS manager_phone,
            mu.user_img AS manager_img,

            -- department name (if available)
            d.dep_name AS manager_dep_name

        FROM bookings b
        LEFT JOIN resources r    ON b.res_id = r.res_id
        LEFT JOIN currentstatus cs ON r.res_status = cs.cs_id
        LEFT JOIN eventimes et   ON b.et_id = et.eventimes_id
        -- resource manager for that resource
        LEFT JOIN resmanagers rm ON b.res_id = rm.res_id
        LEFT JOIN users mu       ON rm.user_id = mu.user_id
        LEFT JOIN departments d  ON rm.dep_id = d.dep_id

        WHERE b.user_id = ?
          AND b.app_id = 1
          AND b.pro_id = 2
          AND b.event_date >= ?

        ORDER BY b.event_date ASC, et.eventimes_id ASC
    ";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return [];
    }

    $stmt->bind_param("is", $student_id, $today);
    $stmt->execute();
    $result = $stmt->get_result();

    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }

    $stmt->close();
    return $rows;
}

// --- STUDENT PENDING REQUESTS HELPERS --------------------------------

/**
 * Count pending booking requests for the currently logged-in student.
 * Conditions: app_id = 2, pro_id = 2
 */
function count_pending_requests_for_student() {
    global $conn;

    if (!isset($_SESSION['user_id'])) {
        return 0;
    }

    $student_id = intval($_SESSION['user_id']);

    $today = date("Y-m-d");

    $sql = "
        SELECT COUNT(*) AS total
        FROM bookings b
        WHERE b.user_id = ?
          AND b.app_id = 2
          AND b.pro_id = 2
          AND event_date >= ?
    ";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return 0;
    }

    $stmt->bind_param("is", $student_id, $today);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return (int) ($res['total'] ?? 0);
}

/**
 * Get pending bookings for the currently logged-in student with resource + manager contact.
 * Conditions: app_id = 2, pro_id = 2
 * ORDER BY b.datetime_of_booking ASC
 */
function get_pending_bookings_for_student() {
    global $conn;

    if (!isset($_SESSION['user_id'])) {
        return [];
    }

    $student_id = intval($_SESSION['user_id']);

    $today = date("Y-m-d");

    $sql = "
        SELECT
            b.booking_id,
            b.user_id,
            b.rm_id,
            b.res_id,
            b.app_id,
            b.pro_id,
            b.et_id,
            b.event_date,
            b.capacity,
            b.purpose,
            b.datetime_of_booking,

            -- resource info
            r.res_name,
            r.res_img,

            -- resource manager info (user record)
            m.user_id AS manager_user_id,
            m.fname AS manager_fname,
            m.lname AS manager_lname,
            m.email AS manager_email,
            m.phone AS manager_phone,
            m.user_img AS manager_img,

            -- event time (from Eventimes table)
            e.times AS event_time,
            d.dep_name AS manager_dep_name

        FROM bookings b
        LEFT JOIN resources r ON b.res_id = r.res_id
        -- find the resource manager record for this resource and then the user details for that manager
        LEFT JOIN resmanagers rm ON b.res_id = rm.res_id
        LEFT JOIN users m ON rm.user_id = m.user_id
        LEFT JOIN eventimes e ON b.et_id = e.eventimes_id
        LEFT JOIN departments d  ON rm.dep_id = d.dep_id

        WHERE b.user_id = ?
          AND b.app_id = 2
          AND b.pro_id = 2
          AND event_date >= ?

        ORDER BY b.datetime_of_booking ASC
    ";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return [];
    }

    $stmt->bind_param("is", $student_id, $today);
    $stmt->execute();
    $result = $stmt->get_result();

    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }

    $stmt->close();
    return $rows;
}

function count_new_requests_for_manager() {
    global $conn;

    if (!isset($_SESSION['user_id'])) {
        return 0;
    }

    $manager_id = $_SESSION['user_id'];

    $today = date("Y-m-d");

    $sql = "
        SELECT COUNT(*) AS total
        FROM bookings b
        INNER JOIN resmanagers rm ON rm.res_id = b.res_id
        WHERE rm.user_id = ?
          AND b.app_id = 2
          AND b.pro_id = 2
          AND event_date >= ?
    ";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return 0;
    }

    $stmt->bind_param("is", $manager_id, $today);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    return (int)$result['total'];
}

function count_events_for_each_manager_view() {
    global $conn;

    if (!isset($_SESSION['user_id'])) {
        return 0;
    }

    $manager_id = $_SESSION['user_id'];

    $today = date("Y-m-d");

    $sql = "
        SELECT COUNT(*) AS total
        FROM bookings b
        INNER JOIN resmanagers rm ON rm.res_id = b.res_id
        WHERE rm.user_id = ?
          AND b.app_id = 1
          AND b.pro_id = 2
          AND event_date >= ?
    ";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return 0;
    }

    $stmt->bind_param("is", $manager_id, $today);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    return (int)$result['total'];
}

function get_all_bookings_for_resources_assigned_to_each_manager() {
    global $conn;

    // Ensure manager is logged in
    if (!isset($_SESSION['user_id'])) {
        return [];
    }

    $manager_id = $_SESSION['user_id'];

    // Today's date (used to prevent past events from showing)
    $today = date("Y-m-d");

    $sql = "
        SELECT
            b.booking_id,
            b.user_id AS student_id,
            b.rm_id,
            b.res_id,
            b.app_id,
            b.pro_id,
            b.et_id,
            b.event_date,
            b.capacity,
            b.purpose,
            b.datetime_of_booking,

            -- USER (STUDENT) INFO
            u.fname,
            u.lname,
            CONCAT(u.fname, ' ', u.lname) AS full_name,
            u.email,
            u.phone,
            u.staff_or_student_id,
            u.user_img,

            -- RESOURCE INFO
            r.res_name,
            r.res_img,

            -- EVENT TIME
            et.times AS event_time

        FROM bookings b

        -- JOIN STUDENT DETAILS
        LEFT JOIN users u 
            ON b.user_id = u.user_id

        -- JOIN RESOURCE DETAILS
        LEFT JOIN resources r 
            ON b.res_id = r.res_id

        -- EVENTIMES JOIN
        LEFT JOIN eventimes et
            ON b.et_id = et.eventimes_id

        -- CHECK IF MANAGER IS ASSIGNED TO THIS RESOURCE
        INNER JOIN resmanagers rm
            ON rm.res_id = b.res_id

        WHERE rm.user_id = ?
          AND b.app_id = 2
          AND b.pro_id = 2
          AND b.event_date >= ?  

        ORDER BY b.datetime_of_booking ASC
    ";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("SQL Error: " . $conn->error);
    }

    $stmt->bind_param("is", $manager_id, $today);
    $stmt->execute();
    $result = $stmt->get_result();

    $bookings = [];
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }

    return $bookings;
}

function get_all_events_for_each_manager_view() {
    global $conn;

    // Ensure manager is logged in
    if (!isset($_SESSION['user_id'])) {
        return [];
    }

    $manager_id = $_SESSION['user_id'];

    // Today's date (used to prevent past events from showing)
    $today = date("Y-m-d");

    $sql = "
        SELECT
            b.booking_id,
            b.user_id AS student_id,
            b.rm_id,
            b.res_id,
            b.app_id,
            b.pro_id,
            b.et_id,
            b.event_date,
            b.capacity,
            b.purpose,
            b.datetime_of_booking,

            -- USER (STUDENT) INFO
            u.fname,
            u.lname,
            CONCAT(u.fname, ' ', u.lname) AS full_name,
            u.email,
            u.phone,
            u.staff_or_student_id,
            u.user_img,

            -- RESOURCE INFO
            r.res_name,
            r.res_img,

            -- EVENT TIME
            et.times AS event_time

        FROM bookings b

        -- JOIN STUDENT DETAILS
        LEFT JOIN users u 
            ON b.user_id = u.user_id

        -- JOIN RESOURCE DETAILS
        LEFT JOIN resources r 
            ON b.res_id = r.res_id

        -- EVENTIMES JOIN
        LEFT JOIN eventimes et
            ON b.et_id = et.eventimes_id

        -- CHECK IF MANAGER IS ASSIGNED TO THIS RESOURCE
        INNER JOIN resmanagers rm
            ON rm.res_id = b.res_id

        WHERE rm.user_id = ?
          AND b.app_id = 1
          AND b.pro_id = 2
          AND b.event_date >= ?  

        ORDER BY b.event_date ASC
    ";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("SQL Error: " . $conn->error);
    }

    $stmt->bind_param("is", $manager_id, $today);
    $stmt->execute();
    $result = $stmt->get_result();

    $bookings = [];
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }

    return $bookings;
}

// Function to get all inactive resources
function get_all_inactive_resources() {
    global $conn;
    
    // Write the SELECT query to get all inactive resources
    $sql = "SELECT *
            FROM resources r
            LEFT JOIN locations l ON r.loc_id = l.loc_id
            LEFT JOIN currentstatus s ON r.res_status = s.cs_id
            WHERE res_status = 2";
    
    // Execute the query
    $result = $conn->query($sql);
    
    // Check if execution worked
    if ($result) {
        // Check if any record was returned
        if ($result->num_rows > 0) {
            // Fetch records and store them in an array
            $resources = array();
            while ($row = $result->fetch_assoc()) {
                $resources[] = $row;
            }
            return $resources;
        } else {
            return array(); // Return an empty array if no records found
        }
    } else {
        // Return false if query execution failed
        return false;
    }
}

// Function to get all resource managers
function get_all_resource_managers() {
    global $conn;

    $sql = "
        SELECT 
            rm.rm_id,
            rm.res_id,
            u.user_id,

            -- Combine first name + last name
            CONCAT(u.fname, ' ', u.lname) AS full_name,

            u.email,
            u.staff_or_student_id,
            u.phone,
            u.user_img,
            d.dep_name,

            -- Count assigned resources
            COUNT(r.res_id) AS assigned_count,

            -- List all assigned resources
            GROUP_CONCAT(r.res_name SEPARATOR ', ') AS assigned_resources,

            r.res_name,
            r.res_status,
            cs.cs_status
        FROM resmanagers rm
        JOIN users u      ON rm.user_id = u.user_id
        JOIN departments d ON rm.dep_id = d.dep_id
        JOIN resources r   ON rm.res_id = r.res_id
        JOIN currentstatus cs  ON r.res_status = cs.cs_id

        GROUP BY u.user_id   -- ensures each manager appears only once

    ";

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    return [];
}

// Function to get all resource managers assigned ONLY to inactive resources
function get_all_inactive_resource_managers() {
    global $conn;

    $sql = "
        SELECT 
            rm.rm_id,
            rm.dep_id,
            rm.user_id,
            rm.res_id,
            u.user_id,

            -- Combine first name + last name
            CONCAT(u.fname, ' ', u.lname) AS full_name,

            u.email,
            u.staff_or_student_id,
            u.phone,
            u.user_img,
            d.dep_name,

            -- Count assigned resources
            COUNT(r.res_id) AS assigned_count,

            -- List all assigned resources
            GROUP_CONCAT(r.res_name SEPARATOR ', ') AS assigned_resources,

            r.res_name,
            r.res_status,
            cs.cs_status
        FROM resmanagers rm
        JOIN users u      ON rm.user_id = u.user_id
        JOIN departments d ON rm.dep_id = d.dep_id
        JOIN resources r   ON rm.res_id = r.res_id
        JOIN currentstatus cs  ON r.res_status = cs.cs_id

        -- Only select inactive resources
        WHERE r.res_status = 2   -- 2 means inactive

        -- EXCLUDE users who have at least one active resource
        AND u.user_id NOT IN (
            SELECT DISTINCT rm2.user_id
            FROM resmanagers rm2
            JOIN resources r2 ON rm2.res_id = r2.res_id
            WHERE r2.res_status = 1
        )

        GROUP BY u.user_id   -- ensures each manager appears only once

    ";

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }

    return [];
}

function getUserFullName($conn) {
    if (!isset($_SESSION['user_id'])) return null;
    $uid = $_SESSION['user_id'];
    $sql = "SELECT fname, lname FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $stmt->bind_result($first, $last);
    $stmt->fetch();
    $stmt->close();
    return trim($first . " " . $last);
}

function getUserEmail($conn) {
    if (!isset($_SESSION['user_id'])) return null;
    $uid = $_SESSION['user_id'];
    $sql = "SELECT email FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $stmt->bind_result($email);
    $stmt->fetch();
    $stmt->close();
    return $email;
}

function getUserPhone($conn) {
    if (!isset($_SESSION['user_id'])) return null;
    $uid = $_SESSION['user_id'];
    $sql = "SELECT phone FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $stmt->bind_result($phone);
    $stmt->fetch();
    $stmt->close();
    return $phone;
}

function getUserImage($conn) {
    if (!isset($_SESSION['user_id'])) return '/images/placeholder.jpg';

    $uid = $_SESSION['user_id'];
    $sql = "SELECT user_img FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $uid);
    $stmt->execute();
    $stmt->bind_result($img);
    $stmt->fetch();
    $stmt->close();

    // If user has no image, return placeholder
    return !empty($img) ? $img : '../../images/placeholder.jpg';
}

$USER_FULL_NAME = getUserFullName($conn);
$USER_IMAGE = getUserImage($conn);
$USER_EMAIL = getUserEmail($conn);
$USER_PHONE = getUserPhone($conn);

/**
 * AUTOMATION FUNCTION
 * Automatically updates resources and booking progress statuses based on event times
 */
function updateResourceAndEventStatus() {
    global $conn;

    date_default_timezone_set('Africa/Accra'); // timezone
    $nowDate = date('Y-m-d');
    $nowTime = date('H:i');

    // --- 1. Update ongoing events: mark resources as occupied (res_status = 1) ---
    $ongoingSql = "
        SELECT b.res_id, et.times
        FROM bookings b
        JOIN eventimes et ON b.et_id = et.eventimes_id
        WHERE b.app_id = 1 AND b.pro_id = 2 AND b.event_date = ?
    ";
    $stmt = $conn->prepare($ongoingSql);
    $stmt->bind_param("s", $nowDate);
    $stmt->execute();
    $res = $stmt->get_result();

    $occupiedResIds = [];
    while ($row = $res->fetch_assoc()) {
        list($startTime, $endTime) = explode(' - ', $row['times']);
        if ($nowTime >= $startTime && $nowTime <= $endTime) {
            $occupiedResIds[] = $row['res_id'];
        }
    }
    $stmt->close();

    // Update resources that are currently occupied
    if (!empty($occupiedResIds)) {
        $ids = implode(',', $occupiedResIds);
        $conn->query("UPDATE resources SET res_status = 1 WHERE res_id IN ($ids) AND res_status != 1");
    }

    // --- 2. Update resources that are free (no ongoing event) ---
    $allResSql = "SELECT res_id FROM resources";
    $allResResult = $conn->query($allResSql);
    $allResIds = [];
    while ($row = $allResResult->fetch_assoc()) {
        $allResIds[] = $row['res_id'];
    }

    $freeResIds = array_diff($allResIds, $occupiedResIds);
    if (!empty($freeResIds)) {
        $ids = implode(',', $freeResIds);
        $conn->query("UPDATE resources SET res_status = 2 WHERE res_id IN ($ids) AND res_status != 2");
    }

    // --- 3. Complete past events (app_id = 1, pro_id = 2) ---
    $pastEventsSql = "
        SELECT b.booking_id, et.times, b.event_date
        FROM bookings b
        JOIN eventimes et ON b.et_id = et.eventimes_id
        WHERE b.app_id = 1 AND b.pro_id = 2
    ";
    $stmt = $conn->prepare($pastEventsSql);
    $stmt->execute();
    $res = $stmt->get_result();

    $completeBookings = [];
    while ($row = $res->fetch_assoc()) {
        list($startTime, $endTime) = explode(' - ', $row['times']);
        if ($row['event_date'] < $nowDate || ($row['event_date'] == $nowDate && $nowTime > $endTime)) {
            $completeBookings[] = $row['booking_id'];
        }
    }
    $stmt->close();

    if (!empty($completeBookings)) {
        $ids = implode(',', $completeBookings);
        $conn->query("UPDATE bookings SET pro_id = 1 WHERE booking_id IN ($ids)");
    }

    // --- 4. Cancel past pending requests (app_id = 2, pro_id = 2) ---
    $pastPendingSql = "
        SELECT b.booking_id, et.times, b.event_date
        FROM bookings b
        JOIN eventimes et ON b.et_id = et.eventimes_id
        WHERE b.app_id = 2 AND b.pro_id = 2
    ";
    $stmt = $conn->prepare($pastPendingSql);
    $stmt->execute();
    $res = $stmt->get_result();

    $cancelBookings = [];
    while ($row = $res->fetch_assoc()) {
        list($startTime, $endTime) = explode(' - ', $row['times']);
        if ($row['event_date'] < $nowDate || ($row['event_date'] == $nowDate && $nowTime > $endTime)) {
            $cancelBookings[] = $row['booking_id'];
        }
    }
    $stmt->close();

    if (!empty($cancelBookings)) {
        $ids = implode(',', $cancelBookings);
        $conn->query("UPDATE bookings SET pro_id = 3 WHERE booking_id IN ($ids)");
    }
}

// --- AUTOMATICALLY RUN THE FUNCTION ON EVERY PAGE LOAD ---
updateResourceAndEventStatus();
?>
