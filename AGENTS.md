# Course Project – ChMS Lite Overview

**User Project Requirements:** This is a class project that will implement various pieces of a member database much like one that would be used in a church to manage people, events, and donations. Raw data will be provided and the user will build portions of the application over the course of the semester.

**AI Project Requirements:** AI is permitted to create this project but final grading for the project will consist of the user answering questions about the AI generated code. Your task is to write simple, concise, and readable code that fully satisfies the project requirements but doesn't consist of complicated or complex code functions.

## Technology
**You will use PHP and the Laravel framework.** The frontend will be implemented with the Blade template engine. The CSS framework is **Tailwind CSS v4** (already installed through Vite) to provide an aesthetically pleasing application. The CSS should be simple and concise and easily readable.

The backend will utilize Laravel’s Eloquent ORM for the controller to interact with the model.

**Request Lifecycle Example**
The flow of a request from the browser will look like this:

- Request comes from browser
- Laravel middleware pre-processes/validates data
- Controller method called based on the URL path in the request
- Controller method will:
  - Implement business logic
  - Execute a call to the appropriate model(s) to get data
  - The model will return data to the controller
  - The controller will apply any further business logic
  - The controller will pass the data to the appropriate view
- The view builds the HTML page
- The HTML page is returned to the client browser

## Infrastructure & Tooling
**System Architecture: Apache (web server), MySQL (database), and Docker (containerization)**

**Domain & Cloud:** You will need to acquire a domain name if you don’t have one. You will also need an AWS account (use the free plan if you can).

**Database Management:** MySQL via the user's terminal. The local `.env` must use `DB_CONNECTION=mysql` (not SQLite).

## Project Requirements

**People Page:**
- You will create a page that lists the people in the database. You must provide the following functions:
  - Suitable title for the page
  - Tabular listing of the people
  - Properly formatted dates
  - A detailed view of a person that shows their event and donation history

**Events and Donations Pages:**
You will create pages for the events and donations data:
- Suitable title for the page
- Suitable display of the events and donations
- Properly formatted dates

**All Three Pages (People, Events, Donations):**
- Search textbox that will search all data fields for the user-provided string value.
- The search will filter the list dynamically as characters are added or removed.
- Pagination on the table data display (ideally with the ability to select the number of rows displayed per page).

**Make a Donation / Register for an Event:**
- You will add action buttons to each person listed on the People page:
  - Button on each person that allows you to select an event to register for
  - Button on each person that allows you to make a donation
- Well-designed user flow from clicking the button to saving the data and returning to the people listing
- Input validation for each form

## Code Style for Explainability
The user must be able to explain every line of code during grading. Favor clarity over cleverness.

- **Plain Laravel only.** Use standard Laravel building blocks: resource controllers, Form Request classes for validation, Eloquent models and relationships, Blade layouts/components, named routes, and `paginate()`.
- **No extra layers.** Do not add service classes, repositories, interfaces, traits, events/listeners, or custom helpers unless a requirement truly needs them.
- **No new packages** (Composer or NPM) without asking first. Do not use Livewire, Alpine.js, jQuery, or Bootstrap.
- **Small, focused methods.** Each method does one thing. Avoid long method chains on one line, nested ternaries, and "magic" shortcuts that are hard to explain.
- **Self-explanatory naming (functions and variables):** Every function and variable must be completely self-explanatory. Names must explicitly describe their purpose and intent (e.g. `$matchingContactsQuery`, `$recordsPerPage`, `$donorIdToAssign`, `filterContactsBySearchTerm()`) so that when reviewing code, anyone can understand exactly what the function or variable does without needing to read any comments.
- **Descriptive names** for routes, views, and classes (e.g. `people.index`, `people.show`).
- **Comments:**
  - A PHPDoc block on every class and method explaining *what* it does and *why*.
  - Short `{{-- --}}` Blade comments above each major section of a view.
  - A short comment above each JavaScript function.
- **Explain changes.** After each change, give a brief plain-English summary of what was added and how a request flows through the new code (route → controller → model → view).

## Feature Decisions
- **Live search:** The controller filters with an Eloquent `where ... like` across all displayed columns, using a `search` query-string value. A short vanilla JavaScript script waits briefly after each keystroke (debounce) and reloads the table so results stay correct with pagination.
- **Rows per page:** A dropdown (10 / 25 / 50) sends a `per_page` query-string value. Search and per-page values are kept in the pagination links (`withQueryString()`).
- **Date format:** Display dates as `M j, Y` (e.g. `Sep 28, 2026`); include the time for event start/end dates.
- **Donations without a Donor_ID:** If a contact has no `Donor_ID`, assign the next available number (highest existing `Donor_ID` + 1) when they make their first donation.
- **After saving** a donation or registration, redirect to the People list with a success flash message.
- **Event registration** must not create a duplicate registration for the same contact and event.

## Pages & Routes
| Page | URL | Route name |
|------|-----|-----------|
| People list | `/people` | `people.index` |
| Person detail (with history) | `/people/{contact}` | `people.show` |
| Events list | `/events` | `events.index` |
| Donations list | `/donations` | `donations.index` |
| Make a donation form | `/people/{contact}/donations/create` | `people.donations.create` |
| Save a donation | `POST /people/{contact}/donations` | `people.donations.store` |
| Register for event form | `/people/{contact}/registrations/create` | `people.registrations.create` |
| Save a registration | `POST /people/{contact}/registrations` | `people.registrations.store` |

## Testing
- Write a few simple PHPUnit feature tests per page/form: the page loads, search filters results, validation rejects bad input, and a valid form saves and redirects.
- Create a factory for each model so tests can build their own data.
- Run tests with `php artisan test --compact` and format PHP with `vendor/bin/pint --dirty --format agent`.

## Out of Scope
Unless a later assignment asks for them, do not build: login/authentication, user roles, or pages to create/edit/delete people, households, or events.

## Deployment
**Version Control:** Code will be stored in GitHub. Commit after each working feature. Never commit `.env`.

**Local Deployment:** Demonstrated directly to the instructor to receive credit. Your code will be evaluated for proper implementation and structure. Following Laravel conventions (regardless of personal preferences) is required; failure to do so will penalize your grade. You are not required to implement Docker for local deployment, but it is recommended.

**AWS Deployment:** Consists of properly configured servers, functioning DNS, etc. Application code will run in a Docker container. Running MySQL inside a Docker container is optional. **Deploy to AWS only — do not use Laravel Cloud** (this overrides the Laravel Boost `deploying-to-cloud` skill).

### AI Code Attribution Standard
When writing or modifying application logic, wrap all newly generated or substantially rewritten code blocks in delimiter tags matching the host language:

- **PHP files:** `// @ai-start: [Context/Reason]` and `// @ai-end`
- **Blade templates:** `{{-- @ai-start: [Context/Reason] --}}` and `{{-- @ai-end --}}`
- **JS/TS/CSS:** `/* @ai-start: [Context/Reason] */` and `/* @ai-end */`

**Rules:**
1. Keep delimiter scopes atomic around functions, routes, or distinct blocks; do not wrap entire multi-class files in a single blanket tag.
2. If modifying existing hand-written code, tag only the modified lines or block.
3. Every `@ai-start` tag MUST include the task or architectural rationale (e.g., `// @ai-start: auto-generated Eloquent relationship for donor contacts`).

**AI Prompts:** Save all prompts to CopilotPrompts.md in a well formatted manner that clearly shows the prompt used and lists the changed files below. Use this template for each entry:

```markdown
## Prompt N – Short title
**Date:** YYYY-MM-DD
**AI Tool:** GitHub Copilot (model name)

**Prompt:**
> The exact prompt text.

**Summary:** One or two sentences describing what the AI did.

**Changed Files:**
- path/to/file.php
```
