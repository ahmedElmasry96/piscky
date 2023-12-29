<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    use ApiResponse;

    public function register(RegisterRequest $request)
    {
        try {
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => bcrypt($request->password), 
            ]);

            $user = User::findOrFail($user->id);
            $token = hash('sha256', Str::random(40));

            $data['token'] =  $user->createToken($token)->plainTextToken;;
            $data['user'] = new UserResource($user);

            return $this->returnData('data', $data, $this->getSuccessMessage());
        } catch(Exception $e) {
            return $this->returnError('please try again', 400);
        }
    }

    public function login(LoginRequest $request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::user(); 
            $token = hash('sha256', Str::random(40));

            $data['token'] =  $user->createToken($token)->plainTextToken;;
            $data['user'] = new UserResource($user);
   
            return $this->returnData('data', $data, $this->getSuccessMessage());
        } 
        else{ 
            return $this->returnError('unauthorized', 401);
        } 
    }
}
