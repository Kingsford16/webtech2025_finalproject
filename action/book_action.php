<?php
session_start();
include "../settings/connection.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Retrieve and sanitize inputs
    $user_id = $_SESSION['user_id'];
    $rm_id = NULL;
    $res_id = $_POST['res_id'];
    $et_id = $_POST['event_time'];     
    $capacity = $_POST['capacity'];
    $event_date = trim($_POST['event_date']);
    $purpose = $_POST['purpose'];
    $app_id = 2; // pending
    $pro_id = 2; // uncompleted
    date_default_timezone_set('Africa/Accra'); // Ghana time
    $datetime_of_booking = date('Y-m-d H:i:s'); // current date & time in Ghana [e.g., 2025-12-05 18:45:30]

    /* -------------------------------------------------------
       VALIDATIONS
    ------------------------------------------------------- */

// Event date must be in the future
if (!empty($event_date)) {
    $selected = strtotime($event_date);
    $today = strtotime(date("Y-m-d"));

    if ($selected <= $today) {
        // Send error and old form values back
        $params = http_build_query([
            'name_error' => "You can only select a future date",
            'res_id' => $res_id,
            'res_name' => $res_name ?? '', // resource name for readonly field
            'capacity' => $capacity,
            'event_date' => $event_date,
            'purpose' => $purpose,
            'event_time' => $et_id
        ]);

        header("Location: ../student/view/book.php?$params");
        exit;
    }
}


    // Validate purpose: at least 3 characters
if (!preg_match('/\S.{2,}/', $purpose)) { // \S ensures at least one non-space character
    $params = http_build_query([
        'name_error' => "Purpose must have at least 3 characters",
        'res_id' => $res_id,
        'res_name' => $_POST['res_name'] ?? '',
        'capacity' => $capacity,
        'event_date' => $event_date,
        'purpose' => $purpose,
        'event_time' => $et_id
    ]);
    header("Location: ../student/view/book.php?$params");
    exit;
}

// Verify resource exists and is active
$sqlResCheck = "SELECT res_id FROM resources WHERE res_id = ?";
$stmtCheck = $conn->prepare($sqlResCheck);
$stmtCheck->bind_param("i", $res_id);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();

if ($resultCheck->num_rows === 0) {
    $params = http_build_query([
        'name_error' => "Invalid resource selected",
        'res_id' => $res_id,
        'res_name' => $_POST['res_name'] ?? '',
        'capacity' => $capacity,
        'event_date' => $event_date,
        'purpose' => $purpose,
        'event_time' => $et_id
    ]);
    header("Location: ../student/view/book.php?$params");
    exit;
}

    /* -------------------------------------------------------------------------
       CHECK IF THIS USER ALREADY HAS A BOOKED EVENT (app_id = 1, pro_id = 2)
    ------------------------------------------------------------------------- */
    $today = date("Y-m-d");

    $sqlUserCheck = "
    SELECT res_id
    FROM bookings
    WHERE user_id = ?
    AND res_id = ?
    AND (app_id = 1 OR app_id = 2)
    AND pro_id = 2
    AND event_date >= ?
    LIMIT 1
";
$stmtCheckUser = $conn->prepare($sqlUserCheck);
if (!$stmtCheckUser) {
    die("Prepare failed: " . $conn->error);
}
$stmtCheckUser->bind_param("iis", $user_id, $res_id, $today);
$stmtCheckUser->execute();
$resultCheckUser = $stmtCheckUser->get_result();

if ($resultCheckUser && $resultCheckUser->num_rows > 0) {
    $params = http_build_query([
        'name_error' => "You already have an active booking for this resource. Complete your current event before booking again",
        'res_id' => $res_id,
        'res_name' => $_POST['res_name'] ?? '',
        'capacity' => $capacity,
        'event_date' => $event_date,
        'purpose' => $purpose,
        'event_time' => $et_id
    ]);
    header("Location: ../student/view/book.php?$params");
    exit;
}
$stmtCheckUser->close();

    /* -------------------------------------------------------------------------
       CHECK IF THIS RESOURCE IS ALREADY BOOKED AT THE SELECTED DATE AND TIME
    ------------------------------------------------------------------------- */
    $today = date("Y-m-d");

    // 1. Get selected time range
    $sqlGetET = "SELECT times, hour_period FROM eventimes WHERE eventimes_id = ?";
    $stmtGetET = $conn->prepare($sqlGetET);
    $stmtGetET->bind_param("i", $et_id);
    $stmtGetET->execute();
    $resultET = $stmtGetET->get_result();
    $selectedET = $resultET->fetch_assoc();
    $stmtGetET->close();

    list($selectedStart, $selectedEnd) = explode(" - ", $selectedET['times']);
    $selectedStart = strtotime($selectedStart);
    $selectedEnd = strtotime($selectedEnd);

    // 2. Load all existing bookings for this resource and date
    $sqlCheckOverlap = "
        SELECT b.et_id, b.event_date, e.times, e.hour_period, r.res_name
        FROM bookings b
        JOIN eventimes e ON b.et_id = e.eventimes_id
        JOIN resources r ON b.res_id = r.res_id
        WHERE b.res_id = ?
        AND b.event_date = ?
        AND (b.app_id = 1 OR b.app_id = 2)
        AND b.pro_id = 2
    ";

    $stmtOverlap = $conn->prepare($sqlCheckOverlap);
    $stmtOverlap->bind_param("is", $res_id, $event_date);
    $stmtOverlap->execute();
    $resultOverlap = $stmtOverlap->get_result();

    $existingRanges = []; // store all ranges for later filtering
    $resName = null;

    while ($row = $resultOverlap->fetch_assoc()) {
        list($existingStart, $existingEnd) = explode(" - ", $row['times']);
        $existingRanges[] = [
            'start' => strtotime($existingStart),
            'end' => strtotime($existingEnd),
            'res_name' => htmlspecialchars($row['res_name'])
        ];
    }

    // 3. Check overlap with any existing booking
    $overlapFound = false;
    foreach ($existingRanges as $range) {
        if ($selectedStart < $range['end'] && $range['start'] < $selectedEnd) {
            $overlapFound = true;
            $resName = $range['res_name'];
            break;
        }
    }

    if ($overlapFound) {
        // 4. Generate available 90-minute slots, excluding overlaps with any existing booking
        $sqlTimes = "SELECT eventimes_id, times FROM eventimes WHERE hour_period = 2 ORDER BY eventimes_id ASC";
        $stmtTimes = $conn->prepare($sqlTimes);
        $stmtTimes->execute();
        $resultTimes = $stmtTimes->get_result();

        $availableTimes = [];
        while ($t = $resultTimes->fetch_assoc()) {
            list($tStart, $tEnd) = explode(" - ", $t['times']);
            $tStart = strtotime($tStart);
            $tEnd = strtotime($tEnd);

            $conflict = false;
            foreach ($existingRanges as $range) {
                if ($tStart < $range['end'] && $range['start'] < $tEnd) {
                    $conflict = true;
                    break;
                }
            }

            if (!$conflict) {
                $availableTimes[] = $t['times'];
            }
        }

        if ($overlapFound) {
    $availableTimesStr = empty($availableTimes) ? "No available times" : implode(", ", $availableTimes);

    // Encode all values to safely pass in URL
    $params = http_build_query([
        'name_error' => "The selected time overlaps with an existing booking. $resName is available at $availableTimesStr on $event_date",
        'res_id' => $res_id,
        'res_name' => $res_name, // the resource name for the readonly field
        'capacity' => $capacity,
        'event_date' => $event_date,
        'purpose' => $purpose,
        'event_time' => $et_id
    ]);

        header("Location: ../student/view/book.php?$params");
            exit;
        }

    }

    $stmtOverlap->close();

    /* -----------------------------
       INSERT INTO DATABASE
    ------------------------------*/
    $stmt = $conn->prepare("
        INSERT INTO bookings (user_id, rm_id, res_id, et_id, app_id, pro_id, capacity, event_date, purpose, datetime_of_booking) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param("iiiiiiisss", $user_id, $rm_id, $res_id, $et_id, $app_id, $pro_id, $capacity, $event_date, $purpose, $datetime_of_booking);

    if ($stmt->execute()) {
        header("Location: ../student/view/book.php?success=Your request has been submitted successfully");
        exit;

    } else {
        header("Location: ../student/view/book.php?image_error=Database insert failed");
        exit;
    }
}
?>
