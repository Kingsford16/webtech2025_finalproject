<?php include "../layout/header.php"; ?>
<?php include "../layout/sidebar.php"; ?>

<div class="min-h-screen bg-gray-100 p-10 ml-64 transition-all sidebar-expanded">

    <div class="flex items-center gap-4">
        <a href="admin_dashboard.php" 
   class="bg-gray-700 hover:bg-gray-900 text-cyan-300 font-bold px-12 py-5 rounded-lg shadow 
          flex items-center space-x-3">
    
    <svg class="w-6 h-6 text-blue-300" fill="none" stroke="currentColor" stroke-width="2"
         viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"/>
    </svg>

    <span>Dashboard</span>
</a>

    </div>
    <div class="mt-40"></div>

    <!-- GRID OF 6 ITEMS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10">

        <?php
        $items = [
            ["name" => "All managers",   "page" => "all_managers.php", "img" => "../../images/all_managers.jpg"],
            ["name" => "All resources",   "page" => "all_resources.php", "img" => "../../images/all_resources.png"],
            ["name" => "Upcoming events",  "page" => "upcoming_events.php", "img" => "../../images/upcoming_events.png"],
        ];

        foreach ($items as $box):
        ?>

            <div class="bg-white shadow-md rounded-xl p-6 text-center">
                
                <!-- TITLE -->
                <h2 class="text-gray-500 text-lg font-semibold mb-3"><?= $box["name"] ?></h2>

                <!-- IMAGE HOLDER -->
                <div class="w-full h-40 bg-gray-200 rounded-lg mb-4 overflow-hidden">
                    <img src="<?= $box["img"] ?>" class="w-full h-full object-cover">
                </div>

                <a href="<?= $box["page"] ?>"
                    class="inline-flex items-center px-4 py-1.5 bg-blue-100 text-blue-700 rounded-full 
                        font-medium hover:bg-blue-200 transition-colors duration-200">
                            More
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 ml-2" fill="none" 
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" 
                        d="M13 7l5 5-5 5M5 12h12" />
                    </svg>
                </a>

            </div>

        <?php endforeach; ?>
    </div>

</div>

<?php include "../layout/footer.php"; ?>
