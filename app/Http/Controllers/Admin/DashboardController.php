<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Company;

class DashboardController extends Controller
{
    public function index()
    {
        $totalCompanies  = Company::count();
        $totalBlogs      = Blog::count();
        $publishedBlogs  = Blog::where('is_active', true)->where('publish_at', '<=', now())->count();
        $recentCompanies = Company::withCount('blogs')->latest()->limit(5)->get();
        $recentBlogs     = Blog::with('company')->latest()->limit(5)->get();

        return view('admin.dashboard', compact(
            'totalCompanies', 'totalBlogs', 'publishedBlogs',
            'recentCompanies', 'recentBlogs'
        ));
    }
}
