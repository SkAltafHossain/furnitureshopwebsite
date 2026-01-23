<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaticPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'last_updated',
        'status',
    ];

    protected $casts = [
        'last_updated' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getStatusBadgeAttribute()
    {
        $badgeClass = $this->status === 'published' ? 'bg-success' : 'bg-warning';
        return '<span class="badge ' . $badgeClass . '">' . ucfirst($this->status) . '</span>';
    }

    public function getFormattedLastUpdatedAttribute()
    {
        return $this->last_updated ? $this->last_updated->format('M d, Y H:i') : 'Never';
    }
}
