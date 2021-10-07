<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Post;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class PostsController extends Controller
{
    public function storePost(Request $request)
    {
        $validator = FacadesValidator::make($request->all(),[
            'desc'=>'required'
        ]);

        if($validator->fails()){
            return response()->json([
                "success" => false,
                "message" => $validator->errors(),
            ], 422);
        }

        $post = new Post;
        $post->user_id = auth()->user()->id; //
        $post->desc = $request->desc; //
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            //membuat nama unik untuk foto
            $photo = time().$file->getClientOriginalName();
            file_put_contents('storage/posts/'.$photo,base64_decode($request->photo)); //
            $post->photo = $photo; //
        }

        $post->save();
        $post->user;
        return response()->json([
            'success' => true,
            'message' => 'posted',
            'post'=> $post
        ]);
    }

    public function updatePost(Request $request,$id)
    {
        $post = Post::find($id); //
        if (Auth::user()->id != $post->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'unauthorized access'
            ],403);
        }

        $post->desc = $request->desc;
        $post->update();
        return response()->json([
            'success' => true,
            'message' => 'updated'
        ]);
    }


    public function deletePost($id)
    {
        $post = Post::find($id); //
        if (Auth::user()->id != $post->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'unauthorized access'
            ],403);
        }

        $post->delete();

        if ($post->photo != null) {
            Storage::delete('public/posts/'.$post->photo);
        }

        return response()->json([
            'success' => true,
            'message' => 'deleted'
        ]);
    }

    public function getPosts()
    {
        $posts = Post::orderBy('created_at','desc')->get();
        foreach ($posts as $post) {
            //get user of post
            $post->user;
            //menghitung komentar
            $post['commentsCount'] = count($post->comments);
            //menghitung like
            $post['likesCount'] = count($post->likes);
            //cek apakah user menyukai postingannya sendiri
            $post['selfLike'] = false;
            foreach ($post->likes as $like) {
                if($like->user_id == Auth::user()->id){
                    $post['selfLike'] = true;
                }
            }
        }
        return response()->json([
            'success'=>true,
            'posts'=> $posts
        ]);
    }
}
