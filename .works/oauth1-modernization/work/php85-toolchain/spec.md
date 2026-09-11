# Establish the PHP 8.5 development baseline

Status: ready
Scout: scout-oauth1.md

## Goal
Make the package installable and fully verifiable on PHP 8.5 with maintained dependencies and one clear local and CI quality workflow, while preserving its current OAuth behavior.

## In scope
- PHP and extension platform requirements, runtime and development dependency ranges, test runner and test syntax, formatting, static analysis, Composer scripts, and GitHub Actions.
- Removal of compatibility packages and hosted tooling that no longer serve the supported platform.

## Out of scope
- OAuth signing or flow behavior changes, integration fixtures, public usage/release documentation, and external Packagist configuration.
- Supporting PHP versions earlier than 8.5.

## Requirements
- R1 — Runtime platform: package installation requires PHP `^8.5`, declares any directly required PHP extensions, uses current compatible Guzzle/PSR packages, and no longer installs PHP compatibility shims.
- R2 — Test preservation: every existing behavioral assertion and failure case remains represented and executable after migration to Pest 5; converting syntax must not reduce the tested behavior or interaction expectations.
- R3 — Quality commands: Composer exposes separate commands for tests, check-only formatting with Pint, and static analysis with PHPStan, each returning a nonzero status for its own class of failure.
- R4 — Continuous integration: GitHub Actions resolves the intentionally unlocked dependency graph on PHP 8.5, validates Composer metadata, and runs formatting, analysis, and tests as distinct checks.
- R5 — Tool retirement: Travis, StyleCI, Scrutinizer, and uninstalled legacy PHP-CS-Fixer configuration are removed from executable/configuration and distribution metadata; public README badge cleanup remains assigned to the later documentation slice.
- R6 — Behavioral compatibility: factory wiring, OAuth 1.0a stage behavior, callback validation, request construction, URI resolution, credential parsing, and provider presets remain observably identical in this slice.
- R7 — Migration boundary: the package contains no persistent data to transform; the new PHP requirement applies only to the next major package version, while consumers needing older PHP can remain pinned to an earlier published major.

## Interfaces and data
The development interface exposes these stable Composer entry points:

```text
composer test
composer format
composer analyse
```

`composer format` checks formatting and does not modify files. Maintainers may invoke Pint directly to apply formatting.

The library remains intentionally unlocked: `composer.lock` is not committed, and CI resolves current versions within the declared ranges.

## Edge cases and failures
- Dependency resolution on PHP earlier than 8.5 → Composer rejects installation from the new major.
- Invalid `composer.json` metadata → CI fails before quality checks.
- Formatting drift → `composer format` and CI fail without rewriting files.
- Static-analysis findings → `composer analyse` and CI fail independently of tests.
- A migrated test that no longer discovers or executes → test count/coverage reconciliation against the 190 legacy cases blocks acceptance.
- A current Guzzle/PSR release changes a directly used API → the preserved unit suite must expose the incompatibility; production behavior may only be adapted with equivalent assertions.

## Must not change
- Callback token mismatch fails before access-token HTTP dispatch — evidence: `tests/Unit/OAuth1Test.php`, callback mismatch case.
- Temporary credentials require `oauth_callback_confirmed=true` — evidence: `tests/Unit/Credentials/CredentialsFactoryTest.php`.
- Protected-resource calls require token credentials and keep current base-URI resolution — evidence: `tests/Unit/OAuth1Test.php` and `tests/Unit/Config/UriConfigTest.php`.
- HMAC-SHA1 and PLAINTEXT outputs and all four provider endpoint presets remain pinned by their migrated unit tests.

## Acceptance criteria
- [ ] AC1 [R1, R7] — Composer metadata validates strictly, requires PHP `^8.5`, resolves current compatible runtime packages on PHP 8.5, and contains no `random_compat` or legacy PHP constraint.
- [ ] AC2 [R2, R6] — Pest runs the complete migrated suite successfully, with every former test case accounted for and no lost assertions or exception/interaction checks.
- [ ] AC3 [R3] — `composer test`, `composer format`, and `composer analyse` each pass on the accepted tree and each can fail for a representative defect in its own domain.
- [ ] AC4 [R4] — GitHub Actions defines PHP 8.5 jobs that perform strict Composer validation, unlocked dependency resolution, formatting, analysis, and tests.
- [ ] AC5 [R5] — legacy Travis, StyleCI, Scrutinizer, and PHP-CS-Fixer files are absent, and distribution attributes do not reference them; this slice does not edit README content.
- [ ] AC6 [R6] — production protocol files are unchanged unless a resolved dependency makes a minimal compatibility edit necessary; any such edit has a focused regression assertion proving equivalent behavior.
- [ ] AC7 [R7] — no lock file or data migration is introduced, and package metadata makes the PHP 8.5 major-version boundary explicit.
