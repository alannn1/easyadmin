<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Transaction extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'transaction_harumo';
    protected $primaryKey = 'id';

    protected $connection = 'pgsql'; // sesuaikan dengan koneksi kamu
    protected $appends = ['btn_destroy', 'btn_edit', 'btn_show'];

    protected $fillable = [
        'transaction_code',
        'product_code',
        'product_name',
        'unit_price',
        'quantity',
        'total_price',
    ];

    public function getBtnDestroyAttribute()
    {
        $html = "<button type='button' class='btn btn-outline-danger btn-sm radius-6' style='margin:1px;' data-bs-toggle='modal' data-bs-target='#modalDelete' onclick='setDelete(" . json_encode($this->id) . ")'>
                    <i class='ti ti-trash'></i>
                </button>";

        return $html;
    }

    public function getBtnEditAttribute()
    {
        $html = "<button type='button' class='btn btn-outline-secondary btn-sm radius-6' style='margin:1px;' data-bs-toggle='offcanvas'  data-bs-target='#drawerEdit' onclick='setEdit(" . json_encode($this->id) . ")'>
                    <i class='ti ti-pencil'></i>
                </button>";

        return $html;
    }

    public function getBtnShowAttribute()
    {
        $html = "<button type='button' class='btn btn-outline-secondary btn-sm radius-6' style='margin:1px;' onclick='setShowPreview(" . json_encode($this->id) . ")'>
                <i class='ti ti-eye'></i>
                </button>";
        return $html;
    }
    public function getUpdatedAtAttribute($value)
    {
        return $value ? date("Y-m-d H:i:s", strtotime($value)) : "-";
    }


    public function getCreatedAtAttribute($value)
    {
        return $value ? date("Y-m-d H:i:s", strtotime($value)) : "-";
    }
}
