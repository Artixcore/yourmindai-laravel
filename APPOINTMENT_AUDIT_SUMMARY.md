# Appointment System Audit Summary

## STEP 1 — AUDIT RESULTS

### 1. doctor_id Status
- **Appointment model**: `doctor_id` exists (fillable, belongsTo User)
- **AppointmentRequest model**: `doctor_id` exists (fillable, nullable)
- **Booking form** (`appointment/book.blade.php`):
  - When visiting via `/book/{doctor_number}`: doctor is pre-selected (hidden input)
  - When visiting `/book`: doctor dropdown exists with option "Any available doctor" (value="")
  - **doctor_id is NOT required** — visitor can submit without selecting a doctor

### 2. How Appointments Are Stored
- **AppointmentRequest** (visitor requests): `appointment_requests` table
  - `preferred_date`, `preferred_time`, `doctor_id` (nullable)
  - Stored via `AppointmentRequestController::store()`
- **Appointment** (confirmed appointments): `appointments` table
  - `doctor_id`, `patient_id`, `date` (datetime), `time_slot`, `status`, etc.
  - Created via `AppointmentRequestController::storePatient()` when admin creates patient from request
  - Uses `date` column (not `appointment_date`)

### 3. Calendar Implementation
- **No calendar widget** — booking form uses `<input type="date">` (native date picker)
- **No FullCalendar** — no calendar library found
- **No Bootstrap datepicker** — simple HTML5 date input only
- Calendar must be added for red indicators

### 4. AJAX Usage
- **Form**: Uses `.ajax-form` class (jQuery)
- **app-ajax.js**: Handles form submit via AJAX, returns JSON `{success, message, errors}`
- **AJAX already used** for appointment request form
- **Global alert system**: `showAjaxAlert()` in app-ajax.js, `#ajax-alerts` container

### 5. Existing Daily Limits
- **AppointmentSlotService**:
  - `MAX_APPOINTMENTS_PER_DOCTOR_PER_DAY = 5`
  - `isDayFull()`, `countOnDate()`, `validateSlot()` exist
  - Used only in `storePatient()` when admin creates patient + appointment
- **NOT enforced** on visitor's `AppointmentRequestController::store()`

### 6. Database Indexes
- `appointments` table: `index('doctor_id')`, `index('date')`
- **No composite index** on `(doctor_id, date)` — consider adding for performance

### 7. Validation
- **AppointmentRequestController::store()**: `doctor_id` is `nullable|exists:users,id`
- No FormRequest — uses inline `$request->validate()`
- No daily limit validation on request store

---

## Implementation Plan

| Step | Action |
|------|--------|
| 2 | Make doctor_id required when doctors shown; validate doctor_id required + exists |
| 3 | Add daily limit check in store() using AppointmentSlotService; block when count >= 5 |
| 4 | Add GET /appointments/doctor/{doctor}/availability endpoint; add inline calendar with red marks |
| 5 | On doctor change: fetch availability, update calendar; on AJAX submit success: refresh day status |
| 6 | Use 422 for validation; use global alerts; no raw error pages |

---

## DELIVERABLES (Implemented)

### 1. Audit Summary
See above.

### 2. Files Modified
- `app/Http/Controllers/AppointmentRequestController.php` — doctor validation, daily limit, availability endpoint
- `resources/views/appointment/book.blade.php` — required doctor select, inline calendar with red marks
- `routes/web.php` — availability route
- `database/migrations/2026_02_17_120000_add_doctor_date_index_to_appointments.php` — composite index

### 3. New Endpoint
- `GET /appointments/doctor/{doctor}/availability?month=YYYY-MM`
- Returns `[{date, count, overloaded}, ...]` for dates with count >= 5

### 4. Validation Changes
- Doctor required: must have doctor_id or resolvable doctor_number
- Daily limit: blocks when doctor has 5+ appointments on selected date

### 5. No Unrelated Code Broken
- AppointmentRequestController store logic preserved
- AppointmentSlotService unchanged
- storePatient flow unchanged
- Doctor/admin panels unaffected
