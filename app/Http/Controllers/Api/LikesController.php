<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Like;
use Illuminate\Support\Facades\Auth;

class LikesController extends Controller
{
    public function like($post_id)
    {
        $like = Like::where('post_id',$post_id)->where('user_id',Auth::user()->id)->first(); //
        if ($like) {
            $like->delete();
            return response()->json([
                'success'=>true,
                'message'=> 'unliked'
            ]);
        }

        $like = new Like;
        $like->user_id = Auth::user()->id;
        $like->post_id = $post_id;
        $like->save();
        return response()->json([
            'success'=>true,
            'message'=>'liked'
        ]);

    }
}
