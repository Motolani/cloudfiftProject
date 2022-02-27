<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    //
    public function createAccount(Request $request)
    {
        $rules = array(
            'name' => 'required',
            'number' => 'required|max:11',
            'intial_deposit' => 'required',
            'type' => 'required',
            
        );
        $validator = Validator::make($request->all(), $rules);

        // process the login
        if ($validator->fails()) {
            return response()->json(
                [
                    'required_fields' => $validator->errors()->all(),
                    'data' =>'Empty field(s)',
                    'status'=> '100',
                ]
            );
        } else {
            
            $accountName = $request->name;
            $accountNumber = $request->number;
            $intialDeposit = $request->intial_deposit;
            $accountType = $request->type;
            $balance = $intialDeposit;
            
            $newAccount = new Account();
            $newAccount->name = $accountName;
            $newAccount->number = $accountNumber;
            $newAccount->intial_deposit = $intialDeposit;
            $newAccount->balance = $balance;
            $newAccount->type = $accountType;
            $saved = $newAccount->save();
            
            if($saved)
            {
                return response()->json(
                    [
                        'message' =>'Account successfully created',
                        'status'=> '200',
                    ]
                );
            }else{
                return response()->json(
                    [
                        'message' =>'Account creation failed',
                        'status'=> '400',
                    ]
                );
            }
            
        }
    }
    
    public function getBalance(Request $request)
    {
        $rules = array(
            'account_name' => 'required',
            'account_number' => 'required',   
        );
        $validator = Validator::make($request->all(), $rules);

        // process the login
        if ($validator->fails()) {
            return response()->json(
                [
                    'required_fields' => $validator->errors()->all(),
                    'data' =>'Empty field(s)',
                    'status'=> '100',
                ]
            );
        } else {
        
            $accountNumber = $request->account_number;
            $accountName = $request->account_name;
            
            
            //checking if Account making transaction exists
            $accountCheck = Account::where("number", $accountNumber)->where("name", $accountName);
            
            if($accountCheck->exists())
            {
                $accountBalance = $accountCheck->pluck('balance');
                
                return response()->json(
                    [
                        'balance' => $accountBalance,
                        'status'=> '200',
                    ]
                );
            }
        }
    }
}
