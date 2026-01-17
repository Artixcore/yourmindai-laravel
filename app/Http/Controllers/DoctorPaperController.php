<?php

namespace App\Http\Controllers;

use App\Models\DoctorPaper;
use App\Models\User;
use App\Http\Requests\StoreDoctorPaperRequest;
use App\Http\Requests\UpdateDoctorPaperRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DoctorPaperController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $doctor = null)
    {
        $user = $request->user();
        
        // Determine which doctor's papers to show
        if ($doctor) {
            if ($user->role !== 'admin') {
                abort(403, 'Access denied');
            }
            $doctorUser = User::findOrFail($doctor);
        } else {
            $doctorUser = $user;
        }

        $papers = DoctorPaper::where('user_id', $doctorUser->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('doctor.papers', [
            'papers' => $papers,
            'doctor' => $doctorUser,
            'isOwnProfile' => $doctorUser->id === $user->id,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', DoctorPaper::class);
        // This is handled by modal in the view
        return redirect()->back();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDoctorPaperRequest $request)
    {
        $this->authorize('create', DoctorPaper::class);

        $data = $request->validated();
        $user = $request->user();

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $category = $data['category'];
            $filename = $category . '_' . time() . '_' . Str::random(10) . '.' . $extension;
            $path = $file->storeAs('doctors/' . $user->id . '/papers', $filename, 'public');
            
            $data['file_path'] = $path;
            $data['user_id'] = $user->id;
        }

        unset($data['file']);

        DoctorPaper::create($data);

        return redirect()
            ->route('doctors.papers.index')
            ->with('success', 'Document uploaded successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(DoctorPaper $paper)
    {
        $this->authorize('view', $paper);

        return view('doctor.paper-details', [
            'paper' => $paper,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DoctorPaper $paper)
    {
        $this->authorize('update', $paper);

        return view('doctor.paper-edit', [
            'paper' => $paper,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDoctorPaperRequest $request, DoctorPaper $paper)
    {
        $this->authorize('update', $paper);

        $data = $request->validated();

        // Handle file upload if new file provided
        if ($request->hasFile('file')) {
            // Delete old file
            if ($paper->file_path) {
                Storage::disk('public')->delete($paper->file_path);
            }

            // Store new file
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $category = $data['category'] ?? $paper->category;
            $filename = $category . '_' . time() . '_' . Str::random(10) . '.' . $extension;
            $path = $file->storeAs('doctors/' . $paper->user_id . '/papers', $filename, 'public');
            
            $data['file_path'] = $path;
        }

        unset($data['file']);

        $paper->update($data);

        return redirect()
            ->route('doctors.papers.index')
            ->with('success', 'Document updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DoctorPaper $paper)
    {
        $this->authorize('delete', $paper);

        // Delete file from storage
        if ($paper->file_path) {
            Storage::disk('public')->delete($paper->file_path);
        }

        $paper->delete();

        return redirect()
            ->route('doctors.papers.index')
            ->with('success', 'Document deleted successfully!');
    }

    /**
     * Download the paper file.
     */
    public function download(DoctorPaper $paper)
    {
        $this->authorize('view', $paper);

        if (!$paper->file_path || !Storage::disk('public')->exists($paper->file_path)) {
            abort(404, 'File not found');
        }

        $path = Storage::disk('public')->path($paper->file_path);
        $filename = $paper->title . '.' . pathinfo($paper->file_path, PATHINFO_EXTENSION);

        return response()->download($path, $filename);
    }
}
