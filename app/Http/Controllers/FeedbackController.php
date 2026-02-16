<?php

namespace App\Http\Controllers;

use App\Http\Requests\Public\StoreFeedbackMessageRequest;
use App\Models\FeedbackMessage;
use App\Services\RecaptchaService;
use Illuminate\Http\RedirectResponse;

class FeedbackController extends Controller
{
    /**
     * Store feedback form message.
     */
    public function store(StoreFeedbackMessageRequest $request): RedirectResponse
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

        FeedbackMessage::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'type' => $validated['type'],
            'message' => $validated['message'],
        ]);

        return back()->with('success', 'Thank you for your feedback. We appreciate your input.');
    }
}
