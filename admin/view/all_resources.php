<?php include "../layout/header.php"; ?>
<?php include "../layout/sidebar.php"; ?>

<?php
echo '<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">';

// Function to display all inactive resources
function display_all_resources() {
    $resources = get_all_resources();

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
                   3. SHOW BUTTON 
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

        <!-- RESOURCES LABEL BOX -->
        <a href="all_resources.php" 
           class="bg-gray-700 hover:bg-gray-900 text-white font-bold px-6 py-3 rounded-lg shadow">
            Resources
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
                    <th class="px-6 py-3 text-left"></th>
                </tr>
            </thead>

            <!-- BODY -->
            <tbody class="bg-white text-black">
                <?php display_all_resources(); ?>
            </tbody>
        </table>
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
