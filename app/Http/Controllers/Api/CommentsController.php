<?php

namespace App\Http\Controllers\Api;

use App\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentsController extends Controller
{
    public function storeComment(Request $request,$id)
    {
        $validator = Validator::make($request->all(),[
            'comment'=>'required',
            'user_id'=>'required'
        ]);

        if($validator->fails()){
            return response()->json([
                "success" => false,
                "message" => $validator->errors(),
            ], 422);
        }

        $post = Post::find($id);
        if(!$post){
            return response()->json([
                'success'=>false,
                'message' => 'post not found'
            ],404);
        }

        $comment = Comment::create([
            'comment'=> $request->comment,
            'post_id'=> $post->id,
            'user_id'=> Auth::user()->id
        ]);

        return response()->json([
            'success'=> true,
            'message' => 'comment added',
            'comment'=>$comment
        ]);

    }

    public function updateComment(Request $request,$post_id,$comment_id)
    {
        $validator = Validator::make($request->all(),[
            'comment'=>'required',
            'user_id'=>'required'
        ]);

        if($validator->fails()){
            return response()->json([
                "success" => false,
                "message" => $validator->errors(),
            ], 422);
        }

        $post = Post::find($post_id);
        if(!$post){
            return response()->json([
                'success'=>false,
                'message' => 'post not found'
            ],404);
        }

        $comment = Comment::find($comment_id);
        if ($comment->user_id != Auth::user()->id) {
            return response()->json([
                'success'=>false,
                'message' => 'unauthorize access'
            ]);
        }

        $comment->comment = $request->comment;
        $comment->update();

        return response()->json([
            'success'=> true,
            'message' => 'comment edited',
            'comment'=>$comment
        ]);


    }

    public function deleteComment($post_id,$comment_id)
    {

        $post = Post::find($post_id);
        if(!$post){
            return response()->json([
                'success'=>false,
                'message' => 'post not found'
            ],404);
        }

        $comment = Comment::find($comment_id);
        if ($comment->user_id != Auth::user()->id) {
            return response()->json([
                'success'=>false,
                'message' => 'unauthorize access'
            ]);
        }

        $comment->delete();

        return response()->json([
            'success'=> true,
            'message' => 'comment deleted',
        ]);
    }

    public function getComments($post_id)
    {
        $comments = Comment::where('post_id',$post_id)->orderBy('created_at','asc')->get();
        foreach ($comments as $comment) {
            $comment->user;
        }

        return response()->json([
            'success'=>true,
            'comments'=>$comments
        ]);
    }



    public function findPost($post_id)
    {
        $post = Post::find($post_id);
        if(!$post){
            return response()->json([
                'success'=>false,
                'message' => 'post not found'
            ],404);
        }
    }
}
