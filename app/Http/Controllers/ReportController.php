<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\Complaint;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    public function dashboard()
    {
        $userData = session('user');
        if (!$userData) {
            return redirect()->route('login')->withErrors(['error' => 'Session expired, please log in again.']);
        }
        $user = new User($userData);
        $account = Account::where('user_id', $user->id)->first();
        return view('dashboard', compact('user', 'account'));
    }

    public function profile(){
        return "Profile Page";
    }
    public function transactionpopup(){
        return "Transaction Page";
    }
    public function complaint(){
        return "Complaint Page";
    }
    public function getProfile()
    {
        if (!Auth::guard('admin')->check()) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
        $user = Auth::guard('admin')->user();
        $account = Account::where('user_id', $user->id)->first();
        return response()->json([
            'user' => $user,
            'account' => $account ?: 'No account found'
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|uuid|exists:transactions,id',
            'complaint_description' => 'required|string',
            'transaction_date' => 'required|date',
        ]);

        Complaint::create([
            'transaction_id' => $request->transaction_id,
            'description' => $request->complaint_description,
            'transaction_date' => $request->transaction_date,
        ]);
        // return redirect()->back()->with('success', 'Complaint submitted successfully.');
        return redirect()->route('dashboard')->with('success', 'Profile updated successfully!');
    }

    public function transaction()
    {
        $user = Auth::guard('admin')->user();
    
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    
        // Get the user's account(s)
        $accountIds = Account::where('user_id', $user->id)->pluck('id');
        // Fetch transactions for those accounts
        $transactions = Transaction::whereIn('account_id', $accountIds)->get();
        return response()->json(['transactions' => $transactions]);
    }
    
    public function downloadPDF()
    {
        $transactions = Transaction::all();

        $pdf = Pdf::loadView('pdf.transaction', ['transactions' => $transactions]);

        return $pdf->download('transactions.pdf');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::guard('admin')->user();
        if (!$user) {
            return redirect()->route('login')->withErrors(['error' => 'User not authenticated.']);
        }
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        // Update user details
        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
        ]);

        return redirect()->route('dashboard')->with('success', 'Profile updated successfully!');
    }


    public function transactionstore(Request $request)
{
    $request->validate([
        'account_id' => 'required|exists:accounts,id',
        'transaction_type' => 'required|in:Credit,Debit',
        'amount' => 'required|numeric|min:0.01',
        'description' => 'nullable|string',
    ]);

    $account = Account::findOrFail($request->account_id);

    DB::beginTransaction();
    try {
        if ($request->transaction_type === 'Debit' && $account->balance < $request->amount) {
            return back()->withErrors(['error' => 'Insufficient balance for this transaction.']);
        }

        if ($request->transaction_type === 'Credit') {
            $account->balance += $request->amount;
        } else {
            $account->balance -= $request->amount;
        }

        $account->modified_by = Auth::guard('admin')->user()->login_id;
        $account->save();

        Transaction::create([
            'id' => Str::uuid(),
            'account_id' => $request->account_id,
            'type' => $request->transaction_type,
            'amount' => $request->amount,
            'description' => $request->description,
            'created_at' => now(),
            'created_by' => Auth::guard('admin')->user()->login_id,
        ]);

        DB::commit();

        return back()->with('success', 'Transaction added successfully.');
    } catch (\Exception $e) {
        DB::rollback();

        return back()->withErrors(['error' => 'Something went wrong: ' . $e->getMessage()]);
    }
}

    public function transactionapi()
    {
            $accountIds = Account::all();    
        return response()->json(['transactions' => $accountIds]);
    }


    public function getTransactions(Request $request)
    {
        $request->validate([
            'account_number' => 'required|string|exists:accounts,account_number',
            'from' => 'required|date',
            'to' => 'required|date|after_or_equal:from'
        ]);
    
        $account = Account::where('account_number', $request->account_number)->first();
    
        if (!$account) {
            return response()->json([
                'success' => false,
                'message' => 'Account not found.'
            ], 404);
        }
        $transactions = Transaction::where('account_id', $account->id)
        ->whereBetween('created_at', [$request->from, $request->to])
        ->get();
    
        return response()->json([
            'success' => true,
            'transactions' => $transactions
        ]);
    }

    public function storeTransaction(Request $request)
{
    $request->validate([
        'account_number' => 'required|exists:accounts,account_number',
        'type' => 'required|in:Credit,Debit',
        'amount' => 'required|numeric|min:0.01',
        'description' => 'nullable|string|max:255',
        'created_by' => 'required|string|exists:users,login_id'
    ]);

    $account = Account::where('account_number', $request->account_number)->firstOrFail();

    if ($request->type == 'Debit' && $account->balance < $request->amount) {
        return response()->json(['error' => 'Insufficient balance for this transaction.'], 400);
    }

    DB::beginTransaction();
    try {
        if ($request->type == 'Credit') {
            $account->balance += $request->amount;
        } else {
            $account->balance -= $request->amount;
        }

        $transaction = Transaction::create([
            'id' => Str::uuid(),
            'account_id' => $account->id,
            'type' => $request->type,
            'amount' => $request->amount,
            'description' => $request->description,
            'created_by' => $request->created_by,
            'modified_by' => $request->created_by,
            'created_at' => now()
        ]);

        $account->save();

        DB::commit();

        return response()->json([
            'message' => 'Transaction recorded successfully',
            'transaction' => $transaction
        ], 201);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['error' => 'Transaction failed', 'details' => $e->getMessage()], 500);
    }
}

}