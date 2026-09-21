<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Blog::with('company')->latest();

            // ── Public API: only active + published ──
            if ($request->is('api/public/blogs')) {
                $query->where('is_active', true)
                      ->where('publish_at', '<=', now());
                $blogs = $query->get();
                return response()->json(['success' => true, 'message' => 'Blog fetched successfully', 'data' => $blogs], 200);
            }

            // ── Authenticated API: return all ──
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Blog fetched successfully',
                    'data'    => $query->get(),
                ], 200);
            }

            // ── Web Admin: search + filters + pagination ──
            $companies = \App\Models\Company::orderBy('name')->get();

            // Search by title / subtitle
            if ($search = $request->input('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('subtitle', 'like', "%{$search}%");
                });
            }

            // Filter by company
            if ($companyId = $request->input('company_id')) {
                $query->where('company_id', $companyId);
            }

            // Filter by status
            $status = $request->input('status');
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($status === 'published') {
                $query->where('is_active', true)->where('publish_at', '<=', now());
            } elseif ($status === 'scheduled') {
                $query->where('is_active', true)->where('publish_at', '>', now());
            }

            $blogs = $query->paginate(10)->withQueryString();

            return view('admin.blogs.index', compact('blogs', 'companies'));
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['success' => false, 'message' => 'Failed to fetch blogs', 'error' => $e->getMessage()], 500);
            }
            return back()->with('error', 'Failed to fetch blogs: ' . $e->getMessage());
        }
    }

    public function create()
    {
        $companies = \App\Models\Company::orderBy('name')->get();
        return view('admin.blogs.form', compact('companies'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'company_id' => 'required|exists:companies,id',
                'title'      => 'required|string',
                'subtitle'   => 'nullable|string',
                'image'      => 'nullable|image|max:2048',
                'content'    => 'nullable|string',
                'faqs'       => 'nullable|string',
                'is_active'  => 'nullable|boolean',
                'publish_at' => 'nullable|date',
            ]);

            // Wrap string HTML in an associative array to satisfy JSON column constraints
            $validated['content']   = $validated['content'] ? ['html' => $validated['content']] : null;
            $validated['faqs']      = $validated['faqs']    ? ['html' => $validated['faqs']]    : null;
            $validated['is_active'] = $request->has('is_active') ? (bool) $request->is_active : false;

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('blogs', 'public');
                $validated['image'] = 'storage/' . $path;
            }

            $blog = Blog::create($validated);

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Blog created successfully',
                    'data'    => $blog->load('company'),
                ], 201);
            }

            return redirect()->route('admin.blogs.index')
                             ->with('success', 'Blog post created successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $e->errors(),
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create blog',
                    'error'   => $e->getMessage(),
                ], 500);
            }
            return back()->with('error', 'Failed to create blog: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Request $request, Blog $blog)
    {
        try {
            $blog->load('company');
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Blog fetched successfully',
                    'data'    => $blog,
                ], 200);
            }
            return view('admin.blogs.form', compact('blog'));
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch blog',
                    'error'   => $e->getMessage(),
                ], 500);
            }
            return back()->with('error', 'Failed to fetch blog: ' . $e->getMessage());
        }
    }

    public function edit(Blog $blog)
    {
        $companies = \App\Models\Company::orderBy('name')->get();
        return view('admin.blogs.form', compact('blog', 'companies'));
    }

    public function update(Request $request, Blog $blog)
    {
        try {
            $validated = $request->validate([
                'company_id' => 'sometimes|exists:companies,id',
                'title'      => 'sometimes|required|string',
                'subtitle'   => 'nullable|string',
                'image'      => 'nullable|image|max:2048',
                'content'    => 'nullable|string',
                'faqs'       => 'nullable|string',
                'is_active'  => 'nullable|boolean',
                'publish_at' => 'nullable|date',
            ]);

            // Wrap string HTML in an associative array to satisfy JSON column constraints
            if (array_key_exists('content', $validated)) {
                $validated['content'] = $validated['content'] ? ['html' => $validated['content']] : null;
            }
            if (array_key_exists('faqs', $validated)) {
                $validated['faqs'] = $validated['faqs'] ? ['html' => $validated['faqs']] : null;
            }
            
            if ($request->has('is_active')) {
                $validated['is_active'] = (bool) $request->is_active;
            }

            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($blog->image && str_starts_with($blog->image, 'storage/')) {
                    $oldPath = str_replace('storage/', '', $blog->image);
                    Storage::disk('public')->delete($oldPath);
                }

                $path = $request->file('image')->store('blogs', 'public');
                $validated['image'] = 'storage/' . $path;
            }

            $blog->update($validated);

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Blog updated successfully',
                    'data'    => $blog->load('company'),
                ], 200);
            }

            return redirect()->route('admin.blogs.index')
                             ->with('success', 'Blog post updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors'  => $e->errors(),
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update blog',
                    'error'   => $e->getMessage(),
                ], 500);
            }
            return back()->with('error', 'Failed to update blog: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Request $request, Blog $blog)
    {
        try {
            if ($blog->image && str_starts_with($blog->image, 'storage/')) {
                $oldPath = str_replace('storage/', '', $blog->image);
                Storage::disk('public')->delete($oldPath);
            }

            $blog->delete();
            
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Blog deleted successfully',
                ], 200);
            }

            return redirect()->route('admin.blogs.index')
                             ->with('success', 'Blog post deleted successfully!');
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete blog',
                    'error'   => $e->getMessage(),
                ], 500);
            }
            return back()->with('error', 'Failed to delete blog: ' . $e->getMessage());
        }
    }
}
