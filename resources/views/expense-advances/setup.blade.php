@extends('layouts.app')
@section('content')
<div class="card border shadow-sm"><div class="card-body p-4"><h3>Weekly Expense Advances — Setup Pending</h3><p>The expense advance table or expense link column is missing in this application's database. Complete the migration on the same machine where this page is running.</p><p>Server commands:</p><pre>cd /var/www/accounting-software
git pull origin main
php artisan migrate --force
php artisan optimize:clear</pre><p>For your Windows local application, run the last two commands inside its project folder. MySQL must be running. Existing data will be retained.</p><a class="btn btn-outline-dark" href="{{ route('expenses.index') }}">Back to Expenses</a></div></div>
@endsection
