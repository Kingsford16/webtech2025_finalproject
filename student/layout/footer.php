<footer class="bg-gray-800 text-white text-center py-4 mt-10 fixed bottom-0 left-0 w-full">
    © <?php echo date("Y"); ?> Campus Resource Management System | All rights reserved

</footer>

<script>
const sidebar  = document.getElementById("sidebar");
const toggle   = document.getElementById("toggleSidebar");
const content  = document.querySelector("div.sidebar-expanded");

toggle.addEventListener("click", () => {
    sidebar.classList.toggle("w-64");
    sidebar.classList.toggle("w-16");

    content.classList.toggle("ml-64");
    content.classList.toggle("ml-16");
});
</script>

</body>
</html>
