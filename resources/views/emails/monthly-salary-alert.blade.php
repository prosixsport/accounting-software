<h2>

Monthly Salary & Expense Alert

</h2>

<hr>

<p>

Hello Boss,

</p>

<p>

Monthly salary cycle has been generated.

</p>

<table
border="1"
cellpadding="10"
width="600">

<tr>

<th align="left">

Employees Salary

</th>

<td>

Rs {{ number_format($alert->employees_salary,2) }}

</td>

</tr>

<tr>

<th align="left">

Contractor Bills

</th>

<td>

Rs {{ number_format($alert->contractor_bills,2) }}

</td>

</tr>

<tr>

<th align="left">

Factory Expenses

</th>

<td>

Rs {{ number_format($alert->factory_expenses,2) }}

</td>

</tr>

<tr>

<th align="left">

Total Required

</th>

<td>

<strong>

Rs {{ number_format($alert->total_required,2) }}

</strong>

</td>

</tr>

</table>

<br>

<h3 style="color:red">

Please arrange funds before salary distribution.

</h3>

<br>

Regards,

<br>

Accounts System
