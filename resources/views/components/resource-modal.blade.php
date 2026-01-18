@props(['patient', 'sessions' => collect(), 'allSessionDays' => collect()])

<div 
    x-data="resourceModal({{ json_encode($patient->id) }}, {{ json_encode($sessions->map(fn($s) => ['id' => $s->id, 'title' => $s->title])->values()) }}, {{ json_encode($allSessionDays) }})"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto"
    style="display: none;"
    @keydown.escape.window="close()"
    @resource-modal-open.window="open = true; editing = false; resetForm()"
    @resource-edit-open.window="openEdit($event.detail.resource)"
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
                <h3 class="text-lg font-semibold text-stone-900" x-text="editing ? 'Edit Resource' : 'Add Resource'"></h3>
            </div>
            
            <!-- Form -->
            <form 
                :action="editing ? '{{ route('patients.resources.update', [$patient, ':resource_id']) }}'.replace(':resource_id', form.resourceId) : '{{ route('patients.resources.store', $patient) }}'"
                method="POST" 
                enctype="multipart/form-data"
                @submit.prevent="submitForm($event)"
                class="px-6 py-4"
            >
                @csrf
                <input type="hidden" name="_method" :value="editing ? 'PUT' : 'POST'">
                <input type="hidden" name="patient_id" :value="patientId">

                <!-- Type Selector -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-stone-700 mb-2">
                        Resource Type <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-4">
                        <label 
                            :class="form.type === 'pdf' ? 'border-teal-500 bg-teal-50' : 'border-stone-300'"
                            class="border-2 rounded-lg p-4 cursor-pointer transition-colors duration-200"
                        >
                            <input 
                                type="radio" 
                                name="type" 
                                value="pdf" 
                                x-model="form.type"
                                class="sr-only"
                                @change="handleTypeChange()"
                            >
                            <div class="flex items-center space-x-3">
                                <svg class="w-6 h-6 text-stone-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                <span class="font-medium">PDF Document</span>
                            </div>
                        </label>
                        <label 
                            :class="form.type === 'youtube' ? 'border-teal-500 bg-teal-50' : 'border-stone-300'"
                            class="border-2 rounded-lg p-4 cursor-pointer transition-colors duration-200"
                        >
                            <input 
                                type="radio" 
                                name="type" 
                                value="youtube" 
                                x-model="form.type"
                                class="sr-only"
                                @change="handleTypeChange()"
                            >
                            <div class="flex items-center space-x-3">
                                <svg class="w-6 h-6 text-stone-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                </svg>
                                <span class="font-medium">YouTube Video</span>
                            </div>
                        </label>
                    </div>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Title -->
                <x-input
                    type="text"
                    name="title"
                    label="Title"
                    :error="$errors->first('title')"
                    required
                    x-model="form.title"
                    placeholder="e.g., Exercise Guide, Therapy Notes"
                />

                <!-- PDF File Upload -->
                <div x-show="form.type === 'pdf'" x-transition class="mb-6">
                    <label class="block text-sm font-medium text-stone-700 mb-2">
                        PDF File <span class="text-red-500">*</span>
                    </label>
                    <div 
                        @click="$refs.fileInput.click()"
                        @dragover.prevent="dragover = true"
                        @dragleave.prevent="dragover = false"
                        @drop.prevent="handleDrop($event)"
                        :class="dragover ? 'border-teal-500 bg-teal-50' : 'border-stone-300'"
                        class="border-2 border-dashed rounded-lg p-6 text-center cursor-pointer transition-colors duration-200"
                    >
                        <input 
                            type="file" 
                            name="file"
                            x-ref="fileInput"
                            accept=".pdf"
                            class="hidden"
                            @change="handleFileSelect($event)"
                        >
                        
                        <template x-if="!selectedFile && !form.existingFile">
                            <div>
                                <svg class="mx-auto h-10 w-10 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                <p class="mt-2 text-sm text-stone-600">
                                    <span class="font-medium text-teal-600">Click to upload</span> or drag and drop
                                </p>
                                <p class="text-xs text-stone-500 mt-1">PDF up to 10MB</p>
                            </div>
                        </template>
                        
                        <template x-if="selectedFile || form.existingFile">
                                <div class="d-flex flex-column gap-2">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-sm font-medium text-stone-900" x-text="selectedFile ? selectedFile.name : 'Existing file'"></span>
                                </div>
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
                    @error('file')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- YouTube URL -->
                <div x-show="form.type === 'youtube'" x-transition class="mb-6">
                    <x-input
                        type="url"
                        name="youtube_url"
                        label="YouTube URL"
                        :error="$errors->first('youtube_url')"
                        required
                        x-model="form.youtube_url"
                        placeholder="https://www.youtube.com/watch?v=..."
                    />
                    <p class="mt-1 text-xs text-stone-500">Supports youtube.com/watch, youtu.be, and embed URLs</p>
                </div>

                <!-- Session (Optional) -->
                <div class="mb-6">
                    <label for="session_id" class="block text-sm font-medium text-stone-700 mb-2">
                        Link to Session (Optional)
                    </label>
                    <select
                        id="session_id"
                        name="session_id"
                        x-model="form.session_id"
                        @change="loadSessionDays()"
                        class="w-full px-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors duration-200"
                    >
                        <option value="">None</option>
                        <template x-for="session in sessions" :key="session.id">
                            <option :value="session.id" x-text="session.title"></option>
                        </template>
                    </select>
                    @error('session_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Session Day (Optional) -->
                <div x-show="form.session_id" x-transition class="mb-6">
                    <label for="session_day_id" class="block text-sm font-medium text-stone-700 mb-2">
                        Link to Session Day (Optional)
                    </label>
                    <select
                        id="session_day_id"
                        name="session_day_id"
                        x-model="form.session_day_id"
                        class="w-full px-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors duration-200"
                    >
                        <option value="">None</option>
                        <template x-for="day in sessionDays" :key="day.id">
                            <option :value="day.id" x-text="day.day_date"></option>
                        </template>
                    </select>
                    @error('session_day_id')
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
                        :disabled="submitting || (form.type === 'pdf' && !selectedFile && !form.existingFile) || (form.type === 'youtube' && !form.youtube_url)"
                        class="btn btn-primary d-flex align-items-center gap-2"
                    >
                        <span x-show="!submitting" x-text="editing ? 'Update' : 'Create'"></span>
                        <span x-show="submitting" class="d-flex align-items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Processing...</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function resourceModal(patientId, sessions, allSessionDays) {
    return {
        open: false,
        editing: false,
        submitting: false,
        selectedFile: null,
        dragover: false,
        fileError: null,
        patientId: patientId,
        sessions: sessions,
        allSessionDays: allSessionDays || {},
        sessionDays: [],
        form: {
            resourceId: null,
            type: 'pdf',
            title: '',
            file: null,
            existingFile: null,
            youtube_url: '',
            session_id: '',
            session_day_id: '',
        },
        
        handleTypeChange() {
            this.selectedFile = null;
            this.form.existingFile = null;
            this.form.youtube_url = '';
            this.fileError = null;
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
            if (file.type !== 'application/pdf') {
                this.fileError = 'Please select a PDF file';
                return;
            }
            
            this.selectedFile = file;
            this.form.existingFile = null;
        },
        
        removeFile() {
            this.selectedFile = null;
            this.form.existingFile = null;
            this.fileError = null;
            const fileInput = this.$refs.fileInput;
            if (fileInput) {
                fileInput.value = '';
            }
        },
        
        loadSessionDays() {
            if (!this.form.session_id) {
                this.sessionDays = [];
                this.form.session_day_id = '';
                return;
            }
            
            // Get session days for selected session from pre-loaded data
            this.sessionDays = this.allSessionDays[this.form.session_id] || [];
            // Reset session_day_id if it doesn't belong to the new session
            if (this.form.session_day_id) {
                const dayExists = this.sessionDays.some(day => day.id == this.form.session_day_id);
                if (!dayExists) {
                    this.form.session_day_id = '';
                }
            }
        },
        
        openEdit(resource) {
            this.editing = true;
            this.open = true;
            this.form = {
                resourceId: resource.id,
                type: resource.type,
                title: resource.title,
                file: null,
                existingFile: resource.type === 'pdf' && resource.file_path ? 'exists' : null,
                youtube_url: resource.youtube_url || '',
                session_id: resource.session_id || '',
                session_day_id: resource.session_day_id || '',
            };
            this.selectedFile = null;
            this.fileError = null;
            
            if (this.form.session_id) {
                this.loadSessionDays();
            }
        },
        
        resetForm() {
            this.editing = false;
            this.form = {
                resourceId: null,
                type: 'pdf',
                title: '',
                file: null,
                existingFile: null,
                youtube_url: '',
                session_id: '',
                session_day_id: '',
            };
            this.selectedFile = null;
            this.fileError = null;
            this.sessionDays = [];
        },
        
        close() {
            this.open = false;
            setTimeout(() => {
                this.resetForm();
            }, 300);
        },
        
        submitForm(event) {
            if (this.form.type === 'pdf' && !this.selectedFile && !this.form.existingFile) {
                this.fileError = 'Please select a PDF file';
                return;
            }
            
            if (this.form.type === 'youtube' && !this.form.youtube_url) {
                return;
            }
            
            this.submitting = true;
            event.target.submit();
        }
    }
}
</script>

<style>
[x-cloak] { display: none !important; }
</style>
