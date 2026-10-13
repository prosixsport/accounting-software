<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ExpenseAdvance extends Model {
 protected $fillable=['recipient_name','advance_date','amount','purpose','submission_key','created_by'];
 protected $casts=['advance_date'=>'date','amount'=>'decimal:2'];
}
