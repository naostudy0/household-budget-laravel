# Testing And Completion

- Follow Agentic TDD for implementation tasks: write tests first, confirm Red, implement the minimum Green change, then refactor.
- Treat tests as executable specifications.
- Prefer Domain unit tests first for entities and value objects because they are fast and protect core accounting rules.
- Use Application tests with in-memory repositories for use cases where appropriate.
- Use Feature tests for HTTP, controller, validation, Inertia component names, props, redirects, and database integration.
- Do not change domain rules without adding or updating Domain tests.
- Completion normally requires the relevant tests to pass, then `php artisan test`, `./vendor/bin/pint --test`, and `npm run format:check`.
- If Inertia screens change, verify component names, props, validation errors, and redirects.
- Do not finish with tests failing.

