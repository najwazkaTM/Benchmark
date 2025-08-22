<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SourceServer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','ip','ssh_user','ssh_password','ssh_port',
        'iface','hping_path',
        // per-protocol options:
        'hping_tcp_options','hping_udp_options','hping_icmp_options',
        // status
        'ssh_status','last_checked_at',
    ];

    protected $hidden = ['ssh_password'];

    protected $casts = [
        'last_checked_at' => 'datetime',
    ];

    public function getSshStatusColorAttribute(): string
    {
        return match ($this->ssh_status) {
            'connected' => 'text-green-700 bg-green-50',
            'failed'    => 'text-red-700 bg-red-50',
            default     => 'text-gray-700 bg-gray-50',
        };
    }
}
