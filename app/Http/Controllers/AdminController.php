<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    //
    public function createAdmin(Request $request)
    {
        $rules = array(
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            
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
        
            $name = $request->name;
            $email = $request->email;
            $password = Hash::make($request->password);
            
            $newAdmin = new User();
            $newAdmin->name = $name;
            $newAdmin->email = $email;
            $newAdmin->password = $password;
            $saved = $newAdmin->save();
            
            if($saved)
            {
                return response()->json(
                    [
                        'message' =>'Admin user created successfully',
                        'status'=> '200',
                    ]
                );
            }else{
                return response()->json(
                    [
                        'message' =>'Failed to create admin user',
                        'status'=> '400',
                    ]
                );   
            }
            
        }
    }
}
