<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class absensi extends Model
{
    public $table = "absensi";
    protected $fillable = ['nama', 'npk', 'tanggal', 'status', 'bukti', 'waktuci', 'waktuco'];
}
