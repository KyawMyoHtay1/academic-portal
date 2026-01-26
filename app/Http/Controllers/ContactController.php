<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ContactMessage;

class ContactController extends Controller
{
    /**
     * Store contact form message
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'firstName' => 'required|string|max:100',
            'lastName'  => 'required|string|max:100',
            'email'     => 'required|email|max:255',
            'phone'     => 'nullable|string|max:30',
            'subject'   => 'required|string|max:100',
            'message'   => 'required|string|max:5000',
        ]);

        ContactMessage::create([
            'first_name' => $validated['firstName'],
            'last_name'  => $validated['lastName'],
            'email'      => $validated['email'],
            'phone'      => $validated['phone'] ?? null,
            'subject'    => $validated['subject'],
            'message'    => $validated['message'],
        ]);

        return back()->with('success', 'Your message has been sent successfully.');
    }
}
