<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\CustomerPoint;
use App\Models\StoreSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function dashboard()
    {
        $todaySales = Sale::whereDate('created_at', today())->sum('total');
        $totalSales = Sale::sum('total');
        $totalProducts = Product::count();
        $lowStockProducts = Product::whereColumn('stock', '<=', 'min_stock')->count();
        
        // Customer statistics
        $totalCustomers = Customer::count();
        $totalPointsAvailable = Customer::sum('available_points');
        
        $recentSales = Sale::with(['user', 'items.product'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'todaySales',
            'totalSales',
            'totalProducts',
            'lowStockProducts',
            'totalCustomers',
            'totalPointsAvailable',
            'recentSales'
        ));
    }

    public function sales()
    {
        $sales = Sale::with(['user', 'items'])->latest()->paginate(20);
        return view('admin.sales', compact('sales'));
    }

    public function saleDetails($id)
    {
        $sale = Sale::with(['user', 'items.product', 'payment'])->findOrFail($id);
        return view('admin.sale-details', compact('sale'));
    }

    // Customer Management Methods
    public function customers(Request $request)
    {
        $query = Customer::query();

        // Search functionality
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('phone', 'like', '%' . $request->search . '%')
                  ->orWhere('name', 'like', '%' . $request->search . '%')
                  ->orWhere('address', 'like', '%' . $request->search . '%');
            });
        }

        // Sort by points or spending
        if ($request->sort) {
            switch ($request->sort) {
                case 'points_desc':
                    $query->orderBy('available_points', 'desc');
                    break;
                case 'points_asc':
                    $query->orderBy('available_points', 'asc');
                    break;
                case 'spent_desc':
                    $query->orderBy('total_spent', 'desc');
                    break;
                case 'spent_asc':
                    $query->orderBy('total_spent', 'asc');
                    break;
                default:
                    $query->latest();
            }
        } else {
            $query->latest();
        }

        $customers = $query->paginate(20);
        
        // Statistics
        $totalCustomers = Customer::count();
        $totalPointsIssued = Customer::sum('total_points');
        $totalPointsAvailable = Customer::sum('available_points');
        $totalCustomerSpending = Customer::sum('total_spent');

        return view('admin.customers.index', compact(
            'customers',
            'totalCustomers',
            'totalPointsIssued',
            'totalPointsAvailable',
            'totalCustomerSpending'
        ));
    }

    public function customerDetails($id)
    {
        $customer = Customer::with(['sales.items.product', 'pointsHistory'])->findOrFail($id);
        
        $pointsHistory = $customer->pointsHistory()
            ->with('sale')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.customers.details', compact('customer', 'pointsHistory'));
    }

    public function editCustomer($id)
    {
        $customer = Customer::findOrFail($id);
        return view('admin.customers.edit', compact('customer'));
    }

    public function updateCustomer(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:191',
            'phone' => 'required|string|max:50|unique:customers,phone,' . $id,
            'address' => 'nullable|string|max:255',
        ]);

        $customer = Customer::findOrFail($id);
        $customer->update($validated);

        return redirect()->route('admin.customers.details', $customer->id)
            ->with('success', 'Customer information updated successfully.');
    }

    public function adjustCustomerPoints(Request $request, $id)
    {
        $validated = $request->validate([
            'points' => 'required|integer',
            'type' => 'required|in:add,subtract,set',
            'reason' => 'required|string|max:255',
        ]);

        $customer = Customer::findOrFail($id);
        $currentPoints = $customer->available_points;
        
        switch ($validated['type']) {
            case 'add':
                $newPoints = $currentPoints + $validated['points'];
                $pointsChange = $validated['points'];
                $transactionType = 'earned';
                break;
            case 'subtract':
                $newPoints = max(0, $currentPoints - $validated['points']);
                $pointsChange = -min($currentPoints, $validated['points']);
                $transactionType = 'redeemed';
                break;
            case 'set':
                $newPoints = max(0, $validated['points']);
                $pointsChange = $newPoints - $currentPoints;
                $transactionType = $pointsChange >= 0 ? 'earned' : 'redeemed';
                break;
        }

        // Update customer points
        $customer->update([
            'available_points' => $newPoints,
            'total_points' => $customer->total_points + max(0, $pointsChange)
        ]);

        // Record the transaction
        $customer->pointsHistory()->create([
            'type' => $transactionType,
            'points' => $pointsChange,
            'description' => 'Admin adjustment: ' . $validated['reason'],
        ]);

        return redirect()->route('admin.customers.details', $customer->id)
            ->with('success', "Customer points adjusted successfully. New balance: {$newPoints} points.");
    }

    public function deleteCustomerPointTransaction(Request $request, $customerId, $transactionId)
    {
        $customer = Customer::findOrFail($customerId);
        $transaction = CustomerPoint::where('customer_id', $customerId)
            ->where('id', $transactionId)
            ->firstOrFail();

        // Reverse the transaction
        $pointsToReverse = -$transaction->points;
        $newAvailablePoints = max(0, $customer->available_points + $pointsToReverse);
        
        // Update customer points
        $customer->update([
            'available_points' => $newAvailablePoints,
            'total_points' => max(0, $customer->total_points - max(0, $transaction->points))
        ]);

        // Delete the transaction
        $transaction->delete();

        return redirect()->route('admin.customers.details', $customer->id)
            ->with('success', 'Transaction deleted and points adjusted.');
    }

    // Store Settings Methods
    public function settings()
    {
        $settings = StoreSetting::all()->groupBy('group');
        return view('admin.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'store_name' => 'required|string|max:255',
            'store_tagline' => 'nullable|string|max:255',
            'store_address' => 'nullable|string|max:500',
            'store_phone' => 'nullable|string|max:50',
            'store_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle logo upload
        if ($request->hasFile('store_logo')) {
            // Delete old logo if exists
            $oldLogo = StoreSetting::get('store_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }

            // Store new logo
            $logoPath = $request->file('store_logo')->store('logos', 'public');
            StoreSetting::set('store_logo', $logoPath, 'image', 'branding');
        }

        // Update other settings
        foreach (['store_name', 'store_tagline', 'store_address', 'store_phone'] as $key) {
            if (isset($validated[$key])) {
                $group = in_array($key, ['store_name', 'store_tagline']) ? 'branding' : 'general';
                StoreSetting::set($key, $validated[$key], 'string', $group);
            }
        }

        return redirect()->route('admin.settings')
            ->with('success', 'Store settings updated successfully!');
    }
}
