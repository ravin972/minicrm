<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'status',
    ];

    /**
     * Get the leads for the customer.
     */
    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

    /**
     * Get the tasks through leads for the customer.
     */
    public function tasks()
    {
        return $this->hasManyThrough(Task::class, Lead::class);
    }

    /**
     * Scope a query to only include active customers.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include prospect customers.
     */
    public function scopeProspect($query)
    {
        return $query->where('status', 'prospect');
    }
}
