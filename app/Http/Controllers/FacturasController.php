<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{User, FacturaModel};
use Illuminate\Support\Facades\DB;

class FacturasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /* Metodo que lista las facturas registradas en DB */
    public function listaFacturas()
    {
        try {
           return FacturaModel::with('detallesFactura')->get()->toArray();
           return response()->json(['message' => '¡Usuario registrado exitosamente!']);
        } catch (\Exception $th) {
            return $th;
        }
    }

    /* Metodo que me permite registrar facturas con sus respectivos items*/
    public function registrar(Request $request)
    {
        try {
            return DB::transaction(function() use($request){
                $facturaModel = new FacturaModel();
                $resp = $facturaModel->guardar($request->all());
                return $resp;
            },5);
        } catch (\Exception $e) {
            return $e;
        }
    }

    /* Eliminar una factura y sus respectivos items */
    public function eliminar(Request $request)
    {
        try {
            return DB::transaction(function() use($request){
                $facturaModel = new FacturaModel();
                $resp = $facturaModel->eliminarFactura($request->all());
                return ($resp);
            },5);
        } catch (\Exception $e) {
            return $e;
        }
    }
    
    /* Actualizar datos de factura e items */
    public function actualizar(Request $request)
    {
        try {
            return DB::transaction(function() use($request){
                $facturaModel = new FacturaModel();
                $resp = $facturaModel->actualizarFactura($request->all());
                return $resp; 
            },5);
        } catch (\Exception $e) {
            return $e;
        }
    }
}
