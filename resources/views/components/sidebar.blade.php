@props(['role' => 'assistant'])

<div 
    x-data="{ collapsed: localStorage.getItem('sidebarCollapsed') === 'true' }"
    x-init="
        $watch('collapsed', value => {
            localStorage.setItem('sidebarCollapsed', value);
            document.body.setAttribute('data-sidebar-collapsed', value);
        });
        document.body.setAttribute('data-sidebar-collapsed', collapsed);
    "
    :class="{ 'sidebar-collapsed': collapsed }"
    class="sidebar-width bg-white border-end border-stone-200 position-fixed start-0 top-0 h-100"
    style="padding-top: 64px; z-index: 1030; overflow-y: auto;"
>
    <nav class="p-4">
        <!-- Collapse Toggle Button (Desktop Only) -->
        <div class="d-none d-md-flex justify-content-end mb-3">
            <button 
                @click="collapsed = !collapsed"
                class="btn btn-link btn-sm text-muted p-1 border-0"
                type="button"
                title="Toggle Sidebar"
            >
                <i class="bi" :class="collapsed ? 'bi-chevron-right' : 'bi-chevron-left'"></i>
            </button>
        </div>

        <div class="d-flex flex-column gap-2">
            <a href="{{ route('dashboard') }}" class="sidebar-nav-item d-flex align-items-center gap-3 text-decoration-none {{ request()->routeIs('dashboard') ? 'active' : '' }}" :class="collapsed ? 'justify-content-center' : ''">
                <i class="bi bi-house-door flex-shrink-0" style="width: 20px; height: 20px; font-size: 20px;"></i>
                <span x-show="!collapsed">Dashboard</span>
            </a>
            
            @if($role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="sidebar-nav-item d-flex align-items-center gap-3 text-decoration-none {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" :class="collapsed ? 'justify-content-center' : ''">
                    <i class="bi bi-speedometer2 flex-shrink-0" style="width: 20px; height: 20px; font-size: 20px;"></i>
                    <span x-show="!collapsed">Admin Dashboard</span>
                </a>
                <a href="{{ route('admin.staff.index') }}" class="sidebar-nav-item d-flex align-items-center gap-3 text-decoration-none {{ request()->routeIs('admin.staff.*') ? 'active' : '' }}" :class="collapsed ? 'justify-content-center' : ''">
                    <i class="bi bi-people flex-shrink-0" style="width: 20px; height: 20px; font-size: 20px;"></i>
                    <span x-show="!collapsed">Staff</span>
                </a>
                <a href="{{ route('admin.patients.index') }}" class="sidebar-nav-item d-flex align-items-center gap-3 text-decoration-none {{ request()->routeIs('admin.patients.*') ? 'active' : '' }}" :class="collapsed ? 'justify-content-center' : ''">
                    <i class="bi bi-person-badge flex-shrink-0" style="width: 20px; height: 20px; font-size: 20px;"></i>
                    <span x-show="!collapsed">Patients</span>
                </a>
                <a href="{{ route('admin.sessions.index') }}" class="sidebar-nav-item d-flex align-items-center gap-3 text-decoration-none {{ request()->routeIs('admin.sessions.*') ? 'active' : '' }}" :class="collapsed ? 'justify-content-center' : ''">
                    <i class="bi bi-calendar-check flex-shrink-0" style="width: 20px; height: 20px; font-size: 20px;"></i>
                    <span x-show="!collapsed">Sessions</span>
                </a>
                <a href="{{ route('admin.ai-reports.index') }}" class="sidebar-nav-item d-flex align-items-center gap-3 text-decoration-none {{ request()->routeIs('admin.ai-reports.*') ? 'active' : '' }}" :class="collapsed ? 'justify-content-center' : ''">
                    <i class="bi bi-file-earmark-text flex-shrink-0" style="width: 20px; height: 20px; font-size: 20px;"></i>
                    <span x-show="!collapsed">AI Reports</span>
                </a>
                <a href="{{ route('admin.analytics.index') }}" class="sidebar-nav-item d-flex align-items-center gap-3 text-decoration-none {{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}" :class="collapsed ? 'justify-content-center' : ''">
                    <i class="bi bi-graph-up flex-shrink-0" style="width: 20px; height: 20px; font-size: 20px;"></i>
                    <span x-show="!collapsed">Analytics</span>
                </a>
                <a href="{{ route('admin.contact.index') }}" class="sidebar-nav-item d-flex align-items-center gap-3 text-decoration-none {{ request()->routeIs('admin.contact.*') ? 'active' : '' }}" :class="collapsed ? 'justify-content-center' : ''">
                    <i class="bi bi-chat-dots flex-shrink-0" style="width: 20px; height: 20px; font-size: 20px;"></i>
                    <span x-show="!collapsed">Contact Inbox</span>
                </a>
                <a href="{{ route('admin.appointment-requests.index') }}" class="sidebar-nav-item d-flex align-items-center gap-3 text-decoration-none {{ request()->routeIs('admin.appointment-requests.*') ? 'active' : '' }}" :class="collapsed ? 'justify-content-center' : ''">
                    <i class="bi bi-calendar-plus flex-shrink-0" style="width: 20px; height: 20px; font-size: 20px;"></i>
                    <span x-show="!collapsed">Appointment Requests</span>
                </a>
                <a href="{{ route('psychometric-scales.index') }}" class="sidebar-nav-item d-flex align-items-center gap-3 text-decoration-none {{ request()->routeIs('psychometric-scales.*') ? 'active' : '' }}" :class="collapsed ? 'justify-content-center' : ''">
                    <i class="bi bi-clipboard-check flex-shrink-0" style="width: 20px; height: 20px; font-size: 20px;"></i>
                    <span x-show="!collapsed">Psychometric Scales</span>
                </a>
            @endif
            
            @if(in_array($role, ['admin', 'doctor']))
                <a href="{{ route('patients.index') }}" class="sidebar-nav-item d-flex align-items-center gap-3 text-decoration-none {{ request()->routeIs('patients.*') ? 'active' : '' }}" :class="collapsed ? 'justify-content-center' : ''">
                    <i class="bi bi-person-badge flex-shrink-0" style="width: 20px; height: 20px; font-size: 20px;"></i>
                    <span x-show="!collapsed">Patients</span>
                </a>
                <a href="{{ route('doctors.appointments.index') }}" class="sidebar-nav-item d-flex align-items-center gap-3 text-decoration-none {{ request()->routeIs('doctors.appointments.*') ? 'active' : '' }}" :class="collapsed ? 'justify-content-center' : ''">
                    <i class="bi bi-calendar-event flex-shrink-0" style="width: 20px; height: 20px; font-size: 20px;"></i>
                    <span x-show="!collapsed">Appointments</span>
                </a>
            @endif
            
            @if($role === 'doctor')
                <a href="{{ route('doctors.appointment-requests.index') }}" class="sidebar-nav-item d-flex align-items-center gap-3 text-decoration-none {{ request()->routeIs('doctors.appointment-requests.*') ? 'active' : '' }}" :class="collapsed ? 'justify-content-center' : ''">
                    <i class="bi bi-calendar-plus flex-shrink-0" style="width: 20px; height: 20px; font-size: 20px;"></i>
                    <span x-show="!collapsed">Appointment Requests</span>
                </a>
                <a href="{{ route('psychometric-scales.index') }}" class="sidebar-nav-item d-flex align-items-center gap-3 text-decoration-none {{ request()->routeIs('psychometric-scales.*') ? 'active' : '' }}" :class="collapsed ? 'justify-content-center' : ''">
                    <i class="bi bi-clipboard-check flex-shrink-0" style="width: 20px; height: 20px; font-size: 20px;"></i>
                    <span x-show="!collapsed">Assessment Scales</span>
                </a>
                <a href="{{ route('doctors.settings') }}" class="sidebar-nav-item d-flex align-items-center gap-3 text-decoration-none {{ request()->routeIs('doctors.settings*') ? 'active' : '' }}" :class="collapsed ? 'justify-content-center' : ''">
                    <i class="bi bi-gear flex-shrink-0" style="width: 20px; height: 20px; font-size: 20px;"></i>
                    <span x-show="!collapsed">Settings</span>
                </a>
                <a href="{{ route('doctors.papers.index') }}" class="sidebar-nav-item d-flex align-items-center gap-3 text-decoration-none {{ request()->routeIs('doctors.papers*') ? 'active' : '' }}" :class="collapsed ? 'justify-content-center' : ''">
                    <i class="bi bi-file-earmark-text flex-shrink-0" style="width: 20px; height: 20px; font-size: 20px;"></i>
                    <span x-show="!collapsed">Papers</span>
                </a>
            @endif
            
            @if(strtolower($role) === 'patient' || $role === 'PATIENT')
                <a href="{{ route('patient.dashboard') }}" class="sidebar-nav-item d-flex align-items-center gap-3 text-decoration-none {{ request()->routeIs('patient.dashboard') ? 'active' : '' }}" :class="collapsed ? 'justify-content-center' : ''">
                    <i class="bi bi-house-door flex-shrink-0" style="width: 20px; height: 20px; font-size: 20px;"></i>
                    <span x-show="!collapsed">Dashboard</span>
                </a>
                <a href="{{ route('patient.profile') }}" class="sidebar-nav-item d-flex align-items-center gap-3 text-decoration-none {{ request()->routeIs('patient.profile') ? 'active' : '' }}" :class="collapsed ? 'justify-content-center' : ''">
                    <i class="bi bi-person flex-shrink-0" style="width: 20px; height: 20px; font-size: 20px;"></i>
                    <span x-show="!collapsed">Profile</span>
                </a>
                <a href="{{ route('patient.sessions.index') }}" class="sidebar-nav-item d-flex align-items-center gap-3 text-decoration-none {{ request()->routeIs('patient.sessions.*') ? 'active' : '' }}" :class="collapsed ? 'justify-content-center' : ''">
                    <i class="bi bi-calendar-check flex-shrink-0" style="width: 20px; height: 20px; font-size: 20px;"></i>
                    <span x-show="!collapsed">Sessions</span>
                </a>
                <a href="{{ route('patient.resources.index') }}" class="sidebar-nav-item d-flex align-items-center gap-3 text-decoration-none {{ request()->routeIs('patient.resources.*') ? 'active' : '' }}" :class="collapsed ? 'justify-content-center' : ''">
                    <i class="bi bi-folder-fill flex-shrink-0" style="width: 20px; height: 20px; font-size: 20px;"></i>
                    <span x-show="!collapsed">Resources</span>
                </a>
                <a href="{{ route('patient.appointments.index') }}" class="sidebar-nav-item d-flex align-items-center gap-3 text-decoration-none {{ request()->routeIs('patient.appointments.*') ? 'active' : '' }}" :class="collapsed ? 'justify-content-center' : ''">
                    <i class="bi bi-calendar-event flex-shrink-0" style="width: 20px; height: 20px; font-size: 20px;"></i>
                    <span x-show="!collapsed">Appointments</span>
                </a>
                <a href="{{ route('patient.assessments.index') }}" class="sidebar-nav-item d-flex align-items-center gap-3 text-decoration-none {{ request()->routeIs('patient.assessments.*') ? 'active' : '' }}" :class="collapsed ? 'justify-content-center' : ''">
                    <i class="bi bi-clipboard-check flex-shrink-0" style="width: 20px; height: 20px; font-size: 20px;"></i>
                    <span x-show="!collapsed">Assessments</span>
                </a>
                <a href="{{ route('patient.progress.index') }}" class="sidebar-nav-item d-flex align-items-center gap-3 text-decoration-none {{ request()->routeIs('patient.progress.*') ? 'active' : '' }}" :class="collapsed ? 'justify-content-center' : ''">
                    <i class="bi bi-graph-up flex-shrink-0" style="width: 20px; height: 20px; font-size: 20px;"></i>
                    <span x-show="!collapsed">Progress</span>
                </a>
                <a href="{{ route('patient.messages.index') }}" class="sidebar-nav-item d-flex align-items-center gap-3 text-decoration-none {{ request()->routeIs('patient.messages.*') ? 'active' : '' }}" :class="collapsed ? 'justify-content-center' : ''">
                    <i class="bi bi-chat-dots flex-shrink-0" style="width: 20px; height: 20px; font-size: 20px;"></i>
                    <span x-show="!collapsed">Messages</span>
                </a>
                <a href="{{ route('patient.medications.index') }}" class="sidebar-nav-item d-flex align-items-center gap-3 text-decoration-none {{ request()->routeIs('patient.medications.*') ? 'active' : '' }}" :class="collapsed ? 'justify-content-center' : ''">
                    <i class="bi bi-capsule flex-shrink-0" style="width: 20px; height: 20px; font-size: 20px;"></i>
                    <span x-show="!collapsed">Medications</span>
                </a>
                <a href="{{ route('patient.journal.index') }}" class="sidebar-nav-item d-flex align-items-center gap-3 text-decoration-none {{ request()->routeIs('patient.journal.*') ? 'active' : '' }}" :class="collapsed ? 'justify-content-center' : ''">
                    <i class="bi bi-journal-text flex-shrink-0" style="width: 20px; height: 20px; font-size: 20px;"></i>
                    <span x-show="!collapsed">Mood Journal</span>
                </a>
            @else
                <a href="{{ route('doctors.messages.index') }}" class="sidebar-nav-item d-flex align-items-center gap-3 text-decoration-none {{ request()->routeIs('doctors.messages.*') ? 'active' : '' }}" :class="collapsed ? 'justify-content-center' : ''">
                    <i class="bi bi-chat-dots flex-shrink-0" style="width: 20px; height: 20px; font-size: 20px;"></i>
                    <span x-show="!collapsed">Messages</span>
                </a>
            @endif
        </div>
    </nav>
</div>
