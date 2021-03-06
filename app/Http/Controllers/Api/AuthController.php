<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Exception;
use Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $creds = $request->only(['email','password']);
        if (!$token=auth()->attempt($creds)){
            return response()->json([
                'success' => false,
                'message' => 'invalid credential'
            ]);
        }

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => Auth::user()
        ]);
    }

    public function logout(Request $request)
    {
        // auth()->logout();  //
        try {
            JWTAuth::invalidate(JWTAuth::parseToken($request->token));
            return response()->json([
                'success' => true,
                'message' => 'logout success'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e
            ]);
        }

        // return response()->json([
        //     'success' => true,
        //     'message' => 'logout success'
        // ]);
    }

    public function register(Request $request)
    {
        $encryptedPass = Hash::make($request->password);
        $user = new User;
        try{
            $user->name= 'Dummy';
            $user->email = $request->email;
            $user->password = $encryptedPass;
            $user->save(); //
            return $this->login($request);
        }catch(Exception $e){
            return response()->json([
                'success' => true,
                'message' => $e
            ]);
        }

    }

    public function saveUserInfo(Request $request)
    {
            $user = User::find(Auth::user()->id);
            $user->name = $request->name;
            $user->lastname = $request->lastname;
            if ($request->has("photo")) {
                // $photo = time().'.jpg';
                // //decode photo string and save to storage/profiles
                // $filepath = $request->file('photo')->storeAs(
                //     'public/profiles',
                //     $photo,
                //     'local'
                // );

                // $image_64 = $request->photo; //your base64 encoded data



                // $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];   // .jpg .png .pdf

                // $replace = substr($image_64, 0, strpos($image_64, ',')+1);

                // // find substring fro replace here eg: data:image/png;base64,

                // $image = str_replace($replace, '', $image_64);

                // $image = str_replace(' ', '+', $image);

                // $imageName = Str::random(10).'.'.$extension;

                // Storage::disk('local')->put("profiles/".$imageName, base64_decode($image));


                $name = Str::random(15).'.png';
                // decode the base64 file
                $file = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '',$request->photo
                ));
                Storage::put("public/profiles/".$name, $file);

                // file_put_contents('storage/profiles/'.$photo,base64_decode($request->photo));
                $user->photo = "profiles/".$name;

                $user->update();
                return response()->json([
                    'success'=>true,
                    'photo'=>"profiles/".$name,
                ]);
            }

            $user->update();
            return response()->json([
                'success'=>false,
                'photo'=>"tidak ada foto",
            ]);
    }
}
