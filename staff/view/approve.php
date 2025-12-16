<?php include "../layout/header.php"; ?>
<?php include "../layout/sidebar.php"; ?>
<?php 
$newRequests = count_new_requests_for_manager();
?>

<?php
echo '<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">';

// Function to display all bookings assigned to this manager
function display_all_bookings_for_resources_assigned_to_each_manager() {
    $myBookings = get_all_bookings_for_resources_assigned_to_each_manager();
    $placeholder = "../../images/placeholder.jpg";

    if ($myBookings !== false) {
        if (!empty($myBookings)) {
            foreach ($myBookings as $book) {
                $studentImg = htmlspecialchars($book['user_img'], ENT_QUOTES);
                $resourceImg = "../../images/resources_uploads/" . htmlspecialchars($book['res_img'], ENT_QUOTES);
                $booking_id = (int)$book['booking_id'];
                $student_email = htmlspecialchars($book['email'], ENT_QUOTES);
                $datetime_of_booking = htmlspecialchars($book['datetime_of_booking'], ENT_QUOTES);

                echo "<tr class='border-b'>";
                echo "<td class='p-3'>
                        <img src='{$studentImg}'
                             alt='Profile Picture'
                             onerror=\"this.onerror=null; this.src='{$placeholder}';\"
                             style='width: 135px; height: 120px; object-fit: cover; border-radius: 6px;'>
                      </td>";

                echo "<td class='text-left font-medium'>{$datetime_of_booking}</td>";

                // ACTION BUTTONS: approve (green) and deny (red)
                echo "<td>
                    <button 
                        class='btn-approve text-white font-bold bg-green-500 hover:bg-green-700 px-2 py-1 rounded'
                        data-booking-id='{$booking_id}'
                        data-student-email='{$student_email}'
                        title='Approve'>
                        <i class='fas fa-check-circle'></i>
                    </button>
                        &nbsp;
                    <button
                        class='btn-deny text-white font-bold bg-red-500 hover:bg-red-700 px-2 py-1 rounded'
                        data-booking-id='{$booking_id}'
                        data-student-email='{$student_email}'
                        title='Deny'>
                        <i class='fas fa-times-circle'></i>
                    </button>
                </td>";

                // Details icon (unchanged)
                $full_name = htmlspecialchars($book["full_name"], ENT_QUOTES);
                $user_img = htmlspecialchars($book["user_img"], ENT_QUOTES);
                $email = htmlspecialchars($book["email"], ENT_QUOTES);
                $phone = htmlspecialchars($book["phone"], ENT_QUOTES);
                $staff_id = htmlspecialchars($book["staff_or_student_id"], ENT_QUOTES);
                $res_name = htmlspecialchars($book["res_name"], ENT_QUOTES);
                $res_img = htmlspecialchars($book["res_img"], ENT_QUOTES);
                $datetime = htmlspecialchars($book["datetime_of_booking"], ENT_QUOTES);
                $capacity = htmlspecialchars($book["capacity"], ENT_QUOTES);
                $event_date = htmlspecialchars($book["event_date"], ENT_QUOTES);
                $event_time = htmlspecialchars($book["event_time"], ENT_QUOTES);
                $purpose = htmlspecialchars($book["purpose"], ENT_QUOTES);

                echo "<td>
                        <span onclick='openHappy({$booking_id}, \"{$full_name}\", \"{$user_img}\", \"{$email}\", \"{$phone}\", \"{$staff_id}\", \"{$res_name}\", \"{$res_img}\", \"{$datetime}\", \"{$capacity}\", \"{$event_date}\", \"{$event_time}\", \"{$purpose}\")'
                              class='hover-info-icon text-blue-600 font-bold underline cursor-pointer' title='Details'>
                            <i class='fas fa-info-circle'></i>
                        </span>
                     </td>";

                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4' style='color: green; text-align: center;'><strong>No requests found</strong></td></tr>";
        }
    } else {
        echo "<tr><td colspan='4'>Error retrieving resources.</td></tr>";
    }
}
?>

<!-- CONFIRMATION MODAL (CENTRALLY ALIGNED) -->
<div id="confirmModal" 
     style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); 
            z-index:9999; justify-content:center; align-items:center; flex-direction:column;">
    <div style="background:#fff; padding:30px; width:450px; max-width:90%; 
                border-radius:16px; text-align:center; 
                box-shadow:0 20px 60px rgba(0,0,0,0.3); 
                animation: popupSlideIn 0.3s ease-out; border:1px solid #e5e7eb;">
        
        <!-- ICON -->
        <div id="confirmIcon" style="font-size:48px; margin-bottom:16px;"></div>
        
        <h3 id="confirmTitle" 
            style="margin:0 0 16px; font-size:24px; font-weight:700; color:#111827;">
            Confirm Action
        </h3>

        <p id="confirmMessage" 
           style="margin-bottom:24px; font-size:16px; color:#374151; line-height:1.5;">
            Are you sure?
        </p>

        <div style="display:flex; gap:16px; justify-content:center;">
            <!-- NO BUTTON -->
            <button id="confirmNoBtn" 
                style="padding:12px 28px; background:#f3f4f6; color:#374151; 
                       border-radius:12px; border:0; cursor:pointer; 
                       font-weight:600; font-size:15px; transition:all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                       box-shadow:0 2px 8px rgba(0,0,0,0.1);
                       position:relative; overflow:hidden;">
                <i class="fas fa-times mr-2"></i>No
            </button>

            <!-- YES BUTTON -->
            <button id="confirmYesBtn" 
                style="padding:12px 28px; background:#10b981; color:white; 
                       border-radius:12px; border:0; cursor:pointer; 
                       font-weight:600; font-size:15px; transition:0.3s;
                       box-shadow:0 4px 12px rgba(16,185,129,0.3);">
                <i class="fas fa-check mr-2"></i>Yes
            </button>
        </div>
    </div>
</div>

<!-- SUCCESS MODAL (APPROVE) -->
<div id="successApproveModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:10000; justify-content:center; align-items:center; flex-direction:column;">
    <div style="background:#fff; padding:30px; width:450px; max-width:90%; 
                border-radius:16px; text-align:center; box-shadow:0 20px 60px rgba(0,0,0,0.3);
                animation: popupSlideIn 0.3s ease-out; border:1px solid #e5e7eb;">
        
        <div style="font-size:60px; margin-bottom:20px; color:#10b981;">
            <i class="fas fa-check-circle"></i>
        </div>
        
        <h2 style="margin:0 0 16px; font-size:28px; font-weight:700; color:#111827;">
            Request Approved!
        </h2>
        
        <p id="successApproveMessage" 
           style="margin-bottom:24px; font-size:18px; color:#059669; font-weight:500; line-height:1.5;">
        </p>

        <button id="successApproveOkBtn" 
                style="padding:14px 32px; background:#10b981; color:white; 
                       border-radius:12px; border:0; cursor:pointer; 
                       font-weight:700; font-size:16px; transition:0.3s;
                       box-shadow:0 4px 12px rgba(16,185,129,0.3);">
            <i class="fas fa-arrow-right mr-2"></i>Continue
        </button>
    </div>
</div>

<!-- DENIED MODAL -->
<div id="successDenyModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:10000; justify-content:center; align-items:center; flex-direction:column;">
    <div style="background:#fff; padding:30px; width:450px; max-width:90%; 
                border-radius:16px; text-align:center; box-shadow:0 20px 60px rgba(0,0,0,0.3);
                animation: popupSlideIn 0.3s ease-out; border:1px solid #e5e7eb;">
        
        <div style="font-size:60px; margin-bottom:20px; color:#ef4444;">
            <i class="fas fa-times-circle"></i>
        </div>
        
        <h2 style="margin:0 0 16px; font-size:28px; font-weight:700; color:#111827;">
            Request Denied!
        </h2>
        
        <p id="successDenyMessage" 
           style="margin-bottom:24px; font-size:18px; color:#dc2626; font-weight:500; line-height:1.5;">
        </p>

        <button id="successDenyOkBtn" 
                style="padding:14px 32px; background:#ef4444; color:white; 
                       border-radius:12px; border:0; cursor:pointer; 
                       font-weight:700; font-size:16px; transition:0.3s;
                       box-shadow:0 4px 12px rgba(239,68,68,0.3);">
            <i class="fas fa-arrow-right mr-2"></i>Continue
        </button>
    </div>
</div>

<!-- ERROR MODAL -->
<div id="errorModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:10000; justify-content:center; align-items:center; flex-direction:column;">
    <div style="background:#fff; padding:30px; width:450px; max-width:90%; 
                border-radius:16px; text-align:center; box-shadow:0 20px 60px rgba(0,0,0,0.3);
                animation: popupSlideIn 0.3s ease-out; border:1px solid #e5e7eb;">
        
        <div style="font-size:60px; margin-bottom:20px; color:#f59e0b;">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        
        <h2 style="margin:0 0 16px; font-size:28px; font-weight:700; color:#111827;">
            Something Went Wrong
        </h2>
        
        <p id="errorMessage" 
           style="margin-bottom:24px; font-size:16px; color:#d97706; font-weight:500; line-height:1.5;">
        </p>

        <button id="errorOkBtn" 
                style="padding:14px 32px; background:#f59e0b; color:white; 
                       border-radius:12px; border:0; cursor:pointer; 
                       font-weight:700; font-size:16px; transition:0.3s;
                       box-shadow:0 4px 12px rgba(245,158,11,0.3);">
            <i class="fas fa-arrow-right mr-2"></i>Try Again
        </button>
    </div>
</div>

<style>
/* POPUP ANIMATION */
@keyframes popupSlideIn {
    from { transform:translateY(-20px); opacity:0; }
    to { transform:translateY(0); opacity:1; }
}

/* BUTTON HOVER EFFECTS - YES BUTTON */
#confirmYesBtn {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

#confirmYesBtn:hover {
    /* 1. DEEPEN COLOR */
    background: #059669 !important;
    box-shadow: 0 8px 25px rgba(16,185,129,0.4) !important;
    
    /* 2. LIFT UP */
    transform: translateY(-3px);
    
    /* 3. SCALE EFFECT */
    transform: translateY(-3px) scale(1.05);
    
    /* 4. GLOW EFFECT */
    box-shadow: 0 8px 25px rgba(16,185,129,0.4), 0 0 20px rgba(16,185,129,0.2) !important;
}

#confirmYesBtn:active {
    /* PRESS EFFECT */
    transform: translateY(-1px) scale(0.98);
}

/* BUTTON HOVER EFFECTS - NO BUTTON */
#confirmNoBtn {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

#confirmNoBtn:hover {
    /* 1. DEEPEN COLOR */
    background: #e5e7eb !important;
    color: #1f2937 !important;
    box-shadow: 0 4px 15px rgba(0,0,0,0.15) !important;
    
    /* 2. LIFT UP */
    transform: translateY(-3px);
    
    /* 3. BORDER GLOW */
    border: 2px solid #d1d5db;
}

#confirmNoBtn:active {
    /* PRESS EFFECT */
    transform: translateY(-1px);
}

/* RIPPLE EFFECT FOR BOTH BUTTONS */
#confirmYesBtn::before,
#confirmNoBtn::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    background: rgba(255,255,255,0.3);
    border-radius: 50%;
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}

#confirmYesBtn:active::before,
#confirmNoBtn:active::before {
    width: 300px;
    height: 300px;
}

/* ICON HOVER ANIMATION */
#confirmYesBtn:hover i,
#confirmNoBtn:hover i {
    transform: scale(1.1) translateX(2px);
    transition: all 0.2s ease;
}

/* OTHER MODAL BUTTONS (Success, Error, etc.) */
#successApproveOkBtn:hover,
#successDenyOkBtn:hover,
#errorOkBtn:hover {
    transform: translateY(-3px) scale(1.05) !important;
    box-shadow: 0 8px 25px rgba(0,0,0,0.3) !important;
}

table { table-layout:fixed; width:100%; border-collapse:collapse; }
th:nth-child(1), td:nth-child(1) { width:40%; }
th:nth-child(2), td:nth-child(2) { width:30%; }
th:nth-child(3), td:nth-child(3) { width:20%; }
th:nth-child(4), td:nth-child(4) { width:10%; }
th, td { padding:12px 8px; text-align:left; border-bottom:1px solid #ddd; }
tr:hover { background:#f8fafc; }
.hover-info-icon i { transition:color 0.3s, transform 0.3s; color:#3b82f6; }
.hover-info-icon i:hover { color:#2563eb; transform:scale(1.2); cursor:pointer; }
</style>

<div class="ml-64 px-8 py-6 sidebar-expanded transition-all">
<!-- TOP ROW: Approve Bookings + 3 New Requests -->
    <div class="flex items-center gap-4">

        <!-- Approve Bookings Container -->
        <a href="approve.php" 
           class="bg-gray-700 hover:bg-gray-900 text-white font-bold px-6 py-3 rounded-md shadow-md">
            Approve Bookings
        </a>

        <!-- New Requests Container -->
        <div class="bg-gray-700 hover:bg-gray-900 text-yellow-500 font-bold px-6 py-3 rounded-md shadow-md">
            Request(s) Pending [<?= $newRequests ?>]
        </div>

    </div>

    <!-- SPACING -->
    <div class="mt-20"></div>

    <!-- CENTER HEADER BOX -->
    <div class="flex justify-center">
        <div class="bg-gray-800 text-white font-bold px-8 py-4 rounded-md shadow-md">
            Approve or Deny Incoming Requests Below
        </div>
    </div>

    <!-- SPACING -->
    <div class="mt-6"></div>

    <!-- TABLE -->
    <div class="overflow-x-auto shadow-lg pb-20">

        <table class="w-full border-collapse">

            <!-- HEADERS -->
            <thead>
                <tr class="bg-gray-800 text-white font-bold">
                    <th class="px-6 py-3 text-left">Dp</th>
                    <th class="px-6 py-3 text-left">When</th>
                    <th class="px-6 py-3 text-left">Action</th>
                    <th class="px-6 py-3 text-left"></th>
                </tr>
            </thead>

            <!-- BODY -->
            <tbody class="bg-white text-black">
                <?php display_all_bookings_for_resources_assigned_to_each_manager(); ?>
            </tbody>
        </table>
    </div>

</div>

<!-- HAPPY POPUP: RESOURCE POPUP -->
<div id="happyModal"
     class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-center items-center p-6">

    <!-- SCROLLABLE WRAPPER (this prevents touching footer & keeps center) -->
    <div class="max-h-[85vh] overflow-y-auto w-full flex justify-center items-start py-6">

        <!-- MODAL BOX -->
        <div class="bg-white w-[90%] max-w-lg rounded-2xl shadow-2xl p-6 mx-auto">

            <!-- STUDENT FULL NAME -->
            <p id="modalStudentFullName" class="text-xl font-bold text-black mb-4"></p>

            <!-- STUDENT IMAGE -->
            <img id="modalStudentImg"
                 src=""
                 alt="Profile picture"
                 class="w-32 h-32 object-cover rounded mb-4">

            <!-- STUDENT EMAIL -->
            <p id="modalStudentEmail" class="text-blue-700 font-semibold mb-2"></p>

            <!-- STUDENT PHONE -->
            <p id="modalStudentPhone" class="text-blue-700 font-semibold mb-2"></p>

            <!-- STUDENT ID -->
            <p id="modalStudentID" class="text-yellow-700 font-semibold mb-4"></p>

            <hr class="my-4">

            <!-- RESOURCE NAME -->
            <p id="modalResourceName" class="text-xl font-bold text-black mb-4"></p>

            <!-- RESOURCE IMAGE -->
            <img id="modalResourceImg"
                 src=""
                 alt="Resource Image"
                 class="w-40 h-40 object-cover rounded mb-4">

            <!-- DATETIME OF BOOKING -->
            <p id="modalBookingDateTime" class="text-green-700 font-semibold mb-4"></p>

            <hr class="my-4">

            <!-- BOOKING DETAILS -->
            <p id="modalBookingCapacity" class="text-blue-900 font-semibold mb-2"></p>
            <p id="modalBookingEventDate" class="text-blue-900 font-semibold mb-2"></p>
            <p id="modalBookingEvenTime" class="text-blue-900 font-semibold mb-2"></p>

            <!-- PURPOSE – ALLOW LONG TEXT -->
            <p id="modalBookingPurpose"
               class="text-black font-semibold whitespace-pre-line mb-6"></p>

            <div class="flex justify-center mt-6">
                <!-- CLOSE BUTTON -->
                <button onclick="closeHappy()"
                        class="px-5 py-2 bg-gray-800 rounded-lg hover:bg-black text-white font-bold shadow">
                    Close
                </button>
            </div>
        </div>

    </div>
</div>

<script>
function openHappy(id, sname, simg, email, phone, stuid, rname, rimg, datetime, capacity, eventdate, eventime, purpose) {
    // Set dynamic fields
    document.getElementById("modalStudentFullName").textContent = sname;

    // build full path
    document.getElementById("modalStudentImg").src = simg;

    document.getElementById("modalStudentEmail").textContent =
        "Email: " + email;

    document.getElementById("modalStudentPhone").textContent =
        "Contact: " + phone;

    document.getElementById("modalStudentID").textContent =
        "Student ID: " + stuid;

    document.getElementById("modalResourceName").textContent = rname;

    document.getElementById("modalResourceImg").src =
        "../../images/resources_uploads/" + rimg;

    document.getElementById("modalBookingDateTime").textContent =
        "When booked: " + datetime;

    document.getElementById("modalBookingCapacity").textContent =
        "Capacity: " + capacity;

    document.getElementById("modalBookingEventDate").textContent =
        "Event date: " + eventdate;

    document.getElementById("modalBookingEvenTime").textContent =
        "Event time: " + eventime;

    document.getElementById("modalBookingPurpose").textContent =
        "Purpose: " + purpose;

    // Show modal
    document.getElementById("happyModal").classList.remove("hidden");
}

function closeHappy() {
    document.getElementById("happyModal").classList.add("hidden");
}

// Event delegation for buttons
document.addEventListener('click', function(e) {
    // APPROVE BUTTON
    if (e.target.closest('.btn-approve')) {
        e.preventDefault();
        const btn = e.target.closest('.btn-approve');
        currentBookingId = btn.dataset.bookingId;
        currentStudentEmail = btn.dataset.studentEmail;
        currentAction = 'approve';
        
        document.getElementById('confirmIcon').innerHTML = '<i class="fas fa-check-circle" style="color:#10b981;"></i>';
        document.getElementById('confirmTitle').textContent = 'Confirm Approval';
        document.getElementById('confirmMessage').textContent = `Approve booking request by ${currentStudentEmail}?`;
        document.getElementById('confirmModal').style.display = 'flex';
    }
    
    // DENY BUTTON
    if (e.target.closest('.btn-deny')) {
        e.preventDefault();
        const btn = e.target.closest('.btn-deny');
        currentBookingId = btn.dataset.bookingId;
        currentStudentEmail = btn.dataset.studentEmail;
        currentAction = 'deny';
        
        document.getElementById('confirmIcon').innerHTML = '<i class="fas fa-times-circle" style="color:#ef4444;"></i>';
        document.getElementById('confirmTitle').textContent = 'Confirm Denial';
        document.getElementById('confirmMessage').textContent = `Deny booking request by ${currentStudentEmail}?`;
        document.getElementById('confirmModal').style.display = 'flex';
    }
});

// CONFIRM NO BUTTON
document.getElementById('confirmNoBtn').onclick = function() {
    document.getElementById('confirmModal').style.display = 'none';
    currentAction = null;
    currentBookingId = null;
    currentStudentEmail = null;
};

// CONFIRM YES BUTTON
document.getElementById('confirmYesBtn').onclick = function() {
    if (currentAction === 'approve') {
        approveBooking(currentBookingId, currentStudentEmail);
    } else if (currentAction === 'deny') {
        denyBooking(currentBookingId, currentStudentEmail);
    }
};

// APPROVE FUNCTION
function approveBooking(bookingId, studentEmail) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '../../action/approve_action.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onload = function() {
        document.getElementById('confirmModal').style.display = 'none';
        
        try {
            const response = JSON.parse(xhr.responseText);
            if (response.success) {
                document.getElementById('successApproveMessage').textContent = `Request by ${studentEmail} is approved ✅`;
                document.getElementById('successApproveModal').style.display = 'flex';
                
                // Remove row
                const row = document.querySelector(`[data-booking-id="${bookingId}"]`)?.closest('tr');
                if (row) row.remove();
            } else {
                showError(response.message);
            }
        } catch (err) {
            document.getElementById('successApproveMessage').textContent = `Request by ${studentEmail} is approved ✅`;
            document.getElementById('successApproveModal').style.display = 'flex';
        }
    };
    
    xhr.send(`booking_id=${bookingId}&student_email=${encodeURIComponent(studentEmail)}`);
}

// DENY FUNCTION
function denyBooking(bookingId, studentEmail) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '../../action/deny_action.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onload = function() {
        document.getElementById('confirmModal').style.display = 'none';
        
        try {
            const response = JSON.parse(xhr.responseText);
            if (response.success) {
                document.getElementById('successDenyMessage').textContent = `Request by ${studentEmail} is denied ❌`;
                document.getElementById('successDenyModal').style.display = 'flex';
                
                // Remove row
                const row = document.querySelector(`[data-booking-id="${bookingId}"]`)?.closest('tr');
                if (row) row.remove();
            } else {
                showError(response.message);
            }
        } catch (err) {
            document.getElementById('successDenyMessage').textContent = `Request by ${studentEmail} is denied ❌`;
            document.getElementById('successDenyModal').style.display = 'flex';
        }
    };
    
    xhr.send(`booking_id=${bookingId}&student_email=${encodeURIComponent(studentEmail)}`);
}

// ERROR FUNCTION
function showError(message) {
    document.getElementById('errorMessage').textContent = message;
    document.getElementById('errorModal').style.display = 'flex';
}

// OK BUTTONS - ALL REDIRECT TO approve.php
document.getElementById('successApproveOkBtn').onclick = 
document.getElementById('successDenyOkBtn').onclick = 
document.getElementById('errorOkBtn').onclick = function() {
    window.location.href = 'approve.php';
};

</script>

<?php include "../layout/footer.php"; ?>
