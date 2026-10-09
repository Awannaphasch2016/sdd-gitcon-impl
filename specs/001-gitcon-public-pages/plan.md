# Implementation Plan: GIT 2025 Conference Public Website

**Branch**: `exp/2026-10-09-gitcon-v1-speckit-01` | **Date**: 2026-10-09 | **Spec**: [spec.md](./spec.md)

**Input**: Feature specification from `/specs/001-gitcon-public-pages/spec.md`

**Note**: This template is filled in by the `/speckit-plan` command; its definition describes the execution workflow.

## Summary

Publish four English pages for GIT 2025 (Home, Program, Registration Fee, Contact) plus a contact form that stores messages and lists them back. Program and fees are read from the fixed JSON fixtures on every request. The site is plain PHP 8.2 or newer with vanilla CSS, one front controller, and one root Dockerfile based on the official Apache PHP image. The container listens on port 80 and does not call the network or any external service at runtime.

## Technical Context

**Language/Version**: PHP 8.2 or newer (image `php:8.2-apache`; local CLI may be 8.3)

**Primary Dependencies**: None. No framework and no Composer packages. Apache `mod_rewrite` is part of the official image.

**Storage**: Read-only fixtures `data/program.json` and `data/fees.json` (byte-for-byte from SPEC.md section 6). Contact messages appended as JSON lines at `data/submissions.jsonl`, created on first success. `data/` is writable by the web server.

**Testing**: Manual curl checks in [quickstart.md](./quickstart.md). Repository tests are optional and not required.

**Target Platform**: Linux container serving HTTP on port 80. Local check with PHP's built-in server.

**Project Type**: Small public website (server-rendered pages).

**Performance Goals**: Each page responds in well under a second for a single visitor. The published program is two days and the fee tables are a handful of rows.

**Constraints**: No runtime network access. No database server, mail, CAPTCHA, analytics, or CDN assets. SPEC.md is immutable. Vanilla CSS and a system font stack only. Messages last for the life of the container filesystem.

**Scale/Scope**: Four HTML pages, one form, one JSON list, one not-found page, and method-not-allowed responses.

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

| Principle | Gate | Result |
| --- | --- | --- |
| PHP 8.2 or newer | Site runs on the official PHP 8.2 Apache image | Pass |
| One root Dockerfile, HTTP on port 80, no runtime network | Single `Dockerfile`; Apache listens on 80; image contains fixtures and code so startup does not fetch anything | Pass |
| No external services | No database server, mail, CAPTCHA, analytics, or CDN | Pass |
| SPEC.md is immutable | Planning and implementation read SPEC.md and do not edit it | Pass |
| Tests are optional | Quickstart curl checks only; no required test suite | Pass |
| Do not search for or copy an existing implementation | Pages are built only from SPEC.md, this plan, and the feature spec | Pass |

No violations. Complexity tracking is empty.

Post-design re-check: fixtures stay as files under `data/`, messages stay as JSON lines in that directory, and the image still has no runtime network or external service. The gate still passes.

## Project Structure

### Documentation (this feature)

```text
specs/001-gitcon-public-pages/
├── plan.md              # This file (/speckit-plan command output)
├── research.md          # Phase 0 output (/speckit-plan command)
├── data-model.md        # Phase 1 output (/speckit-plan command)
├── quickstart.md        # Phase 1 output (/speckit-plan command)
├── contracts/           # Phase 1 output (/speckit-plan command)
└── tasks.md             # Phase 2 output (/speckit-tasks command - NOT created by /speckit-plan)
```

### Source Code (repository root)

```text
Dockerfile                 # php:8.2-apache, document root public/, port 80
public/
├── index.php              # Front controller and built-in-server router
└── assets/
    └── site.css           # Vanilla CSS
src/
├── bootstrap.php          # Paths, escaping, shared layout
├── records.php            # Read program and fee fixtures per request
├── contact.php            # Validate and append JSON lines
└── pages.php              # HTML for each page
data/
├── program.json           # Byte-for-byte SPEC.md §6.1
└── fees.json              # Byte-for-byte SPEC.md §6.2
```

**Structure Decision**: One PHP application with the document root at `public/` so fixtures and message storage stay outside the web root. Apache rewrites missing files to `public/index.php`. The same file is the router script for PHP's built-in server, which otherwise will not execute `/contact/submissions.json`.

## Complexity Tracking

> No constitution violations.
