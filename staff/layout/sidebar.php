<aside id="sidebar" class="fixed top-0 left-0 h-full w-64 bg-gray-900 text-white pt-20 transition-all duration-300">

    <!-- PROFILE IMAGE -->
<div class="flex justify-center relative">
    <img 
        src="<?= htmlspecialchars($USER_IMAGE) ?>" 
        class="w-28 h-28 rounded-full object-cover border-4 border-gray-700 shadow-lg cursor-pointer" 
        alt="Profile Image"
        id="profileImage"
    >
</div>


    <!-- NAVIGATION -->
    <nav class="flex flex-col space-y-4 mt-10 px-2">

        <!-- DASHBOARD -->
        <a href="staff_dashboard.php" 
            class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-md transition
            hover:bg-gray-700 <?php echo basename($_SERVER['PHP_SELF'])=='staff_dashboard.php'?'bg-gray-800':''; ?>">

            <svg class="w-6 h-6 text-blue-300" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"/>
            </svg>

            <span>Dashboard</span>
        </a>

        <!-- APPROVE BOOKINGS -->
        <a href="approve.php" 
            class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-md transition
            hover:bg-gray-700 <?php echo basename($_SERVER['PHP_SELF'])=='approve.php'?'bg-gray-800':''; ?>">

            <svg class="w-6 h-6 text-green-300" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M12 6v6l4 2m6-2a10 10 0 11-20 0 10 10 0 0120 0z"/>
            </svg>

            <span>Approve bookings</span>
        </a>

        <!-- BOOK RESOURCES -->
        <a href="assigned.php" 
            class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-md transition
            hover:bg-gray-700 <?php echo basename($_SERVER['PHP_SELF'])=='assigned.php'?'bg-gray-800':''; ?>">

            <svg class="w-6 h-6 text-green-300" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M12 6v6l4 2m6-2a10 10 0 11-20 0 10 10 0 0120 0z"/>
            </svg>

            <span>Assigned resources</span>
        </a>

        <!-- LOGOUT -->
<button 
    onclick="openLogoutModal()"
    class="sidebar-link flex w-full items-center gap-3 px-4 py-3 rounded-md transition
    hover:bg-orange-500 <?php echo basename($_SERVER['PHP_SELF'])=='logout.php'?'bg-orange-600':''; ?>">
    
    <svg class="w-6 h-6 text-orange-300" fill="none" stroke="currentColor" stroke-width="2"
         viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h4a2 2 0 012 2v1"/>
    </svg>

    <span class="text-orange-300 font-semibold">Logout</span>
</button>


    </nav>

</aside>

<!-- UPLOAD IMAGE MODAL -->
<div id="uploadModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">
    <div class="bg-white w-96 rounded-xl shadow-xl p-6 text-center">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Change Profile Picture</h2>
        <p class="text-gray-600 mb-6">Do you want to upload a new profile image?</p>
        <div class="flex justify-between">
            <button onclick="closeUploadModal()" class="px-4 py-2 bg-gray-300 hover:bg-gray-500 text-gray-800 rounded-md">
                Cancel
            </button>
            <button onclick="triggerFileUpload()" class="px-4 py-2 bg-green-500 hover:bg-green-700 text-white rounded-md">
                Upload Image
            </button>
        </div>
    </div>
</div>

<!-- Hidden file input for upload -->
<form id="uploadForm" action="../../action/upload_profile.php" method="post" enctype="multipart/form-data">
    <input type="file" name="profile_image" id="hiddenFileInput" class="hidden" accept="image/*" onchange="submitProfileForm()">
</form>


<!-- LOGOUT CONFIRMATION MODAL -->
<div id="logoutModal" 
     class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">

    <div class="bg-white w-96 rounded-xl shadow-xl p-6 transform scale-95 opacity-0 transition-all duration-300"
         id="logoutModalBox">
         
        <h2 class="text-xl font-semibold text-gray-800 mb-4 text-center">
            Confirm Logout
        </h2>

        <p class="text-gray-600 text-center mb-6">
            Are you sure you want to log out?
        </p>

        <div class="flex justify-between">
            <!-- Cancel -->
            <button onclick="closeLogoutModal()"
                class="px-4 py-2 bg-gray-300 hover:bg-gray-500 text-gray-800 rounded-md">
                No, Stay
            </button>

            <!-- Confirm -->
            <button onclick="proceedLogout()"
                class="px-4 py-2 bg-red-500 hover:bg-red-700 text-white rounded-md">
                Yes, Logout
            </button>
        </div>
    </div>
</div>

<script>
// Open modal when profile image clicked
document.getElementById('profileImage').addEventListener('click', () => {
    document.getElementById('uploadModal').classList.remove('hidden');
});

// Close modal
function closeUploadModal() {
    document.getElementById('uploadModal').classList.add('hidden');
}

// Trigger file explorer
function triggerFileUpload() {
    document.getElementById('hiddenFileInput').click();
}

// Auto-submit form after selecting file
function submitProfileForm() {
    document.getElementById('uploadForm').submit();
}
</script>


<script>
// OPEN modal
function openLogoutModal() {
    const modal = document.getElementById("logoutModal");
    const box = document.getElementById("logoutModalBox");

    modal.classList.remove("hidden");

    setTimeout(() => {
        box.classList.remove("scale-95", "opacity-0");
        box.classList.add("scale-100", "opacity-100");
    }, 10);
}

// CLOSE modal
function closeLogoutModal() {
    const modal = document.getElementById("logoutModal");
    const box = document.getElementById("logoutModalBox");

    box.classList.add("scale-95", "opacity-0");

    setTimeout(() => {
        modal.classList.add("hidden");
    }, 200);
}

// PROCEED to logout
function proceedLogout() {
    window.location.href = "../../login/logout.php";
}
</script>

