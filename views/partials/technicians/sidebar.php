<script src="https://cdn.jsdelivr.net/npm/heroicons@2.0.17/dist/heroicons.min.js"></script>
    <style>
        /* Custom scrollbar and smooth transitions */
        :root {
            --primary-color: #6366f1;
            --hover-color: #4338ca;
        }
        body {
            font-family: 'Inter', sans-serif;
        }
        .sidebar-shadow {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        .active-menu-item {
            background: linear-gradient(to right, var(--primary-color), #8b5cf6);
            color: white;
        }
    </style>
    <?php
    $menuItems = [
        [
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6"><path d="M11.47 3.84a.75.75 0 011.06 0l8.69 8.69a.75.75 0 101.06-1.06l-8.689-8.69a2.25 2.25 0 00-3.182 0l-8.69 8.69a.75.75 0 001.061 1.06l8.69-8.69z" /><path d="M12 5.432l8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 01-.75-.75v-4.5a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75v4.5c0 .414-.336.75-.75.75h-3.073c-1.034 0-1.875-.84-1.875-1.875v-6.198a2.29 2.29 0 00.091-.086L12 5.432z" /></svg>',
            'text' => 'Dashboard',
            'link' => '/technicians/dashboard'
        ],
        [
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6"><path fill-rule="evenodd" d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0016.5 9h-1.875a1.875 1.875 0 01-1.875-1.875V5.25A3.75 3.75 0 009 1.5H5.625zM7.5 15a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5A.75.75 0 017.5 15zm.75 2.25a.75.75 0 000 1.5H12a.75.75 0 000-1.5H8.25z" clip-rule="evenodd" /></svg>',
            'text' => 'Work Orders',
            'link' => '/technicians/work-order/history'
        ],
        [
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6"><path fill-rule="evenodd" d="M12 2.25a5.25 5.25 0 100 10.5 5.25 5.25 0 000-10.5zm0 9a3.75 3.75 0 110-7.5 3.75 3.75 0 010 7.5zM6.307 15.114a8.25 8.25 0 0111.386 0A5.982 5.982 0 0118 18c0 2.004-1.176 3.75-3 3.75H9c-1.824 0-3-1.746-3-3.75a5.982 5.982 0 01.307-2.886zM8.75 19.5c-.552 0-1-.672-1-1.5s.448-1.5 1-1.5h6.5c.552 0 1 .672 1 1.5s-.448 1.5-1 1.5H8.75z" clip-rule="evenodd"/></svg>',
            'text' => 'Schedule',
            'link' => '/technicians/profile'
        ],
    ];
    ?>

    <!-- Sidebar -->
    <div id="sidebar" class="fixed left-0 top-0 h-full w-[260px] bg-white border-r border-gray-200 shadow-md transition-all duration-300 ease-in-out z-50 overflow-hidden">
        <!-- Sidebar Content -->
        <div class="relative h-full flex flex-col">
            <!-- Logo Section -->
            <div class="px-6 py-6 border-b border-gray-200 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-8 h-8 text-indigo-600">
                        <path fill-rule="evenodd" d="M12.963 2.286a.75.75 0 00-1.071-.136 9.742 9.742 0 00-3.539 6.177A7.547 7.547 0 016.648 6.61a.75.75 0 00-1.152-.082A9 9 0 1021.75 12.75a.75.75 0 00-1.624-.371 7.5 7.5 0 01-9.88 5.393.75.75 0 00-.363-.496A8.963 8.963 0 0112.075 3.3a.75.75 0 00.888-.007z" clip-rule="evenodd" />
                    </svg>
                    <h1 class="text-xl text-center font-bold text-gray-800 tracking-tight sidebar-text">Field Service Pro</h1>
                </div>

                <!-- Sidebar Toggle -->
                <!-- <button id="sidebarToggle" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button> -->
            </div>

            <!-- Navigation Menu -->
            <nav class="flex-grow overflow-y-auto py-4 px-4 space-y-2">
                <?php foreach ($menuItems as $item): ?>
                    <a href="<?= htmlspecialchars($item['link']) ?>" class="group flex items-center space-x-3 px-4 py-2.5 text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all duration-300 ease-in-out">
                        <div class="text-gray-400 group-hover:text-indigo-600 transition-colors duration-300">
                            <?= $item['icon'] ?>
                        </div>
                        <span class="text-sm font-medium"><?= htmlspecialchars($item['text']) ?></span>
                    </a>
                <?php endforeach; ?>
            </nav>

            <!-- Logout Section -->
            <div class="border-t border-gray-200 p-4">
                <a href="/logout" class="flex items-center space-x-3 px-4 py-2.5 text-red-500 hover:bg-red-50 rounded-lg transition-all duration-300 ease-in-out group">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 text-red-400 group-hover:text-red-600 transition-colors duration-300">
                        <path fill-rule="evenodd" d="M7.5 3.75A1.5 1.5 0 006 5.25v13.5a1.5 1.5 0 001.5 1.5h6a1.5 1.5 0 001.5-1.5V15a.75.75 0 011.5 0v3.75a3 3 0 01-3 3h-6a3 3 0 01-3-3V5.25a3 3 0 013-3h6a3 3 0 013 3V9A.75.75 0 0115 9V5.25a1.5 1.5 0 00-1.5-1.5h-6zm5.03 4.72a.75.75 0 010 1.06l-1.72 1.72h10.94a.75.75 0 010 1.5H10.81l1.72 1.72a.75.75 0 11-1.06 1.06l-3-3a.75.75 0 010-1.06l3-3a.75.75 0 011.06 0z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-sm font-medium">Logout</span>
                </a>
                </div>
        </div>
    </div>

