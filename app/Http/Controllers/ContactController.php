<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Services\RecaptchaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Store contact form message.
     */
    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'firstName' => 'required|string|max:100',
            'lastName' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:30',
            'subject' => 'required|string|max:100',
            'message' => 'required|string|max:5000',
        ];

        if (config('recaptcha.site_key')) {
            $rules['recaptcha_token'] = ['required', 'string'];
        }

        $validated = $request->validate($rules);

        if (config('recaptcha.site_key') && isset($validated['recaptcha_token'])) {
            /** @var RecaptchaService $recaptcha */
            $recaptcha = app(RecaptchaService::class);
            if (! $recaptcha->verify($validated['recaptcha_token'], $request->ip())) {
                return back()
                    .withErrors(['recaptcha_token' => 'reCAPTCHA verification failed. Please try again.'])
                        ->withInput();
            }
        }

        ContactMessage::create([
            'first_name' => $validated['firstName'],
            'last_name' => $validated['lastName'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'subject' => $validated['subject'],
            'message' => $validated['message'],
        ]);

        return back()->with('success', 'Your message has been sent successfully.');
    }
}
