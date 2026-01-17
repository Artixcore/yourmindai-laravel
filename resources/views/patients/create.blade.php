@extends('layouts.app')

@section('title', 'Create Patient')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-stone-900">Create Patient</h1>
        <p class="text-stone-600 mt-2">Create a new patient account for the Android app</p>
    </div>

    <!-- Form -->
    <x-card>
        <form action="{{ route('patients.store') }}" method="POST" enctype="multipart/form-data" x-data="patientForm()">
            @csrf

            <!-- Name -->
            <x-input
                type="text"
                name="name"
                label="Full Name"
                value="{{ old('name') }}"
                required
                :error="$errors->first('name')"
            />

            <!-- Email -->
            <x-input
                type="email"
                name="email"
                label="Email"
                value="{{ old('email') }}"
                required
                :error="$errors->first('email')"
            />

            <!-- Phone -->
            <x-input
                type="tel"
                name="phone"
                label="Phone"
                value="{{ old('phone') }}"
                :error="$errors->first('phone')"
            />

            <!-- Photo Upload -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-stone-700 mb-2">
                    Photo (Optional)
                </label>
                <div 
                    @click="$refs.photoInput.click()"
                    @dragover.prevent="dragover = true"
                    @dragleave.prevent="dragover = false"
                    @drop.prevent="handleDrop($event)"
                    :class="dragover ? 'border-teal-500 bg-teal-50' : 'border-stone-300'"
                    class="border-2 border-dashed rounded-lg p-8 text-center cursor-pointer transition-colors duration-200"
                >
                    <input 
                        type="file" 
                        name="photo"
                        x-ref="photoInput"
                        @change="handleFileSelect($event)"
                        accept="image/*"
                        class="hidden"
                    />
                    <div x-show="!selectedFile">
                        <svg class="mx-auto h-12 w-12 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="mt-2 text-sm text-stone-600">
                            <span class="font-medium text-teal-600">Click to upload</span> or drag and drop
                        </p>
                        <p class="text-xs text-stone-500 mt-1">PNG, JPG, GIF up to 2MB</p>
                    </div>
                    <div x-show="selectedFile" class="space-y-2">
                        <img :src="previewUrl" alt="Preview" class="mx-auto h-32 w-32 object-cover rounded-lg" />
                        <p class="text-sm text-stone-600" x-text="selectedFile?.name"></p>
                        <button
                            type="button"
                            @click="removeFile()"
                            class="text-sm text-red-600 hover:text-red-800"
                        >
                            Remove
                        </button>
                    </div>
                </div>
                @if($errors->has('photo'))
                    <p class="mt-1 text-sm text-red-600">{{ $errors->first('photo') }}</p>
                @endif
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-between pt-4 border-t border-stone-200">
                <a
                    href="{{ route('patients.index') }}"
                    class="px-6 py-2 text-stone-700 bg-stone-100 rounded-lg hover:bg-stone-200 transition-colors duration-200"
                >
                    Cancel
                </a>
                <button
                    type="submit"
                    class="px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors duration-200"
                >
                    Create Patient
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
