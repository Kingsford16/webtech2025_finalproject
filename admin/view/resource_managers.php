<?php include "../layout/header.php"; ?>
<?php include "../layout/sidebar.php"; ?>

<?php
echo '<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">';

// Function to display all inactive resource managers
function display_all_inactive_resource_managers() {
    $resource_managers = get_all_inactive_resource_managers();

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
                   3. ACTION BUTTONS (EDIT / DELETE)
                   ========================================================== */
                // We'll use data-* attributes to pass current values into the edit modal
                $safeEmail = htmlspecialchars($resource_manager['email'], ENT_QUOTES);
                $safeImg = htmlspecialchars($resource_manager['user_img'], ENT_QUOTES);
                echo "<td>
                        <a href='#' 
                           class='edit-rm-btn text-yellow-400 bg-cyan-500 hover:bg-cyan-700 px-2 py-1 rounded'
                           title='Edit'
                           data-rm-id='" . $resource_manager['rm_id'] . "'
                           data-user-id='" . $resource_manager['user_id'] . "'
                           data-dep-id='" . $resource_manager['dep_id'] . "'
                           data-res-id='" . $resource_manager['res_id'] . "'
                           data-res-name='" . htmlspecialchars($resource_manager['res_name'], ENT_QUOTES) . "'
                           data-user-img='" . $safeImg . "'
                           data-email='" . $safeEmail . "'
                           data-full-name='" . htmlspecialchars($resource_manager['full_name'] ?? '', ENT_QUOTES) . "'
                        >
                            <i class='fas fa-edit'></i>
                        </a>
                        &nbsp;
                        <a href='#'
                            class='delete-rm-btn text-yellow-100 bg-red-500 hover:bg-red-700 px-2 py-1 rounded'
                            data-rm-id='" . $resource_manager['rm_id'] . "'
                            data-rm-email='" . $safeEmail . "'
                            data-rm-img='" . $safeImg . "'
                            title='Delete'>
                            <i class='fas fa-trash-alt'></i>
                        </a>
                      </td>";


                /* ==========================================================
                   4. SHOW BUTTON 
                   ========================================================== */
                echo "<td>
                        <span 
                            onclick='openHappy(
                                " . $resource_manager["user_id"] . ",
                                \"" . $resource_manager["full_name"] . "\",
                                \"" . $resource_manager["user_img"] . "\",
                                \"" . $resource_manager["email"] . "\",
                                \"" . $resource_manager["staff_or_student_id"] . "\",
                                \"" . $resource_manager["dep_name"] . "\",
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
th:nth-child(2), td:nth-child(2) { width: 30%; }
th:nth-child(3), td:nth-child(3) { width: 20%; }
th:nth-child(4), td:nth-child(4) { width: 10%; }

/* Shared cell style */
th, td {
  padding: 12px 8px;
  text-align: left;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  border-bottom: 1px solid #ddd;
}

tr:hover {
    background: #f5f5f5; 
}
</style>

<div class="ml-64 px-8 py-6 sidebar-expanded transition-all">

    <!-- RESOURCES MANAGERS + ADD + SEARCH -->
    <div class="flex items-center gap-4">

        <!-- RESOURCES MANAGERS LABEL BOX -->
        <a href="resource_managers.php" 
           class="bg-gray-700 hover:bg-gray-900 text-white font-bold px-6 py-3 rounded-lg shadow">
            Resource Managers
        </a>

        <!-- + ADD BOX -->
        <button onclick="openAddForm()" 
                class="bg-gray-700 hover:bg-gray-900 text-white font-bold px-6 py-3 rounded-lg shadow">
            + Add
        </button>

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
                    <th class="px-6 py-3 text-left">Action</th>
                    <th class="px-6 py-3 text-left"></th>
                </tr>
            </thead>

            <!-- BODY -->
            <tbody class="bg-white text-black">
                <?php display_all_inactive_resource_managers(); ?>
            </tbody>
        </table>
    </div>

</div>

<!-- ADD FORM POPUP (unchanged) -->
<div id="addFormModal" 
     class="hidden fixed inset-0 bg-transparent flex justify-center items-center z-50">

    <div class="bg-white w-96 p-6 rounded-lg shadow-xl relative">
        <h2 class="text-xl font-bold mb-4 text-center">Add Resource Managers</h2>

        <!-- FORM -->
        <form action="../../action/add_resource_managers_action.php" method="POST" enctype="multipart/form-data">

            <!-- USERS DROPDOWN: ONLY STAFF -->
            <label class="block font-semibold mb-1">Users | Staff:</label>
            <select name="user" 
                    class="w-full px-3 py-2 border rounded mb-4" required>
                <option value="">~:~ Select Resource Manager ~:~</option>
                <?php
                    // Only pull users whose role = 2
                    $user_query = "SELECT u.user_id, u.email 
                        FROM users u 
                        WHERE u.user_role = 2
                        AND u.user_id NOT IN (SELECT user_id FROM resmanagers)";
                    $user_result = mysqli_query($conn, $user_query);

                    if (mysqli_num_rows($user_result) > 0) {
                        // Loop through the returned users
                        while ($row = mysqli_fetch_assoc($user_result)) {
                            echo "<option value='{$row['user_id']}'>{$row['email']}</option>";
                        }
                    }   else {
                            echo "<option value='' disabled>No available staff</option>";
                    }
                ?>
            </select>

            <!-- DEPARTMENTS DROPDOWN -->
            <label class="block font-semibold mb-1">Department:</label>
            <select name="department" 
                    class="w-full px-3 py-2 border rounded mb-4" required>
                <option value="">~:~ Select Department ~:~</option>
                <?php
                    $dep_query = "SELECT dep_id, dep_name FROM departments";
                    $dep_result = mysqli_query($conn, $dep_query);
                    while ($row = mysqli_fetch_assoc($dep_result)) {
                        echo "<option value='{$row['dep_id']}'>{$row['dep_name']}</option>";
                    }
                ?>
            </select>

            <!-- RESOURCES DROPDOWN: ONLY UNASSIGNED RESOURCES -->
            <label class="block font-semibold mb-1">Resources:</label>
            <select name="resource" class="w-full px-3 py-2 border rounded mb-4" required>
            <option value="">~:~ Select Resource ~:~</option>
            <?php
            // Get only resources NOT already assigned
            $resource_query = "
            SELECT r.res_id, r.res_name 
                FROM resources r
                WHERE r.res_id NOT IN (
                SELECT res_id FROM resmanagers
            )
            ";
        
            $resource_result = mysqli_query($conn, $resource_query);

            if (mysqli_num_rows($resource_result) > 0) {
                while ($row = mysqli_fetch_assoc($resource_result)) {
                    echo "<option value='{$row['res_id']}'>{$row['res_name']}</option>";
                }
            } else {
                echo "<option value='' disabled>No available resources</option>";
            }
            ?>
            </select>


            <!-- BUTTONS -->
            <div class="flex justify-between mt-4">
                <button type="submit"
                    class="bg-green-700 text-white px-4 py-2 rounded shadow font-bold">
                    Submit
                </button>

                <button type="button" onclick="closeAddForm()"
                    class="bg-red-600 text-white px-4 py-2 rounded shadow font-bold">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT FORM POPUP -->
<div id="editFormModal" 
     class="hidden fixed inset-0 bg-transparent flex justify-center items-center z-50">

    <div class="bg-white w-96 p-6 rounded-lg shadow-xl relative">
        <h2 class="text-xl font-bold mb-4 text-center">Edit Resource Manager</h2>

        <!-- FORM -->
        <form id="editRmForm" action="../../action/edit_resource_manager_action.php" method="POST" enctype="multipart/form-data">

            <!-- Hidden fields -->
            <input type="hidden" name="rm_id" id="edit_rm_id">
            <input type="hidden" name="old_user_id" id="edit_old_user_id">
            <input type="hidden" name="old_user_img" id="edit_old_user_img">

            <!-- DEPARTMENTS DROPDOWN -->
            <label class="block font-semibold mb-1">Department:</label>
            <select name="department" id="edit_department" class="w-full px-3 py-2 border rounded mb-4">
                <option value="">~:~ Select Department ~:~</option>
                <?php
                    $dep_query = "SELECT dep_id, dep_name FROM departments";
                    $dep_result = mysqli_query($conn, $dep_query);
                    while ($row = mysqli_fetch_assoc($dep_result)) {
                        echo "<option value='{$row['dep_id']}'>{$row['dep_name']}</option>";
                    }
                ?>
            </select>

            <!-- RESOURCES DROPDOWN: ONLY UNASSIGNED RESOURCES -->
            <label class="block font-semibold mb-1">Resources:</label>
            <select name="resource" id="edit_resource"class="w-full px-3 py-2 border rounded mb-4">
            <option value="">~:~ Select Unassigned Resource ~:~</option>
            <?php
            // Get only resources NOT already assigned
            $resource_query = "
                    SELECT r.res_id, r.res_name 
                    FROM resources r
                    WHERE r.res_id NOT IN 
                        (SELECT res_id FROM resmanagers)";
        
            $resource_result = mysqli_query($conn, $resource_query);

            if (mysqli_num_rows($resource_result) > 0) {
                while ($row = mysqli_fetch_assoc($resource_result)) {
                    echo "<option value='{$row['res_id']}'>{$row['res_name']}</option>";
                }
            } else {
                echo "<option value='' disabled>No available resources</option>";
            }
            ?>
            </select>


            <!-- CURRENT IMAGE PREVIEW -->
            <div class="mb-3">
                <label class="block font-semibold mb-1">Profile Picture:</label>
                <img id="edit_current_img" src="" alt="Current user image" style="width:100px;height:100px;object-fit:cover;border-radius:6px;">
            </div>

            <!-- UPLOAD NEW IMAGE (optional) 
            <label class="block font-semibold mb-1">Change User Image (optional):</label>
            <input type="file" name="user_img" accept=".jpg,.jpeg,.png,.gif" class="w-full mb-4"> -->

            <div class="flex justify-between mt-4">
                <button type="submit"
                    class="bg-green-700 text-white px-4 py-2 rounded shadow font-bold">
                    Submit
                </button>

                <button type="button" onclick="closeEditForm()"
                    class="bg-red-600 text-white px-4 py-2 rounded shadow font-bold">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- SUCCESS POPUP FROM ADD (unchanged) -->
<?php if (isset($_GET['success_msg'])): ?>
<div id="successModal" 
     class="fixed inset-0 bg-black bg-opacity-40 flex justify-center items-center z-50">

    <div class="bg-white w-96 p-6 rounded-lg shadow-xl text-center">
        
        <!-- Green Icon -->
        <div class="text-green-600 text-5xl mb-4">&#10004;</div>

        <!-- Success Message -->
        <p class="font-semibold text-lg mb-6">
            <?= htmlspecialchars($_GET['success_msg']) ?>
        </p>

        <!-- OK BUTTON -->
        <button onclick="window.location.href='resource_managers.php';"
                class="bg-green-600 text-white px-6 py-2 rounded-lg font-bold shadow hover:bg-green-700">
            OK
        </button>

    </div>
</div>
<?php endif; ?>

<!-- DELETE CONFIRMATION POPUP (unchanged) -->
<div id="rmDeletePopup"
     style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
            background:rgba(0,0,0,0.6); justify-content:center; align-items:center;">

    <div style="background:white; padding:20px; border-radius:8px; width:350px; text-align:center;">
        <h3 id="rmDeleteMessage" style="margin-bottom:15px; font-size:16px; font-weight:bold;"></h3>

        <button onclick="closeRmDeletePopup()"
                style="padding:7px 20px; background:gray; color:white; border-radius:5px;">
            No
        </button>

        <button id="yesRmDeleteBtn"
                style="padding:7px 20px; background:red; color:white; border-radius:5px;">
            Yes
        </button>
    </div>
</div>

<!-- DELETE SUCCESS POPUP -->
<div id="rmSuccessPopup"
     style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
            background:rgba(0,0,0,0.6); justify-content:center; align-items:center;">

    <div style="background:white; padding:20px; border-radius:8px; width:350px; text-align:center;">
        <h3 id="rmSuccessMessage" style="color:orange; font-size:16px; font-weight:bold;"></h3>

        <button onclick="window.location.reload()"
                style="padding:7px 20px; background:orange; color:white; border-radius:5px; margin-top:10px;">
            OK
        </button>
    </div>
</div>

<!-- EDIT SUCCESS POPUP (orange) - shown after a successful edit using GET param edited_email -->
<?php if (isset($_GET['edited_email'])): ?>
<div id="rmSuccessPopupInline"
     class="fixed inset-0 bg-black bg-opacity-40 flex justify-center items-center z-50">

    <div class="bg-white p-6 rounded-lg shadow-xl text-center" style="width:360px;">
        <h3 id="rmSuccessMessageInline" style="color:orange; font-size:16px; font-weight:bold;">
            <?= htmlspecialchars($_GET['edited_email']) ?> is edited successfully
        </h3>

        <button onclick="window.location.href='resource_managers.php';"
                style="padding:9px 22px; background:orange; color:white; border-radius:6px; margin-top:12px;">
            OK
        </button>
    </div>
</div>
<?php endif; ?>

<!-- HAPPY POPUP: RESOURCE MANAGER POPUP (unchanged) -->
<div id="happyModal"
     class="hidden fixed inset-0 bg-black bg-opacity-40 flex justify-center items-center z-50 pb-20">

    <div class="bg-white w-80 rounded-lg shadow-xl p-6 text-center">

        <!-- RESOURCE MANAGER NAME -->
        <p id="modalResourceName" class="text-xl font-bold text-black mb-4"></p>

        <!-- RESOURCE MANAGER IMAGE -->
        <img id="modalResourceImg"
             src=""
             alt="Resource Manager Image"
             class="w-50 h-50 object-cover mx-auto rounded mb-4">

        <!-- RESOURCE MANAGER EMAIL -->
        <p id="modalResourceEmail" class="text-black font-semibold mb-2"></p>

        <!-- RESOURCE MANAGER STAFF ID -->
        <p id="modalResourceStatus" class="text-yellow-700 mb-4"></p>

        <!-- RESOURCE MANAGER DEPARTMENT -->
        <p id="modalResourceDep" class="text-black font-semibold mb-3"></p>

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

function openHappy(id, name, img, email, staffid, dep, phone, assignedList, assignedCount) {
    // Set dynamic fields
    document.getElementById("modalResourceName").textContent = name;

    // build full path
    document.getElementById("modalResourceImg").src =
        "" + img;

    document.getElementById("modalResourceEmail").textContent =
        "Email: " + email;

    document.getElementById("modalResourceStatus").textContent =
        "Staff ID: " + staffid;

    document.getElementById("modalResourceDep").textContent =
        "Department: " + dep;

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

document.querySelectorAll(".delete-rm-btn").forEach(btn => {
    btn.addEventListener("click", function(e) {
        e.preventDefault();

        const rmId = this.dataset.rmId;
        const rmEmail = this.dataset.rmEmail;
        const rmImg = this.dataset.rmImg;

        const popup = document.getElementById("rmDeletePopup");
        const msg = document.getElementById("rmDeleteMessage");
        const yesBtn = document.getElementById("yesRmDeleteBtn");

        msg.textContent = `Are you sure you want to delete "${rmEmail}"?`;
        popup.style.display = "flex";

        const newYes = yesBtn.cloneNode(true);
        yesBtn.parentNode.replaceChild(newYes, yesBtn);

        newYes.addEventListener("click", function() {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "../../action/delete_resource_manager_action.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onload = () => {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);

                    if (response.success) {
                        popup.style.display = "none";
                        const success = document.getElementById("rmSuccessPopup");
                        const msg = document.getElementById("rmSuccessMessage");

                        msg.textContent = `${rmEmail} deleted successfully`;
                        success.style.display = "flex";

                        const row = btn.closest("tr");
                        row.parentNode.removeChild(row);
                    } else {
                        alert(response.message);
                    }
                }
            };

            xhr.send(`rm_id=${rmId}&rm_img=${encodeURIComponent(rmImg)}`);
        });
    });
});

function closeRmDeletePopup() {
    document.getElementById("rmDeletePopup").style.display = "none";
}

/* -------------------------------
   EDIT: open/edit modal handlers
   ------------------------------- */
function openEditForm() {
    document.getElementById("editFormModal").classList.remove("hidden");
}
function closeEditForm() {
    document.getElementById("editFormModal").classList.add("hidden");
}

// Wire up all edit buttons
document.querySelectorAll(".edit-rm-btn").forEach(btn => {
    btn.addEventListener("click", function(e) {
        e.preventDefault();

        const rmId = this.dataset.rmId;
        const userId = this.dataset.userId || '';
        const depId = this.dataset.depId || '';
        const resId = this.dataset.resId || '';
        const resName = this.dataset.resName || '';
        const userImg = this.dataset.userImg || '';
        const email = this.dataset.email || '';

        // Populate hidden fields
        document.getElementById("edit_rm_id").value = rmId;
        document.getElementById("edit_old_user_id").value = userId;
        document.getElementById("edit_old_user_img").value = userImg;

        // Populate user dropdown
        const userSelect = document.getElementById("edit_user");
        if (userSelect) userSelect.value = userId;

        // Populate department dropdown
        const depSelect = document.getElementById("edit_department");
        if (depSelect) depSelect.value = depId;

        // Populate resource dropdown
        const resSelect = document.getElementById("edit_resource");
        if (resSelect) {

            // Check if current resource exists
            let exists = [...resSelect.options].some(opt => opt.value === resId);

            // If not, insert as first option
            if (!exists) {
                const opt = document.createElement("option");
                opt.value = resId;
                opt.textContent = resName;
                resSelect.prepend(opt);
            }

            // Select the current resource
            resSelect.value = resId;
        }

        // Set current image
        const preview = document.getElementById("edit_current_img");
        preview.src = userImg || "../../images/placeholder.jpg";

        // Open modal
        openEditForm();
    });
});


document.querySelectorAll(".delete-rm-btn").forEach(btn => {
    btn.addEventListener("click", function(e) {
        e.preventDefault();

        const rmId = this.dataset.rmId;
        const rmEmail = this.dataset.rmEmail;
        const rmImg = this.dataset.rmImg;

        const popup = document.getElementById("rmDeletePopup");
        const msg = document.getElementById("rmDeleteMessage");
        const yesBtn = document.getElementById("yesRmDeleteBtn");

        msg.textContent = `Are you sure you want to delete "${rmEmail}"?`;
        popup.style.display = "flex";

        const newYes = yesBtn.cloneNode(true);
        yesBtn.parentNode.replaceChild(newYes, yesBtn);

        newYes.addEventListener("click", function() {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "../../action/delete_resource_manager_action.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onload = () => {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);

                    if (response.success) {
                        popup.style.display = "none";
                        const success = document.getElementById("rmSuccessPopup");
                        const msg = document.getElementById("rmSuccessMessage");

                        msg.textContent = `${rmEmail} deleted successfully`;
                        success.style.display = "flex";

                        const row = btn.closest("tr");
                        row.parentNode.removeChild(row);
                    } else {
                        alert(response.message);
                    }
                }
            };

            xhr.send(`rm_id=${rmId}&rm_img=${encodeURIComponent(rmImg)}`);
        });
    });
});
</script>

<?php include "../layout/footer.php"; ?>
