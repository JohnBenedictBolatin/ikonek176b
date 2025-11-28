<?php
// app/Models/PaymentMethod.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $table = 'payment_methods';
    protected $primaryKey = 'pay_method_id';
    public $timestamps = false;
    protected $fillable = ['pay_method_name'];
}
