@extends('layouts.app')

@section('title', 'Customer Details - Admin')

@section('content')
<div class="min-h-screen bg-gray-100 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white shadow-lg border-4 border-gray-800 mb-6">
            <div class="px-6 py-4 border-b-2 border-gray-300">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Customer Details</h1>
                        <p class="text-gray-600 mt-1">{{ $customer->name ?: 'Customer ID: ' . $customer->id }}</p>
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('admin.customers.edit', $customer->id) }}" class="bg-yellow-600 text-white px-4 py-2 border-2 border-yellow-800 hover:bg-yellow-700 font-bold transition">
                            <i class="fas fa-edit mr-2"></i>Edit Customer
                        </a>
                        <a href="{{ route('admin.customers') }}" class="bg-gray-600 text-white px-4 py-2 border-2 border-gray-800 hover:bg-gray-700 font-bold transition">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Customers
                        </a>
                    </div>
                </div>
            </div>

            <!-- Customer Info Cards -->
            <div class="p-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-blue-50 border-2 border-blue-300 p-4">
                    <div class="flex items-center">
                        <i class="fas fa-user text-2xl text-blue-600 mr-3"></i>
                        <div>
                            <p class="text-sm font-semibold text-blue-800">Customer Name</p>
                            <p class="text-lg font-bold text-blue-900">{{ $customer->name ?: 'No Name Set' }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-green-50 border-2 border-green-300 p-4">
                    <div class="flex items-center">
                        <i class="fas fa-star text-2xl text-green-600 mr-3"></i>
                        <div>
                            <p class="text-sm font-semibold text-green-800">Available Points</p>
                            <p class="text-2xl font-bold text-green-900">{{ number_format($customer->available_points) }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-purple-50 border-2 border-purple-300 p-4">
                    <div class="flex items-center">
                        <i class="fas fa-dollar-sign text-2xl text-purple-600 mr-3"></i>
                        <div>
                            <p class="text-sm font-semibold text-purple-800">Total Spent</p>
                            <p class="text-2xl font-bold text-purple-900">${{ number_format($customer->total_spent, 2) }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-yellow-50 border-2 border-yellow-300 p-4">
                    <div class="flex items-center">
                        <i class="fas fa-coins text-2xl text-yellow-600 mr-3"></i>
                        <div>
                            <p class="text-sm font-semibold text-yellow-800">Total Points Earned</p>
                            <p class="text-2xl font-bold text-yellow-900">{{ number_format($customer->total_points) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Customer Information -->
            <div class="bg-white shadow-lg border-4 border-gray-800">
                <div class="px-6 py-4 border-b-2 border-gray-300">
                    <h2 class="text-xl font-bold text-gray-900">
                        <i class="fas fa-info-circle mr-2"></i>Customer Information
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700">Phone Number</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $customer->phone }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700">Name</label>
                        <p class="text-lg text-gray-900">{{ $customer->name ?: 'Not provided' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700">Address</label>
                        <p class="text-lg text-gray-900">{{ $customer->address ?: 'Not provided' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700">Member Since</label>
                        <p class="text-lg text-gray-900">{{ $customer->created_at->format('F d, Y') }}</p>
                        <p class="text-sm text-gray-600">{{ $customer->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>

            <!-- Points Management -->
            <div class="bg-white shadow-lg border-4 border-gray-800">
                <div class="px-6 py-4 border-b-2 border-gray-300">
                    <h2 class="text-xl font-bold text-gray-900">
                        <i class="fas fa-cogs mr-2"></i>Points Management
                    </h2>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.customers.adjust-points', $customer->id) }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Adjustment Type</label>
                            <select name="type" required class="w-full border-2 border-gray-400 px-3 py-2 focus:border-blue-500">
                                <option value="">Select Action</option>
                                <option value="add">Add Points</option>
                                <option value="subtract">Subtract Points</option>
                                <option value="set">Set Points To</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Points Amount</label>
                            <input type="number" name="points" min="0" required 
                                   class="w-full border-2 border-gray-400 px-3 py-2 focus:border-blue-500"
                                   placeholder="Enter points amount">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Reason</label>
                            <textarea name="reason" required rows="3" 
                                      class="w-full border-2 border-gray-400 px-3 py-2 focus:border-blue-500"
                                      placeholder="Explain the reason for this adjustment..."></textarea>
                        </div>
                        
                        <button type="submit" class="w-full bg-blue-600 text-white py-2 border-2 border-blue-800 hover:bg-blue-700 font-bold transition">
                            <i class="fas fa-save mr-2"></i>Apply Adjustment
                        </button>
                    </form>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="bg-white shadow-lg border-4 border-gray-800">
                <div class="px-6 py-4 border-b-2 border-gray-300">
                    <h2 class="text-xl font-bold text-gray-900">
                        <i class="fas fa-chart-bar mr-2"></i>Quick Stats
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-semibold text-gray-700">Total Purchases:</span>
                        <span class="font-bold text-gray-900">{{ $customer->sales->count() }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-semibold text-gray-700">Average Order:</span>
                        <span class="font-bold text-gray-900">
                            ${{ $customer->sales->count() > 0 ? number_format($customer->total_spent / $customer->sales->count(), 2) : '0.00' }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-semibold text-gray-700">Points Used:</span>
                        <span class="font-bold text-gray-900">{{ number_format($customer->total_points - $customer->available_points) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-semibold text-gray-700">Points Value:</span>
                        <span class="font-bold text-green-600">${{ number_format($customer->available_points, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Points History -->
        <div class="mt-6 bg-white shadow-lg border-4 border-gray-800">
            <div class="px-6 py-4 border-b-2 border-gray-300">
                <h2 class="text-xl font-bold text-gray-900">
                    <i class="fas fa-history mr-2"></i>Points Transaction History
                </h2>
            </div>

            @if($pointsHistory->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100 border-b-2 border-gray-300">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-bold text-gray-700 border-r border-gray-300">Date</th>
                                <th class="px-6 py-3 text-left text-sm font-bold text-gray-700 border-r border-gray-300">Type</th>
                                <th class="px-6 py-3 text-left text-sm font-bold text-gray-700 border-r border-gray-300">Points</th>
                                <th class="px-6 py-3 text-left text-sm font-bold text-gray-700 border-r border-gray-300">Description</th>
                                <th class="px-6 py-3 text-left text-sm font-bold text-gray-700 border-r border-gray-300">Sale</th>
                                <th class="px-6 py-3 text-left text-sm font-bold text-gray-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($pointsHistory as $transaction)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 border-r border-gray-200">
                                    <p class="text-sm font-semibold text-gray-900">{{ $transaction->created_at->format('M d, Y') }}</p>
                                    <p class="text-xs text-gray-600">{{ $transaction->created_at->format('g:i A') }}</p>
                                </td>
                                <td class="px-6 py-4 border-r border-gray-200">
                                    @if($transaction->type === 'earned')
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-bold bg-green-100 text-green-800 border border-green-300">
                                            <i class="fas fa-plus mr-1"></i>EARNED
                                        </span>
                                    @elseif($transaction->type === 'redeemed')
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-bold bg-red-100 text-red-800 border border-red-300">
                                            <i class="fas fa-minus mr-1"></i>REDEEMED
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-bold bg-blue-100 text-blue-800 border border-blue-300">
                                            <i class="fas fa-undo mr-1"></i>REFUNDED
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 border-r border-gray-200">
                                    <span class="font-bold {{ $transaction->points > 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $transaction->points > 0 ? '+' : '' }}{{ number_format($transaction->points) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 border-r border-gray-200">
                                    <p class="text-sm text-gray-900">{{ $transaction->description }}</p>
                                    @if($transaction->amount_spent)
                                        <p class="text-xs text-gray-600">Amount spent: ${{ number_format($transaction->amount_spent, 2) }}</p>
                                    @endif
                                    @if($transaction->amount_redeemed)
                                        <p class="text-xs text-gray-600">Amount redeemed: ${{ number_format($transaction->amount_redeemed, 2) }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 border-r border-gray-200">
                                    @if($transaction->sale)
                                        <a href="{{ route('admin.sale.details', $transaction->sale->id) }}" 
                                           class="text-blue-600 hover:text-blue-800 font-semibold">
                                            {{ $transaction->sale->invoice_number }}
                                        </a>
                                    @else
                                        <span class="text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if(str_contains($transaction->description, 'Admin adjustment'))
                                        <form method="POST" action="{{ route('admin.customers.delete-transaction', [$customer->id, $transaction->id]) }}" 
                                              onsubmit="return confirm('Are you sure you want to delete this transaction? Points will be adjusted automatically.')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-600 text-white px-2 py-1 text-xs border border-red-800 hover:bg-red-700 transition">
                                                <i class="fas fa-trash mr-1"></i>Delete
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-gray-400 text-xs">System Generated</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t-2 border-gray-300">
                    {{ $pointsHistory->links() }}
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <i class="fas fa-history text-6xl text-gray-300 mb-4"></i>
                    <p class="text-xl font-semibold text-gray-600">No transaction history</p>
                    <p class="text-gray-500 mt-2">Points transactions will appear here</p>
                </div>
            @endif
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
@endsection