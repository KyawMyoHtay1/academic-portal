<?php

namespace App\Http\Controllers;

use App\Http\Requests\Public\StoreContactMessageRequest;
use App\Models\ContactMessage;
use App\Services\RecaptchaService;
use Illuminate\Http\RedirectResponse;

class ContactController extends Controller
{
    /**
     * Store contact form message.
     */
    public function store(StoreContactMessageRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if (config('recaptcha.site_key') && isset($validated['recaptcha_token'])) {
            /** @var RecaptchaService $recaptcha */
            $recaptcha = app(RecaptchaService::class);
            if (! $recaptcha->verify($validated['recaptcha_token'], $request->ip())) {
                return back()
                    ->withErrors(['recaptcha_token' => 'reCAPTCHA verification failed. Please try again.'])
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
