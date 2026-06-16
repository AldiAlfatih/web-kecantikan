<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\OrderItem;
use App\Models\Payment;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_code',
        'total_price',
        'status',

        'receiver_name',
        'receiver_email',
        'receiver_phone',
        'shipping_address',
        'delivery_method',
        'shipping_note',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'menunggu_verifikasi' => 'Menunggu Verifikasi',
            'diproses'            => 'Diproses',
            'dikirim'             => 'Dikirim',
            'terkirim'            => 'Terkirim',
            'ditolak'             => 'Ditolak',
            default               => ucfirst(str_replace('_', ' ', (string) $this->status)),
        };
    }

    public function statusClass(): string
    {
        return match ($this->status) {
            'menunggu_verifikasi' => 'status-wait',
            'diproses'            => 'status-process',
            'dikirim'             => 'status-ship',
            'terkirim'            => 'status-done',
            'ditolak'             => 'status-reject',
            default               => 'status-default',
        };
    }
}