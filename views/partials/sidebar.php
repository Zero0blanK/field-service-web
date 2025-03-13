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
        /* Dropdown styles */
        .dropdown-menu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-in-out;
        }
        .dropdown-menu.open {
            max-height: 200px;
        }
        .dropdown-toggle svg.transform {
            transform: rotate(180deg);
        }
    </style>
    <?php
    $menuItems = [
        [
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6"><path d="M11.47 3.84a.75.75 0 011.06 0l8.69 8.69a.75.75 0 101.06-1.06l-8.689-8.69a2.25 2.25 0 00-3.182 0l-8.69 8.69a.75.75 0 001.061 1.06l8.69-8.69z" /><path d="M12 5.432l8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 01-.75-.75v-4.5a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75v4.5c0 .414-.336.75-.75.75h-3.073c-1.034 0-1.875-.84-1.875-1.875v-6.198a2.29 2.29 0 00.091-.086L12 5.432z" /></svg>',
            'text' => 'Dashboard',
            'link' => '/dashboard'
        ],
        [
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6"><path fill-rule="evenodd" d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0016.5 9h-1.875a1.875 1.875 0 01-1.875-1.875V5.25A3.75 3.75 0 009 1.5H5.625zM7.5 15a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5A.75.75 0 017.5 15zm.75 2.25a.75.75 0 000 1.5H12a.75.75 0 000-1.5H8.25z" clip-rule="evenodd" /></svg>',
            'text' => 'Work Orders',
            'isDropdown' => true,
            'submenu' => [
                [
                    'text' => 'Work Order List',
                    'link' => '/dashboard/work-orders'
                ],
                [
                    'text' => 'Assign Technician',
                    'link' => '/dashboard/work-orders/assign-technician'
                ]
            ]
        ],
        [
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6"><path d="M12.75 12.75a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM7.5 15.75a.75.75 0 100-1.5.75.75 0 000 1.5zM8.25 17.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM9.75 15.75a.75.75 0 100-1.5.75.75 0 000 1.5zM10.5 17.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM12 15.75a.75.75 0 100-1.5.75.75 0 000 1.5zM12.75 17.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM14.25 15.75a.75.75 0 100-1.5.75.75 0 000 1.5zM15 17.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM16.5 15.75a.75.75 0 100-1.5.75.75 0 000 1.5zM15 12.75a.75.75 0 11-1.5 0 .75.75 0 011.5 0zM16.5 13.5a.75.75 0 100-1.5.75.75 0 000 1.5z" /><path fill-rule="evenodd" d="M6.75 2.25A.75.75 0 017.5 1.5h9a.75.75 0 01.75.75v14.25a2.25 2.25 0 002.25-2.25V6.31a.75.75 0 00-.22-.53l-2.25-2.25a.75.75 0 00-.53-.22H6.75A.75.75 0 016 3.75v-2.5zm9.56 14.47a.75.75 0 00.56-.72V3.75c0-.414-.336-.75-.75-.75H6.75a.75.75 0 00-.75.75v14.25c0 .414.336.75.75.75h10.5a2.25 2.25 0 001.56-.66z" clip-rule="evenodd" /></svg>',
            'text' => 'Schedule',
            'link' => '/dashboard/schedule'
        ],
        [
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6"><path d="M4.5 6.375a4.125 4.125 0 118.25 0 4.125 4.125 0 01-8.25 0zM14.25 8.625a3.375 3.375 0 116.75 0 3.375 3.375 0 01-6.75 0zM1.5 19.125a7.125 7.125 0 0114.25 0v.003l-.001.119a.75.75 0 01-.363.63 13.067 13.067 0 01-6.761 1.873c-2.472 0-4.786-.684-6.76-1.873a.75.75 0 01-.364-.63l-.001-.122zM17.25 19.128l-.001.144a2.25 2.25 0 01-.232.96 10.088 10.088 0 005.06-1.01.75.75 0 00.42-.643 4.875 4.875 0 00-6.957-4.611 8.586 8.586 0 011.71 5.157v.003z" /></svg>',
            'text' => 'Customers',
            'link' => '/dashboard/customers'
        ],
        [
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6"><path fill-rule="evenodd" d="M8.25 6.75a3.75 3.75 0 117.5 0 3.75 3.75 0 01-7.5 0zM15.75 9.75a3 3 0 116 0 3 3 0 01-6 0zM2.25 9.75a3 3 0 116 0 3 3 0 01-6 0zM6.31 15.117A6.745 6.745 0 0112 12a6.745 6.745 0 016.709 7.498.75.75 0 01-.698.597H6.988a.75.75 0 01-.698-.597 6.745 6.745 0 011.713-4.693zM12 13.5a3.75 3.75 0 100-7.5 3.75 3.75 0 000 7.5z" clip-rule="evenodd" /><path d="M5.902 18.75c1.202-.902 2.646-1.5 4.238-1.5h.458a.75.75 0 010 1.5h-.458a4.237 4.237 0 00-3.526 1.903.75.75 0 11-1.262-.806z" /><path d="M13.823 19.322c.005-.065.01-.131.015-.197a5.257 5.257 0 00-4.979-4.474.75.75 0 011.112-.643 6.677 6.677 0 012.06 5.965.75.75 0 001.792.176 8.171 8.171 0 00-.133-2.033z" /></svg>',
            'text' => 'Technicians',
            'link' => '/dashboard/technicians'
        ]
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

            </div>

            <!-- Navigation Menu -->
            <nav class="flex-grow overflow-y-auto py-4 px-4 space-y-2">
                <?php foreach ($menuItems as $item): ?>
                    <?php if (isset($item['isDropdown']) && $item['isDropdown']): ?>
                        <!-- Dropdown Menu Item -->
                        <div class="dropdown">
                            <button class="dropdown-toggle w-full flex items-center justify-between space-x-3 px-4 py-2.5 text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all duration-300 ease-in-out group">
                                <div class="flex items-center space-x-3">
                                    <div class="text-gray-400 group-hover:text-indigo-600 transition-colors duration-300">
                                        <?= $item['icon'] ?>
                                    </div>
                                    <span class="text-sm font-medium"><?= htmlspecialchars($item['text']) ?></span>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 transition-transform duration-300 dropdown-arrow">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                            <div class="dropdown-menu ml-7 pl-3 border-l border-gray-200">
                                <?php foreach ($item['submenu'] as $subItem): ?>
                                    <a href="<?= htmlspecialchars($subItem['link']) ?>" class="flex items-center space-x-3 px-4 py-2 text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all duration-300 ease-in-out">
                                        <span class="text-sm"><?= htmlspecialchars($subItem['text']) ?></span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Regular Menu Item -->
                        <a href="<?= htmlspecialchars($item['link']) ?>" class="group flex items-center space-x-3 px-4 py-2.5 text-gray-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all duration-300 ease-in-out">
                            <div class="text-gray-400 group-hover:text-indigo-600 transition-colors duration-300">
                                <?= $item['icon'] ?>
                            </div>
                            <span class="text-sm font-medium"><?= htmlspecialchars($item['text']) ?></span>
                        </a>
                    <?php endif; ?>
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

    <!-- JavaScript for dropdown functionality -->
    <script>
        const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
        
        dropdownToggles.forEach(toggle => {
            toggle.addEventListener('click', function() {
                const dropdown = this.nextElementSibling;
                const arrow = this.querySelector('.dropdown-arrow');
                
                // Toggle dropdown visibility
                dropdown.classList.toggle('open');
                arrow.classList.toggle('transform');
            });
        });
    </script>