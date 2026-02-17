@extends('layouts.admin')

@section('title', 'Pospay Dashboard')
@section('header', 'POSPAY DASHBOARD')

@section('content')
<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="neo-stat-card bg-neo-yellow p-6 rounded-xl float-animation">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-black text-gray-700 uppercase tracking-wide">Today's Sales</p>
                <p class="khmer-small text-gray-600">ការលក់ថ្ងៃនេះ</p>
                <p class="text-4xl font-black mt-2">${{ number_format($todaySales, 2) }}</p>
            </div>
            <div class="w-16 h-16 bg-black rounded-xl flex items-center justify-center">
                <i class="fas fa-dollar-sign text-neo-yellow text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="neo-stat-card bg-neo-green p-6 rounded-xl float-animation" style="animation-delay: 0.1s">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-black text-gray-700 uppercase tracking-wide">Total Sales</p>
                <p class="khmer-small text-gray-600">ការលក់សរុប</p>
                <p class="text-4xl font-black mt-2">${{ number_format($totalSales, 2) }}</p>
            </div>
            <div class="w-16 h-16 bg-black rounded-xl flex items-center justify-center">
                <i class="fas fa-chart-line text-neo-green text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="neo-stat-card bg-neo-purple p-6 rounded-xl float-animation" style="animation-delay: 0.2s">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-black text-gray-700 uppercase tracking-wide">Total Products</p>
                <p class="khmer-small text-gray-600">ទំនិញសរុប</p>
                <p class="text-4xl font-black mt-2">{{ $totalProducts }}</p>
            </div>
            <div class="w-16 h-16 bg-black rounded-xl flex items-center justify-center">
                <i class="fas fa-box text-neo-purple text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="neo-stat-card bg-neo-pink p-6 rounded-xl float-animation" style="animation-delay: 0.3s">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-black text-gray-700 uppercase tracking-wide">Low Stock</p>
                <p class="text-4xl font-black mt-2">{{ $lowStockProducts }}</p>
            </div>
            <div class="w-16 h-16 bg-black rounded-xl flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-neo-pink text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Customer Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div class="neo-stat-card bg-neo-blue p-6 rounded-xl float-animation" style="animation-delay: 0.4s">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-black text-gray-700 uppercase tracking-wide">Total Customers</p>
                <p class="khmer-small text-gray-600">អតិថិជនសរុប</p>
                <p class="text-4xl font-black mt-2">{{ number_format($totalCustomers) }}</p>
                <a href="{{ route('admin.customers') }}" class="text-sm font-bold text-blue-600 hover:text-blue-800 mt-2 inline-block mixed-text">
                    <i class="fas fa-arrow-right mr-1"></i>Manage Customers / គ្រប់គ្រងអតិថិជន
                </a>
            </div>
            <div class="w-16 h-16 bg-black rounded-xl flex items-center justify-center">
                <i class="fas fa-users text-neo-blue text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="neo-stat-card bg-neo-orange p-6 rounded-xl float-animation" style="animation-delay: 0.5s">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-black text-gray-700 uppercase tracking-wide">Available Points</p>
                <p class="text-4xl font-black mt-2">{{ number_format($totalPointsAvailable) }}</p>
                <p class="text-sm font-bold text-orange-600 mt-2">
                    <i class="fas fa-dollar-sign mr-1"></i>Worth ${{ number_format($totalPointsAvailable, 2) }}
                </p>
            </div>
            <div class="w-16 h-16 bg-black rounded-xl flex items-center justify-center">
                <i class="fas fa-star text-neo-orange text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Recent Sales Table -->
<div class="neo-card bg-white rounded-xl overflow-hidden">
    <div class="bg-gradient-to-r from-purple-400 to-pink-400 p-6 border-b-4 border-black">
        <h3 class="text-2xl font-black text-white uppercase tracking-wide">Recent Sales</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full neo-table">
            <thead>
                <tr>
                    <th class="px-6 py-4 text-left">Invoice</th>
                    <th class="px-6 py-4 text-left">Date</th>
                    <th class="px-6 py-4 text-left">Items</th>
                    <th class="px-6 py-4 text-left">Total</th>
                    <th class="px-6 py-4 text-left">Payment</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentSales as $sale)
                <tr class="transition-all duration-200">
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.sale.details', $sale->id) }}" class="font-black text-blue-600 hover:underline">
                            {{ $sale->invoice_number }}
                        </a>
                    </td>
                    <td class="px-6 py-4 font-bold">{{ $sale->created_at->format('M d, Y H:i') }}</td>
                    <td class="px-6 py-4">
                        <span class="neo-badge bg-neo-blue px-3 py-1 rounded-lg inline-block">
                            {{ $sale->items->count() }} items
                        </span>
                    </td>
                    <td class="px-6 py-4 font-black text-lg">${{ number_format($sale->total, 2) }}</td>
                    <td class="px-6 py-4">
                        <span class="neo-badge bg-neo-green px-3 py-1 rounded-lg inline-block">
                            {{ $sale->payment_method }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                            <p class="font-bold text-gray-500 text-lg">No sales yet</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
