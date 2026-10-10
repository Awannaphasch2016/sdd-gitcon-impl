# Data Model: GIT 2025 Conference Public Website

## Published program

Source file: `data/program.json`, byte-for-byte from SPEC.md section 6.1. Read on every program request. Not writable by the application.

- **Program file**
  - `days`: ordered list of Day
- **Day**
  - `id`: string (`monday`, `tuesday` in the fixture)
  - `title`: string, shown as the section heading
  - `sessions`: ordered list of Session
- **Session**
  - `time`: string, may be empty
  - `title`: string, may be empty
  - `speaker`: string, may be empty
  - `affiliation`: string, may be empty
  - `room`: string, may be empty

Validation: the page renders whatever strings are in the file. It does not drop rows with empty fields. Order is file order.

## Published fees

Source file: `data/fees.json`, byte-for-byte from SPEC.md section 6.2. Read on every fee request. Not writable by the application.

- **Fee file**
  - `columns`: ordered list of heading strings, used as the header row of every table
  - `tables`: ordered list of Fee table
- **Fee table**
  - `title`: string, shown as the heading before the table
  - `rows`: ordered list of rows; each row is an ordered list of cell strings

Validation: cells are shown unchanged. The page does not recompute prices.

## Contact message

Stored one JSON object per line in `data/submissions.jsonl`. The file is created on the first accepted submission. Absence of the file means there are no messages.

| Field | Rule |
| --- | --- |
| `group` | Required. Exactly one of `General`, `Registration`, `Abstract Submission`, `Sponsorship`. |
| `subject` | Required after trim. Stored trimmed. |
| `message` | Required after trim. Stored trimmed. |
| `name` | Required after trim. Stored trimmed. |
| `email` | Required after trim. Exactly one `@`, at least one character on each side, and a `.` somewhere after the `@`. Stored trimmed. |
| `phone` | Optional. Missing or whitespace-only is stored as `""`. Otherwise only digits, spaces, `+`, `-`, `(`, and `)`. Stored trimmed. |
| `submitted_at` | Set by the server at acceptance. ISO 8601 UTC to the second with a `Z` suffix, for example `2026-10-09T23:22:00Z`. Not supplied by the visitor. |

State: a message is either rejected (nothing stored, form redisplays) or accepted (one new line appended). There is no edit or delete.

The submissions list is the lines in file order, decoded as a JSON array. Empty or missing file encodes as `[]`.

## Relationships

- A Day has many Sessions. A Fee file has many Fee tables. Contact messages do not reference days or fees.
- Program and fees are independent published records. Contact messages are visitor submissions.
