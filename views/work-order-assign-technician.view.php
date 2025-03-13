<?php require_once 'partials/header.php'; ?>
<?php require_once 'partials/sidebar.php'; ?>

<div class="flex-1 ml-64 p-8">
    <h1 class="text-2xl font-bold mb-6">Assign Technicians</h1>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
            <?php 
            echo $_SESSION['success'];
            unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
            <?php 
            echo $_SESSION['error'];
            unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4">Unassigned Work Orders</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-6 py-3 text-left">Order ID</th>
                        <th class="px-6 py-3 text-left">Customer</th>
                        <th class="px-6 py-3 text-left">City</th>
                        <th class="px-6 py-3 text-left">Schedule</th>
                        <th class="px-6 py-3 text-left">Priority</th>
                        <th class="px-6 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($unassigned_orders as $order):?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4">#<?php echo $order['order_id']; ?></td>
                        <td class="px-6 py-4"><?php echo $order['customer_name']; ?></td>
                        <td class="px-6 py-4"><?php echo $order['customer_city']; ?></td>
                        <td class="px-6 py-4">
                            <?php echo date('M d, Y', strtotime($order['scheduled_date'])); ?>
                            <br>
                            <?php echo date('h:i A', strtotime($order['scheduled_time'])); ?>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-4 py-2 rounded-full text-sm font-medium
                                <?php
                                switch($order['priority']) {
                                    case 'low':
                                        echo 'bg-gray-200 text-gray-800';
                                        break;
                                    case 'medium':
                                        echo 'bg-blue-100 text-blue-800';
                                        break;
                                    case 'high':
                                        echo 'bg-yellow-100 text-yellow-800';
                                        break;
                                    case 'urgent':
                                        echo 'bg-red-100 text-red-800';
                                        break;
                                }
                                ?>">
                                <?php echo ucfirst($order['priority']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <button onclick="showAvailableTechs(<?php echo $order['order_id']; ?>, '<?php echo $order['scheduled_date']; ?>')"
                                    class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                                Assign Technician
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>

<?php require_once 'partials/work_orders/assignTechnicianModal.php'; ?>
<script src="/public/js/assign-technician.js" defer></script>
<?php require_once 'partials/footer.php'; ?>
