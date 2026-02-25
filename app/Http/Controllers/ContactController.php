<?php

namespace App\Http\Controllers;

use App\Http\Requests\Public\StoreContactMessageRequest;
use App\Models\ContactMessage;
use App\Models\User;
use App\Notifications\ContactMessageReceived;
use App\Services\RecaptchaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Notification;

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

        $contactMessage = ContactMessage::create([
            'first_name' => $validated['firstName'],
            'last_name' => $validated['lastName'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'subject' => $validated['subject'],
            'message' => $validated['message'],
        ]);

        $staffRecipients = User::query()
            ->whereIn('role', ['staff', 'admin'])
            ->get();

        if ($staffRecipients->isNotEmpty()) {
            Notification::send($staffRecipients, new ContactMessageReceived($contactMessage));
        }

        return back()->with('success', 'Your message has been sent successfully.');
    }
}
