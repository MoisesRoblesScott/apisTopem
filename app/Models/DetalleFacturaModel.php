<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleFacturaModel extends Model
{
    protected $table = 'detalle_factura';
    protected $primaryKey = 'id';
    protected $fillable = [
        'descripcion',
        'cantidad',
        'valor_unitario',
        'valor_total',
        'id_factura',
    ];
    public $timestamps = false;
}
