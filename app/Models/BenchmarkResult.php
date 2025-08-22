<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BenchmarkResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_server_id','target_ip','protocol','port',
        'packet_count','packet_size','interval_us','duration_sec',
        'packets_sent','packets_received','pps','bitrate_bps',
        'raw_output','status','remote_pid','started_at','stopped_at','iface_used',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'stopped_at' => 'datetime',
    ];

    public function sourceServer()
    {
        return $this->belongsTo(SourceServer::class);
    }
}
