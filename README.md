# Vidya — Coaching Institute Analytics SaaS

> **Topic-level mastery tracking for NEET and IIT-JEE coaching institutes. Sister product to Arya (school analytics).**

---

## 🧭 Quick Navigation (for AI assistants)

**If this is a new chat**, read these files in order to get full context:

1. [README.md](./README.md) — You are here
2. [DECISIONS.md](./DECISIONS.md) — **Start here.** All locked decisions in one place
3. [VIDYA_CONTEXT.md](./VIDYA_CONTEXT.md) — Business concept, positioning, roles
4. [VIDYA_BLUEPRINT.md](./VIDYA_BLUEPRINT.md) — Technical blueprint (25 tables, 48 pages, 5-week build plan)
5. [DESIGN_TOKENS.md](./DESIGN_TOKENS.md) — Colors, typography, spacing (dark theme, blue accent)
6. [FIGMA_INVENTORY.md](./FIGMA_INVENTORY.md) — All 27 designed screens with IDs
7. [FIGMA_LINKS.md](./FIGMA_LINKS.md) — Quick-access Figma screen links

---

## 👤 Founder

**Kavish Sharma** (Jaipur, Rajasthan, India)
- Non-technical founder
- Hinglish (Hindi + English) communication style
- Building bootstrap, pragmatic, ₹0 extra budget where possible
- Works in Claude chat + Claude Code (terminal) + Figma

## 🏢 Previous Product

**Arya** — School analytics SaaS for CBSE schools
- Status: **LIVE** at https://monoloopproductions.in
- Repo: https://github.com/kundansharma903-code/arya
- Pilot: DPS Jaipur
- Stack: Laravel 13.5.0, PHP 8.3.30, MySQL 8.0, Blade + Tailwind + Alpine.js
- 45+ features, 5-week build, git-tagged v1.0-live-production

---

## 🎯 Vidya — What It Is

**One-line pitch:**
> "Keep your existing coaching ERP (ScholarSERP, Proctur, Addmen). Add Vidya for ₹X per student per year and get topic-level mastery analytics your current software doesn't have."

**Core USP:**
Subtopic-level academic mastery tracking. Where competitors show "Physics: 65%", Vidya shows:
```
Physics > Mechanics > Kinematics > Motion in 1D: 42%
Physics > Mechanics > Kinematics > Projectile Motion: 78%
```

**Target Market:**
Indian coaching institutes preparing students for NEET (medical) and IIT-JEE (engineering) entrance exams.

**Positioning:**
NOT a replacement for existing ERPs. A complementary analytics layer that consumes their Excel outputs and adds what they don't have.

---

## 👥 Six Roles in Vidya

| Role | Access | Primary Job |
|---|---|---|
| **Owner** | Full | Institute head, sees everything |
| **Academic Head** | Full read, limited write | Academic oversight |
| **Admin** | Full system admin | Setup and management |
| **Sub-Admin** | OMR upload + students | Day-to-day operations (Priya Sharma) |
| **Teacher** | View-only for own subjects | Reviews their subject's data |
| **Typist** | Test creation only | Digitizes question papers (Ravi Kumar) |

---

## 📚 Courses Supported

| Course | Subjects | Questions per Full Test |
|---|---|---|
| **NEET** | Physics + Chemistry + Zoology + Botany | 180 |
| **IIT-JEE** | Physics + Chemistry + Mathematics | 90 |

**Test paper structure:** ONE paper contains ALL subjects (not separate per-subject tests).

---

## 🔑 Key Architectural Decisions

✅ **Multi-subject in one paper** (180 Qs NEET / 90 Qs JEE)
✅ **Multi-teacher per test** (one teacher per subject)
✅ **Lightweight storage** — only answer key + topic codes, NOT question text (teacher IP protection)
✅ **Topic Code format:** `[SUBJECT]-[CHAPTER]-[TOPIC]-[SUBTOPIC]` (e.g., `P-MEC-KIN-01`)
✅ **Excel-based workflows** — test creation via Excel upload, OMR responses via Excel import
✅ **Dark theme + blue accent** (#7a95c8) to differentiate from Arya's gold

See [DECISIONS.md](./DECISIONS.md) for the complete list.

---

## 🏗️ Tech Stack (Planned — same as Arya)

- **Backend:** Laravel 13.5.0, PHP 8.3.30, MySQL 8.0
- **Frontend:** Blade + Tailwind CSS + Alpine.js + Chart.js 4.4.0
- **Font:** Inter
- **AI:** Gemini 2.5 Flash (for insights generation)
- **Hosting:** Same as Arya initially (monoloopproductions.in)
- **Domain:** `vidya.monoloopproductions.in` (subdomain) → later standalone

---

## 📊 Current Status

### ✅ Done
- Business concept locked (6 roles, multi-subject, multi-teacher)
- VIDYA_CONTEXT.md — 39 KB, 1,390 lines
- VIDYA_BLUEPRINT.md — 77 KB, 2,557 lines (25 SQL tables, ~900 subtopics, ~110 API routes)
- Design tokens locked (blue accent, dark theme)
- **27 Figma screens designed** (~56% coverage of 48 total)
  - Admin: 15 screens ✅
  - Typist: 8 screens ✅
  - Sub-Admin: 6 screens ✅ (Dashboard + OMR 4-step flow + Upload History)

### ⏳ Pending (Design Phase)
- Owner dashboard + reports
- Academic Head dashboard
- Teacher dashboard + Class Heatmap (key differentiator from Arya)
- Reports: Institute Overview, Batch Comparison, Teaching Health
- Modals: Add Student, Import Flow

### ⏳ Pending (Build Phase — after design)
- Excel format finalization (columns for test creation, OMR response, curriculum import)
- Laravel project setup + GitHub repo
- 25 migrations
- Seeders (subjects + NEET 480 subtopics + JEE 420 subtopics ≈ 900 total)
- Authentication for all 6 roles
- 5-week build schedule (see VIDYA_BLUEPRINT.md Section 25)

---

## 🎨 Figma

**File:** https://www.figma.com/design/lgUeyshjJH2kkFkQXxDZjH/Vidya-—-Designs
**File Key:** `lgUeyshjJH2kkFkQXxDZjH`
**Page:** `🖼 Screens` (0:1)

See [FIGMA_INVENTORY.md](./FIGMA_INVENTORY.md) for full screen-by-screen map with Figma node IDs.

---

## 📝 How to Resume Work in a New Chat

Copy-paste this prompt into any new Claude chat:

```
Hi, I'm Kavish Sharma. I'm building Vidya — coaching institute analytics SaaS.

All context is in my public GitHub repo:
https://github.com/kundansharma903-code/vidya

Please read these files first before answering:
1. README.md
2. DECISIONS.md
3. VIDYA_CONTEXT.md
4. VIDYA_BLUEPRINT.md
5. FIGMA_INVENTORY.md

Figma file: https://www.figma.com/design/lgUeyshjJH2kkFkQXxDZjH
(File Key: lgUeyshjJH2kkFkQXxDZjH)

My communication style: Hinglish (Hindi + English mixed), casual but technical.

Continue where we left off. Ask me what's next if unclear.
```

---

## 📁 Repo Structure (flat — all at root)

```
vidya/
├── README.md                    ← You are here
├── DECISIONS.md                 ← Locked decisions (start here)
├── VIDYA_CONTEXT.md             ← Business concept
├── VIDYA_BLUEPRINT.md           ← Technical blueprint
├── DESIGN_TOKENS.md             ← Colors, typography
├── FIGMA_INVENTORY.md           ← 27 designed screens map
├── FIGMA_LINKS.md               ← Quick-access Figma links
└── .gitignore
```

---

**Jai Shri Ram. 🚩**
