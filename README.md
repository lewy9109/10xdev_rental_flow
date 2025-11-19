# RentalFlow

![Status](https://img.shields.io/badge/status-in_development-yellow) ![PHP](https://img.shields.io/badge/PHP-8.2%2B-777bb4) ![License](https://img.shields.io/badge/license-proprietary-lightgrey)

## Table of Contents
- [Project Description](#project-description)
- [Tech Stack](#tech-stack)
- [Getting Started Locally](#getting-started-locally)
- [Available Scripts](#available-scripts)
- [Project Scope](#project-scope)
- [Project Status](#project-status)
- [License](#license)

## Project Description
RentalFlow is a Symfony-based web application built for individual property owners who manage a compact portfolio of rental units. It unifies property, tenant, contract, and payment management so that owners no longer rely on spreadsheets or manual calculations. The platform generates payment schedules automatically, lets owners record payments against installments, and calculates real-time statuses (pending, late, partially late, paid). A centralized dashboard highlights overdue tenants, the number of delinquent installments, and the total outstanding balance. The MVP targets a single-owner role, runs behind authentication, and is delivered with a CI/CD pipeline that builds, tests, and deploys a Dockerized app accessible via HTTPS.

For detailed requirements and user stories, see [Product Requirements](.ai/prd.md).

## Tech Stack
- **Language & Runtime:** PHP 8.2 with strict typing, enums, DTOs/value objects, and modern constructs as mandated by the [PHP/Symfony engineering rules](.ai/rules/php_rules.md).
- **Framework:** Symfony 7.3 (framework-bundle, runtime, Flex-based auto configuration).
- **Admin UI:** EasyAdmin 4.27 for CRUD management of properties, units, tenants, contracts, and payments.
- **CLI & Config Utilities:** `symfony/console`, `symfony/dotenv`, `symfony/yaml`.
- **Developer Tooling:** GrumPHP for pre-commit quality gates, PHPStan for static analysis, and Symfony Maker Bundle for scaffolding.
- **Infrastructure:** Docker services orchestrated via `docker-compose.yaml`, with CI/CD (GitHub Actions) covering build → tests → smoke E2E → Docker image build → deployment.

## Getting Started Locally
### Prerequisites
- Docker and Docker Compose installed.
- `make` available on your system.
- Recommended: modern PHP tooling (Symfony CLI, Composer) for host-based workflows.

### Setup
1. Copy `.env.dist` to `.env` and adjust environment-specific values if needed.
2. Boot containers and install dependencies:
   ```bash
   make start
   make composer-install
   ```
3. Enter the PHP container to run framework commands as `www-data`:
   ```bash
   make exec
   ```
4. Generate application cache or load fixtures when required:
   ```bash
   make cache
   make fixtures
   ```

### Verification & Tests
- Full test suite (PHPUnit): `make tests` (or targeted suites like `make tests-unit`, `make tests-unit-1`, `make tests-e2e`).
- Static analysis and QA checks: `make analyse`, `make grumphp`.
- Host-only development (no Docker): `cd app && composer install`, then use `make analyse-host`, `make phpstan-host`, `make tests-host`.

## Available Scripts
| Command | Description |
| --- | --- |
| `make start` | Boot Docker services defined in `docker-compose.yaml` using `.env` configuration. |
| `make exec` | Open an interactive shell inside the PHP container as `www-data` for running Symfony/Composer commands. |
| `make composer-install` / `make composer-update` | Install or update PHP dependencies inside the container. |
| `make tests`, `make tests-unit`, `make tests-e2e`, `make tests-unit-1`, `make tests-unit-2` | Execute PHPUnit suites covering unit, functional, integration, and E2E scenarios. |
| `make coverage` | Generate HTML code coverage in `app/var/coverage`. |
| `make analyse`, `make grumphp` | Run code quality gates (CS fixer, PHPStan, tests) before committing. |
| `make cache` | Clear or rebuild the Symfony cache. |
| `make fixtures` | Load Doctrine fixtures/data seeds. |
| Host-only targets (`make analyse-host`, `make phpstan-host`, `make tests-host`) | Run quality checks without Docker (requires local Composer install inside `app/`). |

## Project Scope
### In Scope
- Authentication (registration, login, password reset) protecting all business features.
- CRUD for properties, units, tenants, contracts, payment schedules, and payments via EasyAdmin-backed workflows.
- Automatic installment generation per contract, including optional deposits.
- Manual adjustments to installments and payments, with derived payment statuses and overdue detection.
- Owner dashboard with totals, overdue lists, filters, and navigation links.
- CI/CD pipeline that builds, tests, and deploys Docker images to staging and production.

### Out of Scope (MVP)
- Bank/payment provider integrations or automated import of transactions.
- Notifications (email/SMS), PDF management, advanced reporting/exporting, or multi-role access control.
- Multi-currency, multi-language support, or automatic schedule regeneration after contract edits.

## Project Status
- **MVP Timeline:** Target delivery by **December 16** with a single full-stack engineer.
- **Current Focus:** Implementing end-to-end workflow (registration → dashboard) with reliable automated tests and CI/CD coverage.
- **Success Metrics:** Fully functional CRUD flows, accurate status calculations, working dashboard insights, smoke-tested deployments, and a public HTTPS deployment path.

## License
This repository is currently proprietary and not published under an open-source license. Contact the maintainers for usage or distribution inquiries.
