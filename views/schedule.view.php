<?php require_once 'partials/header.php'; ?>
<?php require_once 'partials/sidebar.php'; ?>

<!-- Main Content -->
<div class="flex-1 ml-64 p-8">
    <!-- Today's Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Technician Status -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Technician Status</h2>
            <div class="space-y-4">
                <?php foreach($technicians as $tech): ?>
                    <div class="flex items-center justify-between p-4 border rounded-lg">
                        <div>
                            <h3 class="font-medium"><?php echo $tech['name']; ?></h3>
                            <p class="text-sm text-gray-500">
                                <?php echo $tech['active_orders']; ?> active orders
                            </p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-sm
                            <?php
                            switch($tech['status']) {
                                case 'available':
                                    echo 'bg-green-100 text-green-800';
                                    break;
                                case 'busy':
                                    echo 'bg-yellow-100 text-yellow-800';
                                    break;
                                case 'off-duty':
                                    echo 'bg-gray-100 text-gray-800';
                                    break;
                            }
                            ?>">
                            <?php echo ucfirst($tech['status']); ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Today's Schedule -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Today's Schedule</h2>
            <div class="space-y-4">
                <?php foreach($todays_orders as $order): ?>
                    <div class="p-4 border rounded-lg">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 class="font-medium"><?php echo $order['title']; ?></h3>
                                <p class="text-sm text-gray-500">
                                    <?php echo $order['company_name']; ?>
                                </p>
                            </div>
                            <span class="text-sm font-medium">
                                <?php echo formatTime($order['scheduled_time']); ?>
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mb-2">
                            <i class="fas fa-map-marker-alt mr-1"></i>
                            <?php echo $order['address']; ?>
                        </p>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">
                                Assigned to: <?php echo $order['tech_name'] ?? 'Unassigned'; ?>
                            </span>
                            <span class="px-2 py-1 rounded-full text-xs
                                <?php
                                switch($order['status']) {
                                    case 'pending':
                                        echo 'bg-gray-100 text-gray-800';
                                        break;
                                    case 'assigned':
                                        echo 'bg-blue-100 text-blue-800';
                                        break;
                                    case 'in_progress':
                                        echo 'bg-yellow-100 text-yellow-800';
                                        break;
                                    case 'completed':
                                        echo 'bg-green-100 text-green-800';
                                        break;
                                    case 'cancelled':
                                        echo 'bg-red-100 text-red-800';
                                        break;
                                }
                                ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $order['status'])); ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Weekly Calendar -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4">Weekly Schedule</h2>
        <div class="grid grid-cols-7 gap-4">
            <?php
            $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            $weekly_orders = [];
            foreach($weekly_schedule as $order) {
                $weekly_orders[$order['day_of_week']][] = $order;
            }
            
            foreach($days as $index => $day): 
                $day_number = $index + 1;
                $is_today = date('N') == $day_number;
            ?>
                <div class="border rounded-lg <?php echo $is_today ? 'bg-indigo-50 border-indigo-200' : ''; ?>">
                    <div class="p-2 border-b text-center font-medium">
                        <?php echo $day; ?>
                    </div>
                    <div class="p-2 space-y-2">
                        <?php
                        if (isset($weekly_orders[$day_number])) {
                            foreach($weekly_orders[$day_number] as $order) {
                                $tech_name = !empty($order['tech_name']) ? $order['tech_name'] : '<span class="text-red-500">Unassigned</span>';
                                echo '<div class="text-sm p-2 border rounded bg-white">';
                                echo '<div class="font-medium">' . formatTime($order['scheduled_time']) . '</div>';
                                echo '<div>' . $order['title'] . '</div>';
                                echo '<div class="text-gray-500">' . $tech_name . '</div>';
                                echo '</div>';
                            }
                        }
                        ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once 'partials/footer.php'; ?>
