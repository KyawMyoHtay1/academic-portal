# 4.3 MOSCOW Prioritization – User Settings & Global Search (add to existing 4.3)

Add the following **Manage User** entries into your existing **Should** section under "Manage User" (after "Password Management" and before the next major heading such as "Manage Student").

---

## Should – Manage User (add these blocks)

**User Settings / Preferences**

| Feature | Justification |
|--------|----------------|
| User Settings / Preferences | Let users control their experience and email preferences |
| - View Settings | Display current preferences with defaults (email_announcements, email_messages, email_notifications) |
| - Update Preferences | Allow users to change their preference values |
| - Validate preference values – boolean only | Ensure valid data (email_announcements, email_messages, email_notifications) |
| - Store preferences in user record (JSON), merge with defaults | Persist and apply user choices |
| - Restrict to authenticated user's own preferences | Users can only edit their own settings |

**Global Search**

| Feature | Justification |
|--------|----------------|
| Global Search | Find content quickly from one place across the portal |
| - Search scope by role | Guest: public courses, news, static pages; Staff: students, users, courses, subjects, announcements; Teacher: assigned courses/subjects/assignments, announcements; Student: courses, assignments, announcements |
| - Enforce minimum query length (e.g. 2 characters) | Avoid noisy or empty results |
| - Return results with type, title, subtitle, URL | Clear navigation to matched content |
| - Limit results per entity type (e.g. 5 per type) | Keep response size manageable |
| - Debounce search requests in UI | Reduce server load and improve UX while typing |

---

## Where to insert in your 4.3 document

- Open section **4.3 MOSCOW Prioritization**.
- In the **Should** section, find **Manage User** (with Register User, Update User, Delete User, Search User, Password Management).
- After the last **Manage User** "Should" item (e.g. after "Update Password with confirmation"), add the two blocks above: **User Settings / Preferences** and **Global Search**.

This keeps 4.3 aligned with the new functional requirements in 4.2 (User Settings/Preferences and Global Search).
