@props(['patient', 'session'])

<div 
    x-show="dayModalOpen"
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto"
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
        class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"
        @click="dayModalOpen = false"
    ></div>

    <!-- Modal -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div 
            x-show="dayModalOpen"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="relative transform overflow-hidden rounded-xl bg-white shadow-xl transition-all sm:max-w-2xl w-full"
            @click.away="dayModalOpen = false"
        >
            <!-- Header -->
            <div class="px-6 py-4 border-b border-stone-200">
                <h3 class="text-lg font-semibold text-stone-900">
                    <span x-text="editingDayId ? 'Edit Day Entry' : 'Add Day Entry'"></span>
                </h3>
            </div>
            
            <!-- Form -->
            <div class="px-6 py-4">
                <div class="space-y-4">
                    <!-- Date -->
                    <div>
                        <label for="day_date" class="block text-sm font-medium text-stone-700 mb-2">
                            Date <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="date"
                            id="day_date"
                            x-model="formData.day_date"
                            required
                            class="w-full px-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                        />
                    </div>

                    <!-- Symptoms -->
                    <div>
                        <label for="symptoms" class="block text-sm font-medium text-stone-700 mb-2">
                            Symptoms
                        </label>
                        <textarea
                            id="symptoms"
                            x-model="formData.symptoms"
                            rows="3"
                            class="w-full px-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                            placeholder="Enter symptoms observed..."
                            @blur="debouncedSave()"
                        ></textarea>
                        <p class="mt-1 text-xs text-stone-500">Auto-saves 2 seconds after you stop typing</p>
                    </div>

                    <!-- Alerts -->
                    <div>
                        <label for="alerts" class="block text-sm font-medium text-stone-700 mb-2">
                            Alerts
                        </label>
                        <textarea
                            id="alerts"
                            x-model="formData.alerts"
                            rows="3"
                            class="w-full px-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                            placeholder="Enter any alerts or concerns..."
                            @blur="debouncedSave()"
                        ></textarea>
                        <p class="mt-1 text-xs text-stone-500">Auto-saves 2 seconds after you stop typing</p>
                    </div>

                    <!-- Tasks -->
                    <div>
                        <label for="tasks" class="block text-sm font-medium text-stone-700 mb-2">
                            Tasks
                        </label>
                        <textarea
                            id="tasks"
                            x-model="formData.tasks"
                            rows="3"
                            class="w-full px-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                            placeholder="Enter tasks or activities..."
                            @blur="debouncedSave()"
                        ></textarea>
                        <p class="mt-1 text-xs text-stone-500">Auto-saves 2 seconds after you stop typing</p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 border-t border-stone-200 flex items-center justify-end space-x-4">
                <button
                    @click="dayModalOpen = false"
                    class="px-4 py-2 bg-stone-100 text-stone-700 rounded-lg hover:bg-stone-200 transition-colors"
                >
                    Cancel
                </button>
                <button
                    @click="saveDay()"
                    :disabled="saving"
                    class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2"
                >
                    <span x-show="saving" class="animate-spin">⏳</span>
                    <span x-text="saving ? 'Saving...' : 'Save'"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
