<?php
namespace App\Http\Controllers;
use App\Models\Employee;
use App\Models\SalaryManagementRow;
use Illuminate\Http\Request;
class LoanWorkerController extends Controller
{
 public function index(Request $request) {
  abort_unless($request->user()->hasPermission('payrolls'),403);
  $history=SalaryManagementRow::where(function($q){$q->where('loan_balance','>',0)->orWhere('loan_deduction','>',0);})->orderByDesc('month')->orderByDesc('id')->get()->groupBy('employee_id');
  $employees=Employee::whereIn('id',$history->keys())->orderBy('department')->orderBy('name')->get();
  return view('loan-workers.index',compact('employees','history'));
 }
 public function show(Request $request, Employee $employee) {
  abort_unless($request->user()->hasPermission('payrolls'),403);
  $records=SalaryManagementRow::with('advances')->where('employee_id',$employee->id)->where(function($q){$q->where('loan_balance','>',0)->orWhere('loan_deduction','>',0);})->orderByDesc('month')->get();
  abort_if($records->isEmpty(),404);
  return view('loan-workers.show',compact('employee','records'));
 }
}
