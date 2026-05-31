<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'auto_confirmed'    => 'boolean',
            'paid_at'           => 'datetime',
            'cancelled_at'      => 'datetime',
            'host_notified_at'  => 'datetime',
        ];
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function guest()
    {
        return $this->belongsTo(User::class, 'guest_id');
    }

    public function host()
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    public function room()
    {
        return $this->belongsTo(HotelRoom::class, 'room_id');
    }

    public function availabilities()
    {
        return $this->hasMany(PropertyAvailability::class);
    }

    public function review()
    {
        return $this->hasOne(BookingReview::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'confirmed', 'paid', 'checked_in']);
    }

    // Helpers
    public function isActive(): bool
    {
        return in_array($this->status, ['pending', 'confirmed', 'paid', 'checked_in']);
    }

    public function isPaid(): bool
    {
        return in_array($this->status, ['confirmed', 'paid', 'checked_in', 'checked_out']);
    }

    public function getNightCount(): int
    {
        return Carbon::parse($this->check_in)->diffInDays(Carbon::parse($this->check_out));
    }

    public function getTotalFormatted(): string
    {
        return 'KES ' . number_format($this->total_price, 2);
    }

    public function getBookingRef(): string
    {
        return 'BOOK-' . $this->id . '-' . Carbon::parse($this->check_in)->format('Ymd');
    }
}
