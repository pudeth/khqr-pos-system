<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone',
        'name',
        'address',
        'total_spent',
        'total_points',
        'available_points',
    ];

    protected $casts = [
        'total_spent' => 'decimal:2',
        'total_points' => 'integer',
        'available_points' => 'integer',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function pointsHistory()
    {
        return $this->hasMany(CustomerPoint::class);
    }

    /**
     * Calculate points earned from amount spent
     * 1 point for every $100 spent
     */
    public static function calculatePointsFromAmount($amount)
    {
        return floor($amount / 100);
    }

    /**
     * Add points to customer account
     */
    public function addPoints($points, $saleId = null, $amountSpent = null)
    {
        $this->increment('total_points', $points);
        $this->increment('available_points', $points);
        
        if ($amountSpent) {
            $this->increment('total_spent', $amountSpent);
        }

        // Record the transaction
        $this->pointsHistory()->create([
            'sale_id' => $saleId,
            'type' => 'earned',
            'points' => $points,
            'amount_spent' => $amountSpent,
            'description' => "Earned {$points} points from purchase",
        ]);
    }

    /**
     * Use points for payment (1 point = $1)
     */
    public function usePoints($points, $saleId = null)
    {
        if ($this->available_points < $points) {
            throw new \Exception('Insufficient points available');
        }

        $this->decrement('available_points', $points);
        $amountRedeemed = $points; // 1 point = $1

        // Record the transaction
        $this->pointsHistory()->create([
            'sale_id' => $saleId,
            'type' => 'redeemed',
            'points' => -$points,
            'amount_redeemed' => $amountRedeemed,
            'description' => "Redeemed {$points} points for \${$amountRedeemed}",
        ]);

        return $amountRedeemed;
    }

    /**
     * Refund points (when sale is cancelled)
     */
    public function refundPoints($points, $saleId = null)
    {
        $this->increment('available_points', $points);

        $this->pointsHistory()->create([
            'sale_id' => $saleId,
            'type' => 'refunded',
            'points' => $points,
            'description' => "Refunded {$points} points from cancelled sale",
        ]);
    }

    /**
     * Get or create customer by phone
     */
    public static function findOrCreateByPhone($phone, $name = null, $address = null)
    {
        $customer = self::where('phone', $phone)->first();
        
        if (!$customer) {
            $customer = self::create([
                'phone' => $phone,
                'name' => $name,
                'address' => $address,
            ]);
        } else {
            // Update name and address if provided
            $updates = [];
            if ($name && !$customer->name) {
                $updates['name'] = $name;
            }
            if ($address && !$customer->address) {
                $updates['address'] = $address;
            }
            if (!empty($updates)) {
                $customer->update($updates);
            }
        }

        return $customer;
    }
}