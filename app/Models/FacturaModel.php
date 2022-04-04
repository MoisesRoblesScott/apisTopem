<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FacturaModel extends Model
{
    protected $table = 'factura';
    protected $primaryKey = 'id';
    protected $fillable = [
        'numero_factura',
        'fecha_hora',
        'emisor',
        'comprador',
        'valor',
        'iva',
        'total_pagar',
    ];
    public $timestamps = false;

    public function detallesFactura()
    {
        return $this->hasMany(DetalleFacturaModel::class, 'id_factura');
    }

    public function guardar(array $datos)
    {
        try {
            return DB::transaction(function() use($datos){
                $data = $this->create($datos['data_factura']); 
                foreach ($datos['data_detalles_factura']['items'] as $key => $value) {
                    $value['id_factura'] = $data->id;
                    DetalleFacturaModel::insert($value);
                }
                return $data;
            },5);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function eliminarFactura(array $id_factura)
    {
        try {
            return DB::transaction(function() use($id_factura){
                DetalleFacturaModel::where('id_factura', $id_factura['id'])->delete();
                $r = $this->where('id', $id_factura['id'])->delete();
                return $r;
            },5);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function actualizarFactura(array $datos)
    {
        try {
            return DB::transaction(function() use($datos){
                $data = $this->where('id', $datos['id_factura'])->update($datos['data_factura']);

                foreach ($datos['data_detalles_factura']['items'] as $key => $value) {
                    DetalleFacturaModel::where("id", $value['id'])->update($value);
                }
                
                return $data;
            },5);
        } catch (\Exception $e) {
            return $e;
        }
    }
}
