<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FieldForce Platform</title>
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
        <!-- Right Side - Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12">
            <div class="w-full max-w-md">
                <div class="bg-white rounded-2xl custom-shadow p-8 space-y-6">
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                            <?php 
                            echo $_SESSION['error'];
                            unset($_SESSION['error']);
                            ?>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="bg-green-50 text-green-800 rounded-lg p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check-circle text-green-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium"><?php echo $_SESSION['success']; ?></p>
                                </div>
                            </div>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>

                    <?php if (isset($error)): ?>
                        <div class="bg-red-50 text-red-800 rounded-lg p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-circle text-red-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium"><?php echo $error; ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="text-center mb-8">
                        <h2 class="text-3xl font-bold text-gray-900">Sign In</h2>
                        <p class="mt-2 text-gray-600">Access your field service dashboard</p>
                    </div>

                    <form method="POST" class="space-y-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Email Address</label>
                            <div class="input-group flex items-center border rounded-lg p-3 transition-colors duration-200">
                                <i class="fas fa-envelope text-gray-400 w-6"></i>
                                <input type="email" name="email" required 
                                       class="form-input w-full pl-3 text-gray-800 bg-transparent" 
                                       placeholder="email@example.com">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <div class="flex items-center justify-start">
                                <label class="block text-sm font-medium text-gray-700">Password</label>
                            </div>
                            <div class="input-group flex items-center border rounded-lg p-3 transition-colors duration-200">
                                <i class="fas fa-lock text-gray-400 w-6"></i>
                                <input type="password" name="password" required 
                                       class="form-input w-full pl-3 text-gray-800 bg-transparent" 
                                       placeholder="••••••••">
                            </div>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" id="remember" name="remember" 
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="remember" class="ml-2 block text-sm text-gray-700">
                                Remember me
                            </label>
                        </div>

                        <button type="submit" name="login" 
                                class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors duration-200 flex items-center justify-center">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Sign In
                        </button>

                        <p class="text-center text-sm text-gray-600">
                            Don't have an account? 
                            <a href="/register" class="font-medium text-indigo-600 hover:text-indigo-500">
                                Create one now
                            </a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php require_once 'partials/footer.php'; ?>