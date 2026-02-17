@extends('layouts.admin')

@section('title', 'Pospay Sales')
@section('header', 'SALES HISTORY')

@section('content')
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Invoice</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cashier</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Items</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($sales as $sale)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 font-medium">{{ $sale->invoice_number }}</td>
                <td class="px-6 py-4">{{ $sale->created_at->format('M d, Y H:i') }}</td>
                <td class="px-6 py-4">{{ $sale->user->name ?? 'N/A' }}</td>
                <td class="px-6 py-4">{{ $sale->customer_name ?? '-' }}</td>
                <td class="px-6 py-4">{{ $sale->items->count() }}</td>
                <td class="px-6 py-4 font-semibold">${{ number_format($sale->total, 2) }}</td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                        {{ $sale->payment_method }}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <a href="{{ route('admin.sale.details', $sale->id) }}" class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-eye"></i> View
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-6 py-4 text-center text-gray-500">No sales found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $sales->links() }}
</div>
@endsection
