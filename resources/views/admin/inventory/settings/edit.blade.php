@extends('layouts.app')

@section('title', 'Invoice Settings')

@section('content')
<div class="container-fluid" style="max-width: 800px;">
    <div class="mb-4">
        <x-breadcrumb :items="[
            ['label' => 'Home', 'url' => route('admin.dashboard')],
            ['label' => 'Inventory', 'url' => route('admin.inventory.products.index')],
            ['label' => 'Invoice Settings']
        ]" />
        <h1 class="h3 mb-1 fw-semibold">Invoice Settings</h1>
        <p class="text-muted mb-0">Company info and signature for invoices</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card border-0 shadow-sm">
        <form action="{{ route('admin.inventory.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-3">Company Information</h5>
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <label class="form-label">Company Name</label>
                        <input type="text" name="company_name" value="{{ old('company_name', $setting->company_name) }}" class="form-control @error('company_name') is-invalid @enderror">
                        @error('company_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">Address</label>
                        <textarea name="address" rows="2" class="form-control @error('address') is-invalid @enderror">{{ old('address', $setting->address) }}</textarea>
                        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $setting->phone) }}" class="form-control @error('phone') is-invalid @enderror">
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" value="{{ old('email', $setting->email) }}" class="form-control @error('email') is-invalid @enderror">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <h5 class="fw-semibold mb-3">Invoice Assets</h5>
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-6">
                        <label class="form-label">Logo</label>
                        @if($setting->logo_path)
                            <div class="mb-2"><img src="{{ Storage::url($setting->logo_path) }}" alt="Logo" class="img-thumbnail" style="max-height: 60px;"></div>
                        @endif
                        <input type="file" name="logo" accept="image/*" class="form-control @error('logo') is-invalid @enderror">
                        @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Signature Image</label>
                        @if($setting->signature_image_path)
                            <div class="mb-2"><img src="{{ Storage::url($setting->signature_image_path) }}" alt="Signature" class="img-thumbnail" style="max-height: 50px;"></div>
                        @endif
                        <input type="file" name="signature" accept="image/*" class="form-control @error('signature') is-invalid @enderror">
                        @error('signature')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">Footer Text</label>
                        <textarea name="footer_text" rows="2" class="form-control @error('footer_text') is-invalid @enderror">{{ old('footer_text', $setting->footer_text) }}</textarea>
                        @error('footer_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label">Tax Rate (%)</label>
                        <input type="number" name="tax_rate" value="{{ old('tax_rate', $setting->tax_rate) }}" step="0.01" min="0" max="100" class="form-control @error('tax_rate') is-invalid @enderror">
                        @error('tax_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white border-0 p-4">
                <button type="submit" class="btn btn-primary">Save Settings</button>
                <a href="{{ route('admin.inventory.products.index') }}" class="btn btn-outline-secondary">Back to Inventory</a>
            </div>
        </form>
    </div>
</div>
@endsection
