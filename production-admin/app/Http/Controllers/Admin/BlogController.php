<?php

namespace App\Http\Controllers\Admin;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    // Get all blog posts
    public function index(Request $request)
    {
        $query = Blog::with(['category', 'tags', 'author']);

        if ($request->has('status') && $request->status !== '') {
            if ($request->status === 'scheduled') {
                $query->where('status', 'published')->where('published_at', '>', now());
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->has('category') && $request->category !== '') {
            $query->where('blog_category_id', $request->category);
        }

        if ($request->has('search') && $request->search !== '') {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }

        $posts = $query->orderBy('created_at', 'desc')->get();

        return response()->json($posts);
    }

    // Get single blog post
    public function show($id)
    {
        $post = Blog::with(['category', 'tags'])->findOrFail($id);
        return response()->json($post);
    }

    // Create new blog post
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_posts,slug',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'status' => 'required|in:draft,published',
            'published_at' => 'nullable|date',
            'blog_category_id' => 'nullable|exists:blog_categories,id',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'featured_image' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048'
        ]);

        try {
            // Handle featured image upload
            if ($request->hasFile('featured_image')) {
                $file = $request->file('featured_image');
                $filename = 'blog_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('blog', $filename, 'public');
                $validated['featured_image'] = '/storage/' . $path;
            }

            // Set published_at if status is published and no date provided
            if ($validated['status'] === 'published' && !isset($validated['published_at'])) {
                $validated['published_at'] = now();
            }

            $post = Blog::create($validated);

            // Handle tags
            if ($request->has('tags') && !empty($request->tags)) {
                $tagNames = array_map('trim', explode(',', $request->tags));
                $tagIds = [];
                
                foreach ($tagNames as $tagName) {
                    if (empty($tagName)) continue;
                    $tag = BlogTag::firstOrCreate(['name' => $tagName]);
                    $tagIds[] = $tag->id;
                }
                
                $post->tags()->sync($tagIds);
            }

            return response()->json([
                'success' => true,
                'message' => 'Blog post created successfully',
                'post' => $post
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create blog post: ' . $e->getMessage()
            ], 500);
        }
    }

    // Update blog post
    public function update(Request $request, $id)
    {
        $post = Blog::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_posts,slug,' . $id,
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'status' => 'required|in:draft,published',
            'published_at' => 'nullable|date',
            'blog_category_id' => 'nullable|exists:blog_categories,id',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'featured_image' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048'
        ]);

        try {
            // Handle featured image upload
            if ($request->hasFile('featured_image')) {
                // Delete old image
                if ($post->featured_image && Storage::disk('public')->exists(str_replace('/storage/', '', $post->featured_image))) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $post->featured_image));
                }

                $file = $request->file('featured_image');
                $filename = 'blog_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('blog', $filename, 'public');
                $validated['featured_image'] = '/storage/' . $path;
            }

            $post->update($validated);

            // Handle tags
            if ($request->has('tags')) {
                $tagNames = array_map('trim', explode(',', $request->tags));
                $tagIds = [];
                
                foreach ($tagNames as $tagName) {
                    if (empty($tagName)) continue;
                    $tag = BlogTag::firstOrCreate(['name' => $tagName]);
                    $tagIds[] = $tag->id;
                }
                
                $post->tags()->sync($tagIds);
            }

            return response()->json([
                'success' => true,
                'message' => 'Blog post updated successfully',
                'post' => $post->load(['category', 'tags'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update blog post: ' . $e->getMessage()
            ], 500);
        }
    }

    // Delete blog post
    public function destroy($id)
    {
        try {
            $post = Blog::findOrFail($id);
            
            // Delete featured image if exists
            if ($post->featured_image && Storage::disk('public')->exists(str_replace('/storage/', '', $post->featured_image))) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $post->featured_image));
            }
            
            $post->delete();

            return response()->json([
                'success' => true,
                'message' => 'Blog post deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete blog post: ' . $e->getMessage()
            ], 500);
        }
    }

    // Categories Management
    public function getCategories()
    {
        $categories = BlogCategory::withCount('posts')->orderBy('name')->get();
        return response()->json($categories);
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:blog_categories,name',
            'description' => 'nullable|string'
        ]);

        $category = BlogCategory::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully',
            'category' => $category
        ]);
    }

    public function destroyCategory($id)
    {
        try {
            $category = BlogCategory::findOrFail($id);
            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete category: ' . $e->getMessage()
            ], 500);
        }
    }

    // Tags Management
    public function getTags()
    {
        $tags = BlogTag::withCount('posts')->orderBy('name')->get();
        return response()->json($tags);
    }

    public function storeTag(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:blog_tags,name'
        ]);

        $tag = BlogTag::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tag created successfully',
            'tag' => $tag
        ]);
    }

    public function destroyTag($id)
    {
        try {
            $tag = BlogTag::findOrFail($id);
            $tag->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tag deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete tag: ' . $e->getMessage()
            ], 500);
        }
    }
}
