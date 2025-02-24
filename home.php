<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Sidebar -->
    <nav class="bg-indigo-800 w-64 py-4 px-2 fixed h-full">
        <div class="flex items-center justify-center mb-8">
            <h1 class="text-white text-2xl font-bold">Field Service Pro</h1>
        </div>
        <div>
            <a href="logout.php" class="flex items-center text-white py-2 px-4 rounded hover:bg-indigo-700">
                <i class="fas fa-sign-out mr-3"></i>
                Logout
            </a>
        </div>
    </nav>
    <div class="flex-1 ml-64 p-8">
        <h1>Customer Page</h1>
    </div>
</body>
</html>