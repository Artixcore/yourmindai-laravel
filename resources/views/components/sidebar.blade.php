@props(['role' => 'assistant'])

<div class="sidebar-width bg-white border-end border-stone-200 position-fixed start-0 top-0 h-100" style="padding-top: 64px; z-index: 1030; overflow-y: auto;">
    <nav class="p-4">
        <div class="d-flex flex-column gap-2">
            <a href="{{ route('dashboard') }}" class="d-flex align-items-center gap-3 px-3 py-2 rounded hover-bg-teal-50 text-stone-700 hover-text-teal-700 text-decoration-none {{ request()->routeIs('dashboard') ? 'bg-teal-50 text-teal-700' : '' }}">
                <svg class="flex-shrink-0" style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>Dashboard</span>
            </a>
            
            @if($role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center gap-3 px-3 py-2 rounded hover-bg-teal-50 text-stone-700 hover-text-teal-700 text-decoration-none {{ request()->routeIs('admin.dashboard') ? 'bg-teal-50 text-teal-700' : '' }}">
                    <svg class="flex-shrink-0" style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Admin Dashboard</span>
                </a>
                <a href="{{ route('admin.staff.index') }}" class="d-flex align-items-center gap-3 px-3 py-2 rounded hover-bg-teal-50 text-stone-700 hover-text-teal-700 text-decoration-none {{ request()->routeIs('admin.staff.*') ? 'bg-teal-50 text-teal-700' : '' }}">
                    <svg class="flex-shrink-0" style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span>Staff</span>
                </a>
                <a href="{{ route('admin.patients.index') }}" class="d-flex align-items-center gap-3 px-3 py-2 rounded hover-bg-teal-50 text-stone-700 hover-text-teal-700 text-decoration-none {{ request()->routeIs('admin.patients.*') ? 'bg-teal-50 text-teal-700' : '' }}">
                    <svg class="flex-shrink-0" style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span>Patients</span>
                </a>
                <a href="{{ route('admin.sessions.index') }}" class="d-flex align-items-center gap-3 px-3 py-2 rounded hover-bg-teal-50 text-stone-700 hover-text-teal-700 text-decoration-none {{ request()->routeIs('admin.sessions.*') ? 'bg-teal-50 text-teal-700' : '' }}">
                    <svg class="flex-shrink-0" style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>Sessions</span>
                </a>
                <a href="{{ route('admin.ai-reports.index') }}" class="d-flex align-items-center gap-3 px-3 py-2 rounded hover-bg-teal-50 text-stone-700 hover-text-teal-700 text-decoration-none {{ request()->routeIs('admin.ai-reports.*') ? 'bg-teal-50 text-teal-700' : '' }}">
                    <svg class="flex-shrink-0" style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>AI Reports</span>
                </a>
                <a href="{{ route('admin.analytics.index') }}" class="d-flex align-items-center gap-3 px-3 py-2 rounded hover-bg-teal-50 text-stone-700 hover-text-teal-700 text-decoration-none {{ request()->routeIs('admin.analytics.*') ? 'bg-teal-50 text-teal-700' : '' }}">
                    <svg class="flex-shrink-0" style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span>Analytics</span>
                </a>
                <a href="{{ route('admin.contact.index') }}" class="d-flex align-items-center gap-3 px-3 py-2 rounded hover-bg-teal-50 text-stone-700 hover-text-teal-700 text-decoration-none {{ request()->routeIs('admin.contact.*') ? 'bg-teal-50 text-teal-700' : '' }}">
                    <svg class="flex-shrink-0" style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                    <span>Contact Inbox</span>
                </a>
            @endif
            
            @if(in_array($role, ['admin', 'doctor']))
                <a href="{{ route('patients.index') }}" class="d-flex align-items-center gap-3 px-3 py-2 rounded hover-bg-teal-50 text-stone-700 hover-text-teal-700 text-decoration-none {{ request()->routeIs('patients.*') ? 'bg-teal-50 text-teal-700' : '' }}">
                    <svg class="flex-shrink-0" style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span>Patients</span>
                </a>
                <a href="#" class="d-flex align-items-center gap-3 px-3 py-2 rounded hover-bg-teal-50 text-stone-700 hover-text-teal-700 text-decoration-none">
                    <svg class="flex-shrink-0" style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>Appointments</span>
                </a>
            @endif
            
            @if($role === 'doctor')
                <a href="#" class="d-flex align-items-center gap-3 px-3 py-2 rounded hover-bg-teal-50 text-stone-700 hover-text-teal-700 text-decoration-none">
                    <svg class="flex-shrink-0" style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Notes</span>
                </a>
                <a href="{{ route('doctors.settings') }}" class="d-flex align-items-center gap-3 px-3 py-2 rounded hover-bg-teal-50 text-stone-700 hover-text-teal-700 text-decoration-none {{ request()->routeIs('doctors.settings*') ? 'bg-teal-50 text-teal-700' : '' }}">
                    <svg class="flex-shrink-0" style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Settings</span>
                </a>
                <a href="{{ route('doctors.papers.index') }}" class="d-flex align-items-center gap-3 px-3 py-2 rounded hover-bg-teal-50 text-stone-700 hover-text-teal-700 text-decoration-none {{ request()->routeIs('doctors.papers*') ? 'bg-teal-50 text-teal-700' : '' }}">
                    <svg class="flex-shrink-0" style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Papers</span>
                </a>
            @endif
            
            @if(strtolower($role) === 'patient' || $role === 'PATIENT')
                <a href="{{ route('patient.dashboard') }}" class="d-flex align-items-center gap-3 px-3 py-2 rounded hover-bg-teal-50 text-stone-700 hover-text-teal-700 text-decoration-none {{ request()->routeIs('patient.dashboard') ? 'bg-teal-50 text-teal-700' : '' }}">
                    <i class="bi bi-house-door flex-shrink-0"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('patient.profile') }}" class="d-flex align-items-center gap-3 px-3 py-2 rounded hover-bg-teal-50 text-stone-700 hover-text-teal-700 text-decoration-none {{ request()->routeIs('patient.profile') ? 'bg-teal-50 text-teal-700' : '' }}">
                    <i class="bi bi-person flex-shrink-0"></i>
                    <span>Profile</span>
                </a>
                <a href="{{ route('patient.sessions.index') }}" class="d-flex align-items-center gap-3 px-3 py-2 rounded hover-bg-teal-50 text-stone-700 hover-text-teal-700 text-decoration-none {{ request()->routeIs('patient.sessions.*') ? 'bg-teal-50 text-teal-700' : '' }}">
                    <i class="bi bi-calendar-check flex-shrink-0"></i>
                    <span>Sessions</span>
                </a>
                <a href="{{ route('patient.resources.index') }}" class="d-flex align-items-center gap-3 px-3 py-2 rounded hover-bg-teal-50 text-stone-700 hover-text-teal-700 text-decoration-none {{ request()->routeIs('patient.resources.*') ? 'bg-teal-50 text-teal-700' : '' }}">
                    <i class="bi bi-folder-fill flex-shrink-0"></i>
                    <span>Resources</span>
                </a>
                <a href="{{ route('patient.appointments.index') }}" class="d-flex align-items-center gap-3 px-3 py-2 rounded hover-bg-teal-50 text-stone-700 hover-text-teal-700 text-decoration-none {{ request()->routeIs('patient.appointments.*') ? 'bg-teal-50 text-teal-700' : '' }}">
                    <i class="bi bi-calendar-event flex-shrink-0"></i>
                    <span>Appointments</span>
                </a>
                <a href="{{ route('patient.assessments.index') }}" class="d-flex align-items-center gap-3 px-3 py-2 rounded hover-bg-teal-50 text-stone-700 hover-text-teal-700 text-decoration-none {{ request()->routeIs('patient.assessments.*') ? 'bg-teal-50 text-teal-700' : '' }}">
                    <i class="bi bi-clipboard-check flex-shrink-0"></i>
                    <span>Assessments</span>
                </a>
                <a href="{{ route('patient.progress.index') }}" class="d-flex align-items-center gap-3 px-3 py-2 rounded hover-bg-teal-50 text-stone-700 hover-text-teal-700 text-decoration-none {{ request()->routeIs('patient.progress.*') ? 'bg-teal-50 text-teal-700' : '' }}">
                    <i class="bi bi-graph-up flex-shrink-0"></i>
                    <span>Progress</span>
                </a>
                <a href="{{ route('patient.messages.index') }}" class="d-flex align-items-center gap-3 px-3 py-2 rounded hover-bg-teal-50 text-stone-700 hover-text-teal-700 text-decoration-none {{ request()->routeIs('patient.messages.*') ? 'bg-teal-50 text-teal-700' : '' }}">
                    <i class="bi bi-chat-dots flex-shrink-0"></i>
                    <span>Messages</span>
                </a>
                <a href="{{ route('patient.medications.index') }}" class="d-flex align-items-center gap-3 px-3 py-2 rounded hover-bg-teal-50 text-stone-700 hover-text-teal-700 text-decoration-none {{ request()->routeIs('patient.medications.*') ? 'bg-teal-50 text-teal-700' : '' }}">
                    <i class="bi bi-capsule flex-shrink-0"></i>
                    <span>Medications</span>
                </a>
                <a href="{{ route('patient.journal.index') }}" class="d-flex align-items-center gap-3 px-3 py-2 rounded hover-bg-teal-50 text-stone-700 hover-text-teal-700 text-decoration-none {{ request()->routeIs('patient.journal.*') ? 'bg-teal-50 text-teal-700' : '' }}">
                    <i class="bi bi-journal-text flex-shrink-0"></i>
                    <span>Mood Journal</span>
                </a>
            @else
                <a href="#" class="d-flex align-items-center gap-3 px-3 py-2 rounded hover-bg-teal-50 text-stone-700 hover-text-teal-700 text-decoration-none">
                    <svg class="flex-shrink-0" style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                    <span>Messages</span>
                </a>
            @endif
        </div>
    </nav>
</div>
