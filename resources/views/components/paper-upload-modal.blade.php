@props(['doctorId' => null])

<div 
    x-data="paperUpload()"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto"
    style="display: none;"
    @keydown.escape.window="close()"
    @paper-upload-open.window="open = true"
    @paper-upload-close.window="open = false"
>
    <!-- Backdrop -->
    <div 
        x-show="open"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
        @click="close()"
    ></div>

    <!-- Modal -->
    <div class="d-flex min-vh-100 align-items-center justify-content-center p-4">
        <div 
            x-show="open"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-4"
            class="position-relative overflow-hidden rounded-xl bg-white shadow-xl w-100"
            style="max-width: 672px;"
            @click.stop
        >
            <!-- Header -->
            <div class="px-6 py-4 border-b border-stone-200">
                <h3 class="text-lg font-semibold text-stone-900">Upload Document</h3>
            </div>
            
            <!-- Form -->
            <form 
                action="{{ route('doctors.papers.store') }}" 
                method="POST" 
                enctype="multipart/form-data"
                @submit.prevent="submitForm($event)"
                class="px-6 py-4"
            >
                @csrf

                <!-- File Upload -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-stone-700 mb-2">
                        Document File <span class="text-red-500">*</span>
                    </label>
                    <div 
                        @click="$refs.fileInput.click()"
                        @dragover.prevent="dragover = true"
                        @dragleave.prevent="dragover = false"
                        @drop.prevent="handleDrop($event)"
                        :class="dragover ? 'border-teal-500 bg-teal-50' : 'border-stone-300'"
                        class="border-2 border-dashed rounded-lg p-8 text-center cursor-pointer transition-colors duration-200"
                    >
                        <input 
                            type="file" 
                            name="file"
                            x-ref="fileInput"
                            accept=".pdf,.jpg,.jpeg,.png"
                            class="hidden"
                            @change="handleFileSelect($event)"
                        >
                        
                        <template x-if="!selectedFile">
                            <div>
                                <svg class="mx-auto h-12 w-12 text-stone-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <p class="mt-2 text-sm text-stone-600">
                                    <span class="font-medium text-teal-600">Click to upload</span> or drag and drop
                                </p>
                                <p class="text-xs text-stone-500 mt-1">PDF, JPG, PNG up to 10MB</p>
                            </div>
                        </template>
                        
                        <template x-if="selectedFile">
                            <div class="d-flex flex-column gap-2">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span class="text-sm font-medium text-stone-900" x-text="selectedFile.name"></span>
                                </div>
                                <p class="text-xs text-stone-500" x-text="formatFileSize(selectedFile.size)"></p>
                                <button 
                                    type="button"
                                    @click="removeFile()"
                                    class="text-sm text-red-600 hover:text-red-700"
                                >
                                    Remove
                                </button>
                            </div>
                        </template>
                    </div>
                    <p x-show="fileError" class="mt-1 text-sm text-red-600" x-text="fileError"></p>
                </div>

                <!-- Title -->
                <x-input
                    type="text"
                    name="title"
                    label="Title"
                    :error="$errors->first('title')"
                    required
                    x-model="form.title"
                    placeholder="e.g., Medical License, Board Certification"
                />

                <!-- Category -->
                <div class="mb-4">
                    <label for="category" class="block text-sm font-medium text-stone-700 mb-2">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="category"
                        name="category"
                        x-model="form.category"
                        required
                        class="w-full px-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors duration-200 @error('category') border-red-500 @enderror"
                    >
                        <option value="">Select category</option>
                        <option value="license">License</option>
                        <option value="certificate">Certificate</option>
                        <option value="other">Other</option>
                    </select>
                    @error('category')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Issued Date -->
                <x-input
                    type="date"
                    name="issued_date"
                    label="Issued Date"
                    :error="$errors->first('issued_date')"
                    x-model="form.issued_date"
                />

                <!-- Notes -->
                <div class="mb-4">
                    <label for="notes" class="block text-sm font-medium text-stone-700 mb-2">
                        Notes
                    </label>
                    <textarea
                        id="notes"
                        name="notes"
                        rows="3"
                        x-model="form.notes"
                        placeholder="Additional information about this document..."
                        class="form-control @error('notes') is-invalid @enderror"
                    >{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Actions -->
                <div class="d-flex align-items-center justify-content-end gap-3 mt-4">
                    <button
                        type="button"
                        @click="close()"
                        class="btn btn-outline-secondary"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        :disabled="uploading || !selectedFile"
                        class="btn btn-primary d-flex align-items-center gap-2"
                    >
                        <span x-show="!uploading">Upload</span>
                        <span x-show="uploading" class="d-flex align-items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Uploading...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function paperUpload() {
    return {
        open: false,
        selectedFile: null,
        dragover: false,
        uploading: false,
        fileError: null,
        form: {
            title: '',
            category: '',
            issued_date: '',
            notes: '',
        },
        
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
            this.fileError = null;
            
            if (!file) return;
            
            // Validate file size (10MB)
            if (file.size > 10 * 1024 * 1024) {
                this.fileError = 'File size must be less than 10MB';
                return;
            }
            
            // Validate file type
            const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
            if (!allowedTypes.includes(file.type)) {
                this.fileError = 'Please select a PDF, JPG, or PNG file';
                return;
            }
            
            this.selectedFile = file;
        },
        
        removeFile() {
            this.selectedFile = null;
            this.fileError = null;
            const fileInput = this.$refs.fileInput;
            if (fileInput) {
                fileInput.value = '';
            }
        },
        
        formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        },
        
        close() {
            this.open = false;
            this.selectedFile = null;
            this.fileError = null;
            this.form = {
                title: '',
                category: '',
                issued_date: '',
                notes: '',
            };
        },
        
        submitForm(event) {
            if (!this.selectedFile) {
                this.fileError = 'Please select a file';
                return;
            }
            
            this.uploading = true;
            event.target.submit();
        }
    }
}
</script>

<style>
[x-cloak] { display: none !important; }
</style>
