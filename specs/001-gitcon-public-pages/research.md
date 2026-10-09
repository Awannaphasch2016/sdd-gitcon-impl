# Research: GIT 2025 Conference Public Website

## Decision: Plain PHP, no framework and no Composer packages

- **Decision**: Serve pages from one front controller written in PHP 8.2 or newer, with no framework and no Composer dependencies.
- **Rationale**: The site is four pages and one form. A framework would add packages and a runtime install step that the container does not need. The planning input allows either choice; no framework is the smaller one.
- **Alternatives considered**: A Composer micro-framework (routing and templates). Rejected because it adds a dependency install and does not change the visitor-facing behavior.

## Decision: Official `php:8.2-apache` image, listen on port 80

- **Decision**: The root `Dockerfile` uses `php:8.2-apache`. Apache is enabled with `mod_rewrite`, document root `public/`, and port 80. Startup does not download packages or call other hosts.
- **Rationale**: This is the official PHP image that already serves HTTP on port 80. The constitution requires one root Dockerfile, port 80, and no runtime network.
- **Alternatives considered**: `php:8.2-cli` with `php -S 0.0.0.0:80`. Rejected because the built-in server is a development server and mishandles the submissions path unless a router script is passed. Apache with a rewrite matches `docker run -p 8080:80`.

## Decision: JSON-lines file for contact messages

- **Decision**: Append each accepted message as one JSON object per line in `data/submissions.jsonl`. Create the file on the first success. A missing file means an empty list. The web server user can create and append files in `data/`.
- **Rationale**: SPEC.md allows a SQLite file or JSON lines under `data/`. JSON lines need no extra PHP extension, keep submission order naturally, and are easy to rewrite as the JSON array for `GET /contact/submissions.json`.
- **Alternatives considered**: SQLite (`data/messages.sqlite`). Rejected as a second storage engine for a single append-only list. A single JSON array file. Rejected because SPEC.md names JSON lines or SQLite, not a JSON array file.

## Decision: Read fixtures on every request

- **Decision**: `GET /program` and `GET /registration-fee` decode `data/program.json` and `data/fees.json` on that request. Those files are the code blocks in SPEC.md section 6, copied byte-for-byte. Session and fee text is not copied into PHP or HTML.
- **Rationale**: SPEC.md requires the pages to change when the fixture changes, with a reload and no other edit.
- **Alternatives considered**: Embedding the schedule in the template. Rejected because it fails the "nothing hardcoded" rule.

## Decision: Front controller routing, including the built-in server router

- **Decision**: `public/index.php` chooses the page from the request path and method. Apache rewrites any non-file request to that script. For local `php -S`, the same script is passed as the router argument (`php -S 127.0.0.1:8080 -t public public/index.php`). The router returns `false` only for real files under `/assets/`.
- **Rationale**: PHP's built-in server treats a URL that contains a dot as a static file. `/contact/submissions.json` never reaches `index.php` unless the script is the router. Docker's Apache rewrite does not have that limit. Trailing slashes are removed before matching, except that `/` stays `/`.
- **Alternatives considered**: A physical `public/contact/submissions.json` file. Rejected because storage must be JSON lines or SQLite under `data/`, and a static file cannot apply method rules. A `public/contact/` directory with its own `index.php`. Rejected because it splits routing and still cannot execute a `.json` URL without a router.

## Decision: Server-side validation and escaped text

- **Decision**: Validate posted fields in PHP. Ignore browser constraint attributes for acceptance. Escape every published and submitted string when placing it in HTML. On failure, put the raw posted string back into the control. On success, store trimmed fields.
- **Rationale**: The spec requires server checks independent of HTML attributes, preserved values, and exact error messages. Escaping keeps visitor text from becoming markup.
- **Alternatives considered**: HTML5 `required` only. Rejected because a client can skip it.

## Decision: Vanilla CSS, visible navigation at 375 px

- **Decision**: One stylesheet at `public/assets/site.css`. System font stack. Navigation links stay in a wrapping row so a 375 px viewport does not scroll horizontally. Tables use `overflow-wrap` and a max width of 100% so long session titles wrap inside the page.
- **Rationale**: The clarification chooses visible links rather than a menu button. No CDN and no CSS framework.
- **Alternatives considered**: A hidden menu opened by one button. Allowed by SPEC.md, rejected because visible links are simpler and were selected in clarification.

## Decision: Status and title behavior

- **Decision**: Unknown GET paths return 404 with the shared layout, heading "Page not found", and "Back to Home". Disallowed methods, including non-GET on an unknown path and non-GET on the submissions list, return 405 with heading "Method not allowed". Titles other than Home are "[heading] - GIT 2025".
- **Rationale**: Matches the feature spec clarifications. Home keeps the title required by SPEC.md.
- **Alternatives considered**: HTTP 404 for every method on an unknown path. Rejected because the not-found page is specified as GET-only.
