<?php

require_once 'config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    
    $fields = [
        'name' => trim($_POST['contact_name']),
        'email' => trim($_POST['email']),
        'phone' => trim($_POST['phone']),
        'address' => trim($_POST['address']),
        'city' => trim($_POST['city']),
        'zipcode' => trim($_POST['zipcode']),
        'password' => password_hash(trim($_POST['password']), PASSWORD_DEFAULT)
    ];

    if (in_array('', $fields)) {
        $_SESSION['error'] = "Please fill in all required fields";
        exit();
    }

    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    
    $stmt->bind_param("s", $fields['email']);
    $stmt->execute();

    if ($stmt->get_result()->num_rows > 0) {
        $_SESSION['error'] = "Email already registered!";
    } else {
        $role = 'customer';
        $company_name = $_POST['company_name'] ?? NULL;

        // Insert into users table
        $sql = "INSERT INTO users (name, email, phone, address, city, zipcode, password, role, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        
        $stmt->bind_param("ssssssss",
            $fields['name'],
            $fields['email'],
            $fields['phone'],
            $fields['address'],
            $fields['city'],
            $fields['zipcode'],
            $fields['password'],
            $role
        );

        $user_id = $stmt->insert_id;

        // Insert into customers table
        $stmt = $conn->prepare("INSERT INTO customers (user_id, company_name) VALUES (?, ?)");
        $stmt->bind_param("is", $user_id, $company_name);

        $_SESSION['success'] = "Registration successful! Please login.";
        header("Location: login.php");
        exit();
    }
    header("Location: register.php"); // Reset the form after reloading the page
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join FieldForce - Field Service Platform</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <style>
        .custom-shadow {
            box-shadow: 0 0 60px rgba(0, 0, 0, 0.1);
        }
        .input-group:focus-within {
            border-color: #4F46E5;
        }
        .input-group:focus-within i {
            color: #4F46E5;
        }
        .form-input:focus {
            outline: none;
        }
        .wavy-background {
            background-image: url("data:image/svg+xml,%3Csvg width='100' height='20' viewBox='0 0 100 20' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M21.184 20c.357-.13.72-.264 1.088-.402l1.768-.661C33.64 15.347 39.647 14 50 14c10.271 0 15.362 1.222 24.629 4.928.955.383 1.869.74 2.75 1.072h6.225c-2.51-.73-5.139-1.691-8.233-2.928C65.888 13.278 60.562 12 50 12c-10.626 0-16.855 1.397-26.66 5.063l-1.767.662c-2.475.923-4.66 1.674-6.724 2.275h6.335zm0-20C13.258 2.892 8.077 4 0 4V2c5.744 0 9.951-.574 14.85-2h6.334zM77.38 0C85.239 2.966 90.502 4 100 4V2c-6.842 0-11.386-.542-16.396-2h-6.225zM0 14c8.44 0 13.718-1.21 22.272-4.402l1.768-.661C33.64 5.347 39.647 4 50 4c10.271 0 15.362 1.222 24.629 4.928C84.112 12.722 89.438 14 100 14v-2c-10.271 0-15.362-1.222-24.629-4.928C65.888 3.278 60.562 2 50 2 39.374 2 33.145 3.397 23.34 7.063l-1.767.662C13.223 10.84 8.163 12 0 12v2z' fill='%234F46E5' fill-opacity='0.05' fill-rule='evenodd'/%3E%3C/svg%3E");
        }
    </style>
</head>
<body class="min-h-screen bg-gray-50 wavy-background">
    <div class="min-h-screen flex justify-center">
        <!-- Right Side - Registration Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-2 sm:p-8x">
            <div class="w-full max-w-md space-y-4 bg-white rounded-2xl custom-shadow p-8">
                <!-- Display errors (Hidden by default) -->
                <?php if (!empty($_SESSION['error'])): ?>
                    <div class="bg-red-50 text-red-800 rounded-lg p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium"><?= htmlspecialchars($_SESSION['error'])?></p>
                                <?php unset($_SESSION['error']); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <!-- Account Type Selector -->
                <div class="flex bg-gray-50 rounded-xl p-1">
                    <button type="button" onclick="selectType('resident')" 
                            class="account-type-btn w-1/2 py-3 text-sm font-medium rounded-lg transition-all duration-200 active">
                        <i class="fas fa-user mr-2"></i>Resident
                    </button>
                    <button type="button" onclick="selectType('company')" 
                            class="account-type-btn w-1/2 py-3 text-sm font-medium rounded-lg transition-all duration-200">
                        <i class="fas fa-building mr-2"></i>Company
                    </button>
                </div>

                <form method="POST" action="register.php" class="space-y-4">
                    <input type="hidden" name="account_type" id="account_type" value="resident">

                    <!-- Company Name (Hidden by default) -->
                    <div id="company_field" class="hidden space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Company Name</label>
                        <div class="input-group flex items-center border rounded-lg p-3 transition-colors duration-200">
                            <i class="fas fa-building text-gray-400 w-6"></i>
                            <input type="text" name="company_name" class="form-input w-full pl-3 text-gray-800 bg-transparent" placeholder="Enter company name">
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700" id="name_label">Full Name</label>
                        <div class="input-group flex items-center border rounded-lg p-3 transition-colors duration-200">
                            <i class="fas fa-user text-gray-400 w-6"></i>
                            <input type="text" name="contact_name" required class="form-input w-full pl-3 text-gray-800 bg-transparent" placeholder="Enter your name">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <div class="input-group flex items-center border rounded-lg p-3 transition-colors duration-200">
                                <i class="fas fa-envelope text-gray-400 w-6"></i>
                                <input type="email" name="email" required class="form-input w-full pl-3 text-gray-800 bg-transparent" placeholder="email@example.com">
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Phone</label>
                            <div class="input-group flex items-center border rounded-lg p-3 transition-colors duration-200">
                                <i class="fas fa-phone text-gray-400 w-6"></i>
                                <input type="tel" name="phone" required class="form-input w-full pl-3 text-gray-800 bg-transparent" placeholder="(123) 456-7890">
                            </div>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Address</label>
                        <div class="input-group flex items-center border rounded-lg p-3 transition-colors duration-200">
                            <i class="fas fa-map-marker-alt text-gray-400 w-6"></i>
                            <input type="text" name="address" required class="form-input w-full pl-3 text-gray-800 bg-transparent" placeholder="Enter your address">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">City</label>
                            <div class="input-group flex items-center border rounded-lg p-3 transition-colors duration-200">
                                <i class="fas fa-city text-gray-400 w-6"></i>
                                <input type="text" name="city" required class="form-input w-full pl-3 text-gray-800 bg-transparent" placeholder="Enter your city">
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Zip Code</label>
                            <div class="input-group flex items-center border rounded-lg p-3 transition-colors duration-200">
                                <i class="fas fa-location-pin text-gray-400 w-6"></i>
                                <input type="text" name="zipcode" required class="form-input w-full pl-3 text-gray-800 bg-transparent" placeholder="1234">
                            </div>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-sm font-medium text-gray-700">Password</label>
                        <div class="input-group flex items-center border rounded-lg p-3 transition-colors duration-200">
                            <i class="fas fa-lock text-gray-400 w-6"></i>
                            <input type="password" name="password" required class="form-input w-full pl-3 text-gray-800 bg-transparent">
                        </div>
                    </div>

                    <button type="submit" name="register" class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors duration-200">
                        Create Account
                    </button>

                    <p class="text-center text-sm text-gray-600">
                        Already have an account? 
                        <a href="login.php" class="font-medium text-indigo-600 hover:text-indigo-500">Sign in</a>
                    </p>
                </form>
            </div>
        </div>
    </div>

    <script>
    function selectType(type) {
        const companyField = document.getElementById('company_field');
        const nameLabel = document.getElementById('name_label');
        const accountTypeInput = document.getElementById('account_type');
        const buttons = document.querySelectorAll('.account-type-btn');
        
        // Update buttons
        buttons.forEach(btn => {
            btn.classList.remove('bg-indigo-600', 'text-white', 'active');
            if (btn.textContent.toLowerCase().includes(type)) {
                btn.classList.add('bg-indigo-600', 'text-white', 'active');
            }
        });

        // Update form
        if (type === 'company') {
            companyField.classList.remove('hidden');
            nameLabel.textContent = 'Contact Person';
            accountTypeInput.value = 'company';
        } else {
            companyField.classList.add('hidden');
            nameLabel.textContent = 'Full Name';
            accountTypeInput.value = 'resident';
        }
    }

    // Initialize the form
    document.querySelector('.account-type-btn').classList.add('bg-indigo-600', 'text-white');
    </script>
</body>
</html>