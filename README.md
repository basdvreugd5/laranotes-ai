# LaraNotes AI

LaraNotes is a small Laravel application demonstrating **clean, idiomatic Laravel architecture** with a deliberately minimal feature set.

The project focuses on clarity, restraint around abstractions, and strong separation of responsibilities while remaining close to Laravel’s default conventions.

It includes:

- a small authenticated note-taking application
- a JSON API
- a simple Blade + Tailwind UI
- an AI-powered TL;DR summarization feature

The goal is not feature richness but **architectural clarity**.

---

## Purpose

This project exists as a **reference implementation** showing how a Laravel application can remain clean and maintainable without introducing unnecessary architectural layers.

It demonstrates:

- idiomatic Laravel structure
- policy-driven authorization
- form request validation
- API resources for consistent JSON output
- minimal domain behavior in models
- behavior-focused testing
- careful restraint around abstraction

---

## Core Concept

### What is a Note?

A **Note** is a user-owned resource representing a short piece of text content.

A note:
- belongs to exactly one user
- has a title and optional body
- may optionally have an AI-generated TL;DR summary
- can be archived
- is never shared with other users

---

## AI Summaries

Notes can optionally be summarized using an AI agent.

The application uses the Laravel AI SDK with a small custom agent: 
App\Ai\Agents\NoteSummarizer
The agent generates a short **TL;DR summary** of the note body.

Important design decisions:

- the original note body is **never modified**
- summaries are stored separately in the `tldr` column
- summarizing a note that already has a summary is a **no-op**
- the feature is intentionally simple and synchronous

The goal is to demonstrate **minimal integration of external services** without introducing additional architectural layers.

---

## Ownership & Authorization Rules

Every note belongs to exactly one user.

Users may only:

- view their own notes
- update their own notes
- archive their own notes
- summarize their own notes

Users may never access notes owned by another user.

All access rules are enforced through **Laravel policies**.

---

## Allowed Actions

An authenticated user may:

- create a note
- list their own notes
- update a note they own
- archive a note they own
- generate a summary for a note they own

---

## Forbidden Actions

A user may **not**:

- view another user’s notes
- update another user’s notes
- archive another user’s notes
- exceed the maximum allowed number of notes
- generateSummary twice.

Authorization failures return `403 Forbidden`.
Validation errors return `422 Unprocessable Entity`.

---

## Note Limits

- A user may create up to a fixed maximum number of notes
- The limit is enforced at the **authorization layer**
- Both active and archived notes count toward the limit

Once created, a note always counts toward the limit.

---

## Archiving Behavior

- Archiving is a state change, not deletion
- Archived notes remain in the database
- Archived notes are excluded from active listings by default
- Archiving an already archived note is a no-op

There is no unarchive functionality by design.

---

## API Design

- All endpoints return JSON
- All write operations require authentication
- Authorization is enforced before persistence
- Unauthorized users never receive access to another user’s data
- Sensitive user attributes (e.g. passwords, tokens) are never exposed
- All API output is explicitly shaped using Laravel API Resources

---

## Data Shape (Conceptual)

A note is exposed by the API with the following fields:

- `id`
- `title`
- `body`
- `tldr`
- `archived`
- `timestamps`

Responses are shaped using **API Resources**.

```json
// GET /api/notes
{
    "data": [
        {
            "id": 1,
            "title": "Hello",
            "body": "World!",
            "tldr": "Summary",
            "archived": false
        }
    ],
    "links": { ... },
    "meta": { ... }
}
```

---

## Architecture Overview

The application follows standard Laravel structure:

Request → Form Request → Controller → Policy → Model → API Resource

Responsibilities:

- **Controllers**: orchestration only
- **Form Requests**: validation and authorization delegation
- **Policies**: access rules and limits
- **Models**: persistence and simple domain behavior
- **API Resources**: response formatting

No service layer or additional domain abstractions are introduced unless complexity requires them.

The AI summarization feature is implemented using a small Laravel AI agent rather than introducing a broader service layer.

---

## API Endpoints

Authenticated routes:

- `GET /api/notes` — list active notes
- `POST /api/notes` — create a note
- `PATCH /api/notes/{note}` — update a note
- `POST /api/notes/{note}/archive` — archive a note
- `POST /api/notes/{note}/summarize` — generate a TL;DR summary

---

## Testing Philosophy

Tests focus on **observable behavior**, not implementation details.

The test suite includes:

- Policy tests to validate ownership and note limit rules
- Feature tests for API endpoints
- Feature tests for AI summarization behavior
- Authentication and authorization scenarios

---

## Non-Goals

This project intentionally does **not** include:

- shared notes or collaboration
- roles or permission systems
- background jobs or queues
- event-driven architecture
- complex filtering or querying
- large frontend frameworks

The purpose is to **remain small, readable, and focused.**

---

## Setup (Optional)

## Setup

Clone the repository and install dependencies:

```bash
git clone https://github.com/<your-username>/laranotes-ai.git
cd laranotes-ai
composer install
npm install
```

Set up your environment and database:

```bash
cp .env.example .env
php artisan key:generate
php artisan migrate
```

Build frontend assets:

```bash
npm run build 
```

Start the development server:

```bash
php artisan serve 
```

The application will be available at:

```bash
http://localhost:8000 
```

---

## AI Setup

The TL;DR summarization feature uses the **Laravel AI SDK** with **Ollama** as the default provider.

Install and start Ollama locally:

https://ollama.com

Pull a compatible model, for example:

```bash
ollama pull llama3
```

Add the Ollama configuration to your .env file:

```bash
OLLAMA_HOST=http://localhost:11434
OLLAMA_MODEL=llama3
OLLAMA_API_KEY=YOUR-API-KEY
```

Make sure the Ollama server is running before using the summarization feature.

---

## Running Tests

The suite is built with PHPUnit and focuses on behavioral testing.

```bash
php artisan test
```

---

## Final Notes

This project favors **clarity over cleverness**.

Any abstraction or structural decision should be justified by actual complexity, not anticipation.

