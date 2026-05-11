# Architecture And Conventions

- The project uses a layered architecture influenced by DDD: `Http -> Application -> Domain <- Infrastructure`.
- Apply DDD where domain rules matter, especially money, balances, debit/credit rules, journal consistency, internal transactions, combined-view elimination, and ledger integrity.
- Avoid excessive abstraction for simple CRUD, read-only lists, search, dashboard display queries, and master data until the domain needs it.
- Domain must not depend on Laravel, Eloquent, or the database.
- Application use cases coordinate workflows and should normally expose an `execute()` method.
- Do not pass Request objects into use cases.
- Do not return Eloquent models directly from use cases.
- Repositories are for restoring and saving aggregates/entities and belong as interfaces in Domain with implementations in Infrastructure.
- Read-only optimized access can be implemented as Queries under `app/Application/{Context}/Queries/`; queries may use Eloquent or Query Builder but must not mutate state.
- Controllers should stay thin: receive requests, validate through FormRequests, call use cases or queries, and return Inertia responses or redirects.
- Inertia screens and normal screen actions should use `routes/web.php`.
- Use `routes/api.php` only for external integrations or genuinely JSON-only APIs.
- Vue files should use Composition API with `<script setup>`, then `<template>`, then `<style scoped>` where needed.
- Use Inertia `<Link>` instead of plain anchors for app navigation.
- Use Inertia `useForm` or `router` for form submissions.

