@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h3 class="fw-bold mb-0">Profit & Loss Report</h3>
        <small class="text-muted">
            Business Income & Expense Summary
        </small>
    </div>

</div>

<div class="card border-0 shadow-sm">

    <div class="card-body">

        <table class="table table-bordered">

            <tr>
                <th width="70%">
                    Total Sales Revenue
                </th>

                <td>
                    Rs {{ number_format($totalSales,2) }}
                </td>
            </tr>

            <tr>
                <th>
                    Total Expenses
                </th>

                <td class="text-danger">
                    Rs {{ number_format($totalExpenses,2) }}
                </td>
            </tr>

            <tr>
                <th>
                    Total Payroll Cost
                </th>

                <td class="text-danger">
                    Rs {{ number_format($totalPayroll,2) }}
                </td>
            </tr>

            <tr>
                <th>
                    Total Cost
                </th>

                <td>
                    Rs {{ number_format($totalCost,2) }}
                </td>
            </tr>

            <tr class="table-success">
                <th>
                    Net Profit
                </th>

                <td>
                    <strong>
                        Rs {{ number_format($netProfit,2) }}
                    </strong>
                </td>
            </tr>

        </table>

    </div>

</div>

@endsection
