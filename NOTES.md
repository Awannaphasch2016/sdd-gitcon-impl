# Notes

## Checklist gate

`/speckit-implement` read `specs/001-gitcon-public-pages/checklists/requirements.md` before writing the site.

| Checklist | Total | Checked | Unchecked | Status |
| --- | --- | --- | --- | --- |
| requirements.md | 16 | 16 | 0 | PASS |

There were no unchecked items, so implementation continued.

## Clarifications

`specs/001-gitcon-public-pages/spec.md` records the choices that SPEC.md section 5 does not already answer. Where that section is silent, the simplest option was used:

- Titles other than Home are the page heading plus " - GIT 2025".
- A successful contact response keeps the organiser details and does not show the form.
- A disallowed method, including a non-GET request for an unknown path, returns 405 with the heading "Method not allowed".
- Stored subject, message, name, email, and phone are trimmed. A missing or whitespace-only phone is stored as an empty string. The timestamp is UTC ISO 8601 to the second with a Z suffix.
- At a width of 375 px the four navigation links stay visible. There is no menu button.

## Local server

Verified with `php -S 127.0.0.1:8080 -t public`:

- GET `/`, `/program`, `/program/`, `/registration-fee`, and `/contact` return 200 with the shared header and footer.
- POST `/contact` with empty fields returns 200, field errors, and no confirmation.
- POST `/contact` with valid fields returns 200 and "Thank you, your message has been sent." followed by the subject.
- GET `/contact/submissions.json` returns `Content-Type: application/json` and `[]` before any accepted message.
- GET of an unknown path returns 404. POST `/program` returns 405.

Messages are appended to `data/submissions.jsonl`. The same list is rewritten to `public/contact/submissions.json` because PHP's built-in server will not run PHP for a URL that contains a dot unless that file exists. Apache, and `php -S 127.0.0.1:8080 -t public public/index.php`, return 405 for other methods on `/contact/submissions.json`. Without the router script, the built-in server serves the published JSON file for POST as well.
