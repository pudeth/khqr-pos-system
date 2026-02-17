@extends('layouts.app')

@section('title', 'Edit Customer - Admin')

@section('content')
<div class="min-h-screen bg-gray-100 py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white shadow-lg border-4 border-gray-800 mb-6">
            <div class="px-6 py-4 border-b-2 border-gray-300">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Edit Customer</h1>
                        <p class="text-gray-600 mt-1">Update customer information</p>
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('admin.customers.details', $customer->id) }}" class="bg-blue-600 text-white px-4 py-2 border-2 border-blue-800 hover:bg-blue-700 font-bold transition">
                            <i class="fas fa-eye mr-2"></i>View Details
                        </a>
                        <a href="{{ route('admin.customers') }}" class="bg-gray-600 text-white px-4 py-2 border-2 border-gray-800 hover:bg-gray-700 font-bold transition">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Customers
                        </a>
                    </div>
                </div>
            </div>

            <!-- Customer Summary -->
            <div class="p-6 bg-gray-50 border-b-2 border-gray-300">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="text-center">
                        <p class="text-sm font-semibold text-gray-600">Available Points</p>
                        <p class="text-2xl font-bold text-green-600">{{ number_format($customer->available_points) }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm font-semibold text-gray-600">Total Spent</p>
                        <p class="text-2xl font-bold text-purple-600">${{ number_format($customer->total_spent, 2) }}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm font-semibold text-gray-600">Member Since</p>
                        <p class="text-lg font-bold text-gray-900">{{ $customer->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="bg-white shadow-lg border-4 border-gray-800">
            <div class="px-6 py-4 border-b-2 border-gray-300">
                <h2 class="text-xl font-bold text-gray-900">
                    <i class="fas fa-edit mr-2"></i>Customer Information
                </h2>
            </div>

            <form method="POST" action="{{ route('admin.customers.update', $customer->id) }}" class="p-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Phone Number -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Phone Number <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" required
                               class="w-full border-2 border-gray-400 px-3 py-2 focus:border-blue-500 @error('phone') border-red-500 @enderror"
                               placeholder="Enter phone number">
                        @error('phone')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-gray-600 text-sm mt-1">This is the unique identifier for the customer</p>
                    </div>

                    <!-- Customer Name -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Customer Name
                        </label>
                        <input type="text" name="name" value="{{ old('name', $customer->name) }}"
                               class="w-full border-2 border-gray-400 px-3 py-2 focus:border-blue-500 @error('name') border-red-500 @enderror"
                               placeholder="Enter customer name">
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Address -->
                <div class="mt-6">
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        Address (Street/House Number)
                    </label>
                    <textarea name="address" rows="3" 
                              class="w-full border-2 border-gray-400 px-3 py-2 focus:border-blue-500 @error('address') border-red-500 @enderror"
                              placeholder="Enter customer address">{{ old('address', $customer->address) }}</textarea>
                    @error('address')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Warning Notice -->
                <div class="mt-6 p-4 bg-yellow-50 border-2 border-yellow-300">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-triangle text-yellow-600 mr-3 mt-1"></i>
                        <div>
                            <h3 class="font-bold text-yellow-800">Important Notice</h3>
                            <p class="text-yellow-700 text-sm mt-1">
                                Changing the phone number will affect customer identification in the POS system. 
                                Make sure the new phone number is correct and unique.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-6 flex justify-end space-x-4">
                    <a href="{{ route('admin.customers.details', $customer->id) }}" 
                       class="bg-gray-600 text-white px-6 py-2 border-2 border-gray-800 hover:bg-gray-700 font-bold transition">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button type="submit" 
                            class="bg-green-600 text-white px-6 py-2 border-2 border-green-800 hover:bg-green-700 font-bold transition">
                        <i class="fas fa-save mr-2"></i>Update Customer
                    </button>
                </div>
            </form>
        </div>

        <!-- Points Management Quick Access -->
        <div class="mt-6 bg-white shadow-lg border-4 border-gray-800">
            <div class="px-6 py-4 border-b-2 border-gray-300">
                <h2 class="text-xl font-bold text-gray-900">
                    <i class="fas fa-star mr-2"></i>Quick Points Management
                </h2>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-4">Need to adjust this customer's points? Use the points management tools on the customer details page.</p>
                <a href="{{ route('admin.customers.details', $customer->id) }}#points-management" 
                   class="bg-blue-600 text-white px-4 py-2 border-2 border-blue-800 hover:bg-blue-700 font-bold transition inline-block">
                    <i class="fas fa-cogs mr-2"></i>Manage Points
                </a>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<div class="fixed top-4 right-4 bg-green-100 border-2 border-green-400 text-green-800 px-4 py-3 shadow-lg z-50" id="success-message">
    <div class="flex items-center">
        <i class="fas fa-check-circle mr-2"></i>
        <span>{{ session('success') }}</span>
        <button onclick="document.getElementById('success-message').remove()" class="ml-4 text-green-600 hover:text-green-800">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
<script>
    setTimeout(() => {
        const message = document.getElementById('success-message');
        if (message) message.remove();
    }, 5000);
</script>
@endif

@if($errors->any())
<div class="fixed top-4 right-4 bg-red-100 border-2 border-red-400 text-red-800 px-4 py-3 shadow-lg z-50" id="error-message">
    <div class="flex items-start">
        <i class="fas fa-exclamation-circle mr-2 mt-1"></i>
        <div>
            <p class="font-bold">Please fix the following errors:</p>
            <ul class="list-disc list-inside text-sm mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <button onclick="document.getElementById('error-message').remove()" class="ml-4 text-red-600 hover:text-red-800">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
<script>
    setTimeout(() => {
        const message = document.getElementById('error-message');
        if (message) message.remove();
    }, 8000);
</script>
@endif
@endsection