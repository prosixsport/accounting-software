<?php

namespace App\Http\Controllers;

use App\Models\ContractorBillItem;
use Illuminate\Http\Request;

class ContractorBillItemController extends Controller
{
    public function destroy(ContractorBillItem $contractorBillItem)
    {
        $contractorBillItem->delete();

        return back()->with('success', 'Bill item deleted successfully.');
    }
}
