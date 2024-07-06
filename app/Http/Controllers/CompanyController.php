<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index()
    {
        $company = Company::with(['bank_accounts:id,bank_name,number,company_id'])->findOrFail(1);

        return response()->json($company);
    }
    public function update(Request $request)
    {
        $company = Company::find(1);

        // Validate the request data
        $request->validate([
            'name' => 'required|max:50',
            'phone' => 'required|numeric|min:11',
            'address' => 'required',
            'email' => 'required',
            'website' => 'required',
            'facebook' => 'required',
            'bank_accounts' => 'array',
            'bank_accounts.*.id' => 'integer',
            'bank_accounts.*.bank_name' => 'required',
            'bank_accounts.*.number' => 'required',
            'bank_accounts.*.company_id' => 'required',
        ]);

        // Update the Company data
        $company->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'email' => $request->email,
            'website' => $request->website,
            'facebook' => $request->facebook,
        ]);

        // mengambil id dari request
        $requestBank_accountIds = collect($request->bank_accounts)->pluck('id')->filter();

        // mengamil id dari akunn bank yang ada
        $existingBank_accountIds = $company->bank_accounts->pluck('id');

        // mengidentikikasi id yang tdk ada dari request
        $bank_accountsToDelete = $existingBank_accountIds->diff($requestBank_accountIds);

        // menghapus id yang dari request
        BankAccount::whereIn('id', $bank_accountsToDelete)->delete();

        foreach ($request->bank_accounts as $bank_accountData) {
            if (isset($bank_accountData['id'])) {
                $bank_account = BankAccount::find($bank_accountData['id']);
                $bank_account->update($bank_accountData);
            } else {
                $bank_account = new BankAccount($bank_accountData);
                $company->bank_accounts()->save($bank_account);
            }
        }
        return response()->json(['message' => 'Company and Accouut bank updated successfully']);
    }
}
