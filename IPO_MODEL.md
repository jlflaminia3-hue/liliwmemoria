# Input–Process–Output (IPO) Model — LiliwMemoria Cemetery Management System

This IPO model summarizes how data flows through the LiliwMemoria Cemetery Management System (Laravel-based), covering the major modules visible in the project (lots/plots, reservations, interments, payments, visitor logs, tomb locator map, analytics, and administration).

## Actors

- **Public users / visitors**: view the cemetery map (tomb locator) and submit visitor logs.
- **Clients (authenticated)**: manage their profile and access client-facing features (e.g., contracts/lot-related information).
- **Admin / Master Admin**: manage lots/plots, reservations, interments, exhumations, payments, reports, and analytics; manage users (master admin).

## System-Level IPO

| **Inputs** | **Processes** | **Outputs** |
|---|---|---|
| User credentials and role (admin/master admin/client) | Authentication, authorization (role checks), session management | Authorized dashboards and module access |
| Master data (lots/plots, clients, deceased records, ownerships) | CRUD operations, validation, relational linking (client ↔ lot ↔ deceased) | Updated records, consistent system state |
| Time-based rules (reservation expiry dates) | Reservation expiration (`Reservation::expireDue`) + lot state sync (`LotStateService::sync`) | Accurate lot availability/status shown system-wide |
| Documents & uploads (contracts, receipts, certificates) | PDF generation (Dompdf), file storage, download endpoints | Downloadable PDFs (contracts/receipts/certificates) and stored files |
| Email/communication details | Notification dispatch (e.g., contract sending) | Emails delivered to clients + delivery feedback in UI |
| Map geometry + cemetery map image configuration | Map rendering (image overlay + geometry layers), search indexing | Interactive tomb locator map with popups and search results |

## Module IPO Breakdown

### 1) Lots / Plots Management (Admin)

| **Inputs** | **Processes** | **Outputs** |
|---|---|---|
| Lot details (section/category, block, lot number/id, notes), status (available/reserved/occupied), optional owner display name | Validate inputs; create/update/delete lots; compute next lot number/id; sync status based on interment/reservation/ownership | Lot records, updated availability, list views, lot snapshots for UI |
| Lot location/geometry (latitude/longitude, `geometry_type` rect/poly, geometry JSON) | Store geometry; render on admin map; allow editing via map tools | Accurate plot placement on map and consistent geometry data |

### 2) Tomb Locator & Interactive Map (Public + Admin Map)

| **Inputs** | **Processes** | **Outputs** |
|---|---|---|
| Cemetery map image (configured), lots dataset (geometry + status + deceased association), search query (deceased name / lot search) | Load map image dimensions; overlay image; render lots as rectangles/polygons/markers; style by status; build searchable index (deceased names); display popups | Interactive map with color-coded lots (available/reserved/occupied), popups showing lot + deceased info, search-and-zoom results |
| Reservation expiry dates + lot occupancy rules | Expire due reservations and sync lot state before rendering (`Reservation::expireDue` + `LotStateService::sync`) | Map reflects current and correct lot status |

### 3) Reservations & Client Contracts (Admin)

| **Inputs** | **Processes** | **Outputs** |
|---|---|---|
| Client selection, lot selection (available), payment terms, reservation dates (reserved/expires), notes | Validate; create reservation; mark expired/fulfilled; link reservation ↔ lot ↔ client ↔ payment plan ↔ contract | Reservation records, contract files/links, updated lot status (reserved/available) |

### 4) Interments (Deceased Records) & Exhumations (Admin)

| **Inputs** | **Processes** | **Outputs** |
|---|---|---|
| Deceased details (name, birth/death dates, burial date), lot assignment, client association, supporting documents | Validate; create/update interment; enforce eligibility rules; update lot state to occupied; generate/download/send interment contract | Interment records, lot status becomes occupied, contract PDF and email delivery |
| Exhumation request data + documents | Create/update exhumation; manage exhumation documents; generate transfer certificate | Exhumation record updates, downloadable transfer certificate PDF |

### 5) Payments, Installments, Receipts & Invoices (Admin)

| **Inputs** | **Processes** | **Outputs** |
|---|---|---|
| Payment plan setup (amounts, schedule), payment transactions (amount, date, method, attachments/notes) | Create and allocate transactions; update payment status (downpayment/installment/fully paid); generate receipts and invoices | Payment history, updated plan status, downloadable receipt/invoice PDFs |

### 6) Visitor Logs (Public)

| **Inputs** | **Processes** | **Outputs** |
|---|---|---|
| Visitor details + selected deceased/lot reference (as applicable) | Validate; store visitor log entry; provide locator view for the visit | Saved visitor log record + locator page/map guidance |

### 7) Analytics & Reports (Admin)

| **Inputs** | **Processes** | **Outputs** |
|---|---|---|
| Operational data (lots, clients, reservations, interments, visitors, payments) | Aggregate counts and distributions; filter/search; render charts | Analytics dashboards (plots/clients/payments/interments/visitors/documents) and report views |

### 8) Master Admin (Users & Audit Logs)

| **Inputs** | **Processes** | **Outputs** |
|---|---|---|
| User account details and role assignments | User CRUD; role enforcement; audit log capture and browsing | Managed admin accounts, audit trail visibility for accountability |

