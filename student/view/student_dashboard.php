<?php include "../layout/header.php"; ?>
<?php include "../layout/sidebar.php"; ?>

<?php
echo '<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">';

$pending_count = count_pending_requests_for_student();
$pending_bookings = get_pending_bookings_for_student();

$my_event_count = count_my_events_for_student();
$my_events = get_my_events_for_student();
?>

<!-- MAIN CONTENT -->
<div class="ml-64 p-6 sidebar-expanded transition-all duration-300" x-data="dashboardModals()">

    <!-- TOP NAV BUTTONS -->
    <div class="flex items-center gap-4 mb-8">
        <a href="student_dashboard.php" 
           class="bg-gray-700 hover:bg-gray-900 text-white font-bold px-6 py-3 rounded-md shadow-md">
            Dashboard
        </a>

        <a href="book.php"
           class="bg-gray-700 hover:bg-gray-900 text-white font-bold px-6 py-3 rounded-md shadow-md">
            Booking Requests
        </a>
    </div>

    <div class="mt-10"></div>

    <!-- FLEX WRAPPER FOR LEFT + RIGHT SECTIONS -->
    <div class="flex gap-10">

        <!-- LEFT SIDE -->
        <div class="w-1/2">
            <button class="bg-cyan-700 text-white font-bold px-5 py-2 rounded-md shadow mb-4">
                My Event(s) [<?php echo intval($my_event_count); ?>]
            </button>

            <?php if (empty($my_events)): ?>
                <div class="bg-white p-4 rounded-lg shadow mb-6">
                    <div class="text-gray-600">You have no upcoming events.</div>
                </div>

            <?php else: ?>
                <?php foreach ($my_events as $idx => $ev): 
                    $booking_id = (int) $ev['booking_id'];
                    $res_name = htmlspecialchars($ev['res_name'] ?? 'Unknown Resource');
                    $res_img = htmlspecialchars($ev['res_img'] ?? '');
                    $res_status_text = htmlspecialchars($ev['cs_status'] ?? 'Unknown');
                    $event_date = htmlspecialchars($ev['event_date'] ?? '');
                    $event_time = htmlspecialchars($ev['event_time'] ?? '');
                    $manager_fullname = htmlspecialchars(trim(($ev['manager_fname'] ?? '') . ' ' . ($ev['manager_lname'] ?? '')));
                    $manager_email = htmlspecialchars($ev['manager_email'] ?? '');
                    $manager_phone = htmlspecialchars($ev['manager_phone'] ?? '');
                    $manager_dep = htmlspecialchars($ev['manager_dep_name'] ?? '');
                    $manager_img = htmlspecialchars($ev['manager_img'] ?? '');

                    $img_src = '../../images/resources_uploads/' . ($res_img ?: 'placeholder.png');
                    $mgr_img_src = !empty($manager_img)
                                   ? '' . $manager_img
                                   : '../../images/placeholder.jpg';

                    // Determine if event is ongoing ---
                    date_default_timezone_set('Africa/Accra');
                    $nowTime = date('H:i');
                    $nowDate = date('Y-m-d');

                    // If event is ongoing, force status text to "active (event ongoing)"
                    if (!empty($event_time) && !empty($event_date)) {
                        list($startTime, $endTime) = explode(' - ', $event_time);
                        if ($event_date == $nowDate && $nowTime >= $startTime && $nowTime <= $endTime) {
                            $res_status_text = "active (event ongoing)";
                        }   else {
                            $res_status_text = $res_status_text;
                             }
                        } else {
                            $res_status_text = $res_status_text;
                    }
                    
                    ?>

                <div class="event-card bg-white p-4 rounded-lg shadow mb-6 last:mb-10">

                    <h1 class="text-xl font-bold mb-2"><?php echo $res_name; ?></h1>

                    <div class="w-full h-40 bg-gray-200 rounded-md overflow-hidden mb-2">
                        <img src="<?php echo $img_src; ?>" alt="<?php echo $res_name; ?>" class="w-full h-full object-cover">
                    </div>

                    <div class="inline-block rounded bg-gray-700 px-3 py-2 mb-2">
                        <div class="flex items-center gap-2">
                            <svg class="animate-pulse -ml-1 mr-2 h-5 w-6 text-yellow-400" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 2a1 1 0 011 1v2a1 1 0 11-2 0V3a1 1 0 011-1zM4.22 4.22a1 1 0 011.42 0l1.42 1.42a1 1 0 11-1.42 1.42L4.22 5.64a1 1 0 010-1.42zM2 10a1 1 0 011-1h2a1 1 0 110 2H3a1 1 0 01-1-1zm8 6a1 1 0 011 1v2a1 1 0 11-2 0v-2a1 1 0 011-1zm6.36-1.64a1 1 0 010 1.42l-1.42 1.42a1 1 0 11-1.42-1.42l1.42-1.42a1 1 0 011.42 0z"/>
                            </svg>
                            <span class="text-yellow-400 font-bold"><?php echo $res_status_text; ?></span>
                        </div>

                        <div class="mt-2">
                            <div class="text-gray-400 font-bold">Event date: <?php echo $event_date ?: 'N/A'; ?></div>
                            <div class="text-gray-400 font-bold">Event time: <?php echo $event_time ?: 'N/A'; ?></div>
                        </div>
                    </div>

                    <!-- Contact + Cancel button side by side -->
                    <div class="mt-3 text-sm border-t pt-3 flex justify-between items-start">
                        <div class="flex-1 pr-4">
                            <div class="text-black font-bold mb-2">Contact Resource Manager:</div>

                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-full overflow-hidden bg-gray-200 flex-shrink-0">
                                    <img src="<?php echo $mgr_img_src; ?>" alt="<?php echo $manager_fullname; ?>" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <div class="font-semibold"><?php echo $manager_fullname ?: '<span class="text-gray-500">No manager assigned</span>'; ?></div>
                                    <?php if ($manager_dep): ?><div class="text-sm text-gray-600"><?php echo $manager_dep; ?></div><?php endif; ?>
                                    <?php if ($manager_email): ?><div class="text-blue-700 text-sm"><?php echo $manager_email; ?></div><?php endif; ?>
                                    <?php if ($manager_phone): ?><div class="text-blue-700 text-sm"><?php echo $manager_phone; ?></div><?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- CANCEL EVENT BUTTON -->
                        <button  
                            class="delete-btnEv bg-red-600 hover:bg-red-800 text-white px-4 py-2 rounded-md font-bold shadow flex-shrink-0"
                            data-booking-id="<?php echo $booking_id; ?>"title='Cancel'
                            >
                                Cancel Event
                        </button>
                    </div>

                </div>

                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- RIGHT SIDE -->
        <div class="w-1/2">

            <button class="bg-gray-800 text-cyan-300 font-bold px-5 py-2 rounded-md shadow mb-4">
                Request(s) Pending [<?php echo intval($pending_count); ?>]
            </button>

            <?php if (empty($pending_bookings)): ?>
                <div class="bg-white p-4 rounded-lg shadow mb-6">
                    <div class="text-gray-600">No pending requests.</div>
                </div>

            <?php else: ?>
                <?php foreach ($pending_bookings as $booking):
                    $booking_id = (int) $booking['booking_id'];
                    $res_name = htmlspecialchars($booking['res_name']);
                    $res_img = htmlspecialchars($booking['res_img']);
                    $event_date = htmlspecialchars($booking['event_date']);
                    $event_time = htmlspecialchars($booking['event_time']);
                    $datetime_of_booking = htmlspecialchars($booking['datetime_of_booking']);
                    $manager_fullname = htmlspecialchars(trim(($booking['manager_fname'] ?? '') . ' ' . ($booking['manager_lname'] ?? '')));
                    $manager_email = htmlspecialchars($booking['manager_email']);
                    $manager_phone = htmlspecialchars($booking['manager_phone']);
                    $img_src = '../../images/resources_uploads/' . ($res_img ?: 'placeholder.png');
                    $manager_img = htmlspecialchars($booking['manager_img'] ?? '');

                    $img_src = '../../images/resources_uploads/' . ($res_img ?: 'placeholder.png');
                    $mgr_img_src = !empty($manager_img)
                                   ? '' . $manager_img
                                   : '../../images/placeholder.jpg';
                ?>

                <div class="pending-card bg-white p-4 rounded-lg shadow mb-6 last:mb-10">

                    <h1 class="text-lg font-bold mb-2"><?php echo $res_name; ?></h1>

                    <div class="w-full h-40 bg-gray-200 rounded-md overflow-hidden mb-2">
                        <img src="<?php echo $img_src; ?>" alt="<?php echo $res_name; ?>" class="w-full h-full object-cover">
                    </div>

                    <div class="inline-flex items-center px-4 py-2 rounded bg-gray-700">
                        <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-yellow-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        <span class="text-yellow-500 font-bold">pending ...</span>
                    </div>

                    <div class="mt-2">
                        <span class="text-gray-400 font-bold">Booked on: <?php echo $datetime_of_booking; ?></span><br>
                        <span class="text-gray-400 font-bold">Event date: <?php echo $event_date; ?></span><br>
                        <span class="text-gray-400 font-bold">Event time: <?php echo $event_time; ?></span>
                    </div>

                    <!-- Contact + Cancel button side by side -->
                    <div class="mt-3 text-sm border-t pt-3 flex justify-between items-start">
                        <div class="flex-1 pr-4">
                            <div class="text-black font-bold mb-2">Contact Resource Manager:</div>

                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-full overflow-hidden bg-gray-200 flex-shrink-0">
                                    <img src="<?php echo $mgr_img_src; ?>" alt="<?php echo $manager_fullname; ?>" class="w-full h-full object-cover">
                                </div>
                            <div>

                            <div><?php echo $manager_fullname ?: '<span class="text-gray-500">No manager assigned</span>'; ?></div>
                            <?php if ($manager_email): ?><div class="text-blue-700"><?php echo $manager_email; ?></div><?php endif; ?>
                            <?php if ($manager_phone): ?><div class="text-blue-700"><?php echo $manager_phone; ?></div><?php endif; ?>
                            </div>
                        </div></div>
                        
                        <!-- CANCEL REQUEST BUTTON -->
                        <button  
                            class="delete-btn bg-red-600 hover:bg-red-800 text-white px-4 py-2 rounded-md font-bold shadow flex-shrink-0"
                             data-booking-id="<?php echo $booking_id; ?>" title='Cancel'
                            >
                                Cancel Request
                        </button>
                    </div>
                </div>

                <?php endforeach; ?>
            <?php endif; ?>

        </div>

    </div> <!-- END FLEX -->
</div>

<!-- DELETE CONFIRMATION POPUP: PENDING REQUEST -->
<div id="deletePopup" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50"> 
    <div class="bg-white rounded-lg p-6 shadow-xl w-80 max-w-full mx-4">
        <h2 id="deleteMessage" class="text-xl font-bold text-gray-800 mb-4"></h2>
        <div class="flex justify-end gap-3">
            <button onclick="closeDeletePopup()" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded-md font-semibold">
            No
            </button>
            <button id="yesDeleteBtn" class="px-4 py-2 bg-red-600 hover:bg-red-800 text-white rounded-md font-bold">
            Yes, Cancel
            </button>
        </div>
    </div>
</div>

<!-- DELETE CONFIRMATION POPUP: EVENT -->
<div id="deletePopupEv" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 shadow-xl w-80 max-w-full mx-4">
        <h2 id="deleteMessageEv" class="text-xl font-bold text-gray-800 mb-4"></h2>
        <div class="flex justify-end gap-3">
            <button onclick="closeDeletePopupEv()" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded-md font-semibold">
            No
            </button>
            <button id="yesDeleteBtnEv" class="px-4 py-2 bg-red-600 hover:bg-red-800 text-white rounded-md font-bold">
            Yes, Cancel
            </button>
        </div>
    </div>
</div>

<!-- SUCCESFUL DELETION POPUP: PENDING REQUEST -->
<div id="successPopup" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 shadow-xl w-80 max-w-full mx-4 text-center">
        <h2 id="successMessage" class="text-xl font-bold text-red-600 mb-4"></h2>
        <button onclick="window.location.href='student_dashboard.php';" class="px-5 py-2 bg-red-500 hover:bg-red-700 text-white font-bold rounded-md">OK</button>
    </div>
</div>

<!-- SUCCESFUL DELETION POPUP: EVENT -->
<div id="successPopupEv" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 shadow-xl w-80 max-w-full mx-4 text-center">
        <h2 id="successMessageEv" class="text-xl font-bold text-red-600 mb-4"></h2>
        <button onclick="window.location.href='student_dashboard.php';" class="px-5 py-2 bg-red-500 hover:bg-red-700 text-white font-bold rounded-md">OK</button>
    </div>
</div>

<script>

/*------------------------------------------------------------
   STUDENTS PENDING BOOKING REQUEST DELETION
 ------------------------------------------------------------*/   

// Open confirm delete popup for students pending requests
document.querySelectorAll(".delete-btn").forEach(btn => {
    btn.addEventListener("click", function(e) {
        e.preventDefault();

        const bookingId = this.dataset.bookingId;

        // Show custom delete popup
        const deletePopup = document.getElementById("deletePopup");
        const deleteMessage = document.getElementById("deleteMessage");
        const yesBtn = document.getElementById("yesDeleteBtn");

        deleteMessage.textContent = `Are you sure you want to cancel this booking (pending request)?`;

        deletePopup.style.display = "flex";

        // Remove previous click handler (to avoid multiple bindings)
        const newYesBtn = yesBtn.cloneNode(true);
        yesBtn.parentNode.replaceChild(newYesBtn, yesBtn);

        // On Yes click
        newYesBtn.addEventListener("click", function() {
            // AJAX request
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "../../action/cancel_pending_request_action.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onload = () => {
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            // Remove card
                            const card = btn.closest(".pending-card");
                            if (card) card.remove();

                            // Hide delete popup
                            deletePopup.style.display = "none";

                            // Optionally show success popup
                            const successPopup = document.getElementById("successPopup");
                            const successMsg = document.getElementById("successMessage");
                            if (successPopup && successMsg) {
                                successMsg.textContent = `Done`;
                                successPopup.style.display = "flex";
                            }
                        } else {
                            alert("Error: " + response.message);
                        }
                    } catch (err) {
                        alert("Unexpected error occurred");
                        console.error(err);
                    }
                } else {
                    alert("AJAX request failed. Status: " + xhr.status);
                }
            };

            xhr.send(`booking_id=${encodeURIComponent(bookingId)}`);
        });
    });
});

// Close delete popup function
function closeDeletePopup() {
    document.getElementById("deletePopup").style.display = "none";
}

/*------------------------------------------------------------
   STUDENTS EVENTS DELETION
 ------------------------------------------------------------*/   

// Open confirm delete popup for students events
document.querySelectorAll(".delete-btnEv").forEach(btn => {
    btn.addEventListener("click", function(e) {
        e.preventDefault();

        const bookingId = this.dataset.bookingId;

        // Show custom delete popup
        const deletePopup = document.getElementById("deletePopupEv");
        const deleteMessage = document.getElementById("deleteMessageEv");
        const yesBtn = document.getElementById("yesDeleteBtnEv");

        deleteMessageEv.textContent = `Are you sure you want to cancel this booking (event)?`;

        deletePopupEv.style.display = "flex";

        // Remove previous click handler (to avoid multiple bindings)
        const newYesBtn = yesBtn.cloneNode(true);
        yesBtn.parentNode.replaceChild(newYesBtn, yesBtn);

        // On Yes click
        newYesBtn.addEventListener("click", function() {
            // AJAX request
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "../../action/cancel_student_event_action.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onload = () => {
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            // Remove card
                            const card = btn.closest(".event-card");
                            if (card) card.remove();

                            // Hide delete popup
                            deletePopupEv.style.display = "none";

                            // Optionally show success popup
                            const successPopupEv = document.getElementById("successPopupEv");
                            const successMsg = document.getElementById("successMessageEv");
                            if (successPopupEv && successMsg) {
                                successMsg.textContent = `Done`;
                                successPopupEv.style.display = "flex";
                            }
                        } else {
                            alert("Error: " + response.message);
                        }
                    } catch (err) {
                        alert("Unexpected error occurred");
                        console.error(err);
                    }
                } else {
                    alert("AJAX request failed. Status: " + xhr.status);
                }
            };

            xhr.send(`booking_id=${encodeURIComponent(bookingId)}`);
        });
    });
});

// Close delete popup function
function closeDeletePopupEv() {
    document.getElementById("deletePopupEv").style.display = "none";
}

</script>

<?php include "../layout/footer.php"; ?>
