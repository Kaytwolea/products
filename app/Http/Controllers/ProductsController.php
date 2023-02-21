<?php

namespace App\Http\Controllers;

use App\Mail\Confirmation;
use App\Models\products;
use App\Models\User;
use Exception;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ProductsController extends Controller
{
    //
    public function Addproducts(Request $request)
    {
        try {
            $input = $request->validate([
                'title' => 'required',
                'description' => 'required',
                'price' => 'required',
                'image' => 'required',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'error' => true,
            ], 400);
        }

        $newProducts = products::create($input);

        if ($newProducts) {
            return response()->json([
                'message' => 'New product created successfully',
                'data' => $newProducts,
                'error' => false,
            ], 201);
        } else {
            return response()->json([
                'message' => 'New product could not be created',
                'data' => null,
                'error' => true,
            ], 401);
        }
    }

    public function Getproducts()
    {
        return products::orderBy('id', 'desc')->get();
    }

    public function Editproduct(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required',
        ]);

        $product = products::findOrFail($id);
        $product->title = $request->input('title');
        $product->description = $request->input('description[');
        $product->save();

        return response()->json([
            'message' => 'Product updated successfully',
            'data' => $product,
            'error' => false,
        ], 200);
    }

    public function Signup(Request $request) {
        try {
            $input = $request->validate([
                'first_name' => 'required',
                'last_name' => 'required',
                'username' => 'required',
                'phone_number' => 'required',
                'email' => 'required',
                'password' => 'required'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message'=> $e->getMessage(),
                'data' => null,
                'error' => true
            ], 400);
        }

        $input['password'] = Hash::make($request->password);
        $input['confirmation_code'] = rand(11111, 89999);
        $createUser = User::create($input);
        $token= $createUser->createToken('access-token')->accessToken;

        return response()->json([
            'message' => 'User created successfully, verification your phone number to proceed',
            'token' => $token,
            'data' => $createUser,
            'error' => false
        ], 201);
}
    public function getUser() {
        $authuser = auth()->user();
        $authuser->phone_number_status = !$authuser->phone_number_status;
        return response()->json([
            'message' => 'User successfully retrieved',
            'data' => $authuser
        ], 200);
    }

    public function SendPhoneCode() {
        $user = User::where('id', auth()->id())->first();
        $prefixes = ['234'];
        $check_phone = substr($user->phone_number, 0, 3);
        $phonelength = strlen($user->phone_number);
        $api_key = 'TLs2Bl5jWipLkeH6OatRBn6ib3kl3nTuH7dN9xu46v5SuKrcqKCQKwlvkoQAUq';

        if ($phonelength !== 13) {
            return response()->json([
                'message' => 'Phone number is not valid',
                'error' => true,
            ], 400);
        } elseif($user->phone_number_status == 1) {
            return response()->json([
                'message' => 'Your phone number has already been verified',
                'error' => true
            ], 400);
        } else {
            Mail::send(new Confirmation($user));
            return response()->json([
                'message' => 'Your code has been sent',
                'error' => false
            ], 200);
        }
    }

    public function Confirmcode(Request $request) {
        $code = $request->validate([
            'confirmation_code' => 'required'
        ]);

        $user = User::where('id', auth()->id())->first();
        if ($user->confirmation_code == $code['confirmation_code']) {
            if ($user->markEmailAsVerified()) {
                event(new Verified($user));
            }

            // $user->save();
            return response()->json([
                'message' => 'Your email has been verified.',
            ], 200);
        } else {
            return response()->json([
                'message' => 'Invalid code.',
                'data' => null,
                'error' => true
            ], 400);
        }
    }
}
