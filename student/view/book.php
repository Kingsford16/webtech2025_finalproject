<?php include "../layout/header.php"; ?>
<?php include "../layout/sidebar.php"; ?>

<?php
echo '<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">';

// Get all resources from database
$resources = get_all_resources();
?>

<div x-data="bookingPage()" class="ml-64 p-6 pb-20 bg-gray-100 min-h-screen transition-all duration-300 sidebar-expanded">

<!-- BOOKING REQUESTS HEADER CONTAINER -->
<div class="flex items-center gap-4 mb-8">
<!-- BOOKING REQUESTS -->
<a href="book.php" class="bg-gray-700 hover:bg-gray-900 text-white font-bold px-6 py-3 rounded-md shadow-md">
Booking Requests
</a>

<!-- DASHBOARD -->
<a href="student_dashboard.php" class="bg-gray-700 hover:bg-gray-900 text-white font-bold px-6 py-3 rounded-md shadow-md">
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

<!-- RESOURCES DISPLAY CONTAINER -->
<div id="resources-container" class="min-h-[400px] flex flex-col items-center justify-center py-12">
<?php if (empty($resources)): ?>
    <!-- NO RESOURCES MESSAGE -->
    <div class="text-center">
        <p class="text-2xl font-bold text-black mb-4">Nothing to show here! Come back later.</p>
    </div>
<?php else: ?>
    <!-- RESOURCES GRID -->
    <div class="w-full max-w-7xl grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 justify-items-center">
        <?php foreach ($resources as $resource): ?>
            <div class="bg-white shadow-md rounded-lg p-6 flex flex-col items-center w-full max-w-sm hover:shadow-xl transition-shadow duration-300"
            data-resource-name="<?php echo strtolower(htmlspecialchars($resource['res_name'])); ?>"
            >
                
                <!-- INFO ICON BUTTON -->
                <button 
                    @click="openShowMore('<?php echo $resource['res_id']; ?>', '<?php echo htmlspecialchars($resource['res_name']); ?>', '<?php echo htmlspecialchars($resource['res_img']); ?>', '<?php echo htmlspecialchars($resource['loc_name']); ?>', '<?php echo htmlspecialchars($resource['cs_status']); ?>')"
                    class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold mb-4 hover:bg-blue-600 transition-colors -mt-4 z-10"
                    title="More info">
                    i
                </button>

                <!-- IMAGE HOLDER -->
                <div class="w-full h-48 bg-gray-200 rounded-lg overflow-hidden flex justify-center items-center mb-4">
                    <?php if (!empty($resource['res_img'])): ?>
                        <img 
                            src="../../images/resources_uploads/<?php echo htmlspecialchars($resource['res_img']); ?>" 
                            alt="<?php echo htmlspecialchars($resource['res_name']); ?>"
                            class="w-full h-full object-cover"
                            onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\'w-full h-full bg-gray-300 flex items-center justify-center text-gray-500 text-sm\'>Image not available</div>'"
                        >
                    <?php else: ?>
                        <div class="w-full h-full bg-gray-300 flex items-center justify-center text-gray-500 text-sm">
                            No image
                        </div>
                    <?php endif; ?>
                </div>

                <!-- RESOURCE NAME -->
                <p class="text-cyan-600 font-bold text-lg text-center mb-4 leading-tight">
                    <?php echo htmlspecialchars($resource['res_name']); ?>
                </p>

                <!-- BOOK BUTTON -->
                <button
                    @click="openAddForm('<?php echo htmlspecialchars($resource['res_name'], ENT_QUOTES); ?>', <?php echo $resource['res_id']; ?>)"
                    class="px-8 py-2 bg-gray-700 text-white font-bold text-sm rounded-full hover:bg-gray-900 transition-colors whitespace-nowrap">
                        Book now
                    </button>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
</div>

<!-- ADD FORM POPUP -->
<div id="addFormModal" 
     class="hidden fixed inset-0 bg-transparent flex justify-center items-center z-50 pb-20">

    <div class="bg-white w-96 p-6 rounded-lg shadow-xl relative">

        <h2 class="text-xl font-bold mb-4 text-center">Book Resource</h2>

        <!-- ERROR: NAME -->
        <?php if (isset($_GET['name_error'])): ?>
            <p class="text-red-600 font-bold mb-2"><?= htmlspecialchars($_GET['name_error']) ?></p>
        <?php endif; ?>

        <!-- ERROR: IMAGE -->
        <?php if (isset($_GET['uniqueUser_error'])): ?>
            <p class="text-red-600 font-bold mb-2"><?= htmlspecialchars($_GET['uniqueUser_error']) ?></p>
        <?php endif; ?>
        
        <!-- EEROR MESSAGE DISAPPEARS AFTER THREE SECONDS -->
        <?php if (isset($_GET['name_error']) || isset($_GET['uniqueUser_error'])): ?>
        <script>
            // Auto-hide error messages after 3 seconds
            setTimeout(() => {
                document.querySelectorAll("#addFormModal p.text-red-600").forEach(el => {
                    el.style.display = "none";
                });

                // Remove query parameters so errors don't reappear on refresh
                const url = new URL(window.location.href);
                url.searchParams.delete('name_error');
                url.searchParams.delete('uniqueUser_error');
                window.history.replaceState(null, "", url.toString());
            }, 30000);
        </script>
        <?php endif; ?>

        <!-- FORM -->
        <form action="../../action/book_action.php" method="POST" enctype="multipart/form-data">

            <!-- Resource name (readonly) -->
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Resource Name</label>
                <input type="text" id="resourceName" name="res_name" class="font-bold bg-gray-700 text-yellow-500 w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50" readonly  value="<?= isset($_GET['res_name']) ? htmlspecialchars($_GET['res_name']) : '' ?>">
            </div>
            <!-- Hidden resource ID field -->
            <input type="hidden" id="resourceId" name="res_id" value="<?= isset($_GET['res_id']) ? (int)$_GET['res_id'] : '' ?>">
            <!-- SPACING -->
            <div class="mt-4"></div>

            <!-- Event Date -->
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Event Date <span class="text-red-500">*</span></label>
                <input type="date" name="event_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg" required value="<?= isset($_GET['event_date']) ? htmlspecialchars($_GET['event_date']) : '' ?>">
            </div>
            <!-- SPACING -->
            <div class="mt-4"></div>

            <!-- Capacity -->
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Capacity <span class="text-red-500">*</span></label>
                <input type="number" name="capacity" min="1" step="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg" required value="<?= isset($_GET['capacity']) ? (int)$_GET['capacity'] : '1' ?>">
            </div>
            <!-- SPACING -->
            <div class="mt-4"></div>

            <!-- Purpose -->
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Purpose <span class="text-red-500">*</span></label>
                <input type="text" name="purpose" class="w-full px-3 py-2 border border-gray-300 rounded-lg" required value="<?= isset($_GET['purpose']) ? htmlspecialchars($_GET['purpose']) : '' ?>">
            </div>
            <!-- SPACING -->
            <div class="mt-4"></div>

            <!-- EVENT TIMES DROPDOWN -->
            <div>
            <label class="block font-semibold text-gray-700 mb-1">Event Date <span class="text-red-500">*</span></label>
            <select name="event_time" 
                    class="w-full px-3 py-2 border rounded mb-4" required>
                <option value=""> Select Event Time </option>
                <?php
                    $et_query = "SELECT eventimes_id, times FROM eventimes ORDER BY eventimes_id ASC";
                    $et_result = mysqli_query($conn, $et_query);
                    while ($row = mysqli_fetch_assoc($et_result)) {
                        $selected = (isset($_GET['event_time']) && $_GET['event_time'] == $row['eventimes_id']) ? 'selected' : '';
                        echo "<option value='{$row['eventimes_id']}' $selected>{$row['times']}</option>";
                    }
                ?>
            </select>
            </div>
            <!-- SPACING -->
            <div class="mt-10"></div>

            <div class="flex gap-3">
                <button type="submit" class="flex-1 px-4 py-2 bg-green-500 hover:bg-green-700 text-white font-semibold rounded-lg">Submit</button>

                <button type="button" onclick="closeAddForm()" class="flex-1 px-4 py-2 bg-red-500 hover:bg-red-700 text-white font-semibold rounded-lg">Cancel
                </button>
            </div>

        </form>
    </div>
</div>

<!-- BOOKING SUCCESS POPUP -->
<?php if (isset($_GET['success'])): ?>
<div id="successPopup"
     class="fixed inset-0 bg-black bg-opacity-40 flex justify-center items-center z-50">

    <div class="bg-white w-96 p-6 rounded-lg shadow-lg text-center">

        <div class="text-yellow-500 text-4xl font-bold mb-3"><i class="fas fa-paper-plane"></i></div>

        <p class="font-bold text-gray-900 text-lg mb-4">
            <?= htmlspecialchars($_GET['success']) ?>
        </p>

        <button onclick="redirectBack()"
                class="bg-gray-700 hover:bg-gray-900 text-white px-6 py-2 rounded-lg shadow font-bold">
            OK
        </button>

    </div>
</div>
<?php endif; ?>

<!-- SHOW MORE POPUP -->
<div id="showMore"
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
        <button @click="closeShowMore()" class="px-8 py-3 bg-gray-700 text-white font-semibold rounded-xl hover:bg-gray-900 transition-colors">
            Close
        </button>
    </div>
</div>

</div>

<script>

// To open form and show resource name as read only
function openAddForm(resourceName, resourceId) {
    // Set the readonly field
    document.getElementById('resourceName').value = resourceName;
    // Set the hidden resource ID
    document.getElementById('resourceId').value = resourceId;
    // Show the modal
    document.getElementById("addFormModal").classList.remove("hidden");
}


// To close form
function closeAddForm() {
    document.getElementById("addFormModal").classList.add("hidden");
}

function openShowMore(id, name, img, loc, status) {
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

    document.getElementById("showMore").classList.remove("hidden");
}

function closeShowMore() {
    document.getElementById("showMore").classList.add("hidden");
}

// Capacity field will alwyas be 1 unless you enter a high positive integer, like 2, 3,... infinity.
const capacityInput = document.querySelector('input[name="capacity"]');
capacityInput.addEventListener('input', () => {
    let value = capacityInput.value;
    // Parse as integer
    let intValue = parseInt(value, 10);

    // If not a positive integer, reset to 1
    if (isNaN(intValue) || intValue < 1) {
      capacityInput.value = 1;
    }
});

// Search for resources by name
document.getElementById('resourceSearch').addEventListener('input', function () {
    const filter = this.value.toLowerCase().trim();
    const cards = document.querySelectorAll('[data-resource-name]');

    cards.forEach(card => {
        const name = card.getAttribute('data-resource-name');
        if (!filter || name.includes(filter)) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
});

function redirectBack() {
    window.location.href = "book.php";
}

document.addEventListener("DOMContentLoaded", function () {
    <?php if (isset($_GET['name_error']) || isset($_GET['uniqueUser_error'])): ?>
        document.getElementById("addFormModal").classList.remove("hidden");

        <?php if (isset($_GET['res_name']) && isset($_GET['res_id'])): ?>
            document.getElementById('resourceName').value = "<?= htmlspecialchars($_GET['res_name']) ?>";
            document.getElementById('resourceId').value = "<?= (int)$_GET['res_id'] ?>";
        <?php endif; ?>
    <?php endif; ?>
});

</script>

<?php include "../layout/footer.php"; ?>
