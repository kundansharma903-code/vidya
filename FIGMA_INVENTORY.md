# Vidya Figma — Screen Inventory

**Figma File:** https://www.figma.com/design/lgUeyshjJH2kkFkQXxDZjH/Vidya-—-Designs
**File Key:** `lgUeyshjJH2kkFkQXxDZjH`
**Page:** `🖼 Screens` (0:1)
**Last updated:** 24 April 2026

---

## 📊 Progress Summary

| Section | Designed | Total | % |
|---|---|---|---|
| Admin | 15 | 15 | ✅ 100% |
| Typist | 8 | 8 | ✅ 100% |
| Sub-Admin | 6 | 6 | ✅ 100% |
| Teacher | 0 | ~8 | ⏳ 0% |
| Owner | 0 | ~5 | ⏳ 0% |
| Academic Head | 0 | ~4 | ⏳ 0% |
| Reports | 0 | ~6 | ⏳ 0% |
| **TOTAL** | **29** | **~52** | **~56%** |

> Note: "29" counts screens 16–18 as three because Step 2 has two states (empty upload + validation). The list below uses the file's actual frame naming.

---

## 🛠️ Admin Screens (15) — ✅ COMPLETE

| # | Screen Name | Frame ID | Viewport |
|---|---|---|---|
| 01 | Login (Desktop) | `1:2` | 1440×900 |
| 02 | Admin Dashboard | `5:2` | 1440×900 |
| 03 | Students Management | `9:2` | 1440×900 |
| 04 | Courses Management | `14:2` | 1440×900 |
| 05 | Batches Management | `14:213` | 1440×900 |
| 06 | Subjects Management | `14:535` | 1440×900 |
| 07 | Staff Management | `14:786` | 1440×900 |
| 08 | Curriculum Tree | `17:2` | 1440×900 |
| 09 | Settings | `17:301` | 1440×900 |
| 10 | Sub-Admin Creation | `17:453` | 1440×900 |
| 11 | Assignments Matrix | `17:625` | 1440×900 |
| 12 | Audit Log | `18:2` | 1440×900 |
| 13 | Admin Notifications | `18:340` | 1440×900 |
| 14 | Curriculum Import | `18:515` | 1440×900 |
| 15 | Admin Reports Landing | `18:714` | 1440×900 |

---

## ✍️ Typist Screens (8) — ✅ COMPLETE

| # | Screen Name | Frame ID | Viewport |
|---|---|---|---|
| 16 | Typist Dashboard | `23:2` | 1440×900 |
| 17 | Create Test Step 1 — Metadata | `23:186` | 1440×900 |
| 18a | Create Test Step 2 — Excel Upload (empty state) | `23:375` | 1440×900 |
| 18b | Create Test Step 2 — Validation (populated state) | `24:2` | 1440×1600 |
| 19 | Create Test Step 3 — Assign Teachers | `24:473` | 1440×980 |
| 20 | Create Test Step 4 — Batches + Review | `27:2` | 1440×1340 |
| 20m | Answer Key Upload Modal | `27:265` | 1440×900 |
| 21 | Typist My Tests | `27:315` | 1440×900 |

### Typist Sidebar Structure
```
Dashboard
TESTS
  Create Test
  My Tests
  Drafts
REFERENCE
  Curriculum Tree
SYSTEM
  Help
```

### User Chip
- Avatar: `RK` on green `#7fb685`
- Name: Ravi Kumar
- Role: Typist · ABC Coaching

---

## 📤 Sub-Admin Screens (6) — ✅ COMPLETE

| # | Screen Name | Frame ID | Viewport |
|---|---|---|---|
| 22 | Sub-Admin Dashboard | `29:2` | 1440×900 |
| 23 | OMR Upload Step 1 — Select Test | `30:2` | 1440×900 |
| 24 | OMR Upload Step 2 — Validation | `32:2` | 1440×1500 |
| 25 | OMR Upload Step 3 — Manual Mapping | `36:2` | 1440×1100 |
| 26 | OMR Upload Step 4 — Complete | `37:2` | 1440×1100 |
| 27 | Sub-Admin Upload History | `38:2` | 1440×900 |

### Sub-Admin Sidebar Structure
```
Dashboard
OMR
  Upload Responses
  Upload History
STUDENTS
  Students
  Batches
SYSTEM
  Help
```

### User Chip
- Avatar: `PS` on brown `#c89a6a`
- Name: Priya Sharma
- Role: Sub-Admin · ABC Coaching

---

## ⏳ Pending Screens (Next Sprint)

### Teacher (~8 screens)
- Teacher Dashboard (key differentiator)
- Teacher My Students (3-level nav: Batch → Student → Topic)
- Class Heatmap (KEY differentiator from Arya)
- Topic Drill-down
- Student Deep-dive
- My Tests (view-only)
- Weak Topics Dashboard
- Teacher Notifications

### Owner (~5 screens)
- Owner Dashboard
- Institute-wide KPIs
- Course Performance Comparison
- Teacher Performance Overview
- Financial Summary (basic)

### Academic Head (~4 screens)
- Academic Head Dashboard
- Curriculum Coverage Report
- Test Quality Analysis
- Teacher Effectiveness

### Reports (~6 screens)
- Institute Overview Report
- Batch Comparison Report
- Teaching Health Diagnostic
- Monthly Trends Report
- Subject Mastery Report
- Student At-Risk Report

### Modals (~4)
- Add Student Modal
- Import Students Flow
- Edit Test Modal
- Bulk Actions Modal

---

## 🎨 Shared Design Patterns

### Every screen has:
1. **Sidebar (240px)** — fixed left, contains navigation with role-specific menu
2. **Topbar (60px)** — breadcrumb left, bell + user chip right
3. **Content (1200px wide)** — 32px padding all around

### Breadcrumb pattern
```
[Role] / [Section] / [Current Page]
```
Last segment in Semi Bold white, rest in muted gray.

### Stepper pattern (wizards)
- Circle states: complete (✓ green), active (number blue), upcoming (number gray)
- Labels: "STEP N" (tiny muted) + "Label" (semi bold white)
- Connectors between steps: thin line, white 10% opacity

### Card patterns
- Default card: bg `#14141b`, 1px border `#f5f1e8` at 8% opacity, radius 8px
- Header cards with bottom border `#f5f1e8` at 6% opacity
- KPI cards: 4 across, each with label + big number + subtitle

### Button variants
- **Primary:** Blue `#7a95c8` fill, dark text, shadow
- **Secondary:** Dark bg with 1px subtle border, muted text
- **Ghost:** No bg, muted text
- **Danger:** Coral `#c87064` fill

### Status pills
- Shape: fully rounded (9999px)
- Content: 6px dot + label
- Green = Complete/Live/Valid
- Amber = Draft/Warning/Needs Review
- Coral = Error/Failed
- Blue = Scheduled/Info

---

## 🔗 Quick Access Links

| Want to see… | Click |
|---|---|
| Admin Dashboard | https://www.figma.com/design/lgUeyshjJH2kkFkQXxDZjH/?node-id=5-2 |
| Typist Dashboard | https://www.figma.com/design/lgUeyshjJH2kkFkQXxDZjH/?node-id=23-2 |
| Sub-Admin Dashboard | https://www.figma.com/design/lgUeyshjJH2kkFkQXxDZjH/?node-id=29-2 |
| OMR Upload Step 2 (best example of CORE UX) | https://www.figma.com/design/lgUeyshjJH2kkFkQXxDZjH/?node-id=32-2 |

---

## 📝 How to add new screens

When designing new screens in Figma:

1. **Name format:** `NN — Screen Name (Desktop)` where NN starts from 28
2. **Width:** Always 1440
3. **Height:** 900 default, grow as needed for long content
4. **Sidebar:** Match role-appropriate menu (Teacher, Owner, etc.)
5. **Topbar:** Breadcrumb + role-appropriate user chip
6. **After creation:** Add entry to this file with frame ID

---

**End of inventory.**
