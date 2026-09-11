# Publish current usage and release guidance

Status: ready
Type: chore
Route: bounded-change — documentation and package metadata must be reconciled against the completed runtime behavior
Next: draft-spec

## Request
Implement the documentation, provider disclosure, and Packagist/release portion of `.works/oauth1-modernization/brief.md` after the code and tests stabilize.

## Goal
Give consumers and maintainers one accurate guide to installing, configuring, using, testing, and releasing the PHP 8.5 OAuth 1.0a package.

## Target
- `README.md` — secure end-to-end use, API/options, signatures/providers, troubleshooting, and contributor commands
- `composer.json` — final package metadata and links only
- Provider tests/metadata and a changelog or upgrade guide where final behavior requires them
- Pattern to follow: `../../scout-oauth1.md` — delivery seam 4 and existing four-stage walkthrough

## Acceptance criteria
- [ ] The README clearly distinguishes OAuth 1.0a from OAuth 2.0 and explains every protocol stage and credential involved.
- [ ] Installation and examples match PHP 8.5 and the final public API, without insecure direct serialization guidance.
- [ ] Supported signature methods, request parameter constraints, provider presets, exception behavior, and extension points are explicit.
- [ ] Local test, formatting, analysis, and deterministic integration commands are accurate and runnable.
- [ ] Versioning and tagged-release steps explain that Packagist publishes VCS versions automatically only after the package is registered and GitHub integration/webhook is enabled externally.
- [ ] Package metadata validates with Composer, and obsolete badges/claims are removed.

## Out of scope
- Changing protocol behavior stabilized in children 2–3.
- Configuring Packagist or GitHub account settings outside the repository.

## Assumptions
- Children 1–3 are complete, so their API and commands are the documentation source of truth — `../../roadmap.md`.
