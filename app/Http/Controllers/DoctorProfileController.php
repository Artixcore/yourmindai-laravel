<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\UpdateDoctorProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DoctorProfileController extends Controller
{
    /**
     * Show doctor profile settings page.
     */
    public function edit(Request $request, $doctor = null)
    {
        $user = $request->user();
        
        // Determine which doctor to edit
        if ($doctor) {
            // Admin can view any doctor
            if ($user->role !== 'admin') {
                abort(403, 'Access denied');
            }
            $doctorUser = User::findOrFail($doctor);
            if (!in_array($doctorUser->role, ['doctor', 'admin'])) {
                abort(404, 'Doctor not found');
            }
        } else {
            // Doctor viewing own profile
            if ($user->role !== 'doctor' && $user->role !== 'admin') {
                abort(403, 'Access denied');
            }
            $doctorUser = $user;
        }

        return view('doctor.settings', [
            'doctor' => $doctorUser,
            'isOwnProfile' => $doctorUser->id === $user->id,
        ]);
    }

    /**
     * Update doctor profile.
     */
    public function update(UpdateDoctorProfileRequest $request, $doctor = null)
    {
        $user = $request->user();
        
        // Determine which doctor to update
        if ($doctor) {
            if ($user->role !== 'admin') {
                abort(403, 'Access denied');
            }
            $doctorUser = User::findOrFail($doctor);
        } else {
            $doctorUser = $user;
        }

        $data = $request->validated();
        
        // Handle avatar upload
        if ($request->hasFile('profile_photo')) {
            // Delete old avatar if exists
            if ($doctorUser->avatar_path) {
                Storage::disk('public')->delete($doctorUser->avatar_path);
            }

            // Store new avatar
            $file = $request->file('profile_photo');
            $extension = $file->getClientOriginalExtension();
            $filename = 'avatar_' . time() . '_' . Str::random(10) . '.' . $extension;
            $path = $file->storeAs('doctors/' . $doctorUser->id . '/avatar', $filename, 'public');
            
            $data['avatar_path'] = $path;
        }

        // Remove profile_photo from data (we've handled it)
        unset($data['profile_photo']);

        $doctorUser->update($data);

        return redirect()
            ->route('doctors.settings', $doctor ? ['doctor' => $doctor] : [])
            ->with('success', 'Profile updated successfully!');
    }
}
