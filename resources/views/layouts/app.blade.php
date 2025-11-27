<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Warehouse Management System')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/fallback.css') }}">
    @yield('head')
</head>
<body class="bg-gray-100">
    <nav class="bg-gray-800 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-8">
                    <h1 class="text-xl font-bold">Warehouse Management</h1>
                    <div class="hidden md:flex space-x-4">
                        <a href="{{ route('home') }}" class="hover:bg-gray-700 px-3 py-2 rounded">Dashboard</a>
                        <a href="{{ route('admin.products.index') }}" class="hover:bg-gray-700 px-3 py-2 rounded">Products</a>
                        <a href="{{ route('admin.warehouses.index') }}" class="hover:bg-gray-700 px-3 py-2 rounded">Warehouses</a>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <span>{{ Auth::user()->name ?? 'User' }}</span>
                    @if(Auth::check())
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="bg-red-500 hover:bg-red-600 px-3 py-1 rounded">Logout</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto py-6 px-4">
        @if(session()->has('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    @yield('scripts')
</body>
</html>