# Architecture

How the app is put together, and where its rules are enforced. `docs/wiki/` says *what* the app must do (one page per topic, see `docs/INDEX.md`); this file says *where in the code* it happens. Write each section from code that exists, not from plans, and update it when a change moves one of those places.

## Shape

One Laravel 13 app at the repository root. Describe the request flow once it exists:

```
HTTP request
  → route group in routes/web.php
  → middleware
  → controller (authorization, then validation)
  → model / service
  → view or redirect
```

## Areas

| Area | Routes | Who | Guard |
|---|---|---|---|

## Authorization

Where access is decided, in order, and what a refused request returns (404 for "not yours", 403 for "yours but not allowed"). Every protected route needs every layer listed here.

## Roles

| Role or constant | Who | Used for |
|---|---|---|

## Data

| Table | Model | Notes |
|---|---|---|

## Patterns to copy

## Inventory

| Path | Holds |
|---|---|
