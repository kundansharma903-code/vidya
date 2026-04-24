# Vidya — Locked Decisions

> **This is the single source of truth. All decisions below are FINAL unless explicitly re-opened.**
> Last updated: 24 April 2026

---

## 1. Product Positioning

| Decision | Answer |
|---|---|
| Is Vidya an ERP replacement? | ❌ **NO** — complementary analytics layer |
| Does Vidya replace ScholarSERP/Proctur/Addmen? | ❌ NO — coexists alongside them |
| Pricing model | ₹X per student per year |
| Positioning line | "Keep your existing software. Add Vidya for ₹X." |
| Target users | Indian coaching institutes (NEET + IIT-JEE prep) |
| Key differentiator | Subtopic-level mastery (not just subject-level) |

---

## 2. Roles (LOCKED — 6 roles, not 5, not 7)

1. **Owner** — Institute head, full access
2. **Academic Head** — Academic oversight, full read, limited write
3. **Admin** — Full system admin, can create users
4. **Sub-Admin** — OMR upload + student data management (merged with "Test Operator")
5. **Teacher** — View-only for own subjects
6. **Typist** — Test creation via Excel upload

**Note:** "Sub-Admin" and "Test Operator" were merged into one role called "Sub-Admin" to reduce complexity.

---

## 3. Courses & Subjects

### NEET
- Subjects: **Physics + Chemistry + Zoology + Botany**
- Full test = **180 questions** (45 per subject)

### IIT-JEE
- Subjects: **Physics + Chemistry + Mathematics**
- Full test = **90 questions** (30 per subject)

---

## 4. Test Paper Structure

| Decision | Answer |
|---|---|
| One paper = one subject OR multiple? | ✅ **ONE paper = ALL subjects** |
| NEET full mock | Single paper with 180 Qs across 4 subjects |
| JEE full mock | Single paper with 90 Qs across 3 subjects |
| Unit tests | Can be single-subject (e.g., "Physics Unit Test 3") |
| Multi-teacher per test | ✅ **YES** — one teacher per subject |
| How subjects detected | Auto-detected from topic codes in Excel |

---

## 5. Topic Code Format (LOCKED)

**Format:** `[SUBJECT]-[CHAPTER]-[TOPIC]-[SUBTOPIC]`

**Examples:**
- `P-MEC-KIN-01` = Physics → Mechanics → Kinematics → Motion in 1D
- `C-ORG-HYD-09` = Chemistry → Organic → Hydrocarbons → Alkanes
- `Z-HUM-RPR-12` = Zoology → Human Physiology → Reproductive System → Menstrual Cycle
- `B-PLT-PHO-03` = Botany → Plant Physiology → Photosynthesis → Light Reactions
- `M-CAL-LIM-02` = Mathematics → Calculus → Limits → L'Hôpital's Rule

**Subject prefixes:**
- `P` = Physics
- `C` = Chemistry
- `Z` = Zoology
- `B` = Botany
- `M` = Mathematics

---

## 6. Typist Workflow (LOCKED)

**Source of truth:** Teacher writes paper offline with topic codes next to each question. Typist digitizes into Vidya via 4-step wizard.

### 4-step wizard:
1. **Metadata** — Test name, course, type, date, duration, marking scheme
2. **Excel Upload** — Typist uploads Excel with Q#, Topic Code, Answer, Marks. System validates against curriculum tree.
3. **Assign Teachers** — One dropdown per detected subject. Filters to only that subject's teachers.
4. **Batches + Review** — Select batches, review everything, upload answer key, publish.

### Validation UX (LOCKED)
- **Inline fix with fuzzy match suggestions** (not full re-upload)
- Example: `C-ORG-HYD-99` → suggests `C-ORG-HYD-09` with "Use this" button

---

## 7. Sub-Admin OMR Workflow (LOCKED)

**4-step wizard:**
1. **Select Test** — Choose from tests awaiting OMR upload
2. **Upload + Validate** — Upload Excel from OMR scanner, auto-validate against expected students
3. **Manual Mapping** — Resolve unmatched rolls via fuzzy-match suggestions or manual search
4. **Process** — System scores responses, calculates subtopic mastery, notifies teachers

### Storage Policy (LOCKED)
- Store: answer key + topic codes + student responses
- **DO NOT store:** question text (teacher IP protection)

---

## 8. Excel Formats (DEFERRED — decide before Laravel build)

### Test creation Excel — columns TBD but directionally:
```
Q# | Topic Code  | Answer | Marks
1  | P-MEC-KIN-01 | B      | 4
2  | P-MEC-KIN-02 | A      | 4
```

### OMR response Excel — columns TBD:
```
Roll No | Batch | Q1 | Q2 | Q3 | ... | Q180
NEET2410 | Morning-A | B | A | D | ... | C
```

### Curriculum import Excel — columns TBD:
```
Subject Code | Chapter Code | Topic Code | Subtopic Code | Name
```

**Status:** Format columns to be finalized before Laravel migration step.

---

## 9. Design System (LOCKED)

### Theme
- **Dark mode** primary
- **Accent:** Blue `#7a95c8` (differentiates from Arya's gold)

### Colors
| Token | Hex | Use |
|---|---|---|
| bg0 | `#08080a` | Page background |
| bg1 | `#14141b` | Cards |
| bg2 | `#1a1a24` | Elevated elements |
| bg3 | `#0f0f14` | Inputs |
| bg-readonly | `#0a0a0e` | Read-only fields |
| text primary | `#f5f1e8` | Main text |
| text secondary | `#a8a39c` | Body text |
| text muted | `#6a665f` | Helper text |

### Semantic
| Token | Hex | Use |
|---|---|---|
| success | `#7fb685` | Valid, complete, live |
| warning | `#d4a574` | Draft, needs attention |
| error | `#c87064` | Invalid, failed |
| info purple | `#a392c8` | Owner role |
| neutral teal | `#6ab0b2` | Admin role |

### Subject colors
| Subject | Hex |
|---|---|
| Physics (P) | `#7a95c8` (blue) |
| Chemistry (C) | `#7fb685` (green) |
| Zoology (Z) | `#c87064` (coral) |
| Botany (B) | `#a392c8` (purple) |
| Mathematics (M) | `#d4a574` (amber) |

### Role colors (avatars)
| Role | Hex |
|---|---|
| Owner | Purple `#a392c8` |
| Academic Head | Blue `#7a95c8` |
| Admin | Teal `#6ab0b2` |
| Sub-Admin | Brown `#c89a6a` |
| Teacher | Green `#7fb685` |
| Typist | Green `#7fb685` |

### Layout
- Sidebar: 240px
- Topbar: 60px
- Content width: 1200px
- Content padding: 32px
- Card radius: 8px
- Spacing grid: 8px base
- Viewport: 1440x900

### Typography
- **Font:** Inter (Bold, Semi Bold, Medium, Regular)
- Page title: 28px Bold, tracking -0.56px
- Section header: 15px Semi Bold
- Card title: 14px Semi Bold
- Body: 13px Regular
- Label (uppercase): 11px Medium, tracking 0.88px

---

## 10. Sample Personas (used in Figma designs)

| Role | Name | Avatar | Notes |
|---|---|---|---|
| Admin | Anisha Gupta | AG | Default admin for pilot |
| Sub-Admin | Priya Sharma | PS | Brown avatar |
| Teacher | Dr. Amit Gupta | AG | Physics teacher |
| Teacher | Dr. Neha Verma | NV | Chemistry teacher |
| Teacher | Dr. Rajesh Patel | RP | Zoology teacher |
| Teacher | Dr. Priya Sharma | PS | Botany teacher |
| Typist | Ravi Kumar | RK | Green avatar |
| Institute | ABC Coaching | — | Pilot institute |

---

## 11. Pending Decisions (to revisit)

- [ ] Excel format columns (test creation, OMR, curriculum) — decide before Laravel build
- [ ] Vidya domain: `vidya.monoloopproductions.in` (recommended) vs standalone
- [ ] Arya admin password change from `password123` (security critical)
- [ ] Subscription tier model (if any beyond flat per-student price)
- [ ] Data retention policy (how long to keep old test data)

---

## 12. Explicit Non-Decisions (deferred)

- Mobile app (web-only for MVP)
- Parent portal (not in pilot scope)
- SMS/WhatsApp notifications (email only for MVP)
- Payment gateway integration (manual billing for pilot)
- Multi-institute ("multi-tenancy") — pilot is single-institute only

---

**End of decisions document.** Changes to any decision above must be discussed and re-locked explicitly.
