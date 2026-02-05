# System Validation Guide

This document defines the roles, permissions, business rules, and validation checklists for the ported Laravel/Inertia/Vue system. Use this guide to verify the correctness of the implementation.

## 1. Roles & Permissions

The system implements a Role-Based Access Control (RBAC) system with three primary roles.

| Role | Description | Key Permissions |
| :--- | :--- | :--- |
| **admin** | Super Administrator (Technical/System Owner) | Full Access (`*`) |
| **psadmin** | Platform Service Administrator (Business Owner) | View Users, Manage Offerings (Create/Edit), View All Referrals, Update Referral Status, View Commissions, View All Analytics |
| **associate** | Partner/Referrer (End User) | View Offerings, Create Referrals, View Own Referrals, View Own Commissions, View Own Analytics |

### Detailed Permissions Map (from `RbacService`)

| Permission | `admin` | `psadmin` | `associate` |
| :--- | :---: | :---: | :---: |
| `user:view` | ✅ | ✅ | ❌ |
| `offering:view` | ✅ | ✅ | ✅ |
| `offering:create` | ✅ | ✅ | ❌ |
| `offering:edit_own` | ✅ | ✅ | ❌ |
| `referral:view` (All) | ✅ | ✅ | ❌ |
| `referral:view_own` | ✅ | ✅ | ✅ |
| `referral:create` | ✅ | ❌ | ✅ |
| `referral:update_status`| ✅ | ✅ | ❌ |
| `commission:view` (All) | ✅ | ✅ | ❌ |
| `commission:view_own` | ✅ | ❌ | ✅ |
| `analytics:view_all` | ✅ | ✅ | ❌ |
| `analytics:view_own` | ✅ | ❌ | ✅ |

---

## 2. Business Rules & Logic

These rules are enforced in the Controllers (`ReferralController`, `OfferingController`) and Middleware.

### A. Referrals (`ReferralController`)

1.  **Creation Rules:**
    *   **Access:** Only `associate` (and `admin`/`psadmin` theoretically, but primarily associate flows) can create referrals.
    *   **Conflict of Interest:** An `associate` CANNOT create a referral for an Offering that matches their own `category`.
        *   *Validation:* `if ($offering->category === $user->category) error('Conflicto de intereses')`.
    *   **Validation:** Requires `offering_id` (valid UUID), `client_name` (string). notes/metadata optional.

2.  **Visibility Rules:**
    *   **Associate:** Can ONLY see referrals where `associate_id` matches their profile ID.
    *   **Admin/PSAdmin:** Can see ALL referrals.

3.  **Status Updates:**
    *   **Access:** Only `psadmin` (and `admin`) can update the `status` of a referral.
    *   **Associate:** Cannot change status (read-only regarding status).
    *   **Fields Update:** `psadmin` can also update `deal_value` and `revenue_generated`.

### B. Offerings (`OfferingController`)

1.  **Management Rules:**
    *   **Create/Edit/Update:** Restricted to `psadmin` (and `admin`).
    *   **Associate:** Read-only access to Offerings.

2.  **Visibility (Catalog) Rules:**
    *   **Admin/PSAdmin:** Can view ALL offerings, including inactive ones (using `include_inactive=true`).
    *   **Associate:**
        *   Can ONLY view `is_active=true` offerings.
        *   **Conflict of Interest Filter:** Offerings with a `category` matching the Associate's `category` are HIDDEN from the list.

### C. Dashboard & Analytics

1.  **Dashboard Rules:**
    *   **Associate:** Sees own stats (`balance`, `total_revenue` from closed referrals), recent referrals list (own), and monthly revenue chart (own).
    *   **PSAdmin:** (Implied) Should see platform-wide stats (requires confirmation of Admin Dashboard implementation, but based on permissions they have `analytics:view_all`).

---

## 3. Validation Checklist

Use this checklist to manually validation the system.

### User Roles & Auth
- [ ] **Login as Admin:** Verify full access to all routes.
- [ ] **Login as PSAdmin:** Verify access to Users, generic Referrals list, Offering management.
- [ ] **Login as Associate:** Verify restricted access (cannot see other users, cannot see admin settings).

### Offering Management (PSAdmin)
- [ ] **Create Offering:** As PSAdmin, create a new Offering. Verify it appears in the list.
- [ ] **Edit Offering:** As PSAdmin, edit an existing Offering.
- [ ] **Deactivate Offering:** Set `is_active` to false.
    - [ ] Login as Associate: Verify the inactive offering is NOT visible.
    - [ ] Login as PSAdmin: Verify the inactive offering IS visible (with filter).

### Referral Flow (Associate)
- [ ] **Create Referral (Success):** Create a referral for an allowed offering. Verify success message.
- [ ] **Conflict of Interest:**
    1.  Set User Category to "Legal".
    2.  Set an Offering Category to "Legal".
    3.  Attempt to refer that offering.
    4.  **Expect:** Error message "Conflicto de intereses".
    5.  **Expect:** Verify the offering might not even appear in the dropdown/list due to filtering.
- [ ] **View Referrals:** Verify the Associate only sees their own created referrals.

### Referral Management (PSAdmin)
- [ ] **View All:** As PSAdmin, verify visibility of referrals created by Associates.
- [ ] **Update Status:**
    1.  Open a referral.
    2.  Change status (e.g., from "Pending" to "Closed").
    3.  Enter `revenue_generated` amount.
    4.  Save.
    5.  **Expect:** Success.
- [ ] **Associate Check:** Login as the Associate who created the referral. Verify the status is updated to "Closed".

### Security / Unauthorized Access
- [ ] **Associate vs Admin Route:** As Associate, try to access `/admin` or `/offerings/create`. **Expect:** 403 Forbidden or Redirect.
- [ ] **Associate vs Other Referral:** As Associate A, try to access `/referrals/{id_of_associate_B}` directly via URL. **Expect:** 403 Forbidden.
