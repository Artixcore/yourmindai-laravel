<?php

namespace App\Http\Controllers;

use App\Models\ClientNote;
use App\Models\PatientProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClientNotesController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $patient = PatientProfile::where('user_id', $user->id)->firstOrFail();

        $notes = ClientNote::where('patient_id', $patient->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('client.notes.index', compact('notes'));
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $patient = PatientProfile::where('user_id', $user->id)->firstOrFail();

        $validated = $request->validate([
            'type' => 'required|in:text,voice',
            'content' => 'required_if:type,text|nullable|string|max:10000',
            'voice' => 'required_if:type,voice|nullable|file|mimes:mp3,wav,ogg,webm,m4a|max:20480',
        ]);

        $data = [
            'patient_id' => $patient->id,
            'type' => $validated['type'],
        ];

        if ($validated['type'] === 'text') {
            $data['content'] = $validated['content'] ?? '';
        } else {
            if ($request->hasFile('voice')) {
                $path = $request->file('voice')->store('client-notes/voice', 'public');
                $data['voice_path'] = $path;
                $data['content'] = 'Voice note';
            } else {
                return redirect()->back()->withInput()->withErrors(['voice' => 'Please record or upload an audio file.']);
            }
        }

        ClientNote::create($data);

        return redirect()->route('client.notes.index')->with('success', 'Note saved.');
    }
}
