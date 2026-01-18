@props(['currentAvatar' => null, 'name' => 'profile_photo', 'label' => 'Profile Photo'])

<div class="mb-4" x-data="avatarUpload('{{ $currentAvatar }}')">
    <label class="form-label text-stone-700">
        {{ $label }}
    </label>
    
    <div class="d-flex align-items-center gap-4">
        <!-- Avatar Preview -->
        <div class="position-relative">
            <div class="rounded-circle overflow-hidden bg-stone-200 border border-stone-300 d-flex align-items-center justify-content-center" style="width: 96px; height: 96px;">
                <template x-if="preview">
                    <img :src="preview" alt="Avatar preview" class="w-100 h-100 object-fit-cover">
                </template>
                <template x-if="!preview && currentAvatar">
                    <img :src="currentAvatar" alt="Current avatar" class="w-100 h-100 object-fit-cover">
                </template>
                <template x-if="!preview && !currentAvatar">
                    <svg style="width: 48px; height: 48px;" class="text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </template>
            </div>
            
            <!-- Loading Overlay -->
            <div x-show="uploading" x-cloak class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 rounded-circle d-flex align-items-center justify-content-center">
                <svg style="width: 24px; height: 24px;" class="spinner-border text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </div>
        
        <!-- Upload Button -->
        <div class="flex-grow-1">
            <label for="{{ $name }}" class="cursor-pointer">
                <span class="btn btn-primary d-inline-flex align-items-center gap-2">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    <span x-text="preview || currentAvatar ? 'Change Photo' : 'Upload Photo'"></span>
                </span>
                <input 
                    type="file" 
                    id="{{ $name }}" 
                    name="{{ $name }}"
                    accept="image/jpeg,image/jpg,image/png"
                    class="d-none"
                    @change="handleFileSelect($event)"
                >
            </label>
            
            <!-- Remove Button -->
            <button 
                type="button"
                x-show="preview || currentAvatar"
                @click="removeAvatar()"
                class="btn btn-link text-danger p-0 small ms-3"
            >
                Remove
            </button>
            
            <p class="mt-2 small text-stone-500 mb-0">JPG, PNG up to 2MB</p>
        </div>
    </div>
    
    <!-- Hidden input to track removal -->
    <input type="hidden" name="remove_avatar" x-model="removeAvatarFlag">
</div>

<script>
function avatarUpload(currentAvatar) {
    return {
        preview: null,
        currentAvatar: currentAvatar,
        uploading: false,
        removeAvatarFlag: false,
        
        handleFileSelect(event) {
            const file = event.target.files[0];
            if (file) {
                // Validate file size (2MB)
                if (file.size > 2048 * 1024) {
                    alert('File size must be less than 2MB');
                    event.target.value = '';
                    return;
                }
                
                // Validate file type
                if (!file.type.match('image.*')) {
                    alert('Please select an image file');
                    event.target.value = '';
                    return;
                }
                
                // Create preview
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.preview = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },
        
        removeAvatar() {
            this.preview = null;
            this.removeAvatarFlag = true;
            const fileInput = document.getElementById('{{ $name }}');
            if (fileInput) {
                fileInput.value = '';
            }
        }
    }
}
</script>

<style>
    [x-cloak] { display: none !important; }
    .cursor-pointer {
        cursor: pointer;
    }
</style>
