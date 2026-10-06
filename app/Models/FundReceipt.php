<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class FundReceipt extends Model {
 protected $fillable=['receipt_date','boss','amount','method','notes','created_by','submission_key','receiver_name','receiver_photo'];
 protected $casts=['receipt_date'=>'date','amount'=>'decimal:2'];
}
