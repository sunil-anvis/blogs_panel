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
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<form id="blog-form" action="{{ isset($blog) ? route('admin.blogs.update', $blog) : route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($blog)) @method('PUT') @endif

    <div class="form-layout">

        {{-- ── Main Column ── --}}
        <div class="form-col-main">

            <div class="card">
                <div class="card-header"><h2>Blog Details</h2></div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label" for="title">Title <span>*</span></label>
                        <textarea id="title" name="title" class="form-control">{{ old('title', $blog->title ?? '') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="slug">Slug</label>
                        <input type="text" id="slug" name="slug" class="form-control" placeholder="auto-generated-from-title" value="{{ old('slug', $blog->slug ?? '') }}">
                        <p class="text-muted text-sm mt-2">Leave blank to auto-generate from the title.</p>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="subtitle">Excerpt</label>
                        <textarea id="subtitle" name="subtitle" class="form-control">{{ old('subtitle', $blog->subtitle ?? '') }}</textarea>
                    </div>
                    <div class="form-group" style="margin-bottom:0">
                        <label class="form-label" for="content">Content</label>
                        <textarea id="content" name="content" class="form-control">{{ old('content', $blog->content['html'] ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h2>FAQs</h2></div>
                <div class="card-body">
                    <div class="form-group" style="margin-bottom:0">
                        <label class="form-label" for="faqs">Frequently Asked Questions</label>
                        <textarea id="faqs" name="faqs" class="form-control">{{ old('faqs', $blog->faqs['html'] ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h2>SEO</h2></div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label" for="meta_title">Meta Title <span class="text-muted" style="font-weight:400;font-size:12px;">(max 160 chars)</span></label>
                        <input type="text" id="meta_title" name="meta_title" class="form-control" style="border: 1px solid #ced4da;" maxlength="160" value="{{ old('meta_title', $blog->meta_title ?? '') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="meta_description">Meta Description</label>
                        <textarea id="meta_description" name="meta_description" class="form-control" rows="3" style="border: 1px solid #ced4da; resize:vertical;">{{ old('meta_description', $blog->meta_description ?? '') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="canonical_url">Canonical URL</label>
                        <input type="url" id="canonical_url" name="canonical_url" class="form-control" style="border: 1px solid #ced4da;" placeholder="https://example.com/canonical-page" value="{{ old('canonical_url', $blog->canonical_url ?? '') }}">
                    </div>
                    <div class="form-group" style="margin-bottom:0">
                        <label class="form-label" for="schema_markup">Schema Markup</label>
                        <textarea id="schema_markup" name="schema_markup" class="form-control" rows="8" style="border: 1px solid #ced4da; resize:vertical;font-family:monospace;font-size:13px;line-height:1.6;" spellcheck="false">{{ old('schema_markup', is_array($blog->schema_markup ?? null) ? ($blog->schema_markup['raw'] ?? '') : ($blog->schema_markup ?? '')) }}</textarea>
                        <p class="text-muted text-sm mt-2">Paste your full schema markup code here (including <code>&lt;script&gt;</code> tags if needed).</p>
                    </div>
                </div>
            </div>

        </div>

        {{-- ── Sidebar ── --}}
        <div class="form-col-side">

            <div class="card">
                <div class="card-header"><h2>Publish Settings</h2></div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label" for="company_id">Company <span>*</span></label>
                        <select id="company_id" name="company_id" class="form-control" required>
                            <option value=""> — Select Company — </option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ old('company_id', $blog->company_id ?? '') == $company->id ? 'selected' : '' }}>
                                    {{ $company->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Publish Date</label>
                        <input type="hidden" id="publish_at" name="publish_at" style="border: 1px solid #ced4da;" value="{{ old('publish_at', isset($blog) && $blog->publish_at ? $blog->publish_at->format('Y-m-d') : '') }}">
                        <button type="button" class="form-control publish-date-button" onclick="document.getElementById('publish-modal-form').style.display='flex'">
                            <span id="publish-btn-text">{{ old('publish_at', isset($blog) && $blog->publish_at ? $blog->publish_at->format('d M Y') : 'Publish Date') }}</span>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted">
                                <rect x="3" y="4" width="18" height="18" rx="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/>
                                <line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                        </button>
                    </div>
                    <div class="form-group" style="margin-bottom:0">
                        <label class="form-label">Active Status</label>
                        <div class="toggle-wrap">
                            <label class="toggle">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $blog->is_active ?? true) ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                            <span class="toggle-label">Mark as Active</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h2>Featured Image</h2></div>
                <div class="card-body">
                    @if(isset($blog) && $blog->image)
                        <div id="current-img-wrap" style="margin-bottom:14px">
                            <img id="current-img" src="{{ asset($blog->image) }}" alt="Current image"
                                style="width:100%;border-radius:10px;border:1px solid var(--border);max-height:200px;object-fit:cover;transition:opacity .3s;">
                            <p id="current-img-label" class="text-muted text-sm mt-2">Current image — upload below to replace.</p>
                        </div>
                    @endif
                    <div class="form-group" style="margin-bottom:0">
                        <label class="form-label" for="image">{{ isset($blog) && $blog->image ? 'Replace Image' : 'Upload Image' }}</label>
                        <input id="image" type="file" name="image" class="form-control" accept="image/*" onchange="previewNewImage(this)">
                        <img id="img-preview-box" src="#" alt="New preview"
                            style="display:none;width:100%;border-radius:10px;margin-top:12px;max-height:180px;object-fit:cover;border:2px dashed var(--accent);padding:3px;">
                        <p id="new-img-label" class="text-muted text-sm mt-2" style="display:none">New image selected &mdash; will replace current.</p>
                    </div>
                    <div class="form-group" style="margin-top:14px;margin-bottom:0">
                        <label class="form-label" for="alt_text">Image Alt Text</label>
                        <input type="text" id="alt_text" name="alt_text" class="form-control" placeholder="Describe the image for accessibility & SEO" style="border: 1px solid #ced4da;" value="{{ old('alt_text', $blog->alt_text ?? '') }}">
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary" style="flex:1">{{ isset($blog) ? 'Save Changes' : 'Publish Blog' }}</button>
                <a href="{{ route('admin.blogs.index') }}" class="btn btn-ghost">Cancel</a>
            </div>

        </div>
    </div>
</form>

{{-- ── Publish Date Modal ── --}}
<div id="publish-modal-form" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:100;align-items:center;justify-content:center;backdrop-filter:blur(4px);">
    <div class="modal-content card" style="width:100%;max-width:400px;margin:20px;box-shadow:var(--shadow-lg);">
        <div class="card-header" style="border-bottom:1px solid var(--border);padding:16px 24px;">
            <h2 style="font-size:16px;">Set Publish Date</h2>
        </div>
        <div class="card-body" style="padding:24px;">
            <div class="form-group" style="margin-bottom:0">
                <label class="form-label">Select Date</label>
                <input type="date" id="modal-publish-input" class="form-control">
                <p class="text-muted text-sm mt-2">Cannot select dates older than 1 month or more than 2 months ahead.</p>
            </div>
        </div>
        <div style="padding:16px 24px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:12px;">
            <button type="button" class="btn btn-ghost" onclick="document.getElementById('publish-modal-form').style.display='none'">Cancel</button>
            <button type="button" class="btn btn-primary" onclick="confirmFormDate()">Set Date</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.2/classic/ckeditor.js"></script>

<style>
.form-layout { display:grid; grid-template-columns:minmax(0,1fr) 360px; gap:24px; align-items:start; max-width:1200px; margin:0 auto; }
.form-col-main, .form-col-side { display:flex; flex-direction:column; gap:24px; min-width:0; }
.ck.ck-editor { width:100% !important; max-width:100% !important; box-sizing:border-box; border:1px solid var(--border) !important; border-radius:8px !important; overflow:hidden; box-shadow:var(--shadow-sm) !important; }
.ck-editor__editable { width:100% !important; max-width:100% !important; box-sizing:border-box; background:#fff !important; color:var(--text-main) !important; overflow-wrap:anywhere; word-break:break-word; }
#title    + .ck-editor .ck-editor__editable { min-height:55px  !important; max-height:100px !important; overflow-y:auto; }
#subtitle + .ck-editor .ck-editor__editable { min-height:55px  !important; max-height:100px !important; overflow-y:auto; }
#content  + .ck-editor .ck-editor__editable { min-height:320px !important; max-height:520px !important; overflow-y:auto; }
#faqs     + .ck-editor .ck-editor__editable { min-height:180px !important; max-height:400px !important; overflow-y:auto; }
.ck-toolbar { background:var(--surface2) !important; border-bottom:1px solid var(--border) !important; border-radius:0 !important; }
.ck-button { color:var(--text-main) !important; border-radius:6px !important; }
.ck-button:hover { background:var(--accent-light) !important; }
.ck-button.ck-on { background:var(--accent-light) !important; color:var(--accent) !important; }
.ck-dropdown__panel { background:#fff !important; border:1px solid var(--border) !important; box-shadow:var(--shadow-md) !important; border-radius:8px !important; }
.publish-date-button { text-align:left; background:var(--surface2); cursor:pointer; display:flex; align-items:center; justify-content:space-between; }
@@media (max-width:960px) { .form-layout { grid-template-columns:1fr; } .form-col-side { order:-1; } }
@@media (max-width:600px) { .form-layout { gap:16px; } .form-col-main, .form-col-side { gap:16px; } }
</style>

<script>
const editors  = {};
const slugInput = document.getElementById('slug');
let slugLocked  = slugInput && slugInput.value.trim() !== '';

const titleBar = ['heading','|','bold','italic','link','|','undo','redo'];
const fullBar  = ['heading','|','bold','italic','link','bulletedList','numberedList','blockQuote','|','undo','redo'];

function toSlug(text) {
    return text.toLowerCase()
        .replace(/&[a-z]+;/gi, '').replace(/<[^>]+>/g, '').trim()
        .replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g, '-')
        .replace(/-+/g, '-').replace(/^-+|-+$/g, '');
}

if (slugInput) {
    slugInput.addEventListener('input', () => { slugLocked = slugInput.value.trim() !== ''; });
}

function initEditor(id, toolbar, minH, maxH) {
    const el = document.querySelector('#' + id);
    if (!el) return;
    ClassicEditor.create(el, { toolbar }).then(editor => {
        editors[id] = editor;
        const editable = editor.ui.view.editable.element;
        editable.style.minHeight = minH;
        if (maxH) { editable.style.maxHeight = maxH; editable.style.overflowY = 'auto'; }
        if (id === 'title') {
            if (!editor.getData().trim()) editor.execute('heading', { value: 'heading1' });
            editor.model.document.on('change:data', () => {
                if (slugLocked || !slugInput) return;
                slugInput.value = toSlug(editor.getData().replace(/<[^>]+>/g, ' ').replace(/&[a-z]+;/gi, '').trim());
            });
        }
    }).catch(e => console.error('CKEditor #' + id, e));
}

initEditor('title',    titleBar, '55px',  '100px');
initEditor('subtitle', titleBar, '55px',  '100px');
initEditor('content',  fullBar,  '320px', '520px');
initEditor('faqs',     fullBar,  '180px', '400px');

document.getElementById('blog-form').addEventListener('submit', () => {
    Object.keys(editors).forEach(id => {
        const el = document.getElementById(id);
        if (el && editors[id]) el.value = editors[id].getData();
    });
});

function previewNewImage(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('img-preview-box').src = e.target.result;
        document.getElementById('img-preview-box').style.display = 'block';
        const lbl = document.getElementById('new-img-label');
        if (lbl) lbl.style.display = 'block';
        const cur = document.getElementById('current-img');
        if (cur) cur.style.opacity = '0.35';
        const curLbl = document.getElementById('current-img-label');
        if (curLbl) curLbl.textContent = 'This will be replaced.';
    };
    reader.readAsDataURL(input.files[0]);
}

document.addEventListener('DOMContentLoaded', () => {
    const modalInput  = document.getElementById('modal-publish-input');
    const hiddenInput = document.getElementById('publish_at');
    if (!modalInput) return;
    const now = new Date();
    const min = new Date(); min.setMonth(now.getMonth() - 1);
    const max = new Date(); max.setMonth(now.getMonth() + 2);
    modalInput.min = min.toISOString().slice(0, 10);
    modalInput.max = max.toISOString().slice(0, 10);
    if (hiddenInput && hiddenInput.value) modalInput.value = hiddenInput.value;
});

function confirmFormDate() {
    const modalInput  = document.getElementById('modal-publish-input');
    const hiddenInput = document.getElementById('publish_at');
    const btnText     = document.getElementById('publish-btn-text');
    const value       = modalInput.value;
    if (value) {
        if (value < modalInput.min) return alert('Date cannot be older than 1 month.');
        if (value > modalInput.max) return alert('Date cannot be more than 2 months in the future.');
        hiddenInput.value  = value;
        btnText.textContent = new Date(value).toLocaleDateString('en-GB', { day:'2-digit', month:'short', year:'numeric' });
    } else {
        hiddenInput.value  = '';
        btnText.textContent = 'Select Date';
    }
    document.getElementById('publish-modal-form').style.display = 'none';
}
</script>
@endpush