# Workspaces and dashboards

> Each audience (leadership, HR, coach, student, family, internal) has its own dashboard and layout built from shared components. Unbuilt areas show honest empty states.

**Code:** `WorkspaceController`, `resources/views/layouts`, `resources/views/schools`, `resources/views/student`, `resources/views/guardian`, `resources/views/components/workspace-*`. Tests: `WorkspaceTest`.  
**Last checked:** 2026-11-24 against `6f02660`

## Confirmed

Authenticated school pages use a responsive, role-aware sidebar and a school workspace with quick links and a daily schedule panel. The visual direction follows the logo's navy, teal, and green colors on a pale blue background. Empty schedule and activity states do not imply that training or student workflows are implemented.

Status: prototype login and school-scoped staff workflows are migrated to Laravel; sports features remain planned.

## Open or proposed

Planned pages (students, training programs, assessments, reports): [PRODUCT_PLAN → Proposed page map](../PRODUCT_PLAN.md#proposed-page-map).
