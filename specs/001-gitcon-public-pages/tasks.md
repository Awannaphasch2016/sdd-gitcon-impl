# Tasks: GIT 2025 Conference Public Website

**Input**: Design documents from `/specs/001-gitcon-public-pages/`

**Prerequisites**: plan.md (required), spec.md (required for user stories), research.md, data-model.md, contracts/

**Tests**: Not requested. Quickstart curl checks are in the polish phase. No automated test suite.

**Organization**: Tasks are grouped by user story to enable independent implementation and testing of each story.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to (e.g., US1, US2, US3)
- Include exact file paths in descriptions

## Path Conventions

- **Single project**: `public/`, `src/`, and `data/` at repository root
- Paths below match plan.md

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Project initialization and basic structure

- [x] T001 Create `.gitignore` and `.dockerignore` with PHP, log, editor, and runtime submission patterns
- [x] T002 [P] Write `data/program.json` and `data/fees.json` byte-for-byte from SPEC.md section 6
- [x] T003 [P] Add root `Dockerfile` using `php:8.2-apache`, document root `public/`, port 80, and no runtime network commands

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Core infrastructure that MUST be complete before ANY user story can be implemented

**⚠️ CRITICAL**: No user story work can begin until this phase is complete

- [x] T004 Implement shared layout, escaping, and the request path helper in `src/bootstrap.php` (header link "GIT 2025", navigation Home `/`, Program `/program`, Registration Fee `/registration-fee`, Contact `/contact`, footer organiser, address, phone, fax, mailto, copyright, one `<main>`, one `<h1>`)
- [x] T005 [P] Implement per-request reads of `data/program.json` and `data/fees.json` in `src/records.php`
- [x] T006 Implement method and path dispatch in `public/index.php`, including trailing-slash equivalence and returning false for real files under `public/assets/`
- [x] T007 [P] Add vanilla CSS in `public/assets/site.css` using a system font stack so the four navigation links stay visible at 375 px without horizontal page scrolling

**Checkpoint**: Foundation ready - user story implementation can now begin in parallel

---

## Phase 3: User Story 1 - Read the conference home page (Priority: P1) 🎯 MVP

**Goal**: Home shows the required title, heading, theme, dates, place, three paragraphs, and body links inside the shared shell

**Independent Test**: GET `/` returns 200 with the home title, the three verbatim paragraphs, and the footer email link

### Implementation for User Story 1

- [x] T008 [US1] Render the home page in `src/pages.php` with title "Home - GIT 2025 Responsible Gem & Jewelry Supply Chain", `<h1>` "GIT 2025", separate lines for the theme, dates, and place, the three verbatim paragraphs, and body links "Registration Fee" and "Program"
- [x] T009 [US1] Map GET `/` to the home page in `public/index.php`

**Checkpoint**: User Story 1 should be fully functional and testable independently

---

## Phase 4: User Story 2 - Read the program (Priority: P2)

**Goal**: Program lists every published day and session from the fixture, in order, with empty cells left empty

**Independent Test**: GET `/program` shows both day titles and a session row whose speaker cell is empty for Registration

### Implementation for User Story 2

- [x] T010 [US2] Render one section per Day and one table per day in `src/pages.php` from `src/records.php` (header cells Time, Title, Speaker, Affiliation, Room; session fields time, title, speaker, affiliation, and room shown unchanged, including empty strings)
- [x] T011 [US2] Map GET `/program` to the program page in `public/index.php`

**Checkpoint**: User Stories 1 and 2 should both work independently

---

## Phase 5: User Story 3 - Read registration fees (Priority: P2)

**Goal**: Fee page shows each published table and the inclusion sentence

**Independent Test**: GET `/registration-fee` shows both table titles, the four column headings, and the inclusion sentence

### Implementation for User Story 3

- [x] T012 [US3] Render one heading and table per Fee table in `src/pages.php` using `columns` as the header row and `rows` as the body, then the sentence "All fees include conference materials, lunches and coffee breaks on both days."
- [x] T013 [US3] Map GET `/registration-fee` to the fee page in `public/index.php`

**Checkpoint**: User Stories 1, 2, and 3 should work independently

---

## Phase 6: User Story 4 - Send a contact message (Priority: P3)

**Goal**: Visitors can submit the contact form, see field errors or a confirmation, and read stored messages back in order

**Independent Test**: An empty POST returns field errors and HTTP 200; a valid POST stores one JSON line and GET `/contact/submissions.json` returns that object

### Implementation for User Story 4

- [x] T014 [US4] Implement validation and JSON-lines storage in `src/contact.php` (`group` exactly General, Registration, Abstract Submission, or Sponsorship; `subject`, `message`, `name`, and `email` required after trim; `email` has exactly one `@`, at least one character on each side, and a `.` after the `@`; `phone` optional, missing or whitespace-only stored as `""`, otherwise only digits, spaces, `+`, `-`, `(`, and `)`; store trimmed fields plus `submitted_at` as UTC ISO 8601 to the second with a Z suffix in `data/submissions.jsonl`)
- [x] T015 [US4] Render the contact form, `data-error` messages, organiser details, and `data-confirmation` block in `src/pages.php` (General selected on first view; raw values preserved on failure; form omitted on success)
- [x] T016 [US4] Map GET and POST `/contact` and GET `/contact/submissions.json` in `public/index.php` (`Content-Type: application/json`; empty store returns `[]`)

**Checkpoint**: Contact submission and the submissions list work without changing Home, Program, or Fees

---

## Phase 7: User Story 5 - Recover from a wrong address or action (Priority: P3)

**Goal**: Unknown GET paths return the not-found page; disallowed methods return method-not-allowed

**Independent Test**: GET `/does-not-exist` is 404 with "Back to Home"; POST `/program` is 405 with "Method not allowed"

### Implementation for User Story 5

- [x] T017 [US5] Render the 404 and 405 pages in `src/pages.php` and dispatch them from `public/index.php` per `specs/001-gitcon-public-pages/contracts/http.md` (GET unknown path is 404; any other method on Home, Program, Registration Fee, or an unknown path is 405; methods other than GET or POST on `/contact` are 405; methods other than GET on `/contact/submissions.json` are 405)

**Checkpoint**: All user stories should now be independently functional

---

## Phase 8: Polish & Cross-Cutting Concerns

**Purpose**: Improvements that affect multiple user stories

- [x] T018 Confirm the `Dockerfile` enables `mod_rewrite` to `public/index.php` and makes `data/` writable by the web server
- [x] T019 Run the curl checks in `specs/001-gitcon-public-pages/quickstart.md`

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: No dependencies - can start immediately
- **Foundational (Phase 2)**: Depends on Setup completion - BLOCKS all user stories
- **User Stories (Phase 3+)**: All depend on Foundational phase completion
  - User stories can then proceed in parallel (if staffed)
  - Or sequentially in priority order (P1 → P2 → P3)
- **Polish (Final Phase)**: Depends on all desired user stories being complete

### User Story Dependencies

- **User Story 1 (P1)**: Can start after Foundational (Phase 2) - No dependencies on other stories
- **User Story 2 (P2)**: Can start after Foundational (Phase 2) - Uses the fixture reader but not Home
- **User Story 3 (P2)**: Can start after Foundational (Phase 2) - Uses the fixture reader but not Program
- **User Story 4 (P3)**: Can start after Foundational (Phase 2) - Uses the shared layout only
- **User Story 5 (P3)**: Can start after Foundational (Phase 2) - Uses the shared layout and the router

### Within Each User Story

- Page rendering before the route is considered done
- Story complete before moving to the next priority

### Parallel Opportunities

- T002 and T003 can run in parallel
- T005 and T007 can run in parallel with each other after T001
- After Foundational, User Stories 2, 3, and 4 touch different behavior and can be split by story; `src/pages.php` and `public/index.php` stay sequential because every story edits them

---

## Parallel Example: User Story 2

```bash
# After the fixture reader exists, the program page is one sequential slice:
Task: "Render program tables in src/pages.php"
Task: "Map GET /program in public/index.php"
```

---

## Implementation Strategy

### MVP First (User Story 1 Only)

1. Complete Phase 1: Setup
2. Complete Phase 2: Foundational (CRITICAL - blocks all stories)
3. Complete Phase 3: User Story 1
4. **STOP and VALIDATE**: Test User Story 1 independently
5. Deploy/demo if ready

### Incremental Delivery

1. Complete Setup + Foundational → Foundation ready
2. Add User Story 1 → Test independently → Deploy/Demo (MVP!)
3. Add User Story 2 → Test independently → Deploy/Demo
4. Add User Story 3 → Test independently → Deploy/Demo
5. Add User Story 4 → Test independently → Deploy/Demo
6. Add User Story 5 → Test independently → Deploy/Demo
7. Each story adds value without breaking previous stories

### Parallel Team Strategy

With multiple developers:

1. Team completes Setup + Foundational together
2. Once Foundational is done:
   - Developer A: User Story 1
   - Developer B: User Story 2
   - Developer C: User Story 3
3. Stories complete and integrate independently

---

## Notes

- [P] tasks = different files, no dependencies
- [Story] label maps task to specific user story for traceability
- Each user story should be independently completable and testable
- Commit after each task or logical group
- Stop at any checkpoint to validate story independently
- Avoid: vague tasks, same file conflicts, cross-story dependencies that break independence
