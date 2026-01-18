@extends('layouts.app')

@section('title', 'Edit Patient')

@section('content')
<div class="container-fluid" style="max-width: 768px;">
    <!-- Header -->
    <div class="mb-4">
        <h1 class="h2 fw-bold text-stone-900">Edit Patient</h1>
        <p class="text-stone-600 mt-2 mb-0">Update patient information</p>
    </div>

    <!-- Form -->
    <x-card>
        <form action="{{ route('patients.update', $patient) }}" method="POST" enctype="multipart/form-data" x-data="patientForm()">
            @csrf
            @method('PUT')

            <!-- Name -->
            <x-input
                type="text"
                name="name"
                label="Full Name"
                value="{{ old('name', $patient->name) }}"
                required
                :error="$errors->first('name')"
            />

            <!-- Email -->
            <x-input
                type="email"
                name="email"
                label="Email"
                value="{{ old('email', $patient->email) }}"
                required
                :error="$errors->first('email')"
            />

            <!-- Phone -->
            <x-input
                type="tel"
                name="phone"
                label="Phone"
                value="{{ old('phone', $patient->phone) }}"
                :error="$errors->first('phone')"
            />

            <!-- Photo Upload -->
            <div class="mb-3">
                <label class="form-label text-stone-700">
                    Photo (Optional)
                </label>
                
                <!-- Current Photo -->
                @if($patient->photo_path)
                    <div class="mb-3">
                        <label class="form-label small text-stone-500">Current Photo</label>
                        <img 
                            src="{{ $patient->photo_url }}" 
                            alt="{{ $patient->name }}"
                            class="rounded object-fit-cover border border-stone-200"
                            style="width: 128px; height: 128px;"
                        />
                    </div>
                @endif
                
                <div 
                    @click="$refs.photoInput.click()"
                    @dragover.prevent="dragover = true"
                    @dragleave.prevent="dragover = false"
                    @drop.prevent="handleDrop($event)"
                    :class="dragover ? 'border-primary bg-teal-50' : 'border-stone-300'"
                    class="border border-dashed rounded p-5 text-center cursor-pointer"
                >
                    <input 
                        type="file" 
                        name="photo"
                        x-ref="photoInput"
                        @change="handleFileSelect($event)"
                        accept="image/*"
                        class="d-none"
                    />
                    <div x-show="!selectedFile">
                        <svg class="mx-auto mb-2 text-stone-400" style="width: 48px; height: 48px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="small text-stone-600 mb-1">
                            <span class="font-medium text-teal-700">Click to upload</span> or drag and drop
                        </p>
                        <p class="small text-stone-500 mb-1">PNG, JPG, GIF up to 2MB</p>
                        <p class="small text-stone-400 mb-0">Leave empty to keep current photo</p>
                    </div>
                    <div x-show="selectedFile" class="d-flex flex-column gap-2">
                        <img :src="previewUrl" alt="Preview" class="mx-auto rounded object-fit-cover" style="width: 128px; height: 128px;" />
                        <p class="small text-stone-600 mb-0" x-text="selectedFile?.name"></p>
                        <button
                            type="button"
                            @click="removeFile()"
                            class="btn btn-link text-danger p-0 small"
                        >
                            Remove
                        </button>
                    </div>
                </div>
                @if($errors->has('photo'))
                    <div class="invalid-feedback d-block">
                        {{ $errors->first('photo') }}
                    </div>
                @endif
            </div>

            <!-- Form Actions -->
            <div class="d-flex align-items-center justify-content-between pt-3 border-top border-stone-200">
                <a
                    href="{{ route('patients.show', $patient) }}"
                    class="btn btn-outline-secondary"
                >
                    Cancel
                </a>
                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Update Patient
                </button>
            </div>
        </form>
    </x-card>
</div>

<script>
function patientForm() {
    return {
        selectedFile: null,
        previewUrl: null,
        dragover: false,
        
        handleFileSelect(event) {
            const file = event.target.files[0];
            this.validateAndSetFile(file);
        },
        
        handleDrop(event) {
            this.dragover = false;
            const file = event.dataTransfer.files[0];
            this.validateAndSetFile(file);
        },
        
        validateAndSetFile(file) {
            if (!file) return;
            
            // Validate file size (2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('File size must be less than 2MB');
                return;
            }
            
            // Validate file type
            if (!file.type.startsWith('image/')) {
                alert('Please select an image file');
                return;
            }
            
            this.selectedFile = file;
            
            // Create preview
            const reader = new FileReader();
            reader.onload = (e) => {
                this.previewUrl = e.target.result;
            };
            reader.readAsDataURL(file);
        },
        
        removeFile() {
            this.selectedFile = null;
            this.previewUrl = null;
            const fileInput = this.$refs.photoInput;
            if (fileInput) {
                fileInput.value = '';
            }
        }
    }
}
</script>
@endsection
