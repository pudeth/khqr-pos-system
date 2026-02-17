@extends('layouts.app')

@section('title', 'Customer Management - Admin')

@section('content')
<div class="min-h-screen bg-gray-100 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white shadow-lg border-4 border-gray-800 mb-6">
            <div class="px-6 py-4 border-b-2 border-gray-300">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Customer Management</h1>
                        <p class="text-gray-600 mt-1">Manage customer accounts and loyalty points</p>
                    </div>
                    <a href="{{ route('admin.dashboard') }}" class="bg-gray-600 text-white px-4 py-2 border-2 border-gray-800 hover:bg-gray-700 font-bold transition">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="p-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-blue-50 border-2 border-blue-300 p-4">
                    <div class="flex items-center">
                        <i class="fas fa-users text-2xl text-blue-600 mr-3"></i>
                        <div>
                            <p class="text-sm font-semibold text-blue-800">Total Customers</p>
                            <p class="text-2xl font-bold text-blue-900">{{ number_format($totalCustomers) }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-green-50 border-2 border-green-300 p-4">
                    <div class="flex items-center">
                        <i class="fas fa-coins text-2xl text-green-600 mr-3"></i>
                        <div>
                            <p class="text-sm font-semibold text-green-800">Points Issued</p>
                            <p class="text-2xl font-bold text-green-900">{{ number_format($totalPointsIssued) }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-yellow-50 border-2 border-yellow-300 p-4">
                    <div class="flex items-center">
                        <i class="fas fa-star text-2xl text-yellow-600 mr-3"></i>
                        <div>
                            <p class="text-sm font-semibold text-yellow-800">Available Points</p>
                            <p class="text-2xl font-bold text-yellow-900">{{ number_format($totalPointsAvailable) }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-purple-50 border-2 border-purple-300 p-4">
                    <div class="flex items-center">
                        <i class="fas fa-dollar-sign text-2xl text-purple-600 mr-3"></i>
                        <div>
                            <p class="text-sm font-semibold text-purple-800">Total Spending</p>
                            <p class="text-2xl font-bold text-purple-900">${{ number_format($totalCustomerSpending, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="bg-white shadow-lg border-4 border-gray-800 mb-6 p-6">
            <form method="GET" action="{{ route('admin.customers') }}" class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-64">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Search Customers</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search by phone, name, or address..."
                           class="w-full border-2 border-gray-400 px-3 py-2 focus:border-blue-500">
                </div>
                
                <div class="min-w-48">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Sort By</label>
                    <select name="sort" class="w-full border-2 border-gray-400 px-3 py-2 focus:border-blue-500">
                        <option value="">Latest First</option>
                        <option value="points_desc" {{ request('sort') === 'points_desc' ? 'selected' : '' }}>Most Points</option>
                        <option value="points_asc" {{ request('sort') === 'points_asc' ? 'selected' : '' }}>Least Points</option>
                        <option value="spent_desc" {{ request('sort') === 'spent_desc' ? 'selected' : '' }}>Highest Spending</option>
                        <option value="spent_asc" {{ request('sort') === 'spent_asc' ? 'selected' : '' }}>Lowest Spending</option>
                    </select>
                </div>
                
                <div class="flex gap-2">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 border-2 border-blue-800 hover:bg-blue-700 font-bold transition">
                        <i class="fas fa-search mr-2"></i>Search
                    </button>
                    <a href="{{ route('admin.customers') }}" class="bg-gray-600 text-white px-4 py-2 border-2 border-gray-800 hover:bg-gray-700 font-bold transition">
                        <i class="fas fa-times mr-2"></i>Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Customers Table -->
        <div class="bg-white shadow-lg border-4 border-gray-800">
            <div class="px-6 py-4 border-b-2 border-gray-300">
                <h2 class="text-xl font-bold text-gray-900">
                    <i class="fas fa-users mr-2"></i>Customer List
                    <span class="text-sm font-normal text-gray-600">({{ $customers->total() }} customers)</span>
                </h2>
            </div>

            @if($customers->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100 border-b-2 border-gray-300">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-bold text-gray-700 border-r border-gray-300">Customer Info</th>
                                <th class="px-6 py-3 text-left text-sm font-bold text-gray-700 border-r border-gray-300">Contact</th>
                                <th class="px-6 py-3 text-left text-sm font-bold text-gray-700 border-r border-gray-300">Points</th>
                                <th class="px-6 py-3 text-left text-sm font-bold text-gray-700 border-r border-gray-300">Spending</th>
                                <th class="px-6 py-3 text-left text-sm font-bold text-gray-700 border-r border-gray-300">Joined</th>
                                <th class="px-6 py-3 text-left text-sm font-bold text-gray-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($customers as $customer)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 border-r border-gray-200">
                                    <div>
                                        <p class="font-bold text-gray-900">{{ $customer->name ?: 'No Name' }}</p>
                                        <p class="text-sm text-gray-600">ID: {{ $customer->id }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 border-r border-gray-200">
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $customer->phone }}</p>
                                        <p class="text-sm text-gray-600">{{ $customer->address ?: 'No Address' }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 border-r border-gray-200">
                                    <div>
                                        <p class="font-bold text-green-600">{{ number_format($customer->available_points) }} available</p>
                                        <p class="text-sm text-gray-600">{{ number_format($customer->total_points) }} total earned</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 border-r border-gray-200">
                                    <p class="font-bold text-purple-600">${{ number_format($customer->total_spent, 2) }}</p>
                                </td>
                                <td class="px-6 py-4 border-r border-gray-200">
                                    <p class="text-sm text-gray-600">{{ $customer->created_at->format('M d, Y') }}</p>
                                    <p class="text-xs text-gray-500">{{ $customer->created_at->diffForHumans() }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.customers.details', $customer->id) }}" 
                                           class="bg-blue-600 text-white px-3 py-1 text-sm border border-blue-800 hover:bg-blue-700 transition">
                                            <i class="fas fa-eye mr-1"></i>View
                                        </a>
                                        <a href="{{ route('admin.customers.edit', $customer->id) }}" 
                                           class="bg-yellow-600 text-white px-3 py-1 text-sm border border-yellow-800 hover:bg-yellow-700 transition">
                                            <i class="fas fa-edit mr-1"></i>Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t-2 border-gray-300">
                    {{ $customers->appends(request()->query())->links() }}
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <i class="fas fa-users text-6xl text-gray-300 mb-4"></i>
                    <p class="text-xl font-semibold text-gray-600">No customers found</p>
                    <p class="text-gray-500 mt-2">Customers will appear here after their first purchase</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection