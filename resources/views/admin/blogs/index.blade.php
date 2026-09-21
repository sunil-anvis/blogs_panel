@extends('layouts.app')
@section('title', 'Blogs')
@section('topbar-title', 'Blogs')

@section('content')
<div class="page-header">
    <div>
        <div class="breadcrumb">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <span class="sep">/</span>
            <span>Blogs</span>
        </div>
        <h1>Blogs</h1>
        <p>Create and manage all blog posts.</p>
    </div>
    <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Add Blog
    </a>
</div>

{{-- Search & Filters --}}
<div class="card" style="margin-bottom:24px">
    <div class="card-body" style="padding:18px 24px">
        <form method="GET" action="{{ route('admin.blogs.index') }}" id="filter-form">
            <div class="filter-bar">
                {{-- Search --}}
                <div class="search-wrap">
                    <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input type="text" name="search" class="form-control search-input" placeholder="Search by title or subtitle..." value="{{ request('search') }}">
                </div>

                {{-- Company Filter --}}
                <select name="company_id" class="form-control filter-select" onchange="this.form.submit()">
                    <option value="">All Companies</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>

                {{-- Status Filter --}}
                <select name="status" class="form-control filter-select" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                    <option value="active"    {{ request('status') === 'active'    ? 'selected' : '' }}>Active (unpublished)</option>
                    <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="inactive"  {{ request('status') === 'inactive'  ? 'selected' : '' }}>Inactive</option>
                </select>

                {{-- Search Button --}}
                <button type="submit" class="btn btn-primary" style="white-space:nowrap">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    Search
                </button>

                @if(request()->hasAny(['search','company_id','status']))
                    <a href="{{ route('admin.blogs.index') }}" class="btn btn-ghost" style="white-space:nowrap">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Active filters summary --}}
@if(request()->hasAny(['search','company_id','status']))
<div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:16px">
    <span style="font-size:13px;color:var(--text-muted)">Filters:</span>
    @if(request('search'))
        <span class="filter-chip">Search: <strong>{{ request('search') }}</strong></span>
    @endif
    @if(request('company_id'))
        <span class="filter-chip">Company: <strong>{{ $companies->firstWhere('id', request('company_id'))?->name }}</strong></span>
    @endif
    @if(request('status'))
        <span class="filter-chip">Status: <strong>{{ ucfirst(request('status')) }}</strong></span>
    @endif
    <span style="font-size:13px;color:var(--text-muted);margin-left:4px">— {{ $blogs->total() }} result{{ $blogs->total() != 1 ? 's' : '' }}</span>
</div>
@endif

<div class="card">
    <div class="card-header">
        <h2>
            All Blogs
            <span class="badge badge-purple" style="margin-left:8px">{{ $blogs->total() }}</span>
        </h2>
        <span style="font-size:13px;color:var(--text-muted)">
            Showing {{ $blogs->firstItem() ?? 0 }}–{{ $blogs->lastItem() ?? 0 }} of {{ $blogs->total() }}
        </span>
    </div>
    <div class="table-wrap">
        @if($blogs->isEmpty())
            <div class="empty-state">
                <svg width="54" height="54" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom:16px"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                <h3>No blogs found</h3>
                <p>{{ request()->hasAny(['search','company_id','status']) ? 'No blogs match your current filters. Try adjusting them.' : 'Start creating compelling blog posts.' }}</p>
                @if(!request()->hasAny(['search','company_id','status']))
                    <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary">+ Add Blog</a>
                @else
                    <a href="{{ route('admin.blogs.index') }}" class="btn btn-ghost">Clear Filters</a>
                @endif
            </div>
        @else
        <table>
            <thead>
                <tr>
                    <th>Blog</th>
                    <th>Company</th>
                    <th>Status</th>
                    <th>Publish Date</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($blogs as $blog)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:12px">
                            @if($blog->image)
                                <img src="{{ asset($blog->image) }}" class="img-preview" alt="">
                            @else
                                <div class="no-img">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                </div>
                            @endif
                            <div>
                                <strong>{{ Str::limit(strip_tags($blog->title), 40) }}</strong>
                                @if($blog->subtitle)
                                    <div class="text-muted text-sm">{{ Str::limit(strip_tags($blog->subtitle), 45) }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        <span style="display:inline-flex;align-items:center;gap:6px;font-size:13px">
                            <span style="width:24px;height:24px;border-radius:6px;background:linear-gradient(135deg,var(--accent),#818cf8);display:inline-flex;align-items:center;justify-content:center;font-weight:700;font-size:10px;color:#fff;flex-shrink:0">{{ strtoupper(substr($blog->company->name ?? '?', 0, 1)) }}</span>
                            {{ $blog->company->name ?? '—' }}
                        </span>
                    </td>
                    <td>
                        @php $isPublished = $blog->is_active && $blog->publish_at && $blog->publish_at->lte(now()); @endphp
                        @php $isScheduled = $blog->is_active && $blog->publish_at && $blog->publish_at->gt(now()); @endphp
                        @if($isPublished)
                            <span class="badge badge-success">Published</span>
                        @elseif($isScheduled)
                            <span class="badge badge-purple">Scheduled</span>
                        @elseif($blog->is_active)
                            <span class="badge badge-purple">Active</span>
                        @else
                            <span class="badge badge-danger">Inactive</span>
                        @endif
                    </td>
                    <td class="text-muted text-sm">
                        {{ $blog->publish_at ? $blog->publish_at->format('d M Y, H:i') : '—' }}
                    </td>
                    <td class="text-muted text-sm">{{ $blog->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.blogs.edit', $blog) }}" class="btn btn-ghost btn-sm">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                Edit
                            </a>
                            <form action="{{ route('admin.blogs.destroy', $blog) }}" method="POST"
                                  onsubmit="return confirm('Delete this blog post? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" title="Delete blog">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    {{-- Pagination --}}
    @if($blogs->hasPages())
    <div class="pagination-wrap">
        <span class="pagination-info">
            Page {{ $blogs->currentPage() }} of {{ $blogs->lastPage() }}
        </span>
        <div class="pagination-links">
            {{-- Previous --}}
            @if($blogs->onFirstPage())
                <span class="page-btn disabled">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                    Prev
                </span>
            @else
                <a href="{{ $blogs->previousPageUrl() }}" class="page-btn">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                    Prev
                </a>
            @endif

            {{-- Page Numbers --}}
            @foreach($blogs->getUrlRange(max(1, $blogs->currentPage()-2), min($blogs->lastPage(), $blogs->currentPage()+2)) as $page => $url)
                @if($page == $blogs->currentPage())
                    <span class="page-btn active">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                @endif
            @endforeach

            {{-- Next --}}
            @if($blogs->hasMorePages())
                <a href="{{ $blogs->nextPageUrl() }}" class="page-btn">
                    Next
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
            @else
                <span class="page-btn disabled">
                    Next
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                </span>
            @endif
        </div>
    </div>
    @endif
</div>

@push('styles')
<style>
    .filter-bar {
        display: flex;
        gap: 12px;
        align-items: center;
        flex-wrap: wrap;
    }
    .search-wrap {
        position: relative;
        flex: 1;
        min-width: 200px;
    }
    .search-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        pointer-events: none;
    }
    .search-input { padding-left: 42px !important; }
    .filter-select { width: auto; min-width: 160px; }
    .filter-chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: var(--accent-light);
        color: var(--accent);
        border: 1px solid #c7d2fe;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
    }

    /* Pagination */
    .pagination-wrap {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 24px;
        border-top: 1px solid var(--border);
        flex-wrap: wrap;
        gap: 12px;
    }
    .pagination-info { font-size: 13px; color: var(--text-muted); }
    .pagination-links { display: flex; align-items: center; gap: 6px; }
    .page-btn {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 7px 13px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        text-decoration: none;
        border: 1px solid var(--border);
        color: var(--text-main);
        background: var(--surface);
        transition: all .2s ease;
        cursor: pointer;
    }
    .page-btn:hover:not(.disabled):not(.active) {
        background: var(--accent-light);
        border-color: var(--accent);
        color: var(--accent);
    }
    .page-btn.active {
        background: var(--accent);
        border-color: var(--accent);
        color: #fff;
        font-weight: 600;
    }
    .page-btn.disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }
</style>
@endpush
@endsection
