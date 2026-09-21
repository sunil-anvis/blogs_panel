@extends('layouts.app')
@section('title', 'Dashboard')
@section('topbar-title', 'Dashboard')

@section('content')
<div class="page-header">
    <div>
        <h1>Welcome back</h1>
        <p>Here's what's happening with your blog services today.</p>
    </div>
    <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary">+ New Blog Post</a>
</div>

<div class="stats">
    <div class="stat-card">
        <div class="stat-icon purple">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/></svg>
        </div>
        <div>
            <div class="stat-value">{{ $totalCompanies }}</div>
            <div class="stat-label">Total Companies</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 22h16a2 2 0 002-2V4a2 2 0 00-2-2H8a2 2 0 00-2 2v2"/><path d="M4 22a2 2 0 01-2-2v-7"/><path d="M4 13H2"/><path d="M8 6h8"/><path d="M8 10h8"/><path d="M8 14h4"/></svg>
        </div>
        <div>
            <div class="stat-value">{{ $totalBlogs }}</div>
            <div class="stat-label">Total Blogs</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon amber">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div>
            <div class="stat-value">{{ $publishedBlogs }}</div>
            <div class="stat-label">Published Blogs</div>
        </div>
    </div>
</div>

<div class="dash-grid">
    {{-- Recent Companies --}}
    <div class="card">
        <div class="card-header">
            <h2>Recent Companies</h2>
            <a href="{{ route('admin.companies.index') }}" class="btn btn-ghost btn-sm">View All</a>
        </div>
        <div class="table-wrap">
            @if($recentCompanies->isEmpty())
                <div class="empty-state" style="padding:40px 20px">
                    <p style="color:var(--text-muted);margin-bottom:16px">No companies yet.</p>
                    <a href="{{ route('admin.companies.create') }}" class="btn btn-primary btn-sm">Add Company</a>
                </div>
            @else
            <table>
                <thead><tr><th>#</th><th>Name</th><th>Blogs</th><th></th></tr></thead>
                <tbody>
                @foreach($recentCompanies as $c)
                <tr>
                    <td class="text-muted text-sm">{{ $c->id }}</td>
                    <td><strong>{{ $c->name }}</strong></td>
                    <td><span class="badge badge-purple">{{ $c->blogs_count }}</span></td>
                    <td><a href="{{ route('admin.companies.edit', $c) }}" class="btn btn-ghost btn-sm">Edit</a></td>
                </tr>
                @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>

    {{-- Recent Blogs --}}
    <div class="card">
        <div class="card-header">
            <h2>Recent Blogs</h2>
            <a href="{{ route('admin.blogs.index') }}" class="btn btn-ghost btn-sm">View All</a>
        </div>
        <div class="table-wrap">
            @if($recentBlogs->isEmpty())
                <div class="empty-state" style="padding:40px 20px">
                    <p style="color:var(--text-muted);margin-bottom:16px">No blogs yet.</p>
                    <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary btn-sm">Add Blog</a>
                </div>
            @else
            <table>
                <thead><tr><th>Title</th><th>Company</th><th>Published</th><th>Status</th></tr></thead>
                <tbody>
                @foreach($recentBlogs as $b)
                <tr>
                    <td><strong>{{ Str::limit(strip_tags($b->title), 30) }}</strong></td>
                    <td class="text-muted text-sm">{{ $b->company->name ?? '—' }}</td>
                    <td class="text-sm">
                        @if($b->publish_at)
                            {{ $b->publish_at->format('d M Y') }}
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $isPublished = $b->is_active && $b->publish_at && $b->publish_at->lte(now());
                        @endphp
                        @if($isPublished)
                            <span class="badge badge-success">Published</span>
                        @elseif($b->is_active)
                            <span class="badge badge-purple">Active</span>
                        @else
                            <span class="badge badge-danger">Inactive</span>
                        @endif
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    .dash-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
    @media (max-width: 768px) { .dash-grid { grid-template-columns: 1fr; } }
</style>
@endpush
@endsection
