# HTTP contract: GIT 2025 public pages

Paths below ignore a query string. A trailing slash is removed before matching (`/` stays `/`). Letter case matters.

Shared HTML layout for every HTML response: header link "GIT 2025" to `/`; navigation Home, Program, Registration Fee, Contact; one `<main>`; one `<h1>`; footer organiser, address, phone, fax, mailto email, and copyright as in the feature spec. Every `id` on the page is unique. Text from fixtures and from the visitor is escaped.

## GET `/`

- Status 200.
- Title: `Home - GIT 2025 Responsible Gem & Jewelry Supply Chain`.
- `<h1>`: `GIT 2025`.
- Separate lines: `Responsible Gem & Jewelry Supply Chain`, `8 - 9 September 2025`, `Bangkok, Thailand`.
- Three verbatim paragraphs from the feature spec.
- Body links: `Registration Fee` → `/registration-fee`, `Program` → `/program`.
- Other methods: 405, title `Method not allowed - GIT 2025`, `<h1>` `Method not allowed`.

## GET `/program`

- Status 200. Title `Program - GIT 2025`. `<h1>` `Program`.
- One section per `days[]` entry, in order. `<h2>` is `title`.
- One table per section. Header cells: Time, Title, Speaker, Affiliation, Room. One body row per session, in order. Cell text is the field value; empty string is an empty cell.
- Other methods: 405 as above.

## GET `/registration-fee`

- Status 200. Title `Registration Fee - GIT 2025`. `<h1>` `Registration Fee`.
- For each `tables[]` entry, in order: `<h2>` is `title`, then a table. Header cells are `columns` in order. Body rows are `rows` in order, cells unchanged.
- After the tables: `All fees include conference materials, lunches and coffee breaks on both days.`
- Other methods: 405 as above.

## GET `/contact`

- Status 200. Title `Contact - GIT 2025`. `<h1>` `Contact`.
- Body repeats organiser name, address, phone, fax, and mailto email, then the form.
- Form: `method="post"` `action="/contact"`.
- Controls, each with a `<label>` of the given text and `name` in brackets:
  - Group [`group`]: `<select>` options exactly `General`, `Registration`, `Abstract Submission`, `Sponsorship`. On first view `General` is selected. No blank option.
  - Subject [`subject`]: text input.
  - Message [`message`]: textarea.
  - Full Name [`name`]: text input.
  - Email [`email`]: email input.
  - Phone Number [`phone`]: text input.
  - Submit button text `Send`.
- Other methods except POST: 405 as above.

## POST `/contact`

Validated on the server.

Failure: status 200, same form, raw submitted value preserved in each control, one element directly after each failing control with `data-error="<field name>"` and the message below. No element with `data-confirmation`.

| Field | Message |
| --- | --- |
| `group` | Please select a group |
| `subject` | Please enter a subject |
| `message` | Please enter a message |
| `name` | Please enter your full name |
| `email` | Please enter a valid email |
| `phone` | Please enter a valid phone number |

Success: status 200, `<h1>` Contact, organiser details still shown, form not shown, one element with `data-confirmation` whose text contains `Thank you, your message has been sent.` followed by the submitted subject (trimmed subject as stored). One new JSON line is appended to `data/submissions.jsonl`.

## GET `/contact/submissions.json`

- Status 200.
- `Content-Type: application/json`.
- Body: JSON array, submission order. Objects use keys `group`, `subject`, `message`, `name`, `email`, `phone`, `submitted_at` only.
- No stored messages: `[]`.
- Other methods: 405 as above. Nothing is appended.

## Any other path

- GET: status 404, shared layout, title `Page not found - GIT 2025`, `<h1>` `Page not found`, link `/` with text `Back to Home`.
- Any other method: status 405, shared layout, `<h1>` `Method not allowed`.

## GET `/assets/*`

- If the file exists under `public/assets/`, return that file and do not use the HTML layout.
- If it does not, status 404 with a plain text body, not the not-found page layout.
