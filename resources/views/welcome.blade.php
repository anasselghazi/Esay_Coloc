<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ColocApp</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800">

    <!-- Navbar -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-indigo-600">ColocApp</h1>

            <div class="space-x-4">
                <a href="{{ route('login') }}" 
                   class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-indigo-600">
                   Login
                </a>

                <a href="{{ route('register') }}" 
                   class="px-4 py-2 text-sm font-medium bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                   Get Started
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="max-w-7xl mx-auto px-6 py-20 grid md:grid-cols-2 gap-12 items-center">

        <!-- Text -->
        <div>
            <h2 class="text-4xl font-bold mb-6 leading-tight">
                Manage Your Shared Apartment <br>
                <span class="text-indigo-600">The Smart Way</span>
            </h2>

            <p class="text-gray-600 mb-8">
                Track expenses, manage roommates, split rent,
                and stay organized in one simple platform.
            </p>

            <a href="{{ route('register') }}"
               class="px-6 py-3 bg-indigo-600 text-white rounded-xl shadow-lg hover:bg-indigo-700 transition">
                Create Your Colocation
            </a>
        </div>

        <!-- Image -->
        <div>
            <img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267"
                 class="rounded-2xl shadow-xl"
                 alt="Colocation Apartment">
        </div>

    </section>

    <!-- Features -->
    <section class="bg-white py-16">
        <div class="max-w-6xl mx-auto px-6 text-center">

            <h3 class="text-3xl font-bold mb-12">Everything You Need</h3>

            <div class="grid md:grid-cols-3 gap-8">

                <div class="p-6 rounded-xl shadow hover:shadow-lg transition">
                    <h4 class="text-xl font-semibold mb-3 text-indigo-600">Roommates</h4>
                    <p class="text-gray-600">
                        Invite members and manage your shared home easily.
                    </p>
                </div>

                <div class="p-6 rounded-xl shadow hover:shadow-lg transition">
                    <h4 class="text-xl font-semibold mb-3 text-indigo-600">Expense Tracking</h4>
                    <p class="text-gray-600">
                        Split bills, rent, and utilities automatically.
                    </p>
                </div>

                <div class="p-6 rounded-xl shadow hover:shadow-lg transition">
                    <h4 class="text-xl font-semibold mb-3 text-indigo-600">Payments</h4>
                    <p class="text-gray-600">
                        Keep track of who paid and who still owes.
                    </p>
                </div>

            </div>

        </div>
    </section>

</body>
</html>