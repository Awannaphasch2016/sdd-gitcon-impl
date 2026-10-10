# Feature Specification: GIT 2025 Conference Public Website

**Feature Branch**: `exp/2026-10-09-gitcon-v1-speckit-01`

**Created**: 2026-10-09

**Status**: Draft

**Input**: User description: "A small English-only public website for GIT 2025, The 8th International Gem and Jewelry Conference, with Home, Program, Registration Fee, and Contact pages. Published schedule and fees come from the fixed records in SPEC.md and are read on every visit. Visitors can send a contact message. Unknown addresses and disallowed actions are handled explicitly. The full fixed source text is SPEC.md at the repository root (specification version gitcon-v1)."

## Clarifications

### Session 2026-10-09

- Q: What document title do Program, Registration Fee, Contact, the not-found page, and the method-not-allowed page use? → A: The page heading followed by " - GIT 2025" (Home keeps its fully specified title).
- Q: What does a successful contact response show besides the confirmation? → A: The organiser details stay on the page and the form is not shown.
- Q: What is returned for a disallowed method, including a non-GET request to an unknown path? → A: HTTP 405 with the shared header and footer and the heading "Method not allowed". Unknown paths accept GET only.
- Q: How are contact values stored and shown? → A: Subject, message, name, email, and phone are stored trimmed; a missing or whitespace-only phone is stored as an empty string; the time is UTC ISO 8601 to the second with a Z suffix; published and visitor text is shown as text, not as page markup. The form redisplays the raw typed value after a failure.
- Q: At a width of 375 px, are the navigation links always visible or hidden behind one button? → A: Always visible. No menu button.

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Read the conference home page (Priority: P1)

A visitor opens the site and immediately sees what GIT 2025 is, when and where it happens, and why the organiser is holding it. The same header and footer appear on this page as on every other page, so the visitor can move to Program, Registration Fee, or Contact, and can identify the organiser.

**Why this priority**: Without a recognizable home page and shared navigation, the other pages have nowhere to start. This is the smallest usable public site.

**Independent Test**: Open the home address and confirm the title, the single main heading, the theme, dates, place, the three published paragraphs, the extra body links, and the shared header and footer.

**Acceptance Scenarios**:

1. **Given** a visitor opens `/`, **When** the page loads, **Then** the document title is "Home - GIT 2025 Responsible Gem & Jewelry Supply Chain", the only `<h1>` is "GIT 2025", and directly below it the theme "Responsible Gem & Jewelry Supply Chain", the dates "8 - 9 September 2025", and the place "Bangkok, Thailand" appear on separate lines.
2. **Given** a visitor is on the home page, **When** they read the introduction, **Then** they see these three paragraphs verbatim: (a) "The Gem and Jewelry Institute of Thailand (public organization) or GIT has proudly hosted the Gem and Jewelry Conferences since 2006. Each event has been met with the warmest reception and overwhelming success. The past conferences have not only served as academic platforms but also as networking forums for peers, academia, and industry experts." (b) "This year, the 8th International Gem and Jewelry Conference (GIT 2025) will welcome you to Bangkok with the theme 'Responsible Gem & Jewelry Supply Chain'. The theme was chosen with the main purpose of bringing together experts, academics, and industry professionals to exchange knowledge and ideas on the development of the gem industry, the use of environmentally friendly technology, and the creation of a sustainable supply chain." (c) "The conference will take place in Bangkok, Thailand, from September 8 - 9, 2025. Attendees will also have the opportunity to participate in the 72nd Bangkok Gem and Jewelry Fair, held concurrently."
3. **Given** a visitor is on the home page, **When** they look in the body, **Then** they find a link "Registration Fee" to `/registration-fee` and a link "Program" to `/program`, in addition to the same links in the header.
4. **Given** any page, **When** it is shown, **Then** the header starts with "GIT 2025" linking to `/`, followed by navigation links in this order and with these exact texts: Home (`/`), Program (`/program`), Registration Fee (`/registration-fee`), Contact (`/contact`).
5. **Given** a viewport 375 px wide, **When** a visitor looks at the header, **Then** the four navigation links are visible without using a menu button, and the page does not scroll horizontally.
6. **Given** any page, **When** the visitor reaches the footer, **Then** they see the organiser "The Gem and Jewelry Institute of Thailand (Public Organization)", the address "140, 140/1-3, 140/5 ITF - Tower Building, 1st - 4th and 6th Floor, Silom Road, Suriyawong, Bangrak, Bangkok 10500, Thailand", the phone "(+66) 2634 4999 ext. 451-457", the fax "(+66) 2634 4970", the email "gitconference@git.or.th" as a mailto link, and the copyright line "Copyright © 2025. All rights reserved. The Gem and Jewelry Institute of Thailand (Public Organization)."

---

### User Story 2 - Read the program (Priority: P2)

A visitor opens the program to see each conference day and every session in the order the organiser published them, including the time, title, speaker, affiliation, and room.

**Why this priority**: The schedule is the main reason attendees open the site after they know the event exists.

**Independent Test**: Open `/program` and compare every day heading and every session cell with the published program records, in order, including blank cells.

**Acceptance Scenarios**:

1. **Given** the published program records, **When** a visitor opens `/program`, **Then** the only `<h1>` is "Program" and there is one section per published day, in published order, with an `<h2>` equal to that day's title.
2. **Given** a program day, **When** its section is shown, **Then** it contains one table whose header row is "Time", "Title", "Speaker", "Affiliation", "Room", and one body row per session in published order.
3. **Given** a session field is empty in the published records, **When** the row is shown, **Then** that cell is empty and the other cells still show their text unchanged.
4. **Given** a published day title or session title is changed in the published records, **When** a visitor reloads `/program`, **Then** the page shows the new text and does not keep a previous copy written into the page itself.

---

### User Story 3 - Read registration fees (Priority: P2)

A visitor opens the fee page to compare registrant types and rate periods, including the Thai-citizen subsidy table, and reads what the fee includes.

**Why this priority**: Attendees need the prices before they decide to register. The page is independent of the contact form.

**Independent Test**: Open `/registration-fee` and confirm both table titles, column headings, every cell, and the inclusion sentence.

**Acceptance Scenarios**:

1. **Given** a visitor opens `/registration-fee`, **When** the page loads, **Then** the only `<h1>` is "Registration Fee".
2. **Given** the published fee records, **When** the page is shown, **Then** each published table appears in order, with an `<h2>` equal to its title, a header row equal to the published column headings, and body cells equal to the published rows, unchanged.
3. **Given** the tables are visible, **When** the visitor reads below them, **Then** they see the sentence "All fees include conference materials, lunches and coffee breaks on both days."

---

### User Story 4 - Send a contact message (Priority: P3)

A visitor who needs help chooses a group, writes a subject and message, and leaves their name, email, and optionally a phone number. If something is missing or invalid, they see a specific message beside each problem and keep what they typed. If the message is accepted, they see a confirmation that includes their subject, and the message is kept so it can be read back later in the order it was sent.

**Why this priority**: The form is the only way the site accepts visitor input. It depends on the shared page shell, and the public reading pages are useful without it.

**Independent Test**: Submit an empty form, an invalid email and phone, and one valid message. Confirm errors, preserved values, the confirmation, and that the submissions list grows in order and is empty before the first success.

**Acceptance Scenarios**:

1. **Given** a visitor opens `/contact`, **When** the page loads, **Then** the only `<h1>` is "Contact", the organiser name, address, phone, fax, and email from the footer are repeated above the form, and the document title is "Contact - GIT 2025".
2. **Given** the contact page, **When** the visitor looks at the form, **Then** it is `<form method="post" action="/contact">` and it has a labeled Group select named `group` with only the options General, Registration, Abstract Submission, and Sponsorship (General selected on first view); a labeled Subject text field named `subject`; a labeled Message textarea named `message`; a labeled Full Name text field named `name`; a labeled Email field named `email`; a labeled Phone Number text field named `phone`; and a submit button labeled "Send".
3. **Given** a submission, **When** the site checks it, **Then** the check happens on the server even if the browser would have blocked the form: `group` must be one of the four options; `subject`, `message`, `name`, and `email` are required and must not be blank after trimming; `email` must contain exactly one "@" with at least one character on each side and a "." after the "@"; `phone` may be empty and, when it has characters other than surrounding whitespace, must contain only digits, spaces, "+", "-", "(", and ")".
4. **Given** one or more fields fail, **When** the response returns, **Then** the status is HTTP 200, the form is shown again, every previously entered value is still in its control, one error sits directly after each failing control in an element with `data-error` equal to that field's name, and no confirmation text is present. The messages are exactly: group "Please select a group", subject "Please enter a subject", message "Please enter a message", name "Please enter your full name", email "Please enter a valid email", phone "Please enter a valid phone number".
5. **Given** a valid submission, **When** the site accepts it, **Then** the status is HTTP 200, the `<h1>` is "Contact", a block with `data-confirmation` contains the text "Thank you, your message has been sent." followed by the submitted subject, and the form is not shown.
6. **Given** a valid submission, **When** it is stored, **Then** the stored record has the trimmed group, subject, message, name, email, and phone, plus the server time in ISO 8601 UTC to the second with a Z suffix, and it remains available for the life of this running copy of the site.
7. **Given** stored messages, **When** a reader opens `GET /contact/submissions.json`, **Then** the response media type is `application/json` and the body is a JSON array of those messages in submission order, each with keys `group`, `subject`, `message`, `name`, `email`, `phone`, and `submitted_at`. When nothing has been stored, the body is `[]`.

---

### User Story 5 - Recover from a wrong address or action (Priority: P3)

A visitor who follows a bad link sees a not-found page with the usual header and footer and a way home. A disallowed action on a known page is refused rather than treated as a normal visit.

**Why this priority**: The four public pages already deliver the conference information. Clear failures keep those pages predictable.

**Independent Test**: Open an unknown address with GET, repeat it with POST, and POST to Home, Program, and Registration Fee. Confirm 404 versus 405 and the required text.

**Acceptance Scenarios**:

1. **Given** a GET request for a path other than `/`, `/program`, `/registration-fee`, `/contact`, and `/contact/submissions.json` (trailing slashes ignored), **When** the site responds, **Then** the status is HTTP 404, the shared header and footer are present, the only `<h1>` is "Page not found", and a link to `/` reads "Back to Home".
2. **Given** a method other than GET on Home, Program, Registration Fee, or an unknown path, **When** the site responds, **Then** the status is HTTP 405, the shared header and footer are present, and the only `<h1>` is "Method not allowed".
3. **Given** a method other than GET or POST on `/contact`, or a method other than GET on `/contact/submissions.json`, **When** the site responds, **Then** the status is HTTP 405 with the same "Method not allowed" page.

---

### Edge Cases

- A trailing slash is the same page: `/program` and `/program/` match, as do `/`, `/registration-fee`, `/contact`, and `/contact/submissions.json`.
- Query strings are ignored for choosing the page. Letter case in the path is significant; `/Program` is not the program page.
- Empty session or fee cells stay empty. They are not filled with a placeholder word.
- Whitespace-only subject, message, name, or email fails the required-field check. The form shows the original typed value, including the spaces.
- An email with no "@", more than one "@", nothing on one side of "@", or no "." after "@" fails with "Please enter a valid email", including when the email is blank.
- A missing phone, or a phone that is only whitespace, is accepted and stored as an empty string. A phone containing a letter fails with "Please enter a valid phone number".
- An unrecognised group value fails with "Please select a group". The select only offers the four legal options, so no extra option is added; none of them is selected when the posted value is not one of the four. A recognised posted group stays selected.
- A failed submission never shows the confirmation sentence. A successful submission never redisplays the form.
- The submissions list is empty (`[]`) before the first accepted message, then lists accepted messages only, oldest first.
- Two accepted messages keep submission order even if their subjects are identical.
- Published text and visitor text are shown as text. They are not interpreted as page markup.
- Every `id` on a page is unique. Each page has one `<main>` and one `<h1>`.
- Addresses under `/assets/` are files, not pages. A missing asset does not use the not-found page layout.
- The site does not send email, show a CAPTCHA, limit how often someone may submit, require a login, or add pages, languages, search, downloads, or galleries.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: Every page MUST show the shared header, navigation, and footer described in User Story 1, with one `<main>` and one `<h1>`.
- **FR-002**: Navigation labels and destinations MUST be exactly Home (`/`), Program (`/program`), Registration Fee (`/registration-fee`), and Contact (`/contact`), in that order. "GIT 2025" in the header MUST link to `/`.
- **FR-003**: At a width of 375 px the four navigation links MUST be visible without a menu button, and the page MUST NOT scroll horizontally.
- **FR-004**: The home page MUST use the title, heading, theme, dates, place, three verbatim paragraphs, and body links in User Story 1.
- **FR-005**: The program page MUST render each published day and session from the current published program records on every request, in file order, with empty fields as empty cells, and MUST NOT hardcode session text in the page.
- **FR-006**: The registration fee page MUST render each published fee table from the current published fee records on every request, then the inclusion sentence, with cell text unchanged.
- **FR-007**: The contact page MUST repeat the organiser name, address, phone, fax, and email above the form, and MUST offer the labeled controls and names in User Story 4. General MUST be the default group on first view. There MUST be no empty placeholder option.
- **FR-008**: The site MUST validate contact submissions on the server using the rules and exact error messages in User Story 4, preserve entered values on failure, and MUST NOT show confirmation text on failure.
- **FR-009**: The site MUST accept a valid message, store the trimmed fields and an ISO 8601 UTC timestamp to the second with a Z suffix, and respond with HTTP 200, the Contact heading, and a `data-confirmation` block containing "Thank you, your message has been sent." followed by the submitted subject. The form MUST NOT be shown on success.
- **FR-010**: `GET /contact/submissions.json` MUST return `Content-Type: application/json` and a JSON array of stored messages in submission order with keys `group`, `subject`, `message`, `name`, `email`, `phone`, and `submitted_at`. An empty store MUST return `[]`.
- **FR-011**: The site MUST NOT send email, use CAPTCHA, rate-limit submissions, or depend on an outside service to show pages or accept a message.
- **FR-012**: A GET for an unknown path MUST return HTTP 404 with the shared header and footer, heading "Page not found", and a "Back to Home" link to `/`. Methods other than GET on Home, Program, Registration Fee, and unknown paths, methods other than GET or POST on `/contact`, and methods other than GET on `/contact/submissions.json` MUST return HTTP 405 with the shared header and footer and heading "Method not allowed".
- **FR-013**: Document titles other than Home MUST be "Program - GIT 2025", "Registration Fee - GIT 2025", "Contact - GIT 2025", "Page not found - GIT 2025", and "Method not allowed - GIT 2025" for those pages.
- **FR-014**: Trailing slashes MUST be equivalent for every route in this specification. The page language is English only.

### Key Entities *(include if feature involves data)*

- **Published program**: Ordered days. Each day has a title and ordered sessions. Each session has time, title, speaker, affiliation, and room. Any of those session fields may be empty. The authoritative records are the program fixture in SPEC.md section 6.1.
- **Published fees**: An ordered list of column headings and an ordered list of tables. Each table has a title and rows of cells. The authoritative records are the fee fixture in SPEC.md section 6.2.
- **Contact message**: Group (one of General, Registration, Abstract Submission, Sponsorship), subject, message, full name, email, phone (empty string when omitted), and `submitted_at` (ISO 8601 UTC). Messages are kept in submission order for the life of this running copy of the site.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: A visitor can open Home and find the event name, theme, dates, place, and all three introduction paragraphs on the first screenful of content on a 375 px-wide display, without horizontal scrolling.
- **SC-002**: A visitor can open Program and see 100% of the published days and sessions, in published order, including blank cells, on a single page.
- **SC-003**: After a published session title changes, the next Program visit shows the new title and does not show the replaced title.
- **SC-004**: A visitor can open Registration Fee and match every published cell and both table titles, then read the inclusion sentence, without asking the organiser for a price.
- **SC-005**: A visitor with a valid message can submit the contact form and see confirmation, including their subject, in one response. A visitor who leaves required fields blank sees an error beside each blank field and still sees what they typed.
- **SC-006**: Before any accepted message, the submissions list is empty. After messages are accepted, 100% of them appear in the order they were accepted, with the stored subject matching what was confirmed.
- **SC-007**: An unknown address returns a not-found page with a working link back to Home. A disallowed action returns a method-not-allowed page and does not change stored messages.
- **SC-008**: A reader can use the header to reach each of the four pages in one selection, and the footer on each page shows the same organiser, address, phone, fax, email, and copyright.

## Assumptions

- Trailing slashes are equivalent for the routes in this specification, as in `/program` and `/program/`.
- Files such as stylesheets may live under `/assets/`. They are not pages and do not use the not-found layout. A missing asset is a plain "not found" response.
- Visual design is not prescribed beyond readable text, a system font stack, and no style library loaded from a third-party host. At 375 px the four navigation links stay visible rather than hiding behind a button, because that is the simpler of the two allowed presentations.
- The Group select has no empty placeholder. "General" is selected the first time the form is shown.
- Stored messages last only as long as this running copy of the site. They are not required to survive a replacement of that copy.
- Automated tests in the repository are welcome and not required.
- The site does not add pages, languages, logins, search, downloads, galleries, email delivery, CAPTCHA, or rate limiting.
- Document titles for pages other than Home follow the pattern "[page heading] - GIT 2025".
- On a successful contact submission the organiser details stay on the page and the form is replaced by the confirmation. On failure the form stays and the confirmation does not appear.
- An unknown path supports GET only. Any other method on that path is method-not-allowed, matching the rule for the not-found page.
- Required contact fields are judged after trimming, but the form redisplays the raw submitted text. Stored subject, message, name, email, and phone are the trimmed values. A phone that is missing or only whitespace is stored as "".
- The submissions timestamp uses second precision and a Z suffix, for example `2026-10-09T23:22:00Z`.
- The published program and fee wording is exactly the fixture text in SPEC.md section 6. Pages read those records on every request.
