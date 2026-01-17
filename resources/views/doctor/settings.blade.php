@extends('layouts.app')

@section('title', 'Doctor Settings')

@section('content')
<div class="max-w-4xl mx-auto" x-data="doctorSettings()">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-stone-900">Doctor Settings</h1>
        <p class="text-stone-600 mt-2">Manage your profile information and preferences</p>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div 
            x-data="{ show: true }"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="mb-6 p-4 bg-emerald-100 border border-emerald-400 text-emerald-700 rounded-lg"
            x-init="setTimeout(() => show = false, 5000)"
        >
            {{ session('success') }}
        </div>
    @endif

    <!-- Form -->
    <x-card>
        <form 
            action="{{ route('doctors.settings.update', $doctor ? ['doctor' => $doctor->id] : []) }}" 
            method="POST" 
            enctype="multipart/form-data"
            @submit.prevent="submitForm($event)"
        >
            @csrf
            @method('PUT')

            <!-- Avatar Upload -->
            <x-avatar-upload 
                :currentAvatar="$doctor->avatar_url ?? null" 
                name="profile_photo"
                label="Profile Photo"
            />

            <!-- Username -->
            <x-input
                type="text"
                name="username"
                label="Username"
                :value="old('username', $doctor->username ?? '')"
                :error="$errors->first('username')"
                required
                x-model="form.username"
                @blur="validateField('username')"
                :class="$errors->has('username') ? 'border-red-500' : ''"
            />

            <!-- Full Name -->
            <x-input
                type="text"
                name="full_name"
                label="Full Name"
                :value="old('full_name', $doctor->full_name ?? $doctor->name ?? '')"
                :error="$errors->first('full_name')"
                required
                x-model="form.full_name"
                @blur="validateField('full_name')"
                :class="$errors->has('full_name') ? 'border-red-500' : ''"
            />

            <!-- Email -->
            <x-input
                type="email"
                name="email"
                label="Email"
                :value="old('email', $doctor->email ?? '')"
                :error="$errors->first('email')"
                required
                x-model="form.email"
                @blur="validateField('email')"
                :class="$errors->has('email') ? 'border-red-500' : ''"
            />

            <!-- Phone -->
            <x-input
                type="tel"
                name="phone"
                label="Phone"
                :value="old('phone', $doctor->phone ?? '')"
                :error="$errors->first('phone')"
                x-model="form.phone"
            />

            <!-- Address -->
            <div class="mb-4">
                <label for="address" class="block text-sm font-medium text-stone-700 mb-2">
                    Address
                </label>
                <textarea
                    id="address"
                    name="address"
                    rows="3"
                    x-model="form.address"
                    class="w-full px-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors duration-200 @error('address') border-red-500 @enderror"
                >{{ old('address', $doctor->address ?? '') }}</textarea>
                @error('address')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end space-x-4 mt-6">
                <button
                    type="button"
                    @click="window.history.back()"
                    class="px-6 py-2 border border-stone-300 text-stone-700 rounded-lg hover:bg-stone-50 transition-colors duration-200"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    :disabled="saving"
                    class="px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 flex items-center space-x-2"
                >
                    <span x-show="!saving">Save Changes</span>
                    <span x-show="saving" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Saving...
                    </span>
                </button>
            </div>
        </form>
    </x-card>
</div>

<script>
function doctorSettings() {
    return {
        saving: false,
        form: {
            username: '{{ old('username', $doctor->username ?? '') }}',
            full_name: '{{ old('full_name', $doctor->full_name ?? $doctor->name ?? '') }}',
            email: '{{ old('email', $doctor->email ?? '') }}',
            phone: '{{ old('phone', $doctor->phone ?? '') }}',
            address: '{{ old('address', $doctor->address ?? '') }}',
        },
        errors: {},
        
        validateField(field) {
            // Basic validation
            if (field === 'email' && this.form.email && !this.isValidEmail(this.form.email)) {
                this.errors[field] = 'Please enter a valid email address';
            } else if (field === 'username' && !this.form.username) {
                this.errors[field] = 'Username is required';
            } else if (field === 'full_name' && !this.form.full_name) {
                this.errors[field] = 'Full name is required';
            } else {
                delete this.errors[field];
            }
        },
        
        isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        },
        
        submitForm(event) {
            this.saving = true;
            event.target.submit();
        }
    }
}
</script>
@endsection
