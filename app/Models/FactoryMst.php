<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FactoryMst extends Model
{
    use HasFactory;

    protected $table = 'factory_mst';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [];

    /**
     * リレーション　一対一
     */
    public function factories(): BelongsTo
    {
        return $this->belongsTo(Factory::class);
    }
}
