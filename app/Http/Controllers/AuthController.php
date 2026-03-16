<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreOtpRequest;
use App\Models\Otp;
use App\Models\User;

class AuthController extends Controller
{
    public function sendOtp(StoreOtpRequest $request){
       
        try{
            $otp = rand(100000,999999);
            // Get validated data
            $data = $request->validated();
            // Add OTP to data
            $data['otp'] = $otp;
            $data['expire_at'] = now()->addMinutes(5);
            // Save in database
            $post = Otp::firstOrCreate($data);
            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully',
                'otp' => $otp // remove in production
            ]);

        }  catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function verifyOtp(Request $request) {
        try {

            $request->validate(
                [ 'email' => 'required','otp'=> 'required']
            );

            $otp = Otp::where('email',$request->email)
                ->where('otp',$request->otp)
                ->where('expire_at','>',now())
                ->first();
            
            if(!$otp){
                return response()->json(['message'=>'Invalid OTP'],401);
            }
            
             $user = User::firstOrCreate([
                'email'=>$request->email,
             ]);

            if(!$user->hasAnyRole(['admin','recruiter','candidate'])){
                $user->assignRole('candidate');
            }

            $token = $user->createToken('auth')->plainTextToken;
            return response()->json(['message'=>'Success','token'=>$token,'user'=>$user,'roles'=>$user->getRoleNames()]);

        } catch (\Exception $e) {
              return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
