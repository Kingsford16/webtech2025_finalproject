<?php include "../layout/header.php"; ?>
<?php include "../layout/sidebar.php"; ?>

<?php
echo '<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">';

// Function to display all inactive resources
function display_upcoming_events_for_admin() {
    $resources = get_upcoming_events_for_admin();

    // Placeholder image path
    $placeholder = "../../images/dummy_file.png";

    if ($resources !== false) {
        if (!empty($resources)) {
            
            foreach ($resources as $resource) {

                // Build full image path
                $imgPath = $resource['student_img'];

                echo "<tr class='border-b'>";

                /* ==========================================================
                   1. DISPLAY IMAGE WITH FALLBACK PLACEHOLDER
                   ========================================================== */
                echo "<td class='p-3'>
                        <img src='$imgPath'
                             alt='Resource Image'
                             onerror=\"this.onerror=null; this.src='$placeholder';\"
                             style='width: 120px; height: 120px; object-fit: cover; border-radius: 6px;'>
                      </td>";

                /* ==========================================================
                   2. RESOURCE NAME
                   ========================================================== */
                echo "<td class='text-left font-medium'>" . $resource['datetime'] . "</td>";


                /* ==========================================================
                   3. SHOW BUTTON 
                   ========================================================== */
                echo "<td>
                        <span 
                        onclick='openHappy(" 
                        . $resource["booking_id"] . ", "
                        . "\"" . $resource["student_fullname"] . "\", "
                        . "\"" . $resource["student_img"] . "\", "
                        . "\"" . $resource["student_email"] . "\", "
                        . "\"" . $resource["student_phone"] . "\", "
                        . "\"" . $resource["res_name"] . "\", "
                        . "\"" . $resource["res_img"] . "\", "
                        . "\"" . $resource["event_date"] . "\", "
                        . "\"" . $resource["event_time"] . "\", "
                        . "\"" . $resource["cs_status"] . "\", "
                        . "\"" . $resource["manager_fullname"] . "\", "
                        . "\"" . $resource["manager_img"] . "\", "
                        . "\"" . $resource["manager_email"] . "\", "
                        . "\"" . $resource["manager_phone"] . "\""
                        . ")'
                        class='hover-info-icon text-blue-600 font-bold underline cursor-pointer' title='Details'>
                        <i class='fas fa-info-circle'></i>
                        </span>
                     </td>";


                echo "</tr>";
            }

        } else {
            echo "<tr><td colspan='4' style='color: green; text-align: center;'>
                    <strong>No events found</strong>
                  </td></tr>";
        }

    } else {
        echo "<tr><td colspan='4'>Error retrieving resources.</td></tr>";
    }
}
?>

<style>
.hover-info-icon i {
  transition: color 0.3s ease, transform 0.3s ease;
  color: #3b82f6; /* base blue color matching text-blue-600 */
}

.hover-info-icon i:hover {
  color: #2563eb; /* darker blue on hover */
  transform: scale(1.2); /* slightly enlarge icon on hover */
  cursor: pointer;
}

table {
  table-layout: fixed;
  width: 100%;
  border-collapse: collapse;
}

/* Perfect column alignment */
th:nth-child(1), td:nth-child(1) { width: 40%; }
th:nth-child(2), td:nth-child(2) { width: 40%; }
th:nth-child(3), td:nth-child(3) { width: 20%; }

/* Shared cell style */
th, td {
  padding: 12px 8px;
  text-align: left;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  border-bottom: 1px solid #ddd;
}

/* Header style 
th {
  background-color: #1f2937;
  color: white;
}*/

tr:hover {
    background: #f5f5f5; 
}
</style>

<div class="ml-64 px-8 py-6 sidebar-expanded transition-all">

    <!-- RESOURCES + DASHBOARD + SEARCH -->
    <div class="flex items-center gap-4">

        <!-- RESOURCES LABEL BOX WITH CALENDAR ICON -->
        <a href="upcoming_events.php" 
            class="inline-flex items-center gap-3 bg-gray-700 hover:bg-gray-900 text-white font-bold px-8 py-4 rounded-lg shadow-lg transition-all duration-200 hover:shadow-xl hover:scale-[1.02]">
    
            <!-- Calendar Icon -->
            <div class="flex flex-col shadow-md w-8 h-8 relative bg-white/10 rounded p-1">
            <!-- Top corners -->
            <div class="absolute -top-0.5 left-1.5 w-1 h-1 bg-gray-300 rounded-sm"></div>
            <div class="absolute -top-0.5 right-1.5 w-1 h-1 bg-gray-300 rounded-sm"></div>
        
            <!-- Header (Month) -->
            <div class="bg-blue-400 h-2 rounded-t w-full"></div>
        
            <!-- Day number -->
            <div class="bg-white/80 h-3.5 flex items-center justify-center text-xs font-bold text-gray-800 mt-0.5 rounded-b">
            12
            </div>
            </div>

            <span>Upcoming Events</span>
        </a>

        <!-- DASHBOARD LABEL BOX -->
        <a href="admin_dashboard.php" 
           class="bg-gray-700 hover:bg-gray-900 text-white font-bold px-11 py-5 rounded-lg shadow">
            Dashboard
        </a>

        <!-- SEARCH BOX -->
        <div class="relative w-72">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                <i class="fas fa-search"></i>
            </span>
            <input type="text" 
           id="resourceSearch" 
           placeholder="Search by resource name..." 
           class="pl-10 pr-4 py-3 rounded-lg border border-gray-300 w-full focus:outline-none focus:ring-2 focus:ring-gray-700">
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
                    <th class="px-6 py-3 text-left"></th>
                </tr>
            </thead>

            <!-- BODY -->
            <tbody class="bg-white text-black">
                <?php display_upcoming_events_for_admin(); ?>
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
                 alt="Profile Picture"
                 class="w-32 h-32 object-cover rounded mb-4">
            <!-- STUDENT EMAIL -->
            <p id="modalStudentEmail" class="text-blue-800 font-semibold mb-2"></p>
            <!-- STUDENT PHONE -->
            <p id="modalStudentPhone" class="text-blue-800 font-semibold mb-2"></p>

            <hr class="my-4">

            <!-- RESOURCE NAME -->
            <p id="modalResourceName" class="text-xl font-bold text-black mb-4"></p>
            <!-- RESOURCE IMAGE -->
            <img id="modalResourceImg"
                 src=""
                 alt="Resource Image"
                 class="w-32 h-32 object-cover rounded mb-4">
            <!-- DATETIME OF BOOKING -->
            <p id="modalBookingEvenDate" class="text-text-black font-semibold mb-4"></p>
            <p id="modalBookingEventime" class="text-text-black font-semibold mb-4"></p>
            <p id="modalResourceStatus" class="text-text-black font-semibold mb-4"></p>

            <hr class="my-4">

            <!-- RESOURCE MANAGER FULL NAME -->
            <p id="modalManagerFullName" class="text-xl font-bold text-black mb-4"></p>
            <!-- RESOURCE MANAGER IMAGE -->
            <img id="modalManagerImg"
                 src=""
                 alt="Profile Picture"
                 class="w-32 h-32 object-cover rounded mb-4">
            <p id="modalManagerEmail" class="text-blue-800 font-semibold mb-2"></p>
            <p id="modalManagerPhone" class="text-blue-800 font-semibold mb-2"></p>

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
document.getElementById('resourceSearch').addEventListener('input', function() {
    const filter = this.value.toLowerCase();
    const table = document.querySelector('tbody');
    const rows = table.querySelectorAll('tr');

    let found = false;

    rows.forEach(row => {
        const nameCell = row.cells[1]; // second column = Name
        if (nameCell) {
            const text = nameCell.textContent.toLowerCase();
            if (text.includes(filter) && !found) {
                row.style.display = ''; // show the first match
                found = true;
            } else {
                row.style.display = 'none'; // hide everything else
            }
        }
    });

    // If search box is empty, show all rows
    if (filter === '') {
        rows.forEach(row => row.style.display = '');
    }
});

function openHappy(id, sname, simg, semail, sphone, rname, rimg, eventdate, eventime, status, rmname, rmimg, rmemail, rmphone) {

    document.getElementById("modalStudentFullName").textContent = sname;

    document.getElementById("modalStudentImg").src = simg;

    document.getElementById("modalStudentEmail").textContent =
        "Student email: " + semail;

    document.getElementById("modalStudentPhone").textContent =
        "Student contact: " + sphone;

    document.getElementById("modalResourceName").textContent = rname;

    document.getElementById("modalResourceImg").src = "../../images/resources_uploads/" + rimg;

    document.getElementById("modalBookingEvenDate").textContent =
        "Event date: " + eventdate;

    document.getElementById("modalBookingEventime").textContent =
        "Event time: " + eventime;

    // STATUS ELEMENT
    const statusEl = document.getElementById("modalResourceStatus");

    // Set status text
    statusEl.textContent = "Status: " + status;

    // Remove all color classes first
    statusEl.classList.remove("text-orange-500", "text-gray-500");

    // Apply color based on status text
    if (status.toLowerCase() === "active") {
        statusEl.classList.add("text-orange-500");  
    } else {
        statusEl.classList.add("text-gray-500");   
    }

    document.getElementById("modalManagerFullName").textContent = rmname;

    document.getElementById("modalManagerImg").src = rmimg;

    document.getElementById("modalManagerEmail").textContent =
        "Manager email: " + rmemail;

    document.getElementById("modalManagerPhone").textContent =
        "Manager contact: " + rmphone;

    // Show modal
    document.getElementById("happyModal").classList.remove("hidden");
}

function closeHappy() {
    document.getElementById("happyModal").classList.add("hidden");
}

function redirectBack() {
    window.location.href = "resources.php";
}

document.addEventListener("DOMContentLoaded", function () {

    // If PHP set an error in the URL, open the modal automatically
    <?php if (isset($_GET['name_error']) || isset($_GET['image_error'])): ?>
        document.getElementById("addFormModal").classList.remove("hidden");
    <?php endif; ?>

});
</script>

<?php include "../layout/footer.php"; ?>
