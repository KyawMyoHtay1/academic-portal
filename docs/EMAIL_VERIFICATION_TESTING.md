# How to Confirm Email When Testing

When you see the message *"Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? …"* with **Resend Verification Email** and **Log Out**, use one of the options below to verify in a test environment.

---

## Option 1: One-click dev link (easiest when `APP_ENV=local`)

If your `.env` has **APP_ENV=local**:

1. Stay logged in on the verification prompt page.
2. In the browser address bar, go to: **`/dev/verify-email-now`**
   - Full URL example: `http://localhost:8000/dev/verify-email-now`
3. You will be redirected and your email will be marked as verified; then you’ll land on the dashboard.

This route **only exists when APP_ENV=local** and is not registered in production.

---

## Option 2: Get the link from the log (when using `MAIL_MAILER=log`)

If `.env` has **MAIL_MAILER=log** (Laravel default when not set):

1. On the verification page, click **Resend Verification Email**.
2. Open **`storage/logs/laravel.log`**.
3. Search for the latest “Verify Email” mail; the full verification URL is in the log (it contains `verify-email/` and a long signature).
4. Copy that URL, paste it into the browser, and press Enter. You’ll be verified and redirected.

---

## Option 3: Mark the user as verified in the database

Use Tinker to set the user as verified (no email link needed):

```bash
php artisan tinker
```

Then (replace with the actual email):

```php
$u = \App\Models\User::where('email', 'your@test.email')->first();
$u->markEmailAsVerified();
# or: $u->email_verified_at = now(); $u->save();
exit
```

After that, log in again (or refresh); the app will treat the email as verified.

---

## Option 4: Use a mail catcher (Mailtrap / Mailhog)

If you use **Mailtrap**, **Mailhog**, or similar:

1. Configure `.env` with that mailer (e.g. SMTP settings for Mailtrap).
2. Click **Resend Verification Email**.
3. Open the mail catcher’s inbox (e.g. Mailtrap inbox or Mailhog UI).
4. Open the verification email and click the link in the message.

---

## Summary

| Method              | When to use                          |
|---------------------|--------------------------------------|
| **/dev/verify-email-now** | Local dev, quick one-click verify   |
| **Log file**        | MAIL_MAILER=log, copy link from log  |
| **Tinker**          | Any env, mark user verified in DB   |
| **Mailtrap/Mailhog**| You use a captured-mail inbox       |

To confirm that the email is treated as verified: after using any option, you should be able to open the dashboard and use the app without seeing the verification prompt again.
