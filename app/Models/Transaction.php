<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'transactions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'transaction_id',      // ID de la transacción en el sistema externo
        'payment_gateway',     // Nombre del sistema de pago (EasyMoney o SuperWalletz)
        'amount',              // Monto de la transacción
        'currency',            // Moneda de la transacción
        'status',              // Estado de la transacción (pending, success, failed, etc.)
        'request_data',        // Datos enviados al sistema de pago
        'response_data',       // Respuesta recibida del sistema de pago
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'request_data' => 'array',  // Almacena los datos de solicitud como JSON
        'response_data' => 'array', // Almacena la respuesta como JSON
    ];
}
