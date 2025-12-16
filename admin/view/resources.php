<?php include "../layout/header.php"; ?>
<?php include "../layout/sidebar.php"; ?>

<?php
echo '<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">';

// Function to display all inactive resources
function display_all_inactive_resources() {
    $resources = get_all_inactive_resources();

    // Placeholder image path
    $placeholder = "../../images/dummy_file.png";

    if ($resources !== false) {
        if (!empty($resources)) {
            
            foreach ($resources as $resource) {

                // Build full image path
                $imgPath = "../../images/resources_uploads/" . $resource['res_img'];

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
                echo "<td class='text-left font-medium'>" . $resource['res_name'] . "</td>";

                /* ==========================================================
                   3. ACTION BUTTONS (EDIT / DELETE)
                   ========================================================== */
                echo "<td>
                        <button 
                            class='open-edit-btn text-yellow-400 bg-cyan-500 hover:bg-cyan-700 px-2 py-1 rounded'
                            data-res-id='" . $resource['res_id'] . "'
                            data-res-name='" . htmlspecialchars($resource['res_name'], ENT_QUOTES) . "'
                            data-loc-id='" . $resource['loc_id'] . "'
                            data-res-img='" . htmlspecialchars($resource['res_img'], ENT_QUOTES) . "'
                            title='Edit'>
                            <i class='fas fa-edit'></i>
                        </button>
                            &nbsp;
                        <a href='#' 
                        class='delete-btn text-yellow-100 bg-red-500 hover:bg-red-700 px-2 py-1 rounded'
            data-res-id='" . $resource['res_id'] . "'
            data-res-name='" . htmlspecialchars($resource['res_name'], ENT_QUOTES) . "'
            title='Delete'>
                            <i class='fas fa-trash-alt'></i>
                        </a>
                      </td>";

                /* ==========================================================
                   4. SHOW BUTTON 
                   ========================================================== */
                echo "<td>
                        <span 
                        onclick='openHappy(" 
                        . $resource["res_id"] . ", "
                        . "\"" . $resource["res_name"] . "\", "
                        . "\"" . $resource["res_img"] . "\", "
                        . "\"" . $resource["loc_name"] . "\", "
                        . "\"" . $resource["cs_status"] . "\""
                        . ")'
                        class='hover-info-icon text-blue-600 font-bold underline cursor-pointer' title='Details'>
                        <i class='fas fa-info-circle'></i>
                        </span>
                     </td>";


                echo "</tr>";
            }

        } else {
            echo "<tr><td colspan='4' style='color: green; text-align: center;'>
                    <strong>No resources found !!!</strong>
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

    <!-- RESOURCES + ADD + SEARCH -->
    <div class="flex items-center gap-4">

        <!-- RESOURCES LABEL BOX -->
        <a href="resources.php" 
           class="bg-gray-700 hover:bg-gray-900 text-white font-bold px-6 py-3 rounded-lg shadow">
            Resources
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
                    <th class="px-6 py-3 text-left">Name</th>
                    <th class="px-6 py-3 text-left">Action</th>
                    <th class="px-6 py-3 text-left"></th>
                </tr>
            </thead>

            <!-- BODY -->
            <tbody class="bg-white text-black">
                <?php display_all_inactive_resources(); ?>
            </tbody>
        </table>
    </div>

</div>

<!-- EDIT FORM POPUP -->
<div id="editFormModal"
     class="hidden fixed inset-0 bg-black bg-opacity-40 flex justify-center items-center z-50">

    <div class="bg-white w-96 p-6 rounded-lg shadow-xl relative">

        <h2 class="text-xl font-bold mb-4 text-center">Edit Resource</h2>

        <!-- ERROR: NAME -->
        <?php if (isset($_GET['name_error'])): ?>
            <p class="text-red-600 font-bold mb-2"><?= htmlspecialchars($_GET['name_error']) ?></p>
        <?php endif; ?>

        <!-- ERROR: IMAGE -->
        <?php if (isset($_GET['image_error'])): ?>
            <p class="text-red-600 font-bold mb-2"><?= htmlspecialchars($_GET['image_error']) ?></p>
        <?php endif; ?>

        <!-- AUTO-HIDE ERRORS -->
        <?php if (isset($_GET['name_error']) || isset($_GET['image_error'])): ?>
        <script>
            setTimeout(() => {
                document.querySelectorAll("#editFormModal p.text-red-600").forEach(el => {
                    el.style.display = "none";
                });

                const url = new URL(window.location.href);
                url.searchParams.delete('name_error');
                url.searchParams.delete('image_error');
                window.history.replaceState(null, "", url.toString());
            }, 3000);
        </script>
        <?php endif; ?>

        <form id="editResourceForm"
              action="../../action/edit_resource_action.php"
              method="POST"
              enctype="multipart/form-data">

            <input type="hidden" name="res_id" id="edit_res_id">
            <input type="hidden" name="old_res_img" id="edit_old_res_img">

            <!-- RESOURCE NAME -->
            <label class="font-bold">Resource Name</label>
            <input type="text" name="res_name" id="edit_res_name"
                   class="w-full px-3 py-2 border rounded mb-3">

            <!-- RESOURCE LOCATION DROPDOWN -->
            <label class="block font-semibold mb-1">Resource Location:</label>
            <select name="location" id="edit_location"
                    class="w-full px-3 py-2 border rounded mb-4">
                <option value="">~:~ Select Location ~:~</option>

                <?php
                    $loc_query = "SELECT loc_id, loc_name FROM locations";
                    $loc_result = mysqli_query($conn, $loc_query);
                    while ($row = mysqli_fetch_assoc($loc_result)) {
                        echo "<option value='{$row['loc_id']}'>{$row['loc_name']}</option>";
                    }
                ?>
            </select>

            <!-- CURRENT IMAGE PREVIEW -->
            <div class="mb-3">
                <label class="block font-semibold mb-1">Current Image Preview:</label>
                <img id="edit_current_img" src="" alt="Current user image" style="width:100px;height:100px;object-fit:cover;border-radius:6px;">
            </div>

            <!-- IMAGE -->
            <label class="font-bold">Change Resource Image (Optional):</label>
            <input type="file" name="res_image" accept="image/jpeg,image/jpg,image/png"
                   class="w-full px-3 py-2 border rounded mb-3">

            <!-- BUTTONS -->
            <div class="flex justify-between mt-4">
                <button type="submit"
                        class="bg-green-700 text-white px-4 py-2 rounded shadow font-bold">
                    Update
                </button>

                <button type="button" onclick="closeEditForm()"
                        class="bg-red-600 text-white px-4 py-2 rounded shadow font-bold">
                    Cancel
                </button>
            </div>

        </form>
    </div>
</div>

<?php if (isset($_GET['edit_success'])): ?>
<div id="successModal"
     class="fixed inset-0 bg-black bg-opacity-40 flex justify-center items-center z-50">

    <div class="bg-white p-6 rounded-lg shadow-xl">
        <h2 class="text-orange-600 font-bold text-xl mb-4 text-center">
            <?= htmlspecialchars($_GET['edit_success']) ?> edited successfully!
        </h2>

        <div class="flex justify-center">
            <a href="resources.php"
               class="bg-blue-600 text-white px-4 py-2 rounded font-bold">
                OK
            </a>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- ADD FORM POPUP -->
<div id="addFormModal" 
     class="hidden fixed inset-0 bg-transparent flex justify-center items-center z-50">

    <div class="bg-white w-96 p-6 rounded-lg shadow-xl relative">

        <h2 class="text-xl font-bold mb-4 text-center">Add Resource</h2>

        <!-- ERROR: NAME -->
        <?php if (isset($_GET['name_error'])): ?>
            <p class="text-red-600 font-bold mb-2"><?= htmlspecialchars($_GET['name_error']) ?></p>
        <?php endif; ?>

        <!-- ERROR: IMAGE -->
        <?php if (isset($_GET['image_error'])): ?>
            <p class="text-red-600 font-bold mb-2"><?= htmlspecialchars($_GET['image_error']) ?></p>
        <?php endif; ?>
        
        <!-- EEROR MESSAGE DISAPPEARS AFTER THREE SECONDS -->
        <?php if (isset($_GET['name_error']) || isset($_GET['image_error'])): ?>
        <script>
            // Auto-hide error messages after 3 seconds
            setTimeout(() => {
                document.querySelectorAll("#addFormModal p.text-red-600").forEach(el => {
                    el.style.display = "none";
                });

                // Remove query parameters so errors don't reappear on refresh
                const url = new URL(window.location.href);
                url.searchParams.delete('name_error');
                url.searchParams.delete('image_error');
                window.history.replaceState(null, "", url.toString());
            }, 3000);
        </script>
        <?php endif; ?>

        <!-- FORM -->
        <form action="../../action/add_resources_action.php" method="POST" enctype="multipart/form-data">

            <!-- RESOURCE NAME -->
            <label class="font-bold">Resource Name</label>
            <input type="text" name="res_name"
                   class="w-full px-3 py-2 border rounded mb-3" required>

            <!-- RESOURCE LOCATION DROPDOWN -->
            <label class="block font-semibold mb-1">Resource Location:</label>
            <select name="location" 
                    class="w-full px-3 py-2 border rounded mb-4" required>
                <option value="">~:~ Select Location ~:~</option>

                <?php
                    $loc_query = "SELECT loc_id, loc_name FROM locations";
                    $loc_result = mysqli_query($conn, $loc_query);
                    while ($row = mysqli_fetch_assoc($loc_result)) {
                        echo "<option value='{$row['loc_id']}'>{$row['loc_name']}</option>";
                    }
                ?>
            </select>

            <!-- STATUS (HIDDEN = 2) -->
            <input type="hidden" name="status" value="2">

            <!-- IMAGE -->
            <label class="font-bold">Resource Image:</label>
            <input type="file" name="res_image" accept="image/jpeg,image/jpg,image/png"
                   class="w-full px-3 py-2 border rounded mb-3" required>

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

<!-- SUCCESS POPUP -->
<?php if (isset($_GET['success'])): ?>
<div id="successPopup"
     class="fixed inset-0 bg-black bg-opacity-40 flex justify-center items-center z-50">

    <div class="bg-white w-96 p-6 rounded-lg shadow-lg text-center">

        <div class="text-green-600 text-4xl font-bold mb-3">✔</div>

        <p class="font-bold text-green-700 text-lg mb-4">
            <?= htmlspecialchars($_GET['success']) ?>
        </p>

        <button onclick="redirectBack()"
                class="bg-green-700 text-white px-6 py-2 rounded-lg shadow font-bold">
            OK
        </button>

    </div>
</div>
<?php endif; ?>

<!-- ================= DELETE CONFIRMATION POPUP ================= -->
<div id="deletePopup" 
     style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
            background:rgba(0,0,0,0.5); justify-content:center; align-items:center;">

    <div style="background:white; padding:25px; width:350px; border-radius:8px; text-align:center;">
        <h3 id="deleteMessage" style="font-size:18px; margin-bottom:20px;"></h3>

        <button onclick="closeDeletePopup()" 
                style="background:gray; color:white; padding:8px 20px; border-radius:5px; margin-right:10px;">
            No
        </button>

        <button id="yesDeleteBtn" 
                style="background:red; color:white; padding:8px 20px; border-radius:5px;">
            Yes
        </button>
    </div>
</div>

<!-- ================= SUCCESSFUL DELETE POPUP ================= -->
<div id="successPopup" 
     style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
            background:rgba(0,0,0,0.5); justify-content:center; align-items:center;">

    <div style="background:white; padding:25px; width:350px; border-radius:8px; text-align:center;">
        <h3 id="successMessage" style="color:orange; font-size:18px; margin-bottom:20px;"></h3>

        <button onclick="window.location.href='resources.php';"
                style="background:orange; color:white; padding:8px 20px; border-radius:5px;">
            OK
        </button>
    </div>
</div>

<!-- HAPPY POPUP: RESOURCE POPUP -->
<div id="happyModal"
     class="hidden fixed inset-0 bg-black bg-opacity-40 flex justify-center items-center z-50 pb-20">

    <div class="bg-white w-80 rounded-lg shadow-xl p-6 text-center">

        <!-- RESOURCE NAME -->
        <p id="modalResourceName" class="text-xl font-bold text-black mb-4"></p>

        <!-- RESOURCE IMAGE -->
        <img id="modalResourceImg"
             src=""
             alt="Resource Image"
             class="w-50 h-50 object-cover mx-auto rounded mb-4">

        <!-- RESOURCE LOCATION -->
        <p id="modalResourceLocation" class="text-black font-semibold mb-2"></p>

        <!-- RESOURCE STATUS -->
        <p id="modalResourceStatus" class="text-gray-500 mb-4"></p>

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
function openAddForm() {
    document.getElementById("addFormModal").classList.remove("hidden");
}

function closeAddForm() {
    document.getElementById("addFormModal").classList.add("hidden");
}

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

function openHappy(id, name, img, loc, status) {
    // Set dynamic fields
    document.getElementById("modalResourceName").textContent = name;

    // build full path
    document.getElementById("modalResourceImg").src =
        "../../images/resources_uploads/" + img;

    document.getElementById("modalResourceLocation").textContent =
        "Location: " + loc;

    document.getElementById("modalResourceStatus").textContent =
        "Status: " + status;

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

// Open confirm popup
document.querySelectorAll(".delete-btn").forEach(btn => {
    btn.addEventListener("click", function(e) {
        e.preventDefault();

        const resId = this.dataset.resId;
        const resName = this.dataset.resName;

        // Show custom delete popup
        const deletePopup = document.getElementById("deletePopup");
        const deleteMessage = document.getElementById("deleteMessage");
        const yesBtn = document.getElementById("yesDeleteBtn");

        deleteMessage.textContent = `Are you sure you want to delete "${resName}"?`;

        deletePopup.style.display = "flex";

        // Remove previous click handler (to avoid multiple bindings)
        const newYesBtn = yesBtn.cloneNode(true);
        yesBtn.parentNode.replaceChild(newYesBtn, yesBtn);

        // On Yes click
        newYesBtn.addEventListener("click", function() {
            // AJAX request
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "../../action/delete_resource_action.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onload = () => {
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            // Remove row
                            const row = btn.closest("tr");
                            row.parentNode.removeChild(row);

                            // Hide delete popup
                            deletePopup.style.display = "none";

                            // Optionally show success popup
                            const successPopup = document.getElementById("successPopup");
                            const successMsg = document.getElementById("successMessage");
                            if (successPopup && successMsg) {
                                successMsg.textContent = `${resName} deleted successfully`;
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

            xhr.send(`res_id=${encodeURIComponent(resId)}`);
        });
    });
});

// Close delete popup function
function closeDeletePopup() {
    document.getElementById("deletePopup").style.display = "none";
}

function closeEditForm() {
    document.getElementById("editFormModal").classList.add("hidden");
}

// OPEN EDIT FORM
document.querySelectorAll(".open-edit-btn").forEach(btn => {
    btn.addEventListener("click", function () {

        const id = this.dataset.resId;
        const name = this.dataset.resName;
        const loc = this.dataset.locId;
        const img = this.dataset.resImg;

        document.getElementById("edit_res_id").value = id;
        document.getElementById("edit_res_name").value = name;
        document.getElementById("edit_location").value = loc;
        document.getElementById("edit_old_res_img").value = img;


        // set image preview
        const preview = document.getElementById("edit_current_img");
        preview.src = img || "../../images/placeholder.jpg";

        document.getElementById("editFormModal").classList.remove("hidden");
    });
});

// OPEN EDIT FORM
document.querySelectorAll(".open-edit-btn").forEach(btn => {
    btn.addEventListener("click", function () {
        const id = this.dataset.resId;
        const name = this.dataset.resName;
        const loc = this.dataset.locId;
        const img = this.dataset.resImg;

        // Populate form fields
        document.getElementById("edit_res_id").value = id;
        document.getElementById("edit_res_name").value = name;
        document.getElementById("edit_location").value = loc;
        document.getElementById("edit_old_res_img").value = img;

        // FIXED: Set image preview with FULL PATH
        const preview = document.getElementById("edit_current_img");
        if (img) {
            preview.src = "../../images/resources_uploads/" + img;
            preview.onerror = function() {
                this.src = "../../images/dummy_file.png"; // fallback
            };
        } else {
            preview.src = "../../images/dummy_file.png";
        }

        // Show modal
        document.getElementById("editFormModal").classList.remove("hidden");
    });
});


</script>

<?php include "../layout/footer.php"; ?>
