# Vidya — Design Tokens

> Single reference for all design system values. Use these exact values in Figma, Laravel Blade, Tailwind configs, and any other surface.

---

## 🎨 Colors

### Backgrounds
```css
--bg-0:           #08080a;  /* Page background */
--bg-1:           #14141b;  /* Cards */
--bg-2:           #1a1a24;  /* Elevated elements, table headers */
--bg-3:           #0f0f14;  /* Input fields */
--bg-readonly:    #0a0a0e;  /* Read-only / disabled backgrounds */
```

### Text
```css
--text-primary:   #f5f1e8;  /* Headings, labels, big numbers */
--text-secondary: #a8a39c;  /* Body text, descriptions */
--text-muted:     #6a665f;  /* Helper text, placeholders, captions */
```

### Accent
```css
--accent:         #7a95c8;  /* Vidya blue — primary CTA, active states */
```

### Semantic
```css
--success:        #7fb685;  /* Green: valid, complete, live */
--warning:        #d4a574;  /* Amber: draft, needs attention */
--error:          #c87064;  /* Coral: invalid, failed */
--info:           #a392c8;  /* Purple: info, owner role */
--neutral:        #6ab0b2;  /* Teal: admin role */
```

### Subject colors
```css
--subject-physics:    #7a95c8;  /* Blue */
--subject-chemistry:  #7fb685;  /* Green */
--subject-zoology:    #c87064;  /* Coral */
--subject-botany:     #a392c8;  /* Purple */
--subject-maths:      #d4a574;  /* Amber */
```

### Role colors (for avatars)
```css
--role-owner:         #a392c8;  /* Purple */
--role-academic-head: #7a95c8;  /* Blue */
--role-admin:         #6ab0b2;  /* Teal */
--role-sub-admin:     #c89a6a;  /* Brown */
--role-teacher:       #7fb685;  /* Green */
--role-typist:        #7fb685;  /* Green */
```

### Borders & dividers (use with rgba)
```css
--border-subtle:   rgba(245, 241, 232, 0.06);  /* Dividers, card borders */
--border-default:  rgba(245, 241, 232, 0.08);  /* Default border */
--border-input:    rgba(245, 241, 232, 0.1);   /* Input borders */
--border-strong:   rgba(245, 241, 232, 0.15);  /* Emphasized borders */
```

### Color tints (semantic)
For backgrounds like `bg-success-subtle`, use the semantic color at 12% opacity:
```css
/* Example */
background: rgba(127, 182, 133, 0.12);  /* success at 12% */
border:     rgba(127, 182, 133, 0.3);   /* success at 30% */
color:      #7fb685;                    /* success solid */
```

Common opacity stops:
- `0.08` = very subtle background
- `0.12` = status pill backgrounds
- `0.15` = icon container backgrounds
- `0.18` = subject tile backgrounds (Hawaii letter tiles)
- `0.20` = stroke/border accents
- `0.30` = card borders for tinted cards
- `0.40` = emphasized borders

---

## 🔤 Typography

**Font family:** Inter (loaded weights: Regular, Medium, Semi Bold, Bold)

### Type scale

| Role | Size | Weight | Letter-spacing | Color |
|---|---|---|---|---|
| Page title | 28px | Bold | -0.56px | `#f5f1e8` |
| Section header | 18px | Semi Bold | — | `#f5f1e8` |
| Big number (KPI) | 28px | Bold | -0.56px | `#f5f1e8` |
| Card title | 14-16px | Semi Bold | — | `#f5f1e8` |
| Label (uppercase small) | 10-11px | Medium | 0.88-1.2px | `#a8a39c` |
| Body | 13px | Regular | — | `#a8a39c` |
| Helper / caption | 11px | Regular | — | `#6a665f` |
| Code / monospace | 11-12px | Medium | — | `#a8a39c` |

### Button text
- Primary button: 13-14px **Semi Bold**, dark color `#14141b`
- Secondary button: 13px **Medium**, muted `#a8a39c`

---

## 📐 Spacing

**Base unit:** 4px grid (occasionally 2px for fine details)

### Common values
```
4px   8px   12px   14px   16px   20px   24px   32px   40px
```

### Layout
| Element | Value |
|---|---|
| Sidebar width | 240px |
| Topbar height | 60px |
| Content width | 1200px |
| Page padding (L/R/T/B) | 32px |
| Section gap | 24px |
| Card padding | 20px default, 24px for emphasized |
| Input padding | 10-14px horizontal, 10-11px vertical |

---

## 🔘 Radii

```
--radius-sm:  4px;    /* Code pills, small badges */
--radius-md:  6px;    /* Buttons, inputs, most UI */
--radius-lg:  8px;    /* Cards */
--radius-xl:  10-12px;/* Emphasized cards (CTAs) */
--radius-pill: 9999px;/* Status pills, avatars */
```

---

## 🌓 Shadows

```css
/* Primary button glow */
box-shadow: 0px 4px 12px 0px rgba(122, 149, 200, 0.3);

/* Secondary button subtle */
box-shadow: 0px 2px 6px 0px rgba(122, 149, 200, 0.2);
```

Dark UI — we use shadows sparingly (only on primary CTAs).

---

## 🧩 Component Patterns

### Card (default)
```
background: #14141b
border: 1px solid rgba(245,241,232,0.08)
radius: 8px
padding: 20px
```

### Card (emphasized / CTA)
```
background: rgba(122,149,200,0.1)
border: 1px solid rgba(122,149,200,0.3)
radius: 10px
padding: 28-32px
```

### KPI Card
```
background: #14141b
border: 1px solid rgba(245,241,232,0.08)
radius: 8px
padding: 20px
gap: 8px between label / number / subtitle

structure:
  [label — uppercase, 11px, muted]
  [BIG NUMBER — 28px bold, primary or semantic color]
  [subtitle — 11px, muted]
```

### Status pill
```
background: rgba([semantic], 0.12)
border-radius: 9999px
padding: 4px 10px
gap: 6px

structure:
  [colored dot 6px] [label 11px medium, semantic color]
```

### Code pill (for test codes like NEET-MOCK-007)
```
background: #0f0f14
border-radius: 4px
padding: 4px 8px
text: 11px Medium, color #a8a39c (or semantic for error)
font: monospace feel
```

### Subject letter tile (P, C, Z, B, M)
```
background: rgba([subject], 0.18)
border: 1px solid rgba([subject], 0.4)
radius: 4px
size: 26x26px (or 20x20 small, 40x40 large)
text: 12px Bold, [subject] color
```

### Input field
```
background: #0f0f14
border: 1px solid rgba(245,241,232,0.1)
radius: 6px
padding: 10-11px vertical, 14px horizontal
text: 13px Regular, #a8a39c
placeholder: #6a665f
```

### Primary button
```
background: #7a95c8
radius: 6-8px
padding: 10-14px vertical, 14-22px horizontal
shadow: 0 4px 12px rgba(122,149,200,0.3)
text: 13-14px Semi Bold, color #14141b
gap: 8px (for icon + text)
```

### Secondary button
```
background: #14141b
border: 1px solid rgba(245,241,232,0.1)
radius: 6px
padding: 10px vertical, 14-16px horizontal
text: 13px Medium, color #a8a39c
```

### Stepper (wizard)
```
Container:
  background: #14141b
  border: 1px solid rgba(245,241,232,0.08)
  radius: 8px
  padding: 20px 24px

Each step:
  circle 32x32px, radius 16px
    complete: #7fb685 fill with ✓
    active:   #7a95c8 fill with number
    upcoming: #1a1a24 with 1px border, muted number

Labels beside circle:
  "STEP N" - 9px medium, muted, letter-spacing 1.08px
  "Label"  - 13px semi bold (active) / medium (else)

Connector between steps:
  1px line, rgba(245,241,232,0.1), flex-grow
```

---

## 🖼️ Icon conventions

- Emoji used liberally throughout UI (🔔 📄 📤 ↑ ↓ → ← ✓ ⚠ ✕ 💡 🔍 📊 📝 🎓 📅 👥 🌳 📌)
- Font size typically 11-14px for inline icons, 16-20px for emphasis, 36-48px for hero
- Bold arrow chars (`→` `←`) use Inter Bold, 10-13px

---

## 📐 Common dimensions

| Element | Size |
|---|---|
| Avatar small (user chip) | 28×28px |
| Avatar medium (student list) | 40×40px |
| Avatar large (profile) | 80×80px |
| Button height | 36-42px |
| Input height | 40-42px |
| KPI card height | ~100-120px |
| Table row height | 44-64px (data) / 40px (header) |
| Stepper circle | 32×32px |

---

**End of design tokens.**

Use these values exactly. If a new value is needed (e.g., new color for a new feature), document it here first before using it.
