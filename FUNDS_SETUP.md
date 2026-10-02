# Funds Management update

Back up database and files before replacing project code. Preserve .env, database files and uploads.

1. Replace code with this package.
2. Run `php artisan migrate --force` then `php artisan optimize:clear` from the project directory.
3. Open Funds Management. Receive cash using Boss Azeem, Boss Atif or Boss Kashif.
4. Record salary, contractor and other payments in their original modules. They appear automatically in Funds Management; do not enter the same payment twice.
5. Select month for opening, receipts, payments, remaining balance and monthly history.

Owner Funds routes, views and controller are removed. Existing owner_funds database records are preserved as historical receipts and counted once. Do not drop that table. Existing owner_funds permission key is retained for access compatibility.

User Activity captures Eloquent created/updated/deleted events on funds receipts and payment models after installation. Old users cannot be reconstructed. Query-builder SQL, direct database changes and bulk Eloquent updates bypass model observers. Activity is not a tamper-proof security log. Access panel reflects existing permission rules, including full access when no permissions are assigned.

Cash and bank receipts show their method. Salary and contractor sources do not yet support cash/bank assignment, so remaining balance is combined. Boss receipts feed a shared pool; payments cannot be attributed to a particular boss without explicit allocation data. Amounts aggregate as integer paise. Existing duplicate records require reconciliation. Historical source edits affect monthly balances; month locks are not implemented.

PHP/Laravel execution was unavailable in the build environment. Run `php artisan test --filter=FundsLedgerTest` and test receipts, permissions and source payments in a staging copy before live deployment.
