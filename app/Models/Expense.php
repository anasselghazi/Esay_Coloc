<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = ['colocation_id', 'payer_id', 'category_id', 'title', 'amount', 'expense_date', 'description'];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function colocation()
    {
        return $this->belongsTo(Colocation::class);
    }

    public function payer()
    {
        return $this->belongsTo(User::class, 'payer_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
