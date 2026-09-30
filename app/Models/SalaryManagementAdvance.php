<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SalaryManagementAdvance extends Model {
 protected $guarded=['id'];
 protected $casts=['advance_date'=>'date'];
}
