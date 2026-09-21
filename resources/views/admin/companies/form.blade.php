@extends('layouts.app')
@section('title', isset($company) ? 'Edit Company' : 'Add Company')
@section('topbar-title', isset($company) ? 'Edit Company' : 'Add Company')

@section('content')
<div class="page-header">
    <div>
        <div class="breadcrumb">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <span class="sep"><i class="fas fa-chevron-right"></i></span>
            <a href="{{ route('admin.companies.index') }}">Companies</a>
            <span class="sep"><i class="fas fa-chevron-right"></i></span>
            <span>{{ isset($company) ? 'Edit' : 'Create' }}</span>
        </div>
        <h1>{{ isset($company) ? 'Edit Company' : 'Add New Company' }}</h1>
        <p>{{ isset($company) ? 'Update the company details below.' : 'Fill in the details to create a new company.' }}</p>
    </div>
    <a href="{{ route('admin.companies.index') }}" class="btn btn-ghost">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

@if($errors->any())
    <div class="alert alert-danger">
        <i class="fas fa-triangle-exclamation"></i>
        <div>
            <strong>Please fix the following errors:</strong>
            <ul style="margin:4px 0 0 16px">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    </div>
@endif

<div class="card" style="max-width:600px">
    <div class="card-header">
        <h2><i class="fas fa-building" style="color:var(--accent);margin-right:8px"></i>
            {{ isset($company) ? 'Edit Company' : 'Company Details' }}
        </h2>
    </div>
    <div class="card-body">
        <form action="{{ isset($company) ? route('admin.companies.update', $company) : route('admin.companies.store') }}"
              method="POST">
            @csrf
            @if(isset($company)) @method('PUT') @endif

            <div class="form-group">
                <label class="form-label" for="name">Company Name <span>*</span></label>
                <input id="name" type="text" name="name" class="form-control"
                       value="{{ old('name', $company->name ?? '') }}"
                       placeholder="e.g. Acme Corporation" required>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-{{ isset($company) ? 'floppy-disk' : 'plus' }}"></i>
                    {{ isset($company) ? 'Save Changes' : 'Create Company' }}
                </button>
                <a href="{{ route('admin.companies.index') }}" class="btn btn-ghost">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
