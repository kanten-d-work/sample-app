<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemMst extends Model
{
    use HasFactory;

    protected $table = 'item_mst';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [];

    /**
     * リレーション
     */
    public function items(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
