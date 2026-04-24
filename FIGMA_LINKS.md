# VIDYA — Master Project Context

**Version:** 1.0 (Concept Locked)
**Date:** April 24, 2026
**Founder:** Kavish Sharma (Jaipur, India)
**Status:** Pre-build, blueprint phase
**Sister Product:** Arya (deployed at monoloopproductions.in)

---

## TABLE OF CONTENTS

1. [Product Identity](#1-product-identity)
2. [Positioning & Market](#2-positioning--market)
3. [Core USP & Philosophy](#3-core-usp--philosophy)
4. [Organizational Hierarchy](#4-organizational-hierarchy)
5. [Roles & Permissions](#5-roles--permissions)
6. [Subjects & Curriculum](#6-subjects--curriculum)
7. [Test Workflow (End-to-End)](#7-test-workflow-end-to-end)
8. [Topic Code System](#8-topic-code-system)
9. [Admin Panel Features](#9-admin-panel-features)
10. [Role-Specific Workflows](#10-role-specific-workflows)
11. [What Vidya Stores vs Doesn't](#11-what-vidya-stores-vs-doesnt)
12. [AI Integration](#12-ai-integration)
13. [Reports & Analytics](#13-reports--analytics)
14. [Tech Stack](#14-tech-stack)
15. [Design System](#15-design-system)
16. [Non-Goals (What NOT to Build)](#16-non-goals-what-not-to-build)
17. [Success Metrics & Pilot Plan](#17-success-metrics--pilot-plan)
18. [Development Workflow](#18-development-workflow)

---

## 1. PRODUCT IDENTITY

**Product Name:** Vidya (विद्या = knowledge)

**Product Category:** Subtopic Mastery Analytics Layer for Coaching Institutes

**Tagline:** *"The analytics layer your OMR software forgot to build."*

**One-Line Pitch:** Vidya tells coaching institutes EXACTLY which subtopic each student is weak in — the one question ScholarSERP, Proctur, and other ERPs can't answer.

**Target Customer:** Indian coaching institutes preparing students for NEET, IIT-JEE, and similar competitive exams.

**Founder Story:** Built by Kavish Sharma, Jaipur-based founder who previously built Arya (school analytics). Vidya applies the same subtopic-mastery USP to the larger, more lucrative coaching market.

---

## 2. POSITIONING & MARKET

### 2.1 Target Market

**Primary:** Indian coaching institutes — NEET, IIT-JEE focused
- Kota (coaching capital of India — 200,000+ students)
- Delhi (UPSC, JEE, banking coaching)
- Jaipur (local pilot target)
- Hyderabad, Mumbai, Bangalore
- Tier 2/3 city coaching centers

**Market Size:**
- 4+ lakh coaching institutes in India
- Average revenue per student: ₹1-3 lakh/year
- Willingness to pay for analytics: HIGH (results = revenue)

### 2.2 Competitive Positioning

**Vidya is NOT a coaching ERP.** Vidya is a **complementary analytics layer** that works alongside existing software.

| Product | Type | Vidya's Relationship |
|---------|------|---------------------|
| ScholarSERP | Full ERP (20 modules) | Complement, not compete |
| Proctur | ERP + LMS | Complement |
| Addmen OMR | OMR scanning software | Direct integration (Excel upload) |
| Teachmint | ERP + LMS | Complement |
| Classplus | Coaching platform | Complement |

### 2.3 Positioning Statement

> *"Keep your existing OMR software (ScholarSERP, Proctur, Addmen). Add Vidya for ₹X per student per year. Your faculty will know exactly which subtopic each student is weak in — not just which subject. The one question no ERP can answer."*

### 2.4 Why This Positioning Wins

- ✅ Zero switching cost (institute keeps existing software)
- ✅ No feature-parity war (we don't compete on breadth)
- ✅ Sharp value prop (one clear problem solved)
- ✅ Easy sales pitch (add-on, not replacement)
- ✅ Fast adoption (non-disruptive)
- ✅ Defensible moat (subtopic-level tracking is architecturally hard to retrofit)

### 2.5 Pricing Model

**₹X per student per year** (exact pricing TBD post-pilot)
- Per-student model (not per-institute, not per-module)
- Annual subscription
- Volume discounts for 1000+ students
- Small add-on pricing (not ERP pricing)

---

## 3. CORE USP & PHILOSOPHY

### 3.1 The USP

**Subtopic-level mastery tracking for every student, for every test.**

Current coaching software tells faculty:
- ❌ "Rohan scored 32/45 in Physics" (subject-level)

Vidya tells faculty:
- ✅ "Rohan is strong in Kinematics (8/10), Newton's Laws (7/8), but WEAK in Rotational Motion (2/8)"

The difference: **Actionable remediation vs generic advice.**

### 3.2 Product Philosophy

**"System assists, Faculty decides."** (inherited from Arya)

Vidya's role:
- Aggregate data
- Surface patterns
- Suggest insights
- Flag anomalies

Faculty's role:
- Interpret context
- Make teaching decisions
- Design interventions
- Maintain relationships

Vidya **empowers** faculty, doesn't replace them.

### 3.3 Design Principles

1. **Analytics first, not features first** — We do ONE thing extraordinarily well
2. **Lightweight storage** — We don't become a question bank or ERP
3. **Existing workflow compatible** — Institutes don't change their process
4. **Teacher IP protection** — Question papers never enter Vidya
5. **Audit-ready from day 1** — Every action logged
6. **Multi-tenant from day 1** — One codebase, many institutes

---

## 4. ORGANIZATIONAL HIERARCHY

### 4.1 Hierarchy Tree

```
Coaching Institute (tenant)
  └─ Course (e.g., NEET-2026, IIT-JEE-2027)
      └─ Batch (e.g., Morning Batch A, Evening Batch B)
          └─ Student
```

### 4.2 Comparison with Arya

| Arya (Schools) | Vidya (Coaching) |
|---|---|
| School | Coaching Institute |
| Grade + Section (e.g., 10-A) | Course + Batch (e.g., NEET-Batch-A) |
| Academic Year | Batch Year/Cohort |
| Subjects (8+ CBSE) | Subjects (3-4 exam-specific) |

### 4.3 Example Hierarchy

**Institute:** ABC Coaching, Jaipur

**Courses:**
- NEET-2026 (1-year crash course)
- NEET-2027 (2-year program)
- IIT-JEE-2026 (1-year crash)
- IIT-JEE-2027 (2-year)

**Batches (under NEET-2026):**
- Morning Batch A (40 students)
- Morning Batch B (45 students)
- Evening Batch A (38 students)
- Weekend Batch (30 students)

**Staff Assignment:**
- Teachers assigned at Batch level
- Can teach multiple batches
- Typically subject-specific (e.g., Physics teacher for multiple batches)

---

## 5. ROLES & PERMISSIONS

### 5.1 Six Roles

Vidya has **6 roles** (vs Arya's 4):

1. **Owner** — Strategic, top of hierarchy
2. **Academic Head** — Full operational visibility (equivalent to Arya's Principal)
3. **Admin** — System setup, user management, can create sub-admins
4. **Sub-Admin** — OMR response handler + student data access
5. **Teacher** — View-only for assigned tests and students
6. **Typist** — Test blueprint creator (Excel-based)

### 5.2 Role Responsibilities

#### 👑 Owner
- Strategic dashboard (YoY, enrollment trends)
- Course performance comparison (NEET vs IIT-JEE)
- Batch performance analytics
- Revenue insights (future)
- Access to ALL data across institute
- Activity log visibility

#### 🎓 Academic Head
- Full visibility into all student performance
- Batch-wise analytics
- Teacher growth reports
- Teaching health diagnostics
- Monthly trends & trajectories
- Cross-batch comparisons
- Section heatmaps (class performance matrix)
- Activity log
- Curriculum decisions

#### ⚙️ Admin
- Institute setup (courses, batches)
- Staff management (create all roles)
- Student import & management
- Subject + Curriculum tree management
- Teacher-to-batch assignments
- Sub-admin creation with custom permissions
- Settings (Coaching Profile, Notifications, AI Settings)
- System configuration

#### 📤 Sub-Admin
- Upload OMR response Excel files
- View all tests (to select for upload)
- Full student database access
- Student performance records (view)
- Manual roll-number matching (for validation errors)
- Cannot see teacher growth reports
- Cannot create tests
- Cannot modify test blueprints
- **Custom permissions** (admin can grant specific areas)

#### 👨‍🏫 Teacher
- View assigned tests only
- View conducted tests + results
- My Students section (assigned students only)
- Student report cards
- Subtopic-wise performance
- Class heatmap (for assigned batches)
- Individual student deep-dives
- Cannot create tests
- Cannot upload responses
- Cannot see other teachers' data

#### ⌨️ Typist
- Download Excel template from Vidya
- Create test with metadata (code, date, type, batches)
- Fill Excel: Q#, Answer, Topic Code, Marks
- Upload Excel to Vidya
- Assign teachers by subject (dropdown)
- View own created tests
- Cannot see student data
- Cannot upload responses
- Cannot see reports

### 5.3 Permission Matrix

```
                    Owner | AcadHd | Admin | SubAdm | Teacher | Typist
──────────────────────────────────────────────────────────────────────
Create test                                                      ✅
Upload answer key                                                 ✅
Assign test to teachers                                           ✅
Upload OMR responses        (auto)          ✅
View all tests         ✅      ✅      ✅     ✅
View own tests                               ✅      ✅           ✅
View all students      ✅      ✅      ✅     ✅
View assigned students                               ✅
Create staff                         ✅
Create sub-admin                     ✅
Configure system                     ✅
View teacher reports   ✅      ✅     (ltd)
View student reports   ✅      ✅      ✅     ✅      ✅(ltd)
View activity log      ✅      ✅      ✅
Business insights      ✅      ✅
Strategic dashboard    ✅
```

### 5.4 Sub-Admin Permission System

Admin creates sub-admins with granular permissions:

**Pre-defined Templates:**
- **Response Manager** (OMR upload + student view)
- **Student Manager** (student data + imports)
- **Batch Coordinator** (specific batches only)
- **Custom** (checkbox-based granular)

**Granular Permissions (checkbox):**
- [ ] Can upload OMR responses
- [ ] Can view all students
- [ ] Can view specific batches only
- [ ] Can edit student data
- [ ] Can create students
- [ ] Can view audit logs
- [ ] Can configure notifications
- [ ] Can manage faculty accounts

---

## 6. SUBJECTS & CURRICULUM

### 6.1 Course-wise Subjects

**NEET (Medical Entrance):**
- Physics
- Chemistry
- Biology
  - Zoology
  - Botany

**IIT-JEE (Engineering Entrance):**
- Physics
- Chemistry
- Mathematics

### 6.2 Pre-Seeded Curriculum Tree

Vidya ships with pre-loaded syllabus for:

**NEET Syllabus (~500 subtopics):**
- Physics (150+ subtopics)
- Chemistry (130+ subtopics)
- Zoology (110+ subtopics)
- Botany (110+ subtopics)

**IIT-JEE Syllabus (~400 subtopics):**
- Physics (140+ subtopics)
- Chemistry (130+ subtopics)
- Mathematics (130+ subtopics)

### 6.3 Curriculum Structure (4-level tree)

```
Subject (e.g., Physics)
  └─ Chapter (e.g., Mechanics)
      └─ Topic (e.g., Kinematics)
          └─ Subtopic (e.g., Motion in 1D)
```

### 6.4 Admin Customization

Admin can:
- ✅ Add new subjects/chapters/topics/subtopics
- ✅ Modify existing codes
- ✅ Deactivate unused items
- ✅ Reorder display sequence
- ❌ Cannot delete subtopics that have test data linked

---

## 7. TEST WORKFLOW (END-TO-END)

### 7.1 Complete Lifecycle

```
STAGE 1: Paper Design (OFFLINE — Teacher's work)
  ↓
STAGE 2: Test Blueprint Creation (Typist in Vidya)
  ↓
STAGE 3: OMR Sheet Printing & Distribution (OFFLINE)
  ↓
STAGE 4: Test Conducted (OFFLINE — Students)
  ↓
STAGE 5: OMR Scanning (EXTERNAL software)
  ↓
STAGE 6: Response Upload (Sub-Admin in Vidya)
  ↓
STAGE 7: Automated Analysis (Vidya system)
  ↓
STAGE 8: Reports Available (all roles per permissions)
```

### 7.2 Stage-by-Stage Details

#### Stage 1: Paper Design (Teacher, offline)
- Teacher writes questions on Word/PDF
- Teacher adds topic code to EACH question (from Vidya's reference list)
- Teacher creates answer key
- Teacher hands over paper + answer key + topic codes to Typist

**Example question from teacher:**
```
Q1. A body moves with uniform acceleration from rest...
    Topic: P-MEC-KIN-01
    Correct Answer: B
```

#### Stage 2: Test Blueprint Creation (Typist, in Vidya)

Typist workflow:
1. Login to Vidya
2. Click "Create New Test"
3. Enter test metadata:
   - Test Code (e.g., NEET-MOCK-2026-01)
   - Test Name
   - Course (dropdown)
   - Batches (multi-select)
   - Test Date
   - Test Type (DPT/Weekly/Mock/FLT)
   - Duration (minutes)
   - Total Questions
   - Marking Scheme (+4/-1, +4/0, custom)
4. Download Excel Template
5. Fill Excel (from teacher's data):
   ```
   Q# | Answer | Topic Code     | Marks
   ------------------------------------
   1  | B      | P-MEC-KIN-01   | +4/-1
   2  | A      | P-MEC-KIN-02   | +4/-1
   ...
   180| D      | B-ECO-ECS-12   | +4/-1
   ```
6. Upload Excel to Vidya
7. System validates:
   - All topic codes exist in DB
   - Question count matches metadata
   - All answers valid (A/B/C/D or numeric)
   - No duplicate question numbers
8. Assign teachers by subject:
   - Subject distribution auto-detected from topic codes
   - Dropdown per subject (filtered by subject + batch)
   - Example: Physics Q1-45 → Dropdown shows Physics teachers
9. Review & Submit
10. Test status: "Blueprint Ready" ✅

#### Stage 3: OMR Sheet Printing (OFFLINE)
- Institute's standard process
- Not handled by Vidya
- OMR sheets printed with test code, roll number bubbles, answer bubbles

#### Stage 4: Test Conducted (OFFLINE)
- Students take test on OMR sheets
- Fill roll number, test code, answers
- Submit to invigilator

#### Stage 5: OMR Scanning (EXTERNAL)
- Institute uses their existing OMR scanner
- Scanner generates Excel file:
  ```
  Roll No | Q1 | Q2 | Q3 | ... | Q180
  2401    | B  | A  | C  | ... | D
  2402    | A  | A  | B  | ... | C
  ```
- This Excel is what gets uploaded to Vidya

#### Stage 6: Response Upload (Sub-Admin, in Vidya)

Sub-Admin workflow:
1. Login to Vidya
2. Click "Upload Responses"
3. See list of tests awaiting responses (filtered by test status)
4. Select test (e.g., NEET-MOCK-2026-01)
5. Upload OMR Excel file (drag-drop or browse)
6. System validates:
   - All roll numbers exist in student database
   - Test code matches blueprint
   - Question count matches
   - All answer values valid
7. If validation errors:
   - Show list of unmatched students
   - Suggest fuzzy matches (e.g., 2401 → 2410 Rohan)
   - Allow manual map, skip, or add new
   - Cannot proceed until resolved
8. If validation passes:
   - Click "Process Responses"
   - Confirmation dialog
   - Submit

#### Stage 7: Automated Analysis (Vidya backend)

System automatically (background job):
- Matches each student's response with answer key
- Applies marking scheme (+/- negative marking)
- Tags each response with topic code (from blueprint)
- Calculates per-subject score (Physics: 32/45)
- Calculates per-topic score (Kinematics: 8/10)
- Calculates per-subtopic mastery (Motion 1D: 75%)
- Updates student_subtopic_mastery table (cumulative)
- Calculates rank, percentile
- Generates all reports (cached)
- Triggers notifications to relevant roles

#### Stage 8: Reports Available

- Students can see own report (if student portal enabled in Phase 2)
- Teacher sees class heatmap + individual cards (assigned only)
- Sub-Admin sees all students + performance
- Academic Head sees full picture + teacher reports
- Owner sees strategic view

---

## 8. TOPIC CODE SYSTEM

### 8.1 Code Format

**Pattern:** `[SUBJECT]-[CHAPTER]-[TOPIC]-[SUBTOPIC]`

### 8.2 Subject Codes

| Code | Subject |
|------|---------|
| P | Physics |
| C | Chemistry |
| Z | Zoology |
| B | Botany |
| M | Mathematics (IIT-JEE only) |

### 8.3 Examples

| Code | Meaning |
|------|---------|
| P-MEC-KIN-01 | Physics > Mechanics > Kinematics > Motion in 1D |
| P-MEC-KIN-02 | Physics > Mechanics > Kinematics > Motion in 2D |
| P-MEC-NEW-01 | Physics > Mechanics > Newton's Laws > First Law |
| P-OPT-REF-03 | Physics > Optics > Refraction > Snell's Law |
| C-ORG-ALK-01 | Chemistry > Organic > Alkanes > Nomenclature |
| C-PHY-EQL-05 | Chemistry > Physical > Equilibrium > Kp & Kc |
| Z-CEL-DNA-02 | Zoology > Cell > DNA > Replication |
| B-ECO-ECS-01 | Botany > Ecology > Ecosystems > Food Chains |
| M-CAL-DIF-05 | Math > Calculus > Differentiation > Chain Rule |

### 8.4 Code Rules

- 3-letter codes for chapter/topic/subtopic
- Easy to type, recognize, remember
- Teachers get printable reference sheet
- Auto-complete in typist UI
- Codes immutable once tests conducted (data integrity)

---

## 9. ADMIN PANEL FEATURES

### 9.1 Reused from Arya (90% copy)

- ✅ Subjects management UI
- ✅ Curriculum tree (topic/subtopic management)
- ✅ Staff management (add/edit/deactivate users)
- ✅ Student import (CSV/Excel)
- ✅ Settings (3 tabs: Profile, Notifications, AI)
- ✅ Assignment flows
- ✅ Audit log viewer

### 9.2 Modified for Vidya

| Arya | Vidya |
|------|-------|
| Classrooms (Grade-Section) | Courses + Batches |
| School Profile | Coaching Profile |
| School-level settings | Institute-level settings |

### 9.3 New for Vidya

- ➕ Course management (pre-seeded NEET/JEE templates)
- ➕ Batch management (under courses)
- ➕ Sub-admin creation with permission templates
- ➕ Topic code reference printable (for teachers)
- ➕ Negative marking config per course

### 9.4 Settings Tabs

#### Tab 1: Coaching Profile
- Institute name
- Logo upload
- Address, phone, email, website
- Academic year format
- Timezone

#### Tab 2: Notifications
- Email SMTP config
- Notification preferences per role
- Alert thresholds (at-risk student definition)
- Weekly digest settings

#### Tab 3: AI Settings
- Gemini API key
- AI mode (auto/gemini-only/template-only)
- Circuit breaker settings
- Cache TTL
- Usage monitoring

---

## 10. ROLE-SPECIFIC WORKFLOWS

### 10.1 Teacher Workflow

```
Login → Dashboard
├── My Tests
│   ├── Upcoming (assigned but not conducted)
│   ├── Pending Results (conducted, awaiting upload)
│   └── Completed (with results)
│
├── My Students
│   ├── Level 1: Batch list (my assigned batches)
│   ├── Level 2: Students in selected batch
│   └── Level 3: Individual student deep-dive
│
├── Reports
│   ├── Class Heatmap (students × subtopics)
│   ├── Student Performance Cards
│   └── Subtopic Strength/Weakness
│
└── Profile
```

### 10.2 Typist Workflow

```
Login → Dashboard
├── Create Test (main feature)
│   ├── Step 1: Metadata
│   ├── Step 2: Download template
│   ├── Step 3: Upload filled Excel
│   ├── Step 4: Assign teachers by subject
│   └── Step 5: Review & submit
│
├── My Tests Created
│   ├── Draft (not yet submitted)
│   ├── Ready (submitted, awaiting responses)
│   └── Analyzed (responses uploaded)
│
└── Profile
```

### 10.3 Sub-Admin Workflow

```
Login → Dashboard
├── Upload Responses (main feature)
│   ├── Select test (from ready list)
│   ├── Upload OMR Excel
│   ├── Validation + manual matching if needed
│   └── Submit for processing
│
├── My Students
│   ├── All students (if permission)
│   ├── Specific batches (if permission)
│   └── Performance records
│
├── Student Data
│   ├── View student profile
│   ├── Edit details (if permission)
│   └── Export reports
│
└── Profile
```

### 10.4 Academic Head Workflow

```
Login → Dashboard
├── Overview (aggregate KPIs)
│
├── Students
│   ├── All students (cross-batch)
│   ├── At-risk report
│   └── Top performers
│
├── Batches
│   ├── Batch performance comparison
│   ├── Course-wise analytics
│   └── Batch heatmaps
│
├── Teachers
│   ├── Teaching Health Diagnostic
│   ├── Teacher growth reports
│   └── Subject-wise effectiveness
│
├── Reports
│   ├── Section Comparison (PR1)
│   ├── Monthly Trends (PR3)
│   ├── Class Heatmap (TR1)
│   └── Institute Overview
│
├── Activity Log
│
└── Profile
```

### 10.5 Owner Workflow

```
Login → Dashboard (Strategic View)
├── Executive Summary
│   ├── Total students, batches, tests
│   ├── YoY performance
│   └── Course comparison
│
├── Performance Metrics
│   ├── NEET vs IIT-JEE performance
│   ├── Batch comparisons
│   ├── Grade-tier analysis
│   └── Student bands distribution
│
├── Enrollment & Growth
│   ├── Admission trends (future)
│   ├── Retention metrics (future)
│   └── Capacity utilization
│
├── Full Reports Access
│
├── Activity Log
│
└── Profile
```

### 10.6 Admin Workflow

```
Login → Dashboard (Setup & Config)
├── Setup
│   ├── Courses (NEET, IIT-JEE)
│   ├── Batches (under courses)
│   ├── Subjects (pre-seeded, editable)
│   └── Curriculum (topic tree)
│
├── Users
│   ├── Staff management (all roles)
│   ├── Create sub-admin with permissions
│   ├── Activate/deactivate users
│   └── Reset passwords
│
├── Students
│   ├── Bulk import (Excel/CSV)
│   ├── Add/edit individual
│   └── Batch assignments
│
├── Assignments
│   ├── Teacher ↔ Batch
│   ├── Teacher ↔ Subject
│   └── Sub-admin ↔ Batches (if scoped)
│
├── Settings (3 tabs)
│
├── Audit Log
│
└── Profile
```

---

## 11. WHAT VIDYA STORES vs DOESN'T

### 11.1 Vidya STORES ✅

- Institute metadata (name, logo, contact)
- Users (all 6 roles)
- Course definitions
- Batch definitions
- Student profiles (academic data only)
- Subject tree
- Curriculum (chapters, topics, subtopics, codes)
- Test metadata (code, date, type, batch)
- Answer keys (per question)
- Topic codes per question
- Teacher assignments to tests
- Student responses (per question)
- Calculated scores (per test, per student)
- Calculated mastery (per subtopic, cumulative)
- Rank, percentile
- Activity logs
- Notifications
- AI insights (cached)

### 11.2 Vidya DOES NOT STORE ❌

- **Question text** (teacher's IP stays with teacher)
- **Question images/diagrams**
- Answer explanations
- Video/audio content
- Student photos (only name + roll + academic data)
- Parent contact (Phase 2)
- Fee information (never — ERP scope)
- Attendance (never — ERP scope)
- Hostel/transport data (never)
- Library data (never)
- Admission forms (never)

### 11.3 Storage Philosophy

**Analytics layer, not question bank.**

By storing only metadata + answer keys + responses:
- ✅ Small database footprint
- ✅ Fast queries
- ✅ No IP concerns for teachers
- ✅ No copyright issues
- ✅ Focused product identity
- ✅ Clear boundary with ERP products

---

## 12. AI INTEGRATION

### 12.1 Reuse Arya's Pattern (100%)

- Package: `google-gemini-php/laravel`
- Model: Gemini 2.5 Flash (free tier, 1000/day)
- Service: `AiInsightService.php` (copy from Arya, adapt prompts)
- Fallback: Template-based (always works)
- Circuit breaker (5 failures → auto-disable)
- 3 admin modes (auto/gemini-only/template-only)
- Cache TTL (1 hour default)

### 12.2 AI Context Methods for Vidya

```
AiInsightService methods:
├── batchComparison()     (similar to Arya PR1)
├── teachingHealth()      (similar to Arya PR2)
├── monthlyTrends()       (similar to Arya PR3)
├── classHeatmap()        (similar to Arya TR1)
├── instituteOverview()   (similar to Arya School Overview)
└── studentInsight()      (individual student AI narrative)
```

### 12.3 AI-Powered Features

- Batch performance narratives
- Teacher health diagnostics (supportive language)
- Student trajectory predictions
- At-risk student alerts with AI explanation
- Monthly executive summary for owner
- Subtopic weakness pattern detection

### 12.4 AI Tone & Philosophy (from Arya)

- Supportive, not punitive
- "Needs support" not "failing"
- Indian education context aware
- Actionable, not just descriptive
- 3-4 sentence limit
- Specific numbers/names included

---

## 13. REPORTS & ANALYTICS

### 13.1 Report Types

#### Student Reports
- **Performance Card**: Subject-wise summary with trends
- **Subtopic Mastery**: Detailed topic breakdown
- **Test-by-Test**: Chronological performance
- **Rank & Percentile**: Competitive positioning
- **Strengths & Weaknesses**: Auto-detected patterns

#### Teacher Reports
- **Class Heatmap**: Students × Subtopics matrix
- **Subject Performance**: Teacher's subject across batches
- **Individual Student Deep-Dive**: Per-student analysis
- **Weak Subtopic Detection**: Where class struggles

#### Batch Reports (Academic Head + Owner)
- **Batch Comparison**: Multi-batch performance
- **Grade-tier Analysis**: Top/middle/bottom performers
- **Enrollment Trends**: Growth over time
- **Course Performance**: NEET vs JEE comparison

#### Institute Reports (Academic Head + Owner)
- **Institute Overview**: PDF + Excel exports (like Arya)
- **Monthly Executive Summary**: KPIs with trends
- **Teaching Health Diagnostic**: Faculty signals
- **At-Risk Students**: Intervention list

### 13.2 Key Metrics

**Student Level:**
- Subject score (%)
- Topic mastery (%)
- Subtopic mastery (%)
- Rank (batch, course, institute)
- Percentile
- Trend (improving/stagnant/declining)

**Batch Level:**
- Average mastery
- At-risk count
- Top performer count
- Subject strengths/weaknesses
- Test coverage compliance

**Teacher Level:**
- Class performance vs institute avg
- Test cadence compliance
- Marks entry speed
- Section spread (multi-batch teachers)
- Signal band (Healthy/Watch/Needs Support)

**Institute Level:**
- Total students, teachers, batches
- Overall mastery average
- Course performance
- Grade-tier distribution
- YoY trends

### 13.3 Export Formats

- **PDF** (dompdf) — Professional reports
- **Excel** (maatwebsite) — Data exports
- **In-app charts** (Chart.js) — Interactive dashboards

---

## 14. TECH STACK

### 14.1 Core Stack (100% reuse from Arya)

| Layer | Technology |
|-------|-----------|
| Language | PHP 8.3+ |
| Framework | Laravel 13.5 |
| Database | MySQL 8.0 |
| Frontend | Blade + Tailwind CSS |
| JavaScript | Alpine.js (minimal) |
| Charts | Chart.js 4.4.0 |
| AI | Gemini 2.5 Flash (via google-gemini-php/laravel) |
| PDF | dompdf |
| Excel | maatwebsite/excel |
| Typography | Inter font |

### 14.2 Development Tools

- VS Code
- Laravel Herd (local PHP 8.4)
- MySQL 8 (local)
- Git + GitHub
- Composer
- Node.js + npm
- Claude Code (terminal)

### 14.3 Hosting & Deployment

- **Host:** Hostinger (same as Arya — 50 websites capacity available)
- **Domain:** `vidya.monoloopproductions.in` (subdomain) OR separate `vidyacoaching.in`
- **Database:** MySQL on Hostinger
- **SSL:** Let's Encrypt (free)
- **Deployment:** Git pull via SSH (same as Arya)

### 14.4 Why This Stack

- ✅ Proven by Arya (production live)
- ✅ Composer auto-manages dependencies
- ✅ Hostinger shared hosting compatible
- ✅ Claude Code expert in this stack
- ✅ Zero extra learning curve
- ✅ ₹0 extra infrastructure cost

---

## 15. DESIGN SYSTEM

### 15.1 Theme

**Dark mode** (same as Arya)
- Reduces eye strain for long coaching sessions
- Premium, professional feel
- Works in low-light environments

### 15.2 Color Palette

**Backgrounds (same as Arya):**
- `bg0`: `#08080a` (page background)
- `bg1`: `#14141b` (cards)
- `bg2`: `#1a1a24` (elevated surfaces)
- `bg3`: `#0f0f14` (chart backgrounds)

**Text (same as Arya):**
- `text-primary`: `#f5f1e8` (headlines)
- `text-secondary`: `#a8a39c` (body)
- `text-muted`: `#6a665f` (captions)

**Accent Colors:**

**Decision needed (before build):**
- Option A: **Reuse Arya's gold** (`#d4b67a`) — consistent brand family
- Option B: **New blue accent** (`#7a95c8`) — differentiate from Arya
- Option C: **Teal accent** (`#00BFA5`) — modern, competitive edge

**Recommendation:** Option B (blue) — signals "analytics/tech" better than gold's "scholarly" vibe. Coaching market is more competitive, less traditional.

**Secondary Colors (same as Arya):**
- Amber: `#d4a574` (warnings, watch signals)
- Green: `#7fb685` (success, strong performance)
- Coral: `#c87064` (errors, at-risk)
- Blue: `#7a95c8` (info, neutral accents)
- Purple: `#a392c8` (special metrics)

### 15.3 Typography

**Font:** Inter (same as Arya)
- Bold (700)
- Semi Bold (600)
- Medium (500)
- Regular (400)

### 15.4 Layout Patterns

- Sidebar navigation (240px fixed)
- Top bar (60px)
- Content area (fluid)
- Card-based layouts
- 12-column grid where needed
- Consistent spacing (8px baseline)

### 15.5 Component Library

Reuse Arya's components:
- Buttons (primary, secondary, outline, ghost)
- Input fields
- Dropdowns
- Cards (with hover states)
- Tables (sortable, paginated)
- Modals
- Toasts / Notifications
- Empty states
- Loading states
- Charts (bar, line, pie, heatmap)

---

## 16. NON-GOALS (What NOT to Build)

### 16.1 Never Build (Out of Scope Forever)

- ❌ **Fee management** (ERP scope)
- ❌ **Attendance tracking** (ERP scope)
- ❌ **Hostel management** (ERP scope)
- ❌ **Transportation management** (ERP scope)
- ❌ **Library management** (ERP scope)
- ❌ **Admissions module** (ERP scope)
- ❌ **Staff payroll** (ERP scope)
- ❌ **Messaging/chat platform** (use Slack/WhatsApp)
- ❌ **Live classes** (Zoom exists)
- ❌ **Video content library** (not our USP)
- ❌ **Question bank** (teacher IP)
- ❌ **Practice test generator** (not our USP)
- ❌ **Doubt solving** (not our USP)

### 16.2 Phase 2 Features (Not in MVP)

- ⏳ Parent portal (view child's performance)
- ⏳ Student portal (view own performance)
- ⏳ SMS notifications
- ⏳ Native mobile app (web responsive for now)
- ⏳ AI-powered question tagging (auto-detect topic)
- ⏳ Predictive rank analysis
- ⏳ JEE Advanced support (focus NEET + JEE Main first)
- ⏳ CAT/UPSC/GATE support (stay focused)
- ⏳ Integrated OMR scanning (use external scanner)

### 16.3 Discipline Rules

- ✅ If a feature doesn't improve **subtopic-level tracking** or **role-based insights**, reject it
- ✅ If a feature is already well-served by competitors, don't duplicate
- ✅ If a feature adds >1 week to timeline, defer to Phase 2
- ✅ If unsure, consult Arya pattern — did Arya build it?

---

## 17. SUCCESS METRICS & PILOT PLAN

### 17.1 MVP Definition (5-week target)

**Core features that MUST work for pilot:**
- [x] 6 role logins working
- [x] Admin setup flow complete
- [x] Curriculum tree loaded (NEET + JEE)
- [x] Typist can create test via Excel upload
- [x] Sub-admin can upload OMR responses
- [x] System matches responses → generates reports
- [x] Teacher sees assigned tests + students
- [x] Academic Head sees all reports
- [x] Owner sees strategic dashboard
- [x] PDF/Excel exports work
- [x] Gemini AI integration live
- [x] Activity log captures all actions
- [x] Multi-tenant ready (isolated institutes)

### 17.2 Pilot Target

**Phase 1 Pilot:** 1 local Jaipur coaching institute
- Duration: 1-2 months
- Students: 200-500
- Tests per week: 2-3
- Full workflow testing

**Phase 2 Pilot:** 2-3 more institutes (different sizes)
- Mix of NEET-focused + JEE-focused
- Different city (Kota for market validation)

### 17.3 Success Metrics

**Product KPIs:**
- Time from OMR Excel upload to reports: **<60 seconds**
- Test blueprint creation time: **<10 minutes**
- Student performance query: **<2 seconds**
- Uptime: **99.5%+**
- AI response time: **<3 seconds**

**Business KPIs (post-pilot):**
- Pilot conversion: 50%+ (2 of 3 institutes sign up paid)
- Monthly recurring revenue: ₹10K+ by Month 3
- Retention: 90%+ after pilot
- Net promoter score: 40+

**Usage KPIs:**
- Daily active users (Typist + Sub-Admin): 100%
- Weekly report views (Teachers): 80%+
- Monthly Owner dashboard views: 100%

### 17.4 Pilot Onboarding Plan

**Week 1:** Setup
- Institute registration
- Admin setup (courses, batches)
- Student import
- Staff accounts created
- Training session (2 hours)

**Week 2:** First test
- Typist creates first test
- Sub-admin uploads first OMR responses
- Reports generated
- Feedback collected

**Week 3-4:** Steady state
- Multiple tests per week
- All roles actively using
- Weekly feedback sessions
- Bug fixes + iterations

**Week 5-8:** Optimization
- Performance tuning
- Feature refinements
- Prep for 2nd institute

### 17.5 Kill Criteria

**Stop and reconsider if:**
- Pilot institute cannot complete test workflow after 2 training sessions
- Test response processing takes >5 minutes consistently
- Teachers don't use reports after 2 weeks
- Sub-admins struggle with OMR upload validation
- AI costs exceed ₹2,000/month for pilot (unlikely with free tier)

---

## 18. DEVELOPMENT WORKFLOW

### 18.1 7-Day Foundation Plan

Following Arya's proven pattern:

**Day 0:** ✅ Concept locked (this document)

**Day 1:**
- Finalize VIDYA_CONTEXT.md (this doc)
- Create vidya-design-tokens.md
- Start Figma design file

**Day 2:**
- Design core screens in Figma
- Login, 6 dashboards, key workflows

**Day 3:**
- Dev environment ready (reuse Arya setup)
- `composer create-project laravel/laravel vidya`
- Folder structure scaffolded

**Day 3-4:**
- Git + GitHub setup (new repo: `vidya`)
- .gitignore configured
- First commit

**Day 4-5:**
- Database schema design (vidya-database.sql)
- All migrations created
- `php artisan migrate` runs cleanly

**Day 5-6:**
- Models + relationships
- Authentication (6 roles)
- Seeders (subjects, curriculum, 1 test admin user)

**Day 6-7:**
- First feature: Login + Admin dashboard
- Tag v0.1-auth-working
- **Foundation complete** ✅

### 18.2 Feature Development (Weeks 2-5)

**Week 2:** Admin Panel
- Course + Batch management
- Staff management
- Student import
- Settings (3 tabs)
- Assignments

**Week 3:** Test Workflow
- Typist test creation (Excel upload)
- Test blueprint storage
- Sub-admin OMR upload
- Response matching engine
- Mastery calculation

**Week 4:** Dashboards & Reports
- Teacher dashboard + My Tests + My Students
- Academic Head reports (4 main reports)
- Owner strategic dashboard
- PDF/Excel exports

**Week 5:** AI + Polish
- Gemini integration
- AI insights across reports
- Activity log UI
- Notifications
- Bug fixes

**Week 6 (if needed):** Buffer + Deployment
- Production deployment
- Pilot institute onboarding
- First training session

### 18.3 Review Rhythm

**Daily:**
- Claude Code builds features
- Kavish reviews in browser
- Feedback collected

**Feature-level:**
- Each feature gets separate commit
- Meaningful commit messages
- Tested before merge

**Weekly:**
- Strategic review (this chat)
- Scope check
- Priorities adjustment

**Major milestones:**
- Git tags (v0.1, v0.2, v1.0-release)
- Push to GitHub
- Deploy to staging/production

### 18.4 Build Tools Division

| Tool | Role |
|------|------|
| **This chat (Strategy)** | Meta-review, decisions, prompt engineering |
| **Learning chat** | Lessons + context docs + guidance |
| **Claude Code (terminal)** | Feature implementation, coding |
| **Figma** | UI designs, visual source of truth |
| **GitHub** | Version control, collaboration |
| **Hostinger** | Deployment, production hosting |

### 18.5 Commit Hygiene

- ✅ Every feature = separate commit
- ✅ Meaningful messages (`feat:`, `fix:`, `refactor:`)
- ✅ Tag releases (`v0.1`, `v1.0-pre-deployment`)
- ✅ Never commit `.env` or secrets
- ✅ `.gitignore` configured properly

### 18.6 Deployment Rhythm

**Local → GitHub → Hostinger:**
```
Local development
  ↓ git push
GitHub (source of truth)
  ↓ SSH + git pull
Hostinger production
```

---

## APPENDIX A: GLOSSARY

| Term | Definition |
|------|-----------|
| **DPT** | Daily Practice Test (short, 30-50 questions) |
| **FLT** | Full Length Test (full exam pattern, 180Q for NEET) |
| **OMR** | Optical Mark Recognition (bubble sheets) |
| **Topic Code** | Unique identifier for subtopic (e.g., P-MEC-KIN-01) |
| **Mastery** | Student's proficiency in a subtopic (0-100%) |
| **Batch** | Group of students (like a classroom section) |
| **Course** | Exam-specific track (NEET, IIT-JEE) |
| **Institute** | The tenant (coaching center using Vidya) |
| **Blueprint** | Test metadata + answer key + assignments |

## APPENDIX B: CAST (Sample Pilot Institute)

**Institute:** ABC Coaching, Jaipur

**Staff (for testing):**
- Owner: Ashok Mehta (strategic view)
- Academic Head: Dr. Rekha Iyer
- Admin: Vikram Singh
- Sub-Admin: Priya Sharma (OMR uploads)
- Sub-Admin 2: Rajesh Kumar (batch manager)
- Teachers:
  - Dr. Amit Gupta (Physics)
  - Dr. Neha Verma (Chemistry)
  - Dr. Rajesh Patel (Biology - Zoology)
  - Dr. Sunita Rao (Biology - Botany)
  - Dr. Anil Joshi (Mathematics, for IIT-JEE)
- Typists:
  - Ravi Kumar
  - Anita Devi

**Default password (dev):** `password123` (change after first login)

## APPENDIX C: QUICK DECISIONS LOG

| Decision | Option Chosen | Date |
|----------|---------------|------|
| Product positioning | Complementary analytics layer | Apr 24, 2026 |
| Matching logic | Auto with validation gate | Apr 24, 2026 |
| Scope | FASTEST (5-6 weeks pilot) | Apr 24, 2026 |
| Role count | 6 (Sub-Admin = Test Operator) | Apr 24, 2026 |
| Teacher-test assignment | Dropdown (manual pick) | Apr 24, 2026 |
| Multi-teacher per test | Yes (one test, multiple teachers by subject) | Apr 24, 2026 |
| Test content storage | Lightweight (no questions, only answer key + codes) | Apr 24, 2026 |
| Test creation UI | Excel upload (like OMR response flow) | Apr 24, 2026 |
| AI provider | Gemini 2.5 Flash (reuse Arya) | Apr 24, 2026 |
| Tech stack | 100% reuse Arya | Apr 24, 2026 |
| Design accent color | TBD (blue recommended) | — |
| Domain | TBD (subdomain vs new) | — |

## APPENDIX D: COMPARISON WITH ARYA

| Aspect | Arya | Vidya |
|--------|------|-------|
| Market | Schools | Coaching institutes |
| TAM | ~1.5 lakh schools | ~4 lakh institutes |
| Revenue/customer | ₹20-50K/year | ₹50K-2 lakh/year |
| Roles | 4 | 6 |
| Org unit | Classroom | Batch |
| Academic structure | Grade + Section | Course + Batch |
| Subjects | 8+ CBSE | 3-4 exam-specific |
| Test types | Unit/Term/Annual | DPT/Weekly/Mock/FLT |
| Test creation | Teacher in Vidya | Typist via Excel |
| Marks entry | Teacher in UI | Sub-Admin via OMR Excel |
| Storage | Marks, simple | Responses, OMR, topic codes |
| USP | Subtopic mastery | Subtopic mastery |
| Tech stack | Laravel 13.5 | Laravel 13.5 (reuse) |
| Deployment | monoloopproductions.in | TBD (vidya.monoloopproductions.in?) |
| Timeline | 5 weeks (from zero) | 5 weeks (with Arya foundation) |

---

## 🏆 CONCEPT STATUS: LOCKED ✅

**This document is the single source of truth for Vidya v1.0.**

Changes to this context require:
1. Explicit approval in chat
2. Version bump (v1.1, v1.2, etc.)
3. Changelog entry
4. Review with founder (Kavish)

**No feature creep. No scope expansion. Ship MVP in 5-6 weeks.**

**Next step:** Database design (Lesson 2) → vidya-database.sql → migrations → foundation build.

---

*Document version: 1.0*
*Created: April 24, 2026*
*Founder: Kavish Sharma*
*Product: Vidya*
*Sister product: Arya (live at monoloopproductions.in)*
*Inspired by: Arya's PROJECT_CONTEXT.md (567 lines)*

🚩 *Jai Shri Ram*
