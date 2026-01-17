<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $query = ContactMessage::with('resolvedBy');
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $messages = $query->latest()->paginate(20);
        
        return view('admin.contact.index', compact('messages'));
    }
    
    public function show(ContactMessage $contact)
    {
        $contact->load('resolvedBy');
        return view('admin.contact.show', compact('contact'));
    }
    
    public function resolve(Request $request, ContactMessage $contact)
    {
        $request->validate([
            'admin_notes' => 'nullable|string',
        ]);
        
        $contact->update([
            'status' => 'resolved',
            'admin_notes' => $request->admin_notes,
            'resolved_at' => now(),
            'resolved_by' => auth()->id(),
        ]);
        
        return back()->with('success', 'Message marked as resolved.');
    }
    
    public function addNotes(Request $request, ContactMessage $contact)
    {
        $request->validate([
            'admin_notes' => 'required|string',
        ]);
        
        $contact->update([
            'admin_notes' => $request->admin_notes,
        ]);
        
        return back()->with('success', 'Notes added successfully.');
    }
}
