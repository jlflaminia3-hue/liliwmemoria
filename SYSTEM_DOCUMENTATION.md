# Liliwmemoria Cemetery Management System - Documentation

## Table of Contents
1. [System Overview](#system-overview)
2. [Entity Descriptions](#entity-descriptions)
3. [Data Model & Relationships](#data-model--relationships)
4. [Business Workflows](#business-workflows)
5. [Key Concepts](#key-concepts)
6. [Database Architecture](#database-architecture)

---

## System Overview

**Liliwmemoria** is a comprehensive cemetery management system designed to handle the complete lifecycle of cemetery operations, including:
- Lot/plot management with spatial data
- Client (lot owner) management
- Deceased individual records
- Contract and service agreements
- Payment processing with installment plans
- Exhumation requests and workflows
- Maintenance service tracking
- Communication logs and family relationships

The system serves as a central hub for managing cemetery operations, client interactions, financial transactions, and statutory compliance.

---

## Entity Descriptions

### Core Management Entities

#### **USERS**
**Purpose:** System user management and authentication
- **Primary Key:** id (bigint)
- **Key Fields:**
  - `name` — User's full name
  - `email` — Unique email address
  - `email_verified_at` — Email verification timestamp
  - `password` — Hashed password
  - `remember_token` — Session token
  - `created_at`, `updated_at` — Audit timestamps

**Role:** Authentication and authorization baseline for all system users

---

#### **CLIENTS**
**Purpose:** Core entity representing cemetery lot owners and service seekers
- **Primary Key:** id (bigint)
- **Key Fields:**
  - `first_name`, `last_name` — Client identification
  - `email` — Unique contact email
  - `phone` — Contact phone number
  - `address_line1`, `address_line2` — Street address
  - `barangay`, `city`, `province`, `postal_code`, `country` — Complete address components
  - `notes` — Client-specific notes or special instructions
  - `consent_given` — GDPR/data processing consent flag
  - `created_at`, `updated_at` — Record lifecycle

**Role:** Central entity linking to lot ownership, contracts, payments, and communications

---

#### **LOTS**
**Purpose:** Physical cemetery plot/burial space records
- **Primary Key:** id (bigint)
- **Key Fields:**
  - `name` — Lot identifier/name
  - `lot_number` — Unique lot number (unique per category)
  - `section` — Cemetery section designation
  - `block` — Block code within section
  - `latitude`, `longitude` — GPS coordinates (10 and 11 decimal places for precision)
  - `geometry` — GIS geometry data type for spatial queries
  - `category` — Lot type classification
  - `geometry_type` — Geometry representation type
  - `is_occupied` — Boolean flag for occupancy status
  - `status` — Current lot status (available, reserved, occupied, etc.)
  - `notes` — Lot-specific notes and restrictions
  - `created_at`, `updated_at` — Record lifecycle

**Role:** Physical asset tracking with spatial capabilities for cemetery mapping and location services

---

#### **DECEASED**
**Purpose:** Individual burial record tracking
- **Primary Key:** id (bigint)
- **Foreign Keys:** lot_id (belongs to LOTS)
- **Key Fields:**
  - `first_name`, `last_name` — Deceased identification
  - `date_of_birth`, `date_of_death` — Vital dates
  - `burial_date` — Actual burial date
  - `status` — Current status (pending, buried, exhumed, etc.)
  - `death_certificate_path` — Document storage path
  - `burial_permit_path` — Permit documentation
  - `interment_form_path` — Interment documentation
  - `interment_form_decimal_1_2` — Custom fields for interment data
  - `interment_sum_path` — Summary document path
  - `payment_before_interment` — Financial tracking before burial
  - `payment_since_burial_date` — Ongoing burial-related payments
  - `interment_date_to_date` — Date range for interment
  - `interment_number`, `contract_path` — Financial linkage
  - `notes` — Remarks about the deceased
  - `created_at`, `updated_at` — Record lifecycle

**Role:** Official record of individuals buried in the cemetery with complete documentation trail

---

#### **EXHUMATIONS**
**Purpose:** Management of disinterment/exhumation requests and processes
- **Primary Key:** id (bigint)
- **Foreign Keys:** deceased_id (belongs to DECEASED)
- **Workflow Status:** enum('draft', 'submitted', 'approved', 'scheduled', 'completed', 'archived')
- **Key Fields:**
  - `requested_by_name`, `requested_by_relationship` — Requestor information
  - `requested_at`, `approved_at`, `exhumed_at` — Workflow timestamps
  - `exhumation_permit_path`, `transfer_permit_path` — Regulatory documents
  - `destination_cemetery_name`, `destination_address` — Transfer destination
  - `destination_city`, `destination_province` — Destination location details
  - `destination_contact_person`, `destination_contact_phone`, `destination_contact_email` — Receiving facility contact
  - `transport_company`, `transport_vehicle_plate`, `transport_driver_name` — Transportation logistics
  - `transport_log` — Detailed transport record
  - `transfer_certificate_path`, `transfer_certificate_generated_at` — Final documentation
  - `workflow_status` — Current workflow state
  - `notes` — Additional remarks
  - `created_at`, `updated_at` — Record lifecycle

**Role:** Complex workflow management for exhumation requests with multi-stage approvals and inter-cemetery transfers

---

### Relationship & Ownership Entities

#### **CLIENT_LOT_OWNERSHIPS**
**Purpose:** Tracks lot ownership history and relationships over time
- **Primary Key:** id (bigint)
- **Foreign Keys:** client_id (belongs to CLIENTS), lot_id (belongs to LOTS)
- **Unique Constraint:** (client_id, lot_id)
- **Key Fields:**
  - `ownership_type` — Type of ownership (owner, co-owner, lessee, etc.)
  - `started_at`, `ended_at` — Ownership tenure dates
  - `notes` — Ownership terms or special conditions
  - `created_at`, `updated_at` — Record lifecycle

**Role:** Enables temporal tracking of lot ownership changes and multiple ownership scenarios

---

#### **CLIENT_FAMILY_LINKS**
**Purpose:** Tracks family relationships between clients for inheritance and communication
- **Primary Key:** id (bigint)
- **Foreign Keys:** client_id (belongs to CLIENTS), related_client_id (foreign key to CLIENTS)
- **Unique Constraint:** (client_id, related_client_id)
- **Key Fields:**
  - `relationship` — Family relationship type (sister, father, spouse, etc.)
  - `notes` — Additional family information
  - `created_at`, `updated_at` — Record lifecycle

**Role:** Maintains family tree structure for communication and beneficiary tracking

---

#### **CLIENT_COMMUNICATIONS**
**Purpose:** Audit trail of all client interactions
- **Primary Key:** id (bigint)
- **Foreign Keys:** client_id (belongs to CLIENTS), created_by (belongs to USERS)
- **Key Fields:**
  - `channel` — Communication method (email, phone, sms, in-person, etc.)
  - `subject` — Communication topic
  - `message` — Full message/note content
  - `occurred_at` — When the communication happened
  - `created_by` — System user who logged the communication
  - `created_at`, `updated_at` — Record lifecycle

**Role:** Complete communication history for customer relationship management and compliance

---

### Contract & Financial Entities

#### **CLIENT_CONTRACTS**
**Purpose:** Legal service agreements between cemetery and clients
- **Primary Key:** id (bigint)
- **Foreign Keys:** client_id (belongs to CLIENTS), lot_id (nullable, belongs to LOTS)
- **Key Fields:**
  - `contract_number` — Unique contract identifier (indexed)
  - `contract_type` — Type (purchase, lease, maintenance, etc.)
  - `status` — Contract status (draft, active, completed, terminated, etc.)
  - `duration_months` — Contract term in months
  - `lot_kind` — Categorization of lot/service type
  - `total_amount` — Total contract value (12,2 decimal)
  - `amount_paid` — Cumulative payments received
  - `due_date` — Contract expiration or renewal date
  - `signed_at` — Contract signature date
  - `pdf_path`, `pdf_generated_at` — Generated contract document
  - `pdf_emailed_at` — Document delivery timestamp
  - `notes` — Contract terms or special conditions
  - `created_at`, `updated_at` — Record lifecycle

**Role:** Formal documentation of cemetery services and financial obligations

---

#### **PAYMENT_PLANS**
**Purpose:** Installment payment structures for contracts
- **Primary Key:** id (bigint)
- **Foreign Keys:** client_id (belongs to CLIENTS), client_contract_id (nullable), lot_id (nullable)
- **Key Fields:**
  - `plan_number` — Unique payment plan identifier
  - `status` — Plan status (active, completed, canceled)
  - `principal_amount` — Total principal balance (12,2 decimal)
  - `downpayment_amount` — Initial payment required
  - `term_months` — Payment term (12, 18, 24 months)
  - `interest_rate_percent` — Annual interest rate (5,2 decimal - e.g., 10.00%)
  - `financed_principal` — Amount being financed
  - `interest_amount` — Total interest to be paid
  - `start_date` — Payment plan start date
  - `penalty_grace_days` — Days before penalty accrues
  - `penalty_rate_percent` — Penalty rate per 30-day overdue period
  - `last_notified_at` — Last payment reminder timestamp
  - `notes` — Plan-specific terms
  - `created_at`, `updated_at` — Record lifecycle

**Role:** Flexible payment structuring with interest and penalty calculations

---

#### **PAYMENT_INSTALLMENTS**
**Purpose:** Individual payment due dates and amounts within a payment plan
- **Primary Key:** id (bigint)
- **Foreign Keys:** payment_plan_id (belongs to PAYMENT_PLANS)
- **Key Fields:**
  - `sequence` — Installment order (0=downpayment, 1..term=regular installments)
  - `type` — Installment classification (downpayment, installment)
  - `due_date` — Payment due date (indexed for aging reports)
  - `amount_due` — Total amount due including interest
  - `principal_due` — Principal portion
  - `interest_due` — Interest portion
  - `amount_paid` — Amount paid to date
  - `penalty_accrued` — Late fees accumulated
  - `penalty_paid` — Late fees paid
  - `status` — Installment status (pending, partial, paid, overdue)
  - `paid_at` — Actual payment date
  - `created_at`, `updated_at` — Record lifecycle

**Role:** Granular payment tracking with principal/interest breakdown and penalty management

---

#### **PAYMENT_TRANSACTIONS**
**Purpose:** Recording of actual payments received
- **Primary Key:** id (bigint)
- **Foreign Keys:** payment_plan_id (belongs to PAYMENT_PLANS), client_id (belongs to CLIENTS), created_by (nullable, belongs to USERS)
- **Key Fields:**
  - `transaction_date` — Payment receipt date (indexed)
  - `amount` — Payment amount (12,2 decimal)
  - `method` — Payment method (check, cash, credit card, bank transfer, etc.)
  - `reference_number` — Payment reference/receipt number (indexed)
  - `unapplied_amount` — Amount not yet allocated to specific installments
  - `receipt_path` — Scanned receipt or proof of payment
  - `notes` — Payment notes or special handling
  - `created_by` — User who recorded the payment
  - `created_at`, `updated_at` — Record lifecycle

**Role:** Transaction recording with flexible payment allocation mechanism

---

#### **PAYMENT_TRANSACTION_ALLOCATIONS**
**Purpose:** Allocation of payments against specific installments and penalties
- **Primary Key:** id (bigint)
- **Foreign Keys:** payment_transaction_id (belongs to PAYMENT_TRANSACTIONS), payment_installment_id (nullable, belongs to PAYMENT_INSTALLMENTS)
- **Key Fields:**
  - `type` — Allocation type (penalty, installment, unapplied)
  - `amount_applied` — Amount applied (12,2 decimal)
  - `created_at`, `updated_at` — Record lifecycle

**Role:** Flexible payment application allowing one payment to be split across multiple installments and penalties

---

### Operations & Service Entities

#### **RESERVATIONS**
**Purpose:** Lot reservation management with payment linking
- **Primary Key:** id (bigint)
- **Foreign Keys:** client_id (belongs to CLIENTS), lot_id (belongs to LOTS), payment_plan_id (nullable, belongs to PAYMENT_PLANS), client_contract_id (nullable)
- **Key Fields:**
  - `reserved_at` — Reservation date
  - `expires_at` — Reservation expiration date
  - `status` — Reservation status (active, expired, fulfilled, etc.)
  - `payment_status` — Payment status relative to reservation
  - `payment_terms` — Negotiated payment terms
  - `contract_path` — Generated reservation/contract document
  - `fulfilled_at` — Date reservation was completed (burial occurred)
  - `notes` — Special reservation conditions
  - `created_at`, `updated_at` — Record lifecycle
  - **Composite Index:** (lot_id, status) for queries

**Role:** Pre-burial lot reservation with temporal expiration and payment status tracking

---

#### **MAINTENANCE_RECORDS**
**Purpose:** Track cemetery maintenance and lot upkeep services
- **Primary Key:** id (bigint)
- **Foreign Keys:** client_id (belongs to CLIENTS), lot_id (nullable, belongs to LOTS), client_contract_id (nullable), created_by (nullable, belongs to USERS)
- **Key Fields:**
  - `service_type` — Type of maintenance (general, cleaning, repair, landscaping, etc.)
  - `status` — Service status (scheduled, in-progress, completed, canceled)
  - `service_date` — Scheduled or completed service date
  - `amount` — Service cost (12,2 decimal)
  - `notes` — Service details or results
  - `created_by` — Staff member handling service
  - `created_at`, `updated_at` — Record lifecycle
  - **Composite Index:** (client_id, service_date) for historical queries

**Role:** Operational tracking of maintenance activities and associated costs

---

#### **VISITOR_LOGS**
**Purpose:** Track cemetery visits for security and audit purposes
- **Primary Key:** id (bigint)
- **Foreign Keys:** deceased_id (belongs to DECEASED)
- **Key Fields:**
  - `contact_number` — Visitor contact information
  - `address` — Visitor address
  - `purpose` — Visit purpose
  - `visit_date` — Date of visit
  - `escalation_date` — Escalation timestamp if needed
  - `contact_path` — Contact method/channel
  - `created_at`, `updated_at` — Record lifecycle

**Role:** Audit trail for cemetery visits and visitor management

---

#### **PASSWORD_RESET_TOKENS**
**Purpose:** Laravel authentication token management
- **Primary Key:** email (varchar)
- **Key Fields:**
  - `token` — Hashed reset token
  - `created_at` — Token generation timestamp

**Role:** System-generated table for password reset functionality

---

---

## Data Model & Relationships

### Primary Relationship Flows

#### 1. **Client → Lot Ownership Flow**
```
CLIENT (1) ──→ (many) CLIENT_LOT_OWNERSHIPS (many) ← (1) LOTS
```
- Allows clients to own multiple lots and lots to have multiple owners
- Tracks ownership history with start/end dates
- Supports co-ownership scenarios

#### 2. **Contract → Payment Flow**
```
CLIENT (1) ──→ (many) CLIENT_CONTRACTS
                        ├──(many) PAYMENT_PLANS (1) ──→ (many) PAYMENT_INSTALLMENTS
                        └──Payment Planning Infrastructure
```
- Each client can have multiple contracts
- Each contract can have multiple payment plans
- Each payment plan broken into installments

#### 3. **Payment Processing Flow**
```
PAYMENT_TRANSACTIONS (many) ──→ PAYMENT_TRANSACTION_ALLOCATIONS (many)
                                        └──→ PAYMENT_INSTALLMENTS
```
- Flexible allocation allows one transaction to address multiple installments
- Separate tracking of principal, interest, and penalty portions

#### 4. **Deceased → Burial & Exhumation Flow**
```
LOTS (1) ──→ (many) DECEASED ──→ (0..1) EXHUMATIONS
```
- Multiple deceased records per lot (family plots)
- Each deceased can have 0 or 1 exhumation request
- Complete workflow tracking from burial to exhumation

#### 5. **Client Communication Hub**
```
CLIENT (1) ──→ (many) CLIENT_COMMUNICATIONS
CLIENT (1) ──→ (many) CLIENT_FAMILY_LINKS
CLIENT (1) ──→ (many) RESERVATIONS
CLIENT (1) ──→ (many) MAINTENANCE_RECORDS
```
- Clients are central to most operations
- Multiple relationship types tracked independently

---

## Business Workflows

### Workflow 1: Lot Sale to Burial

**Step 1: Lot Creation & Availability**
- Lot created with spatial coordinates, section, block
- Status set to `available`
- GIS geometry stored for map integration

**Step 2: Client Engagement**
- Client record created
- Address and contact information stored
- Consent captured for data processing

**Step 3: Reservation**
- Lot reserved by client
- Reservation date and expiration set
- Status tracked as `active`

**Step 4: Contract Execution**
- Client_Contract created (type: 'purchase' or 'lease')
- Contract_number generated and indexed
- Total amount and terms negotiated
- Stored as PDF for documentation

**Step 5: Payment Planning**
- Payment_Plan created linked to contract
- Plan terms set (duration, interest rate, down payment)
- Individual installments automatically calculated based on term_months

**Step 6: Payment Collection**
- Payment_Transactions recorded as received
- Amounts applied to Payment_Installments via Allocations
- Principal, interest, and penalties tracked separately
- Overdue penalties calculated automatically

**Step 7: Burial & Deceased Record**
- Deceased record created
- Linked to specific lot
- Burial date recorded
- Lot status changed to `occupied`

---

### Workflow 2: Exhumation Request

**Step 1: Request Initiation**
- Exhumation record created in `draft` status
- Linked to Deceased record
- Requested_by information captured

**Step 2: Approval Chain**
```
draft → submitted → approved → scheduled → completed → archived
```
- Each status transition timestamped
- Approval dates recorded
- Workflow_status enum restricts invalid transitions

**Step 3: Permit Acquisition**
- Exhumation_permit_path stored
- Transfer_permit_path if transferring to another cemetery

**Step 4: Transfer Logistics**
- Destination cemetery details recorded
- Contact person and facility information stored
- Transport company, vehicle, driver assigned
- Transport log maintained

**Step 5: Completion**
- Exhumation date recorded
- Transfer certificate generated and dated
- Status moved to `completed`
- Final archival possible

---

### Workflow 3: Maintenance Service**

**Step 1: Service Request**
- Maintenance_Record created
- Client and lot linked
- Service_type selected
- Scheduled service_date set

**Step 2: Execution**
- Status moved to `in_progress`
- Amount quoted or finalized

**Step 3: Completion & Cost**
- Service_date updated if different from scheduled
- Amount finalized
- Status moved to `completed`
- Created_by staff member recorded

**Step 4: Historical Tracking**
- Composite index (client_id, service_date) enables easy maintenance history queries
- Enables preventive maintenance planning

---

### Workflow 4: Family Management**

**Step 1: Client Record Creation**
- Individual clients created as needed

**Step 2: Family Linking**
- Client_Family_Links created between related clients
- Relationship type specified (spouse, parent, child, sibling, etc.)
- Bidirectional relationships can be recorded

**Step 3: Communication**
- Client_Communications logged for each interaction
- Channel, subject, message captured
- Occurred_at timestamp (when communication happened vs recorded)
- Can target related family members for important updates

---

## Key Concepts

### Financial Calculations

#### Interest Calculation
- Simple interest: `interest = principal × rate × (term_months / 12)`
- Stored in payment_plans for transparency

#### Penalty Calculation
- Applied to overdue installments per 30-day period
- `penalty = unpaid_balance × penalty_rate_percent`
- Accrued separately from principal and interest
- Tracked in payment_installments and payment_transaction_allocations

#### Payment Allocation Priority
- Penalties paid first (oldest to newest)
- Then interest
- Then principal
- Flexible via payment_transaction_allocations.type

### Spatial Data

**GPS Precision:**
- Latitude: 10 decimal places (≈1.1mm accuracy)
- Longitude: 11 decimal places (≈1.1mm accuracy)
- GIS geometry data type for complex spatial queries

**Usage:**
- Map visualization of lots
- Proximity searches (nearby lots)
- Polygon-based cemetery section queries
- Integration with GIS tools

### Temporal Tracking

- **Ownership tenure** via CLIENT_LOT_OWNERSHIPS (started_at, ended_at)
- **Workflow states** via EXHUMATIONS (requested_at, approved_at, exhumed_at, transfer_certificate_generated_at)
- **Payment history** via PAYMENT_TRANSACTIONS (transaction_date, paid_at)
- **Communication log** via CLIENT_COMMUNICATIONS (occurred_at, created_at)

### Audit & Compliance

- **Consent tracking:** consent_given flag on CLIENTS
- **User tracking:** created_by foreign key on auditable actions
- **Complete audit trail:** timestamps on all tables
- **Document paths:** PDF generation and email delivery tracking

---

## Database Architecture

### Indexing Strategy

**Frequently Queried Paths:**
- `LOTS.lot_number` — Unique constraint for per-category lookup
- `CLIENT_CONTRACTS.contract_number` — Unique for retrieval
- `PAYMENT_PLANS.plan_number` — Index for quick lookup
- `PAYMENT_PLANS.status` — Filter for active plans
- `PAYMENT_INSTALLMENTS.due_date` — Aging reports and overdue queries
- `PAYMENT_TRANSACTIONS.transaction_date` — Cash flow analysis
- `PAYMENT_TRANSACTIONS.reference_number` — Receipt matching

**Composite Indexes:**
- `RESERVATIONS(lot_id, status)` — Lot availability queries
- `CLIENT_COMMUNICATIONS(client_id)` — Communication history
- `MAINTENANCE_RECORDS(client_id, service_date)` — Service history
- `EXHUMATIONS(workflow_status, requested_at)` — Workflow dashboards

### Foreign Key Strategy

**Cascade Delete:**
- CLIENTS → all dependent records (when client deleted, cascade to contracts, payments, communications)
- LOTS → DECEASED, CLIENT_LOT_OWNERSHIPS, RESERVATIONS (lot deletion cascades)
- PAYMENT_PLANS → PAYMENT_INSTALLMENTS, PAYMENT_TRANSACTIONS (plan deletion cascades)

**Null On Delete:**
- CLIENT_CONTRACTS.lot_id (lot can be deleted, contract remains)
- RESERVATIONS.payment_plan_id (plan can be deleted, reservation historical record persists)
- PAYMENT_TRANSACTION_ALLOCATIONS.payment_installment_id (installment records can be archived)

### Constraints

**Unique Constraints:**
- CLIENTS.email
- CLIENT_LOT_OWNERSHIPS(client_id, lot_id) — One relationship per client-lot pair
- CLIENT_FAMILY_LINKS(client_id, related_client_id) — One link per family pair
- CLIENT_CONTRACTS.contract_number
- LOTS.lot_number per category (check constraint)

**Check Constraints:**
- EXHUMATIONS.workflow_status enum validation
- PAYMENT_INSTALLMENTS.status enum validation

---

## Summary

The Liliwmemoria system provides a comprehensive cemetery management solution with:
- **Complete client lifecycle management** from initial contact through burial and post-burial services
- **Flexible financial management** with installment plans, interest, and penalty tracking
- **Spatial capabilities** for map-based lot visualization and queries
- **Audit-complete architecture** with user tracking, timestamps, and document preservation
- **Workflow management** for complex processes like exhumations
- **Scalable design** supporting multiple locations, thousands of lots, and complex family relationships

The database normalizes well to prevent data duplication while maintaining queryability across the full business domain.
