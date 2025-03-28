<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Account;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class WebAuthController extends Controller
{

    public function showLogin()
    {
        return view('login');
    }

    public function showRegister()
    {
        return view('register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'nullable|string|max:50',
            'login_id' => 'required|string|max:50|unique:users,login_id',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'account_type' => 'required|in:Personal,Business',
            'currency' => 'required|in:USD,EUR,GBP',
               ]);
        DB::beginTransaction();
        try {
            // Create user
            $user = User::create([
                'id' => Str::uuid(),
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'login_id' => $request->login_id,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'account_type' => $request->account_type,
                'created_by' => $request->login_id,
            ]);

            // Generate a unique 12-digit Luhn-compliant account number
            $accountNumber = $this->generateLuhnAccountNumber();

            // Create an account linked to the user
            Account::create([
                'id' => Str::uuid(),
                'user_id' => $user->id,
                'account_name' => $request->login_id,
                'account_number' => $accountNumber,
                'account_type' => $request->account_type,
                'currency' => $request->currency,
                'balance' => 0.00,
                'created_by' => $request->login_id,
            ]);
            // Commit transaction if everything is successful
            DB::commit();
            return redirect()->route('login')->with('success', 'Account created successfully!');
        } catch (\Exception $e) {
            // Rollback transaction if any error occurs
            DB::rollback();
            return back()->withErrors(['error' => 'Something went wrong! Please try again.']);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'login_id' => 'required|string',
            'password' => 'required|string',
        ]);

        try {
            $user = User::where('login_id', $request->login_id)->first();

            if (!$user) {
                return back()->withErrors(['login_id' => 'User not found'])->withInput($request->only('login_id'));
            }

            $remember = $request->has('remember');
            $credentials = ['login_id' => $request->login_id, 'password' => $request->password];

            if (Auth::guard('admin')->attempt($credentials, $remember)) {
                $request->session()->regenerate();
                $authenticatedUser = Auth::guard('admin')->user();
                // Store user and account data in session
                session([
                    'user' => $authenticatedUser->toArray(),
                    'account' => Account::where('user_id', $authenticatedUser->id)->first(),
                ]);
                return redirect()->route('dashboard')->with('success', 'Login successful!');
            }
            return back()->withErrors(['password' => 'Invalid credentials'])->withInput($request->only('login_id'));
        } catch (Exception $ex) {
            return redirect()->back()->withErrors(['error' => $ex->getMessage()])->withInput($request->all());
        }
    }
   

//Generate a 12-digit Luhn-compliant account number
     
    private function generateLuhnAccountNumber()
    {
        do {
            $accountNumber = $this->generateRandomLuhnNumber();
        } while (Account::where('account_number', $accountNumber)->exists());

        return $accountNumber;
    }

      //Generate a Luhn-valid number with 12 digits
     
    private function generateRandomLuhnNumber()
    {
        $accountNumber = (string) mt_rand(10000000000, 99999999999);
        return $accountNumber . $this->calculateLuhnChecksum($accountNumber);
    }

    // Compute Luhn checksum digit
    private function calculateLuhnChecksum($number)
    {
        $digits = str_split($number);
        $sum = 0;
        $alternate = true;
        for ($i = count($digits) - 1; $i >= 0; $i--) {
            $digit = (int) $digits[$i];
            if ($alternate) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            $sum += $digit;
            $alternate = !$alternate;
        }
        return (10 - ($sum % 10)) % 10;
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }

    public function getAccountDetails(Request $request)
    {
        $request->validate([
            'account_number' => 'required|string|exists:accounts,account_number',
        ]);
    
        $account = Account::where('account_number', $request->account_number)
            ->with('user') 
            ->first();
    
        if (!$account) {
            return response()->json(['error' => 'Account not found'], 404);
        }
    
        return response()->json([
            'account' => $account,
        ], 200);
    }

    public function deleteUser(Request $request)
{
    $request->validate([
        'account_number' => 'required|string|exists:accounts,account_number',
    ]);

    DB::beginTransaction();
    try {
        $account = Account::where('account_number', $request->account_number)->first();

        if (!$account) {
            return response()->json(['error' => 'Account not found'], 404);
        }

        $user = $account->user;

        if (!$user) {
            return response()->json(['error' => 'User not found for this account'], 404);
        }
        $user->delete();

        DB::commit();

        return response()->json(['message' => 'User and associated account deleted successfully'], 200);
    } catch (\Exception $e) {
        DB::rollback();

        return response()->json([
            'error' => 'Something went wrong!',
            'details' => $e->getMessage()
        ], 500);
    }
}
public function updateAccountAndUser(Request $request)
    {
        $validatedData = $request->validate([
            'account_number' => 'required|string|exists:accounts,account_number',
            'account_name' => 'nullable|string|max:255',
            'account_type' => 'nullable|in:Personal,Business',
            'currency' => 'nullable|in:USD,EUR,GBP',
            'balance' => 'nullable|numeric|min:0',
            'modified_by' => 'required|string|max:50',
            'first_name' => 'nullable|string|max:50',
            'last_name' => 'nullable|string|max:50',
            'email' => 'nullable|email|unique:users,email',
            'login_id' => 'nullable|string|max:50|unique:users,login_id'
        ]);

        DB::beginTransaction();

        try {
            $account = Account::where('account_number', $validatedData['account_number'])->firstOrFail();
            
            $user = User::findOrFail($account->user_id);

            $account->update([
                'account_name' => $validatedData['account_name'] ?? $account->account_name,
                'account_type' => $validatedData['account_type'] ?? $account->account_type,
                'currency' => $validatedData['currency'] ?? $account->currency,
                'balance' => $validatedData['balance'] ?? $account->balance,
                'modified_by' => $validatedData['modified_by'],
            ]);

            $user->update([
                'first_name' => $validatedData['first_name'] ?? $user->first_name,
                'last_name' => $validatedData['last_name'] ?? $user->last_name,
                'email' => $validatedData['email'] ?? $user->email,
                'login_id' => $validatedData['login_id'] ?? $user->login_id,
            ]);

            DB::commit();

            return response()->json(['message' => 'Account and user details updated successfully'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Something went wrong!', 'details' => $e->getMessage()], 500);
        }
    }

    
   
}
