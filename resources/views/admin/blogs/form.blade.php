@extends('layouts.app')
@section('title', isset($blog) ? 'Edit Blog' : 'Add Blog')
@section('topbar-title', isset($blog) ? 'Edit Blog' : 'Add Blog')

@section('content')
<div class="page-header">
    <div>
        <div class="breadcrumb">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <span class="sep">/</span>
            <a href="{{ route('admin.blogs.index') }}">Blogs</a>
            <span class="sep">/</span>
            <span>{{ isset($blog) ? 'Edit' : 'Create' }}</span>
        </div>
        <h1>{{ isset($blog) ? 'Edit Blog Post' : 'Add New Blog Post' }}</h1>
        <p>{{ isset($blog) ? 'Update the blog details below.' : 'Fill in the details to create a new blog post.' }}</p>
    </div>
    <a href="{{ route('admin.blogs.index') }}" class="btn btn-ghost">&larr; Back</a>
</div>

@if($errors->any())
    <div class="alert alert-danger">
        <div>
            <strong>Please fix the following errors:</strong>
            <ul style="margin:6px 0 0 16px">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    </div>
@endif

<form id="blog-form"
      action="{{ isset($blog) ? route('admin.blogs.update', $blog) : route('admin.blogs.store') }}"
      method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($blog)) @method('PUT') @endif

    <div class="form-layout">

        {{-- Left Column --}}
        <div class="form-col-main">

            <div class="card">
                <div class="card-header"><h2>Blog Details</h2></div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label" for="title">Title <span>*</span></label>
                        <textarea id="title" name="title" class="form-control">{{ old('title', $blog->title ?? '') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="subtitle">Subtitle</label>
                        <textarea id="subtitle" name="subtitle" class="form-control">{{ old('subtitle', $blog->subtitle ?? '') }}</textarea>
                    </div>
                    <div class="form-group" style="margin-bottom:0">
                        <label class="form-label" for="content">Content</label>
                        <textarea id="content" name="content" class="form-control" style="min-height:300px">{{ old('content', $blog->content['html'] ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h2>FAQs</h2></div>
                <div class="card-body">
                    <div class="form-group" style="margin-bottom:0">
                        <label class="form-label" for="faqs">Frequently Asked Questions</label>
                        <textarea id="faqs" name="faqs" class="form-control" style="min-height:200px">{{ old('faqs', $blog->faqs['html'] ?? '') }}</textarea>
                    </div>
                </div>
            </div>

        </div>

        {{-- Right Column --}}
        <div class="form-col-side">

            <div class="card">
                <div class="card-header"><h2>Publish Settings</h2></div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label" for="company_id">Company <span>*</span></label>
                        <select id="company_id" name="company_id" class="form-control" required>
                            <option value="">— Select Company —</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}"
                                    {{ old('company_id', $blog->company_id ?? '') == $company->id ? 'selected' : '' }}>
                                    {{ $company->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Publish Date</label>
                        <input type="hidden" id="publish_at" name="publish_at" value="{{ old('publish_at', isset($blog) && $blog->publish_at ? $blog->publish_at->format('Y-m-d') : '') }}">
                        <button type="button" class="form-control" style="text-align: left; background: var(--surface2); cursor: pointer; display: flex; align-items: center; justify-content: space-between;" onclick="document.getElementById('publish-modal-form').style.display='flex'">
                            <span id="publish-btn-text">
                                {{ old('publish_at', isset($blog) && $blog->publish_at ? $blog->publish_at->format('d M Y') : 'Publish Date') }}
                            </span>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        </button>
                    </div>

                    <!-- Active Status Group -->
                    <div class="form-group" style="margin-bottom:0">
                        <label class="form-label">Active Status</label>
                        <div class="toggle-wrap">
                            <label class="toggle">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1"
                                    {{ old('is_active', $blog->is_active ?? true) ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                            <span class="toggle-label">Mark as Active</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Image --}}
            <div class="card">
                <div class="card-header"><h2>Featured Image</h2></div>
                <div class="card-body">
                    @if(isset($blog) && $blog->image)
                        <div id="current-img-wrap" style="margin-bottom:14px">
                            <img id="current-img" src="{{ asset($blog->image) }}" alt="Current image"
                                 style="width:100%;border-radius:10px;border:1px solid var(--border);max-height:200px;object-fit:cover;transition:opacity .3s">
                            <p id="current-img-label" class="text-muted text-sm mt-2">Current image — upload below to replace.</p>
                        </div>
                    @endif

                    <div class="form-group" style="margin-bottom:0">
                        <label class="form-label" for="image">{{ isset($blog) && $blog->image ? 'Replace Image' : 'Upload Image' }}</label>
                        <input id="image" type="file" name="image" class="form-control" accept="image/*"
                               onchange="previewNewImage(this)">
                        <img id="img-preview-box" src="#" alt="New preview"
                             style="display:none;width:100%;border-radius:10px;margin-top:12px;max-height:180px;object-fit:cover;border:2px dashed var(--accent);padding:3px">
                        <p id="new-img-label" class="text-muted text-sm mt-2" style="display:none">New image selected &mdash; will replace current.</p>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary" style="flex:1">
                    {{ isset($blog) ? 'Save Changes' : 'Create Blog Post' }}
                </button>
                <a href="{{ route('admin.blogs.index') }}" class="btn btn-ghost">Cancel</a>
            </div>

        </div>
    </div>
</form>

{{-- Publish Date Modal (Form version) --}}
<div id="publish-modal-form" class="modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:100; align-items:center; justify-content:center; backdrop-filter:blur(4px);">
    <div class="modal-content card" style="width: 100%; max-width: 400px; margin: 20px; box-shadow: var(--shadow-lg);">
        <div class="card-header" style="border-bottom: 1px solid var(--border); padding: 16px 24px;">
            <h2 style="font-size:16px;">Set Publish Date</h2>
        </div>
        <div class="card-body" style="padding: 24px;">
            <div class="form-group" style="margin-bottom:0">
                <label class="form-label">Select Date</label>
                <input type="date" id="modal-publish-input" class="form-control">
                <p class="text-muted text-sm mt-2">Cannot select dates older than 1 month or more than 2 months ahead.</p>
            </div>
        </div>
        <div style="padding: 16px 24px; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; gap: 12px;">
            <button type="button" class="btn btn-ghost" onclick="document.getElementById('publish-modal-form').style.display='none'">Cancel</button>
            <button type="button" class="btn btn-primary" onclick="confirmFormDate()">Set Date</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.2/classic/ckeditor.js"></script>
<style>
    .form-layout { display: grid; grid-template-columns: 1fr 360px; gap: 24px; align-items: start; max-width: 1200px; margin: 0 auto; }
    @media (max-width: 960px) { .form-layout { grid-template-columns: 1fr; } }
    .form-col-main, .form-col-side { display: flex; flex-direction: column; gap: 24px; }

    /* CKEditor light theme overrides */
    .ck.ck-editor { border: 1px solid var(--border) !important; border-radius: 8px !important; overflow: hidden; box-shadow: var(--shadow-sm) !important; }
    .ck-editor__editable { min-height: 200px; background: #fff !important; color: var(--text-main) !important; }
    .ck-toolbar { background: var(--surface2) !important; border-bottom: 1px solid var(--border) !important; border-radius: 0 !important; }
    .ck-button { color: var(--text-main) !important; border-radius: 6px !important; }
    .ck-button:hover { background: var(--accent-light) !important; }
    .ck-button.ck-on { background: var(--accent-light) !important; color: var(--accent) !important; }
    .ck-dropdown__panel { background: #fff !important; border: 1px solid var(--border) !important; box-shadow: var(--shadow-md) !important; border-radius: 8px !important; }

    @media (max-width: 960px) {
        .form-layout { grid-template-columns: 1fr; }
        .form-col-side { order: -1; }
    }
</style>
<script>
    const editors = {};

    function initEditor(id, toolbar) {
        ClassicEditor.create(document.querySelector('#' + id), { toolbar: toolbar })
            .then(editor => { editors[id] = editor; })
            .catch(err => console.error('CKEditor #' + id, err));
    }

    const minBar  = ['heading', '|', 'bold', 'italic', 'link', '|', 'undo', 'redo'];
    const fullBar = ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|', 'undo', 'redo'];

    initEditor('title',    minBar);
    initEditor('subtitle', minBar);
    initEditor('content',  fullBar);
    initEditor('faqs',     fullBar);

    // *** CRITICAL: Sync CKEditor data back to hidden textareas before submit ***
    // CKEditor hides the original textarea and uses its own contenteditable div.
    // Without this, all editor fields (title, subtitle, content, faqs) submit as empty.
    document.getElementById('blog-form').addEventListener('submit', function() {
        Object.keys(editors).forEach(function(id) {
            const el = document.getElementById(id);
            if (el && editors[id]) {
                el.value = editors[id].getData();
            }
        });
    });

    // Show new image preview and dim the old one to indicate it will be replaced
    function previewNewImage(input) {
        const previewBox = document.getElementById('img-preview-box');
        const newLabel   = document.getElementById('new-img-label');
        const currentImg = document.getElementById('current-img');
        const currentLbl = document.getElementById('current-img-label');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewBox.src = e.target.result;
                previewBox.style.display = 'block';
                if (newLabel)   newLabel.style.display = 'block';
                if (currentImg) currentImg.style.opacity = '0.35';
                if (currentLbl) currentLbl.textContent  = 'This will be replaced.';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Apply min/max limits to modal publish date
    document.addEventListener("DOMContentLoaded", function() {
        const modalInput = document.getElementById('modal-publish-input');
        const hiddenInput = document.getElementById('publish_at');
        if (modalInput) {
            const now = new Date();
            
            const minDate = new Date();
            minDate.setMonth(now.getMonth() - 1);
            
            const maxDate = new Date();
            maxDate.setMonth(now.getMonth() + 2);
            
            modalInput.min = minDate.toISOString().slice(0, 10);
            modalInput.max = maxDate.toISOString().slice(0, 10);
            
            // Set initial value to modal if hidden input has one
            if (hiddenInput && hiddenInput.value) {
                modalInput.value = hiddenInput.value;
            }
        }
    });

    function confirmFormDate() {
        const modalInput = document.getElementById('modal-publish-input');
        const hiddenInput = document.getElementById('publish_at');
        const btnText = document.getElementById('publish-btn-text');
        
        const value = modalInput.value;
        
        if (value) {
            if (value < modalInput.min) return alert("Date cannot be older than 1 month.");
            if (value > modalInput.max) return alert("Date cannot be more than 2 months in the future.");
            
            hiddenInput.value = value;
            
            // Format for display: d M Y
            const d = new Date(value);
            const formatted = d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
            btnText.textContent = formatted;
        } else {
            hiddenInput.value = '';
            btnText.textContent = 'Select Date';
        }
        
        document.getElementById('publish-modal-form').style.display = 'none';
    }
</script>
@endpush
