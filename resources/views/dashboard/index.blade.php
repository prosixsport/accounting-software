@extends('layouts.app')

@section('content')

<style>
.db { color:#111; }
.db-top{display:flex;justify-content:space-between;align-items:center;margin-bottom:26px}
.db-top h2{font-size:30px;font-weight:800;margin:0}
.db-date{background:#fff;border:1px solid #e5e7eb;border-radius:50px;padding:10px 18px;color:#555;box-shadow:0 2px 10px rgba(0,0,0,.04)}
.db-cards{display:grid;grid-template-columns:repeat(4,1fr);gap:18px;margin-bottom:22px}
.db-card{background:#fff;border-radius:20px;border:1px solid #eee;padding:20px;box-shadow:0 3px 14px rgba(0,0,0,.05);transition:.2s}
.db-card:hover{transform:translateY(-3px);box-shadow:0 12px 32px rgba(0,0,0,.09)}
.db-card.dark{background:#111;color:#fff}
.db-ico{width:44px;height:44px;border-radius:13px;background:#f2f2f2;display:flex;align-items:center;justify-content:center;font-size:21px;margin-bottom:16px}
.db-card.dark .db-ico{background:#222}
.db-lbl{font-size:12px;color:#999;text-transform:uppercase;letter-spacing:1px;font-weight:700}
.db-val{font-size:30px;font-weight:800;margin-top:6px}
.db-sub{font-size:13px;color:#999;margin-top:4px}
.db-grid{display:grid;grid-template-columns:1fr;gap:20px}
.db-panel{background:#fff;border:1px solid #eee;border-radius:20px;box-shadow:0 3px 14px rgba(0,0,0,.05);overflow:hidden;margin-bottom:20px}
.db-ph{display:flex;justify-content:space-between;align-items:center;padding:18px 22px;border-bottom:1px solid #f4f4f4}
.db-ph h5{font-weight:800;margin:0}
.summary-row{display:flex;justify-content:space-between;padding:15px 22px;border-bottom:1px solid #f7f7f7}
.summary-row:last-child{border-bottom:0}

.monthly-alert-box{
    background:linear-gradient(135deg,#dc3545,#a41220);
    color:#f0f0f0;
    border-radius:20px;
    padding:22px 26px;
    margin-bottom:24px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 12px 35px rgba(220,53,69,.28);
}
.monthly-alert-box h4{
    font-weight:900;
    margin:0 0 6px;
}
.monthly-alert-box p{
    margin:0;
    font-size:15px;
}
.monthly-alert-box .amount{
    font-size:24px;
    font-weight:900;
}
.monthly-alert-actions{
    display:flex;
    gap:10px;
}

@media(max-width:1100px){.db-cards{grid-template-columns:repeat(2,1fr)}}
@media(max-width:600px){
    .db-cards{grid-template-columns:1fr}
    .db-top{display:block}
    .db-date{margin-top:12px;display:inline-flex}
    .monthly-alert-box{display:block}
    .monthly-alert-actions{margin-top:15px}
}
</style>

<div class="db">

    <div class="db-top">
        <div>
            <h2>Dashboard</h2>
            <div class="text-muted">
                Welcome back, <strong>{{ auth()->user()->name }}</strong>
            </div>
        </div>

        <div class="db-date">
            <i class="bi bi-calendar3 me-2"></i>
            {{ now()->format('d M Y') }}
        </div>
    </div>

    @if(!empty($monthlyAlert))
        <div class="monthly-alert-box"
             id="monthlyAlertBox"
             data-amount="{{ number_format($monthlyAlert->total_required, 2) }}">

            <div>
                <h4>⚠ Monthly Salary Cycle Due</h4>
                <p>
                    Please arrange
                    <span class="amount">Rs {{ number_format($monthlyAlert->total_required, 2) }}</span>
                    before salary distribution.
                </p>
            </div>

            <div class="monthly-alert-actions">
                <a href="{{ route('monthly-alerts.show', $monthlyAlert->id) }}" class="btn btn-light">
                    View Details
                </a>

                <form action="{{ route('monthly-alerts.arranged', $monthlyAlert->id) }}" method="POST">
                    @csrf
                    <button class="btn btn-success">
                        Funds Arranged
                    </button>
                </form>
            </div>
        </div>
    @endif

    <div class="db-cards">

        <div class="db-card dark">
            <div class="db-ico"><i class="bi bi-cash-stack"></i></div>
            <div class="db-lbl">Total Sales</div>
            <div class="db-val">Rs {{ number_format($totalSales ?? 0, 2) }}</div>
            <div class="db-sub">Invoice sales amount</div>
        </div>

        <div class="db-card">
            <div class="db-ico"><i class="bi bi-wallet2"></i></div>
            <div class="db-lbl">Factory Expenses</div>
            <div class="db-val text-danger">Rs {{ number_format($totalExpenses ?? 0, 2) }}</div>
            <div class="db-sub">All expense records</div>
        </div>

        <div class="db-card">
            <div class="db-ico"><i class="bi bi-people"></i></div>
            <div class="db-lbl">Employees</div>
            <div class="db-val">{{ $totalEmployees ?? 0 }}</div>
            <div class="db-sub">Workers / staff</div>
        </div>

        <div class="db-card">
            <div class="db-ico"><i class="bi bi-receipt"></i></div>
            <div class="db-lbl">Invoices</div>
            <div class="db-val">{{ $totalInvoices ?? 0 }}</div>
            <div class="db-sub">Total invoices</div>
        </div>

    </div>

    <div class="db-cards">

        <div class="db-card">
            <div class="db-ico"><i class="bi bi-credit-card"></i></div>
            <div class="db-lbl">Payments Received</div>
            <div class="db-val text-primary">Rs {{ number_format($totalPayments ?? 0, 2) }}</div>
            <div class="db-sub">Customer payments</div>
        </div>

        <div class="db-card">
            <div class="db-ico"><i class="bi bi-person-lines-fill"></i></div>
            <div class="db-lbl">Customers</div>
            <div class="db-val">{{ $totalCustomers ?? 0 }}</div>
            <div class="db-sub">Total customers</div>
        </div>

        <div class="db-card">
            <div class="db-ico"><i class="bi bi-currency-exchange"></i></div>
            <div class="db-lbl">Payroll</div>
            <div class="db-val text-warning">Rs {{ number_format($totalPayroll ?? 0, 2) }}</div>
            <div class="db-sub">Salary expense</div>
        </div>

        <div class="db-card">
            <div class="db-ico"><i class="bi bi-graph-up-arrow"></i></div>
            <div class="db-lbl">Net Profit</div>
            <div class="db-val {{ ($netProfit ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                Rs {{ number_format($netProfit ?? 0, 2) }}
            </div>
            <div class="db-sub">Sales - expenses - payroll</div>
        </div>

    </div>

    <div class="db-grid">
        <div class="db-panel">
            <div class="db-ph">
                <h5>Business Summary</h5>
                <span class="text-muted small">Factory accounts overview</span>
            </div>

            <div class="summary-row">
                <strong>Total Sales</strong>
                <span>Rs {{ number_format($totalSales ?? 0, 2) }}</span>
            </div>

            <div class="summary-row">
                <strong>Total Expenses</strong>
                <span class="text-danger">Rs {{ number_format($totalExpenses ?? 0, 2) }}</span>
            </div>

            <div class="summary-row">
                <strong>Total Payroll</strong>
                <span class="text-warning">Rs {{ number_format($totalPayroll ?? 0, 2) }}</span>
            </div>

            <div class="summary-row">
                <strong>Receivables</strong>
                <span class="text-info">Rs {{ number_format($pendingReceivables ?? 0, 2) }}</span>
            </div>

            <div class="summary-row">
                <strong>Net Profit</strong>
                <strong class="{{ ($netProfit ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                    Rs {{ number_format($netProfit ?? 0, 2) }}
                </strong>
            </div>
        </div>
    </div>

</div>

@if(!empty($monthlyAlert))
<script>
document.addEventListener('DOMContentLoaded', function () {
    const alertBox = document.getElementById('monthlyAlertBox');
    const amount = alertBox.dataset.amount;

    function showMonthlyNotification() {
        new Notification("Accounts System", {
            body: "⚠ Monthly Salary Alert: Please arrange Rs " + amount + " before salary distribution.",
            icon: "/favicon.ico"
        });
    }

    if ("Notification" in window) {
        if (Notification.permission === "granted") {
            showMonthlyNotification();
        } else if (Notification.permission !== "denied") {
            Notification.requestPermission().then(function (permission) {
                if (permission === "granted") {
                    showMonthlyNotification();
                }
            });
        }
    }
});
</script>
@endif

@endsection
