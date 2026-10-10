# GIT 2025 website: how it is put together

The pictures live in `docs/diagrams/`. This page says what each one is for.

There is no separate frontend application. The browser receives a finished HTML page, loads one stylesheet, and posts the contact form. PHP builds every page.

## 1. Context

`docs/diagrams/01-context.svg`

A visitor reads the pages and sends the contact form. A staff reader opens the submissions list. Both talk only to this website.

No outside service is required while the site is running. There is no database server, mail server, CAPTCHA, analytics, CDN, or login provider. The PHP image is downloaded only when the container image is built.

## 2. Containers

`docs/diagrams/02-container.svg`

One Docker container runs Apache and PHP 8.2 and listens on port 80.

| Folder | Role |
| --- | --- |
| `public/` | What Apache can serve. Router, contact entry, stylesheet, published message list. |
| `src/` | PHP that builds pages. Not the web root. |
| `data/` | JSON files. Program, fees, and the message store. Not a database server. |

Inside the image those folders are `/var/www/app/public`, `/var/www/app/src`, and `/var/www/app/data`.

## 3. Components

`docs/diagrams/03-component.svg`

`public/index.php` chooses the page. `public/contact/index.php` handles `/contact` when that path is a folder. Both load `src/bootstrap.php`, which loads the other PHP files and draws the shared header and footer.

`src/pages.php` writes the HTML. It asks `src/records.php` for the schedule and the fees, and asks `src/contact.php` to check or store a message. `public/assets/site.css` is a file, not PHP.

## 4. Code structure

`docs/diagrams/04-structure.svg`

The PHP has functions, not classes. Each file is one group of functions. `contact.php` is the only file that writes. `layout_start` and `layout_end` wrap every HTML page, so the header and footer are written once.

## 5. Data

`docs/diagrams/05-data.svg`

- `data/program.json` is an ordered list of days. Each day has an ordered list of sessions.
- `data/fees.json` is the shared column headings plus an ordered list of tables.
- `data/submissions.jsonl` is one contact message per line. That file is the store.
- `public/contact/submissions.json` is the same messages as a JSON array, rewritten after each accepted message. Before any message it is `[]`.

Program, fees, and messages do not point at each other.

## 6. Sequences

`docs/diagrams/06-sequence-page.svg` shows a visitor opening Program. Home and Registration Fee use the same path: browser, router, page function, record function, JSON file, then one HTML response.

`docs/diagrams/07-sequence-contact.svg` shows the contact form. PHP checks the fields. A failure returns the form and does not write. A success appends one line to `data/submissions.jsonl`, rewrites the public JSON list, and returns the confirmation. A later GET of `/contact/submissions.json` returns that list.
