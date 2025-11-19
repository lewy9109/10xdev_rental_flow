# Repository Guidelines

## Project Structure & Module Organization
- Root make targets and Docker configs live in `Makefile`, `docker/`, and `docker-compose.yaml`.
- Symfony app code is under `app/`:
  - Source: `app/src`, Config: `app/config`, Public: `app/public`, Templates: `app/templates`.
  - Tests: `app/tests` (suites configured via `app/phpunit.xml.dist`).
  - Tooling/config: `app/composer.json`, `app/phpstan*.neon`, `app/.php-cs-fixer.dist.php`, `app/grumphp.yml`.

## Build, Test, and Development Commands
- `make start` — boot Docker services using `.env` or `.env.dist`.
- `make exec` — open a shell in the PHP container as `www-data`.
- `make composer-install` / `make composer-update` — install/update PHP deps in container.
- `make tests` — run full PHPUnit test suite; `make tests-unit`, `make tests-e2e`, `make tests-unit-1/2` for subsets.
- `make coverage` — generate HTML coverage in `app/var/coverage`.
- `make grumphp` / `make analyse` — run linters and quality checks.
- `make cache` — clear Symfony cache; `make fixtures` — load Doctrine fixtures.
  Host-only (no Docker): `make analyse-host`, `make phpstan-host`, `make tests-host` (run `cd app && composer install` first).

## Coding Style & Naming Conventions
- PHP 8+ with PSR-12 style. Use Php CS Fixer config: `app/.php-cs-fixer.dist.php`.
- Static analysis via PHPStan (`app/phpstan*.neon`). Target level consistent with repo config.
- Namespaces under `App\\...`; classes in `app/src` follow PSR-4. Tests mirror source in `app/tests` with `*Test.php` suffix.
- Use typed properties/params/returns; prefer immutable value objects and constructor injection.

## Testing Guidelines
- Framework: PHPUnit (Symfony bridge). Suites defined in `app/phpunit.xml.dist` (Unit_1, Unit_2, Functional, Integration).
- Place unit tests in `app/tests/Unit/...`, functional in `app/tests/Functional`, integration in `app/tests/Integration`.
- Name tests `SomethingTest.php`; one assertion theme per test; use data providers for variants.
- Run locally via `make tests` or filter: `make test filter='App\\\\Tests\\\\Unit\\\\ExampleTest::testExample'`.

## Commit & Pull Request Guidelines
- Commits: present tense, scoped changes, reference issues (e.g., "feat(core): add price calculator, closes #123").
- PRs: include summary, screenshots for UI (if any), reproduction steps, and linked issues. Ensure CI green (tests, GrumPHP, PHPStan) and coverage unaffected for critical paths.

## Security & Configuration Tips
- Do not commit real secrets. Use `.env.dist` as a template; keep `.env` local.
- Database dumps live under `docker/etc/db/`. Use `make db-create-dump` / `make db-restore-dump` as needed.
