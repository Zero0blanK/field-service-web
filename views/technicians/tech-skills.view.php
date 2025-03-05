<?php require_once VIEWS . 'partials/header.php' ?>
<?php require_once VIEWS . 'partials/technicians/sidebar.php'; ?>


<div class="flex-1 ml-64 p-8">
    <div class="grid md:grid-cols-2 gap-6">
        <!-- Current Skills -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-2xl font-bold mb-4">Your Current Skills</h2>
            <?php if (empty($current_skills)): ?>
                <p class="text-gray-500">You haven't added any skills yet.</p>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach($current_skills as $skill): ?>
                        <div class="bg-blue-50 p-4 rounded-lg flex justify-between items-center">
                            <div>
                                <h3 class="font-semibold text-blue-800"><?php echo $skill['name']; ?></h3>
                                <p class="text-sm text-gray-600"><?php echo $skill['description']; ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="/public/js/technicians/tech-skills.js"></script>
<?php require_once VIEWS . 'partials/footer.php' ?>