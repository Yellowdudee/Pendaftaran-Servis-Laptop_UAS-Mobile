<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'device_name', 'serial_number', 'phone_number', 'complaints', 'status', 'total_cost', 'technician_notes'])]
class LaptopService extends Model
{
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'total_cost' => 'decimal:2',
        ];
    }

    /**
     * Get the user that owns the laptop service.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
