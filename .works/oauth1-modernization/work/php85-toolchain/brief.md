# Establish the PHP 8.5 development baseline

Status: ready
Type: migration
Route: bounded-change — one coherent platform and tooling migration with repository-wide test syntax changes
Next: draft-spec

## Request
Implement the PHP runtime, dependency, test-runner, formatting, analysis, and CI portion of `.works/oauth1-modernization/brief.md`.

## Goal
Make the unchanged package behavior installable and verifiable on PHP 8.5 with maintained dependencies and one clear local/CI quality workflow.

## Target
- `composer.json` — PHP platform, runtime/dev dependencies, autoloading, and scripts
- `tests/`, `phpunit.xml.dist` and Pest configuration — migrate the legacy suite without weakening its assertions
- `.github/workflows/` and legacy root CI/style files — replace obsolete automation
- Pattern to follow: `../../scout-oauth1.md` — delivery seam 1 and existing unit-test ownership

## Acceptance criteria
- [ ] Composer requires PHP `^8.5` and current compatible Guzzle/PSR/runtime dependencies, with obsolete compatibility packages removed.
- [ ] All existing behavioral assertions run on a maintained PHPUnit/Pest stack; Pest is used if its PHP 8.5 support is stable and the conversion remains clear.
- [ ] Pint or a current standalone equivalent formats production and test code through a Composer script.
- [ ] Static analysis and test commands are documented as Composer scripts and pass.
- [ ] GitHub Actions runs install, formatting, analysis, and tests on PHP 8.5; Travis and obsolete hosted-style configs are removed.
- [ ] No protocol behavior changes in this slice.

## Out of scope
- RFC 5849 signing corrections (`rfc5849-signing`).
- Full OAuth HTTP integration coverage (`oauth10a-integration`).
- README and release guidance (`docs-and-release`).

## Assumptions
- This is the first roadmap slice and supplies the test/tool contract consumed by later slices — `../../roadmap.md`.
- A Composer lock file remains uncommitted for this reusable library unless an established repository convention or current Composer guidance gives a stronger reason.
