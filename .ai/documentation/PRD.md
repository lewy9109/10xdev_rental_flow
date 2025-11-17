# Product Requirements Document (PRD) – RentalFlow
## 1. Product Overview

RentalFlow is a web application designed for individual property owners managing a small portfolio of rental units. It provides tools to register properties and tenants, create simple rental contracts, automatically generate payment schedules, record payments, and detect overdue installments. A central dashboard highlights who is overdue and by how much, enabling clear financial oversight.

The product targets a single-owner model with the possibility of scaling to a multi-tenant SaaS architecture in the future. The MVP is scheduled for delivery by December 16, developed by a single full-stack engineer (Symfony + EasyAdmin). The application will run under a public URL and be deployed via a minimal CI/CD pipeline.

## 2. User Problem

Property owners often rely on scattered tools—spreadsheets, notes, or simple apps—to track tenants, contracts, and payments. This leads to:

- No unified view of rental status and overdue payments
- Manual errors in calculating or tracking payment schedules
- Delayed detection of unpaid rent
- Repetitive manual work to maintain financial records

RentalFlow solves these issues by centralizing data, automating calculations, and providing real-time insight into payment status and tenant liabilities.

## 3. Functional Requirements

### 3.1. User Accounts
- Owner account registration (email + password)
- Login / logout
- Password reset via email link
- Single role in MVP: owner
- Entire application protected behind authentication

### 3.2. Properties & Units
- CRUD for properties and units
- Property fields: name/label, address, apartment number, size, type (optional)
- Unit listing per property

### 3.3. Tenants
- CRUD for tenants
- Fields: first name, last name, email, phone (optional), notes (optional)

### 3.4. Rental Contracts
- Associate tenant with unit
- Fields: start date, end date (optional), monthly rent, optional deposit, status
- Contract details view with linked payment schedule
- Editing basic contract details
- Contract updates do not auto-rebuild schedules in MVP

### 3.5. Payment Schedule (Installments)
- Auto-generation upon contract creation
- Monthly fixed-amount installments
- Optional deposit as a one-time installment
- Manual editing of installment due date and amount
- Fields: amount, due date, type (rent or deposit)

### 3.6. Payment Registration
- Add multiple payments to an installment
- Edit or delete payments
- Fields: amount, payment_date, notes

### 3.7. Payment Status & Overdue Detection
Statuses calculated dynamically:
- pending (before due date, no payments)
- late (past due date, no payments)
- partially_late (past due date, partial payment)
- paid (fully paid)

No overpayment logic in MVP.

### 3.8. Owner Dashboard
- Total overdue installments
- Total overdue amount
- List of tenants with overdue payments
- Links to tenant/contract details
- Filters:
    - by property
    - by status type (late / partially late)

### 3.9. End-to-End Scenario
- Registration → login → create property → add tenant → create contract → auto-generate schedule → register payments → validate dashboard data

### 3.10. CI/CD
- GitHub Actions pipeline:
    - build application
    - run unit tests
    - run single smoke e2e test
    - build Docker image
    - deploy to staging & production

## 4. Product Boundaries

### 4.1. In Scope
- CRUD for properties, units, tenants, contracts, schedules, payments
- Auto-generated payment schedules
- Dynamic payment status calculation
- Dashboard for overdue tracking
- Basic authentication and security
- Deployment pipeline and production hosting

### 4.2. Out of Scope (MVP)
- Bank integrations / automated payment imports
- Email/SMS notifications
- PDF contract storage
- Advanced reporting or exports
- Multi-role permissions
- Multiple currencies or languages
- Automatic schedule regeneration on contract changes

## 5. User Stories

### US-001 — User Registration
Description: As a new owner, I want to register an account so I can access the system.  
Acceptance criteria:
- I can provide email and password.
- I can log in after registering.
- Password is securely hashed.

### US-002 — User Login
Description: As an owner, I want to log in to my account.  
Acceptance criteria:
- I can enter email and password.
- Invalid credentials are rejected.
- After login, I am redirected to the dashboard.

### US-003 — Password Reset
Description: As an owner, I want to reset my password.  
Acceptance criteria:
- I can request a reset link.
- Email contains a valid reset token.
- I can set a new password.

### US-004 — Add Property
Description: As an owner, I want to add a property.  
Acceptance criteria:
- I can enter name and address.
- Property appears in my list.

### US-005 — Edit/Delete Property
Description: As an owner, I want to modify or delete a property.  
Acceptance criteria:
- I can update property details.
- I cannot delete a property with active contracts.

### US-006 — Add Unit (Apartment)
Description: As an owner, I want to add a unit to a property.  
Acceptance criteria:
- I can add unit details (number, size).
- Unit is displayed under the selected property.

### US-007 — Tenant Management
Description: As an owner, I want to manage tenants.  
Acceptance criteria:
- I can create, edit, delete tenants.
- Email and phone fields are validated.

### US-008 — Create Rental Contract
Description: As an owner, I want to create a contract linking a tenant to a unit.  
Acceptance criteria:
- I select tenant & unit.
- I set start/end dates, rent amount, optional deposit.
- Schedule is generated automatically.

### US-009 — Edit Installment
Description: As an owner, I want to manually adjust an installment.  
Acceptance criteria:
- I can modify the due date.
- I can modify the amount.

### US-010 — View Payment Schedule
Description: As an owner, I want to see all installments for a contract.  
Acceptance criteria:
- Installments show due dates, amounts, and status.
- Deposit installment is clearly marked.

### US-011 — Add Payment
Description: As an owner, I want to record payments.  
Acceptance criteria:
- I can add payment with amount and date.
- Payment updates installment status.

### US-012 — Edit/Delete Payment
Description: As an owner, I want to correct mistakes in payment entries.  
Acceptance criteria:
- I can edit payments.
- I can delete payments.

### US-013 — Automatic Payment Statuses
Description: As an owner, I want accurate status calculation.  
Acceptance criteria:
- pending, late, partially_late, paid logic works for real scenarios.

### US-014 — Dashboard Overview
Description: As an owner, I want to quickly see who is overdue.  
Acceptance criteria:
- Dashboard shows overdue count and total amount.
- List includes tenant name, property, overdue amount.

### US-015 — Dashboard Filters
Description: As an owner, I want to filter overdue results.  
Acceptance criteria:
- I can filter by property.
- I can filter by status.

### US-016 — Navigate to Details
Description: As an owner, I want to open tenant or contract details from the dashboard.  
Acceptance criteria:
- Each row includes a working link to details.

### US-017 — End-to-End Flow
Description: As an owner, I want the entire workflow to function seamlessly.  
Acceptance criteria:
- The full scenario from registration to overdue dashboard passes with no errors.

### US-018 — Authenticated Access Only
Description: As an owner, I want the app to restrict access.  
Acceptance criteria:
- Only login/register/reset pages are public.
- Unauthorized access redirects to login.

### US-019 — CI/CD Pipeline
Description: As an owner, I want automated deployment.  
Acceptance criteria:
- Pipeline builds the application and Docker image.
- Pipeline runs unit tests and an e2e smoke test.
- Pipeline deploys to production automatically.

## 6. Success Metrics

- All CRUD operations function correctly.
- Payment schedules generate automatically with correct dates and amounts.
- Payment status logic is accurate across test scenarios.
- Dashboard fully reflects overdue installments and totals.
- e2e scenario passes in staging and production.
- CI/CD pipeline executes with 100% success (build → test → deploy).
- App is publicly available under HTTPS and allows full onboarding flow.
