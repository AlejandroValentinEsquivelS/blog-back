<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with(['user', 'category'])->latest()->get();
        return response()->json($posts);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|string'
        ]);

        $post = Post::create($request->all());

        return response()->json([
            'message' => 'Post creado exitosamente',
            'post' => $post
        ], 201);
    }

    public function show($id)
    {
        $post = Post::with(['user', 'category'])->findOrFail($id);
        return response()->json($post);
    }

    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        $request->validate([
            'category_id' => 'exists:categories,id',
            'title' => 'string|max:255',
            'content' => 'string',
            'image' => 'nullable|string'
        ]);

        $post->update($request->all());

        return response()->json([
            'message' => 'Post actualizado exitosamente',
            'post' => $post
        ]);
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return response()->json([
            'message' => 'Post eliminado exitosamente'
        ]);
    }
}