<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    //
    public function transfer(Request $request)
    {
        $rules = array(
            'account_number' => 'required|max:11',
            // 'transaction_type' => 'required',
            'amount' => 'required',
            'debit_to' => 'required',
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
            $transactionType = $request->transaction_type;
            $amount = $request->amount;
            $debitTo = $request->debit_to;
            
            //checking if Account making transaction exists
            $accountCheck = Account::where("number", $accountNumber);
            
            if($accountCheck->exists())
            {
                $account = Account::where("number", $accountNumber)->first();
                
                $accountbalance = $account->balance;
                
                //checking if account making transaction has sufficent funds
                if($amount > $accountbalance)
                {
                    return response()->json(
                        [
                            'message' =>'Insufficent Funds',
                            'status'=> '400',
                        ]
                    );
                }else{
                
                    //checking if receiver exists
                    $accountDebitToCheck = Account::where("number", $debitTo);
                    
                    if($accountDebitToCheck->exists())
                    {
                    //for Creditor
                        $newBalance = $accountbalance - $amount;
                        
                        //for Debitor
                        $receiver = Account::where("number", $debitTo)->first();
                        $receiverBalance = $receiver->balance;
                        $receiverNewBalance = $receiverBalance + $amount; 
                        
                        //saving transaction for transaction history
                        $newTransaction = new Transaction();
                        $newTransaction->transaction_type = 'debit';
                        $newTransaction->account_number = $accountNumber;
                        $newTransaction->amount = $amount;
                        $newTransaction->balance = $newBalance;
                        $newTransaction->debit_to = $debitTo;
                        $saved = $newTransaction->save();
                        
                        if($saved)
                        {
                            $newTransaction = new Transaction();
                            $newTransaction->transaction_type = 'credit';
                            $newTransaction->account_number = $debitTo;
                            $newTransaction->amount = $amount;
                            $newTransaction->balance = $receiverNewBalance;
                            $newTransaction->credit_from = $accountNumber;
                            $finalSaved = $newTransaction->save();
                            
                            if($finalSaved)
                            {
                                //updating sender account balance
                                
                                Account::where("number", $accountNumber)->update([
                                    'balance' => $newBalance
                                ]);
                                
                                //updating receiver account balance
                                
                                
                                Account::where("number", $debitTo)->update([
                                    'balance' => $receiverNewBalance
                                ]);
                                
                                return response()->json(
                                    [
                                        'message' =>'Transfer successfully',
                                        'status'=> '200',
                                    ]
                                );
                            }else{
                                return response()->json(
                                    [
                                        'message' =>'Transfer failed',
                                        'status'=> '400',
                                    ]
                                );
                            }
                        }else{
                            return response()->json(
                                [
                                    'message' =>'Transfer failed',
                                    'status'=> '400',
                                ]
                            );
                        }
                        
                        
                    }else{
                        return response()->json(
                            [
                                'message' =>'Account to be credited is Invalid',
                                'status'=> '400',
                            ]
                        );
                    }
                    
                }
            }else{
                return response()->json(
                    [
                        'message' =>'Account does not exist, please register',
                        'status'=> '400',
                    ]
                );
            }
        }
    } 
    
    public function transferHistory(Request $request)
    {
        $rules = array(
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
        
            $acountNumber = $request->account_number;
            
            $accountCheck = Account::where("number", $acountNumber);
            
            if($accountCheck->exists())
            {
                $accountTransaction = Transaction::where("account_number", $acountNumber)->get();

                
                return response()->json(
                    [
                        'Transactions' => $accountTransaction,
                        'status'=> '200',
                    ]
                );
            }else{
                return response()->json(
                    [
                        'message' => 'Account does not exist',
                        'status'=> '400',
                    ]
                );
            }
            
        }
    }
}