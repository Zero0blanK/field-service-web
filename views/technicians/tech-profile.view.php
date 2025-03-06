<?php require_once VIEWS . 'partials/header.php' ?>
<?php require_once VIEWS . 'partials/technicians/sidebar.php'; ?>

<div class="flex-1 ml-64 p-8">
    <div class="grid md:grid-cols-3 gap-6">
        <!-- Profile Section -->
        <div class="md:col-span-1 bg-white shadow-md rounded-lg p-6">
            <div class="text-center">
            <img 
                src="https://www.w3schools.com/howto/img_avatar.png" 
                alt="Profile Picture" 
                class="w-32 h-32 rounded-full mx-auto mb-4 object-cover"
            >

                <h2 class="text-2xl font-bold"><?php echo htmlspecialchars($profile['name']); ?></h2>
                <p class="text-gray-600">Technician</p>
            </div>

            <div class="mt-6 space-y-4">
                <div>
                    <label class="block text-black-800 text-md font-bold mt-8 mb-2">Status</label>
                    <span class="px-4 py-2 font-bold
                        <?php 
                        echo $profile['technician_status'] === 'available' 
                            ? 'bg-green-100 text-green-800' 
                            : 'bg-yellow-100 text-yellow-800'; 
                        ?> 
                        px-2 py-1 rounded text-sm">
                        <?php echo ucfirst($profile['technician_status']); ?>
                    </span>
                </div>
                <div class="space-y-4">
                    <label class="block text-black-800 text-md font-bold mb-2">Contact Information</label>
                    <div class="bg-blue-50 p-4 rounded-lg flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-700"><strong>Email:</strong> <?php echo htmlspecialchars($profile['email']); ?></p>
                            <p class="text-sm text-gray-700"><strong>Phone:</strong> <?php echo htmlspecialchars($profile['phone'] ?? 'Not provided'); ?></p>
                        </div>
                    </div>
                </div>
                <div class="space-y-4">
                    <label class="block text-black-800 text-md font-bold mb-2">Address</label>
                    <div class="bg-blue-50 p-4 rounded-lg flex justify-between items-center">
                        <div>
                            <h3 class="font-semibold text-gray-700"><?php echo htmlspecialchars($profile['address'] ?? 'Not provided'); ?><br></h3>
                            <p class="text-sm text-gray-600">
                                <?php echo htmlspecialchars($profile['city'] ?? ''); ?>, 
                                <?php echo htmlspecialchars($profile['zipcode'] ?? ''); ?></p>
                        </div>
                    </div>
                </div>
                <div class="space-y-4">
                    <label class="block text-black-800 text-md font-bold mb-2">Skills</label>
                    <?php foreach($current_skills as $skill): ?>
                        <div class="bg-blue-50 p-4 rounded-lg flex justify-between items-center">
                            <div>
                                <h3 class="font-semibold text-gray-800"><?php echo $skill['name']; ?></h3>
                                <p class="text-sm text-gray-600"><?php echo $skill['description']; ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Profile Update & Work Stats -->
        <div class="md:col-span-2 space-y-6">
            <!-- Work Statistics -->
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-2xl font-bold mb-4">Work Performance</h2>
                <div class="grid md:grid-cols-3 gap-4">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold text-blue-800">Total Orders</h3>
                        <p class="text-3xl font-bold">
                            <?php echo $work_stats['total_orders'] ?? 0; ?>
                        </p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold text-green-800">Completed Orders</h3>
                        <p class="text-3xl font-bold">
                            <?php echo $work_stats['completed_orders'] ?? 0; ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Profile Update Form -->
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-2xl font-bold mb-4">Update Profile</h2>
                <form id="profileUpdateForm" method="POST">
                    <input type="hidden" name="password" value="<?php echo htmlspecialchars($profile['password']); ?>">
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Full Name</label>
                            <input type="text" name="name" 
                                    value="<?php echo htmlspecialchars($profile['name']); ?>"
                                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                            <input type="email" name="email" 
                                    value="<?php echo htmlspecialchars($profile['email']); ?>"
                                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Phone</label>
                            <input type="tel" name="phone" 
                                    value="<?php echo htmlspecialchars($profile['phone'] ?? ''); ?>"
                                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">City</label>
                            <input type="text" name="city" 
                                    value="<?php echo htmlspecialchars($profile['city'] ?? ''); ?>"
                                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Address</label>
                            <input type="text" name="address" 
                                    value="<?php echo htmlspecialchars($profile['address'] ?? ''); ?>"
                                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Zipcode</label>
                            <input type="text" name="zipcode" 
                                    value="<?php echo htmlspecialchars($profile['zipcode'] ?? ''); ?>"
                                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="mt-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Change Password</label>
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-500 text-xs mb-1">Current Password</label>
                                <input type="password" name="current_password" 
                                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-gray-500 text-xs mb-1">New Password</label>
                                <input type="password" name="new_password" 
                                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" name="update_profile"
                                class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600">
                            Update Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<?php require_once VIEWS . 'partials/footer.php' ?>
<script src="/public/js/technicians/tech-profile.js"></script>
</body>
</html>