<?php include "../layout/header.php"; ?>
<?php include "../layout/sidebar.php"; ?>

<?php
echo '<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">';

// Function to display all inactive resource managers
function display_all_resource_managers() {
    $resource_managers = get_all_resource_managers();

    // Placeholder image path
    $placeholder = "../../images/placeholder.jpg";

    if ($resource_managers !== false) {
        if (!empty($resource_managers)) {
            
            foreach ($resource_managers as $resource_manager) {

                // Build full image path
                $imgPath = $resource_manager['user_img'];

                echo "<tr class='border-b'>";

                /* ==========================================================
                   1. DISPLAY IMAGE WITH FALLBACK PLACEHOLDER
                   ========================================================== */
                echo "<td class='p-3'>
                        <img src='$imgPath'
                             alt='Staff DP'
                             onerror=\"this.onerror=null; this.src='$placeholder';\"
                             style='width: 120px; height: 150px; object-fit: cover; border-radius: 6px;'>
                      </td>";

                /* ==========================================================
                   2. RESOURCE NAME
                   ========================================================== */
                echo "<td class='text-left font-medium'>" . $resource_manager['email'] . "</td>";

                /* ==========================================================
                   3. SHOW BUTTON 
                   ========================================================== */
                echo "<td>
                        <span 
                            onclick='openHappy(
                                " . $resource_manager["user_id"] . ",
                                \"" . $resource_manager["full_name"] . "\",
                                \"" . $resource_manager["user_img"] . "\",
                                \"" . $resource_manager["email"] . "\",
                                \"" . $resource_manager["staff_or_student_id"] . "\",
                                \"" . $resource_manager["phone"] . "\",
                                \"" . $resource_manager["assigned_resources"] . "\",
                                " . $resource_manager["assigned_count"] . "
                            )'
                        class='hover-info-icon text-blue-600 font-bold underline cursor-pointer' title='Details'>
                        <i class='fas fa-info-circle'></i>
                        </span>
                     </td>";

                echo "</tr>";
            }

        } else {
            echo "<tr><td colspan='4' style='color: green; text-align: center;'>
                    <strong>No resource managers found !!!</strong>
                  </td></tr>";
        }

    } else {
        echo "<tr><td colspan='4'>Error retrieving resource managers.</td></tr>";
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

    <!-- RESOURCES MANAGERS + DASHBOARD + SEARCH -->
    <div class="flex items-center gap-4">

        <!-- RESOURCES MANAGERS LABEL BOX -->
        <a href="all_managers.php" 
           class="bg-gray-700 hover:bg-gray-900 text-white font-bold px-6 py-3 rounded-lg shadow">
            Resource Managers
        </a>

        <!-- DASHBOARD LABEL BOX -->
        <a href="admin_dashboard.php" 
           class="bg-gray-700 hover:bg-gray-900 text-white font-bold px-6 py-3 rounded-lg shadow">
            Dashboard
        </a>

        <!-- SEARCH BOX -->
        <div class="relative w-72">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                <i class="fas fa-search"></i>
            </span>
            <input type="text" 
           id="searchEmail" 
           placeholder="Search by email..." 
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
                    <th class="px-6 py-3 text-left">Email</th>
                    <th class="px-6 py-3 text-left"></th>
                </tr>
            </thead>

            <!-- BODY -->
            <tbody class="bg-white text-black">
                <?php display_all_resource_managers(); ?>
            </tbody>
        </table>
    </div>

</div>

<!-- HAPPY POPUP: RESOURCE MANAGER POPUP -->
<div id="happyModal"
     class="hidden fixed inset-0 bg-black bg-opacity-40 flex justify-center items-center z-50 pb-20">

    <div class="bg-white w-80 rounded-lg shadow-xl p-6 text-center">

        <!-- RESOURCE MANAGER NAME -->
        <p id="modalResourceName" class="text-xl font-bold text-black mb-4"></p>

        <!-- RESOURCE MANAGER IMAGE -->
        <img id="modalResourceImg"
             src=""
             alt="Resource Image"
             class="w-50 h-50 object-cover mx-auto rounded mb-4">

        <!-- RESOURCE MANAGER EMAIL -->
        <p id="modalResourceEmail" class="text-black font-semibold mb-2"></p>

        <!-- RESOURCE MANAGER STAFF ID -->
        <p id="modalResourceStatus" class="text-yellow-700 mb-4"></p>

        <!-- RESOURCE MANAGER PHONE NUMBER -->
        <p id="modalResourcePhone" class="text-black font-semibold mb-2"></p>

        <!-- ASSIGNED RESOURCES COUNT -->
        <p id="modalAssignedCount" class="font-bold text-black mt-4 mb-1"></p>

        <!-- ASSIGNED RESOURCES LIST (Scrollable + Bullets) -->
        <ul id="modalAssignedList"
            class="text-gray-700 text-sm text-left list-disc list-inside 
           max-h-40 overflow-y-auto px-4 py-2 bg-gray-100 rounded">
        </ul>


        <!-- SPACING -->
        <div class="mt-6"></div>

        <!-- CLOSE BUTTON -->
        <button onclick="closeHappy()" 
                class="px-4 py-2 bg-gray-700 rounded hover:bg-gray-900 text-white font-bold">
            Close
        </button>
    </div>
</div>

<script>
// OPEN ADD FORM
function openAddForm() {
    document.getElementById("addFormModal").classList.remove("hidden");
}
// CLOSE ADD FORM
function closeAddForm() {
    document.getElementById("addFormModal").classList.add("hidden");
}

document.getElementById("searchEmail").addEventListener("keyup", function () {
    const searchValue = this.value.toLowerCase().trim();
    const tableRows = document.querySelectorAll("tbody tr");

    tableRows.forEach(row => {
        const emailCell = row.querySelector("td:nth-child(2)");
        if (!emailCell) return;

        const emailText = emailCell.textContent.toLowerCase();

        // MATCH EMAIL ONLY
        if (emailText.includes(searchValue)) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
});

function openHappy(id, name, img, email, staffid, phone, assignedList, assignedCount) {
    // Set dynamic fields
    document.getElementById("modalResourceName").textContent = name;

    // build full path
    document.getElementById("modalResourceImg").src =
        "" + img;

    document.getElementById("modalResourceEmail").textContent =
        "Email: " + email;

    document.getElementById("modalResourceStatus").textContent =
        "Staff ID: " + staffid;

    document.getElementById("modalResourcePhone").textContent =
        "Phone: " + phone;

    // Assigned resources section
    document.getElementById("modalAssignedCount").textContent =
        "Assigned Resources (" + assignedCount + ")";

    let listContainer = document.getElementById("modalAssignedList");
    listContainer.innerHTML = ""; // clear previous list

    if (assignedList && assignedList.trim() !== "") {
        let items = assignedList.split(","); // split into array

        items.forEach(item => {
            let li = document.createElement("li");
            li.textContent = item.trim();
            listContainer.appendChild(li);
        });
    } else {
        let li = document.createElement("li");
        li.textContent = "No assigned resources.";
        listContainer.appendChild(li);
    }

    // Show modal
    document.getElementById("happyModal").classList.remove("hidden");
}

// CLOSE HAPPY POPUP
function closeHappy() {
    document.getElementById("happyModal").classList.add("hidden");
}
</script>

<?php include "../layout/footer.php"; ?>
