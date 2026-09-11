# Plan: PHP 8.5 toolchain baseline — v6

VERDICT: PLAN_READY_FOR_REVIEW

## Bindings
- Brief: `/home/risan/projects/code/oauth1/.works/oauth1-modernization/work/php85-toolchain/spec.md#sha256:b79755dec6d49bb7a6cf21bd0f2584615c31ed5bbad173c7a3c7973a8fd8469f`
- Repository: `/home/risan/projects/code/oauth1`; immutable code base/head=`0badc63c04361ecc8bce2f852c7569991721f37a`; parent=`a5a143baf4c25f8a881cccafe9eaa59a5c605eb9`
- Governing instructions: none
- Repo context: `.works/oauth1-modernization/work/php85-toolchain/scout-oauth1.md#sha256:d67f4110468a0179b926c75034c90ba07fb4725ab5f4eb738819b7d9fcd0ddcf`
- Pre-build control state: caller materializes `PREBUILD_STATE_V1` after this plan's approval/review artifacts are persisted and immediately before implementation; the approval record binds its SHA-256.

## Intake
- `REQ-1`: exact PHP/dependency contract: PHP `^8.5`, Guzzle `^8.2`, PSR-7 `^3.1`, PSR HTTP Message `^2.0`, needed extensions, Pest `^5.1`, Pint `^1.32`, PHPStan `^2.2`; no random_compat/direct PHPUnit.
- `REQ-2`: Pest executes 190 existing cases with assertion arguments, exceptions, mock interactions, and call constraints preserved.
- `REQ-3`: exact scripts `test=pest`, `format=pint --test`, `analyse=phpstan analyse --no-progress --memory-limit=1G`, with independent failures.
- `REQ-4`: distinct CI jobs `validate`, `test`, `format`, `analyse` use the same PHP image and exact scripts.
- `REQ-5`: remove `.travis.yml`, `.php_cs.dist`, `.styleci.yml`, `.scrutinizer.yml` and distribution references; README is reserved.
- `REQ-6`: `src/**`, README, and every pre-build control artifact equal their authenticated baselines; no extra out-of-scope path.
- `REQ-7`: composer.lock ignored, untracked, absent; next-major boundary; no data migration.
- `AC-1 [REQ-1,7]`: self-tested manifest contract, strict validation, update, and platform checks pass in PHP 8.5.
- `AC-2 [REQ-2,6]`: 190/focused tests pass and self-tested migration verifier accepts only enumerated transforms.
- `AC-3 [REQ-3]`: each exact script rejects its named defect then passes after byte restoration.
- `AC-4 [REQ-4]`: actionlint plus self-tested CI contract verifier prove job/command parity.
- `AC-5 [REQ-5,6]`: exact retired files/attributes absent; README/control artifacts unchanged.
- `AC-6 [REQ-6]`: immutable code base plus authenticated pre-build control inventory detect tracked/untracked content, mode, path additions, removals, and changes after commit.
- `AC-7 [REQ-7]`: lock absent/untracked/ignored.
- `ASSUMPTION-1`: pinned inputs `php:8.5.0-cli-bookworm`, `composer:2.8.11`, `rhysd/actionlint:1.7.7`; reversible, Docker replaces absent host tools.

## Outcome and boundaries
- Writer allowlist: `composer.json`, `phpunit.xml`, `tests/Pest.php`, `tests/Unit/**`, `tests/Tooling/**`, `tools/{verify-manifest,verify-test-migration,verify-ci-workflow,verify-scope}.php`, `docker/php85/Dockerfile`, `pint.json`, `phpstan.neon`, `.github/workflows/ci.yml`, `.gitignore`, `.gitattributes`, exact REQ-5 deletions.
- Protected: `src/**`, `README.md`, and authenticated `PREBUILD_STATE_V1` paths; compare code to `0badc63c...`, control artifacts to their recorded content hashes/modes.
- Reversible outputs allowed outside writer list: `vendor/**`, `composer.lock` during resolution only, `.phpunit.cache/**`, `.php-cs-fixer.cache`; absent from final acceptance except ignored caches/vendor.
- Integrated unit: all writer paths; partial state not releasable.
- Rollback/stop: revert whole slice; stop on source incompatibility or verifier semantics.
- Compatibility: next major, no deploy/data.

## Research digest
- `composer.json:34-47`: legacy manifest; `OAuth1Test.php:135-176` and `ProtocolParameterTest.php:383-392`: critical semantics.
- Four files at Credentials tests lines 15 and Signature trait tests lines 15/17 use removed helpers.
- `.gitignore:1-4`, `.gitattributes:1-8`: lock/distribution rules.
- Flow: harness → authenticated pre-build inventory → contract RED/GREEN → test migration → quality/CI → post-commit base/inventory proof.

## Test-stack preflight
- Unit/tooling; legacy PHPUnit; historical `vendor/bin/phpunit --no-coverage`; 28 files/190 cases; no services; host PHP/Composer unavailable; baseline unavailable, immutable Git base supplies code/test oracle.

## Evidence
- `EVID-1`: pinned harness/runtime.
- `EVID-2`: manifest self/base/green plus resolve/platform/validate.
- `EVID-3`: Pest/focused/test-verifier self/green.
- `EVID-4`: format/PHPStan controlled failures and green.
- `EVID-5`: actionlint/CI verifier self/green.
- `EVID-6`: authenticated pre-build inventory plus scope verifier self/post-commit green.
All are local, required, and publication-gating.

## Command specifications
- Defaults: repo cwd; Docker; 2 CPU/2 GiB; 300s.
- `CMD-HARNESS`: create only Dockerfile pre-RED; PHP 8.5.0 target installs git, unzip, libonig/libxml2 dev libs, mbstring/dom/xml/xmlwriter; copy Composer 2.8.11; verify PHP/Composer versions, commands, extensions.
- `CMD-PREBUILD`: after review+approval persistence and before any implementation file, root runs `php tools/control-state-builder.php` from a trusted temporary copy (or equivalent read-only caller utility) to produce canonical sorted records `path<TAB>mode<TAB>sha256` for every untracked `.codex/**` and `.works/**` path excluding the new state file itself; record base Git SHA, exact inventory count, and state digest in `PLAN_APPROVAL_V1`. The state file becomes protected; any missing approval digest blocks implementation. No broad directory exemption exists.
- `CMD-MANIFEST-SELF`: `verify-manifest --self-test`; mutate each exact constraint, shim/direct PHPUnit, script, allow-plugin entry; named failures; fixture passes.
- `CMD-MANIFEST-BASE/GREEN`: base via `git show 0badc63c...:composer.json` must fail named legacy diagnostics; working manifest must match REQ-1/3 and allow-plugins exactly `{pestphp/pest-plugin:true}`.
- `CMD-RESOLVE`: same image update noninteractive, check-platform-reqs, delete generated lock, strict validate.
- `CMD-TEST/FOCUSED`: exact Composer test, 190 pass; four removed-helper files pass.
- `CMD-PRESERVE-SELF`: mutations to assertion argument, exception, expects/method/with, discovery each rejected; allowed transforms pass.
- `CMD-PRESERVE`: baseline via git show; per-path allowlist only: attributes/public lifecycle, two assertion names, onlyMethods/toggle removal, two anonymous credential subclasses same args, two anonymous exact-trait classes; all other normalized tokens/arguments/interactions equal; 28/190 map.
- `CMD-PHPSTAN`: fixed paths `src` and `tests/Tooling/phpstan-fixtures`; no baseline/ignoreErrors/excludes. Run levels 10→0, select first passing and record higher failures. Temporary fixture `function expectsString(): string { return 1; }` must report return.type; restore exact bytes; selected level passes and analyzes nonzero files.
- `CMD-FORMAT`: writer paths only; temporary style defect fails exact Composer script; restore then pass.
- `CMD-CI-SELF/GREEN`: verifier mutations for YAML/jobs/image/build/update/validate/platform/scripts rejected; canonical passes; actionlint 1.7.7 passes. Four jobs each checkout/build/update; validate performs manifest/platform/unlock/strict, others exact scripts.
- `CMD-SCOPE-SELF`: fixtures prove unchanged pre-existing control artifacts pass; added, removed, content-changed, or mode-changed `.codex/.works` artifact fails; disallowed tracked/untracked paths, legacy residue, lock-ignore/track/presence mutations fail; allowed reversible outputs handled exactly.
- `CMD-SCOPE`: after implementation commit, `verify-scope 0badc63c... PREBUILD_STATE_V1 --expected-state-sha256 <approval-bound-digest>` authenticates state before use; verifies every control record unchanged, rejects new control paths; checks base-relative name/status/modes against writer allowlist, all untracked paths against authenticated controls/reversible outputs, protected code/README, exact deletions/attributes, lock absent/untracked/check-ignore, 28/190, and `git diff --check 0badc63c...`.

## Logical commits
- `COMMIT-1`: Dockerfile harness only; parent base; green harness.
- `COMMIT-2`: remaining writer paths; parent COMMIT-1; all evidence green.

## Steps

### STEP-1 — Harness, pre-build state, and manifest
- Done: harness valid, approval-bound control inventory valid, verifier self-test/base fail/green pass, dependencies/platform/metadata pass.
- Depends: none. Trace: `REQ-1,3,4,5,7 / AC-1,3,4,5,7 / EVID-1,2,6`.
- RED: after harness and authenticated state, add manifest verifier; self-test passes and base fails exact legacy diagnostics; forbid harness/network/plugin-prompt failures.
- GREEN: exact manifest/plugin/scripts/config/CI draft and legacy deletions; resolve sequence passes; no product identity change.
- Rollback: whole slice; no data/deploy; source adaptation stops.

### STEP-2 — Test migration
- Done: 190/focused pass; verifier self/green pass.
- Depends: STEP-1. Trace: `REQ-2,6 / AC-2,6 / EVID-3`.
- RED: exact Composer test on legacy files reports removed discovery/lifecycle/assertion/mock/abstract/trait APIs; no harness failure.
- GREEN: attributes/lifecycle/assertion/mock migrations, two anonymous credential subclasses, two trait harnesses; no source edit.
- Rollback: test migration; semantic hunk stops.

### STEP-3 — Quality, CI, and scope closure
- Done: controlled defects fail, restored commands pass, commit exists, authenticated post-commit scope passes.
- Depends: STEP-2. Trace: `REQ-3..7 / AC-3..7 / EVID-4,5,6`.
- RED: named PHPStan/format and all verifier self-test mutations fail with domain diagnostics.
- GREEN: restore exact bytes; record PHPStan level; finalize CI; commit; run every green command plus scope using approval-bound state digest.
- Rollback: whole slice; no data/deploy.

## Traceability result
- Orphans: none; contradictions: none.

## Whole-change rollback and residual risks
- Revert both commits before release; after release publish correction without changing tags.
- Byte-identical PHPStan level may be low; record and raise in later source modernization; nonblocking.

## Self-check
- `SELF_CHECK`: result=`PASS`; failed_ids=`none`; dispositions=`none`; residual_blockers=`none`
