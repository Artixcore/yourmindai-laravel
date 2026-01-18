@props(['patient', 'session'])

<div 
    x-show="dayModalOpen"
    x-cloak
    class="position-fixed top-0 start-0 w-100 h-100 overflow-y-auto z-50"
    style="display: none;"
    @keydown.escape.window="dayModalOpen = false"
>
    <!-- Backdrop -->
    <div 
        x-show="dayModalOpen"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50"
        @click="dayModalOpen = false"
    ></div>

    <!-- Modal -->
    <div class="d-flex min-vh-100 align-items-center justify-content-center p-4">
        <div 
            x-show="dayModalOpen"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-4"
            class="position-relative overflow-hidden rounded-xl bg-white shadow-xl w-100"
            style="max-width: 672px;"
            @click.away="dayModalOpen = false"
        >
            <!-- Header -->
            <div class="px-4 px-md-5 py-4 border-bottom border-stone-200">
                <h3 class="h5 fw-semibold text-stone-900 mb-0">
                    <span x-text="editingDayId ? 'Edit Day Entry' : 'Add Day Entry'"></span>
                </h3>
            </div>
            
            <!-- Form -->
            <div class="px-4 px-md-5 py-4">
                <div class="d-flex flex-column gap-4">
                    <!-- Date -->
                    <div>
                        <label for="day_date" class="form-label text-stone-700">
                            Date <span class="text-danger">*</span>
                        </label>
                        <input
                            type="date"
                            id="day_date"
                            x-model="formData.day_date"
                            required
                            class="form-control"
                        />
                    </div>

                    <!-- Symptoms -->
                    <div>
                        <label for="symptoms" class="form-label text-stone-700">
                            Symptoms
                        </label>
                        <textarea
                            id="symptoms"
                            x-model="formData.symptoms"
                            rows="3"
                            class="form-control"
                            placeholder="Enter symptoms observed..."
                            @blur="debouncedSave()"
                        ></textarea>
                        <p class="mt-1 small text-stone-500 mb-0">Auto-saves 2 seconds after you stop typing</p>
                    </div>

                    <!-- Alerts -->
                    <div>
                        <label for="alerts" class="form-label text-stone-700">
                            Alerts
                        </label>
                        <textarea
                            id="alerts"
                            x-model="formData.alerts"
                            rows="3"
                            class="form-control"
                            placeholder="Enter any alerts or concerns..."
                            @blur="debouncedSave()"
                        ></textarea>
                        <p class="mt-1 small text-stone-500 mb-0">Auto-saves 2 seconds after you stop typing</p>
                    </div>

                    <!-- Tasks -->
                    <div>
                        <label for="tasks" class="form-label text-stone-700">
                            Tasks
                        </label>
                        <textarea
                            id="tasks"
                            x-model="formData.tasks"
                            rows="3"
                            class="form-control"
                            placeholder="Enter tasks or activities..."
                            @blur="debouncedSave()"
                        ></textarea>
                        <p class="mt-1 small text-stone-500 mb-0">Auto-saves 2 seconds after you stop typing</p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-4 px-md-5 py-4 border-top border-stone-200 d-flex align-items-center justify-content-end gap-3">
                <button
                    @click="dayModalOpen = false"
                    class="btn btn-outline-secondary"
                >
                    Cancel
                </button>
                <button
                    @click="saveDay()"
                    :disabled="saving"
                    class="btn btn-primary d-flex align-items-center gap-2"
                >
                    <span x-show="saving" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    <span x-text="saving ? 'Saving...' : 'Save'"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
