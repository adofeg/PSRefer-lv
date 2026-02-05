# Project Context: PS Refer (Laravel Edition)

## 1. Project Overview
**PS Refer** is a referral management platform where "Associates" refer clients to various "Offerings" (services/products) and earn "Commissions".
This codebase (`laravel/`) is a complete port of the original Node.js/React application, now running on **Laravel 12** and **Vue 3 (Inertia.js)**.

## 2. Technical Stack
- **Framework**: Laravel 12.x
- **Frontend**: Vue 3 (Composition API) via Inertia.js
- **Styling**: Tailwind CSS
- **Database**: PostgreSQL (Strict UUID usage)
- **Build Tool**: Vite

## 3. Architecture & Key Components

### Database Schema
Key design decisions enforced via migrations:
- **UUIDs**: All primary keys (`id`) use UUIDs.
- **JSONB**: Flexible configuration fields use JSONB columns:
  - `offerings.form_schema`: Dynamic form definitions.
  - `offerings.commission_config`: Rules for commission calculation.
  - `users.payment_info`: JSON storage for varied payment details.
- **Soft Deletes**: Used on Users, Offerings, and Referrals.

### Core Entities (Models)
- **User**: The system actor. Roles: `psadmin` (Admin), `associate` (Referrer), `client` (End user).
- **Offering**: A product or service that can be referred. Contains commission rules.
- **Referral**: The link between a User and an Offering. Has a lifecycle status:
  - `Prospecto` -> `Contactado` -> `En Proceso` -> `Cerrado` (Won) | `Perdido` (Lost).
- **Commission**: Financial record generated when a Referral becomes `Cerrado`.

### Business Logic (Services)
Located in `app/Services/`:
- **ReferralService**: Handles validation, creation, and status transitions of referrals. Triggers commission generation upon "Closing" a sale.
- **CommissionService**: Complex logic to calculate commission amounts based on:
  - Fixed rates vs Percentages.
  - Offering-specific configurations.
  - User-specific overrides (VIP rates).
- **AuditService**: Logs critical state changes (e.g., Status updates) to the database for compliance.

### Frontend Structure
Located in `resources/js/`:
- **Layouts**: `AuthenticatedLayout.vue` provides the standard Sidebar/Header shell.
- **Pages**:
  - `Dashboard.vue`: Role-aware dashboard (associates see their stats, admins see overview).
  - `Offerings/`: Catalog inspection and creation.
  - `Referrals/`: Management grid and detailed view with state transition buttons.
- **Components**: Atomic UI parts (`Card.vue`, `Badge.vue`, `Sidebar.vue`).

## 4. Current Status
- **Migration Complete**: All legacy Node.js features have been ported.
- **Build Status**: The frontend compiles successfully (`npm run build`).
- **Next Steps**:
  - Refinement of UI/UX.
  - Deployment to production environment (e.g., Plesk).

## 5. Development Commands
```bash
# Install Dependencies
composer install
npm install

# Run Migrations
php artisan migrate

# Start Dev Server
php artisan serve
npm run dev

# Build for Production
npm run build
```
