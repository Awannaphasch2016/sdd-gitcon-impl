# Quickstart: GIT 2025 Conference Public Website

Confirms the public pages against [contracts/http.md](./contracts/http.md) and [data-model.md](./data-model.md). No application source is duplicated here.

## Prerequisites

- PHP 8.2 or newer on the host, or Docker able to build the root `Dockerfile`.
- Repository root as the working directory. `data/program.json` and `data/fees.json` match SPEC.md section 6.

## Local server

PHP's built-in server only runs the front controller for `/contact/submissions.json` when that file is the router script. From the repository root:

```bash
php -S 127.0.0.1:8080 -t public public/index.php
```

Container (port 80 inside, host 8080):

```bash
docker build -t site .
docker run --rm -p 8080:80 site
```

The container does not need network access after the image exists.

## Checks

Use another shell. Expect the status and a body snippet.

```bash
curl -sS -D- -o /tmp/home.html http://127.0.0.1:8080/
curl -sS -D- -o /tmp/program.html http://127.0.0.1:8080/program
curl -sS -o /tmp/program-slash.html http://127.0.0.1:8080/program/
curl -sS -D- -o /tmp/fees.html http://127.0.0.1:8080/registration-fee
curl -sS -D- -o /tmp/contact.html http://127.0.0.1:8080/contact
curl -sS -D- -o /tmp/missing.html http://127.0.0.1:8080/does-not-exist
curl -sS -D- -o /tmp/method.html -X POST http://127.0.0.1:8080/program
```

- `/` is 200 and contains `Home - GIT 2025 Responsible Gem & Jewelry Supply Chain` and the three home paragraphs.
- `/program` and `/program/` are 200 and both contain `Monday 8 September 2025` and `Tuesday 9 September 2025`.
- `/registration-fee` is 200 and contains `All fees include conference materials, lunches and coffee breaks on both days.`
- `/contact` is 200, General is selected, and the form posts to `/contact`.
- `/does-not-exist` is 404 and contains `Page not found` and `Back to Home`.
- POST `/program` is 405 and contains `Method not allowed`.

Empty contact post (failure, 200, errors, no confirmation):

```bash
curl -sS -D- -o /tmp/bad.html -X POST http://127.0.0.1:8080/contact \
  -d 'group=&subject=&message=&name=&email=&phone='
```

Valid contact post (200, confirmation, then one JSON object):

```bash
curl -sS -D- -o /tmp/ok.html -X POST http://127.0.0.1:8080/contact \
  --data-urlencode 'group=General' \
  --data-urlencode 'subject=Booth question' \
  --data-urlencode 'message=Where is the foyer?' \
  --data-urlencode 'name=Amina Shah' \
  --data-urlencode 'email=amina@example.com' \
  --data-urlencode 'phone=+66 81 234 5678'
curl -sS -D- http://127.0.0.1:8080/contact/submissions.json
```

- The failure body contains `data-error="subject"` and does not contain `data-confirmation`.
- The success body contains `data-confirmation` and `Thank you, your message has been sent.` followed by `Booth question`.
- The submissions response has `Content-Type: application/json` and a one-element array with `"subject":"Booth question"`.
- Before any success, that same address returns `[]`.

Changing `title` inside `data/program.json` and reloading `/program` shows the new title. Restore the file afterward so it still matches SPEC.md.
