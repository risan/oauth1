# Plan: PHP 8.5 toolchain baseline — v5

VERDICT: PLAN_READY_FOR_REVIEW

## Bindings
- Brief: `/home/risan/projects/code/oauth1/.works/oauth1-modernization/work/php85-toolchain/spec.md#sha256:b79755dec6d49bb7a6cf21bd0f2584615c31ed5bbad173c7a3c7973a8fd8469f`
- Repository: `/home/risan/projects/code/oauth1`; immutable base/head=`0badc63c04361ecc8bce2f852c7569991721f37a`; parent=`a5a143baf4c25f8a881cccafe9eaa59a5c605eb9`
- Governing instructions: none
- Repo context: `.works/oauth1-modernization/work/php85-toolchain/scout-oauth1.md#sha256:d67f4110468a0179b926c75034c90ba07fb4725ab5f4eb738819b7d9fcd0ddcf`

## Intake
- `REQ-1`: require PHP `^8.5`, Guzzle `^8.2`, PSR-7 `^3.1`, PSR HTTP Message `^2.0`, needed extensions, Pest `^5.1`, Pint `^1.32`, PHPStan `^2.2`; remove random_compat and direct PHPUnit.
- `REQ-2`: Pest executes all 190 existing cases with assertion arguments, exceptions, mocks, and call constraints preserved.
- `REQ-3`: exact scripts: `test=pest`, `format=pint --test`, `analyse=phpstan analyse --no-progress --memory-limit=1G`; each fails independently.
- `REQ-4`: CI has distinct `validate`, `test`, `format`, `analyse` jobs using the same PHP 8.5 image and exact scripts.
- `REQ-5`: remove `.travis.yml`, `.php_cs.dist`, `.styleci.yml`, `.scrutinizer.yml` and their distribution references; README belongs to later docs slice.
- `REQ-6`: `src/**` and `README.md` equal immutable base in content, mode, and path set; incompatibility stops for respec.
- `REQ-7`: composer.lock remains ignored, untracked, absent; change is next-major and has no data migration.
- `AC-1 [REQ-1,REQ-7]`: self-tested manifest contract, strict validation, update, and platform check pass in PHP 8.5.
- `AC-2 [REQ-2,REQ-6]`: 190 tests and four focused legacy-helper groups pass; self-tested migration verifier accepts only enumerated transforms.
- `AC-3 [REQ-3]`: each exact script rejects its named temporary defect, then passes after byte restoration.
- `AC-4 [REQ-4]`: actionlint and self-tested CI contract verifier prove job/command parity.
- `AC-5 [REQ-5,REQ-6]`: exact retired files/attributes absent and README protected.
- `AC-6 [REQ-6]`: immutable-base tracked diff plus untracked scan pass after implementation commit.
- `AC-7 [REQ-7]`: lock absence, untracked status, and git-ignore match pass.
- `ASSUMPTION-1`: pinned verification inputs are `php:8.5.0-cli-bookworm`, `composer:2.8.11`, `rhysd/actionlint:1.7.7`; reversible; Docker replaces absent host PHP tools.

## Outcome and boundaries
- Writer allowlist: `composer.json`, `phpunit.xml`, `tests/Pest.php`, `tests/Unit/**`, `tests/Tooling/**`, `tools/{verify-manifest,verify-test-migration,verify-ci-workflow,verify-scope}.php`, `docker/php85/Dockerfile`, `pint.json`, `phpstan.neon`, `.github/workflows/ci.yml`, `.gitignore`, `.gitattributes`, and exact legacy deletions from REQ-5.
- Protected: `src/**`, `README.md`; compare to immutable base even after commit.
- Integrated unit: all writer paths; partial state is not releasable.
- Rollback/stop: revert whole slice before release; stop on source incompatibility or verifier-detected semantics.
- Compatibility: next major; older consumers pin prior major; no deploy/data.

## Research digest
- `composer.json:34-47`: complete legacy manifest.
- `OAuth1Test.php:135-176`, `ProtocolParameterTest.php:383-392`: critical exception/interaction surfaces.
- `CredentialsTest.php:15`, `ServerIssuedCredentialsTest.php:15`: removed abstract mock helper.
- `CanBuildBaseStringTest.php:15`, `CanGetSigningKeyTest.php:17`: removed trait helper.
- `.gitignore:1-4`, `.gitattributes:1-8`: lock/distribution contracts.
- Flow: harness → manifest RED/GREEN → test migration RED/GREEN → independent script/verifier/CI failure proofs → post-commit immutable-base scope proof.

## Test-stack preflight
- `TESTSTACK-1`: unit/tooling; legacy PHPUnit from composer.json/phpunit.xml.dist; historical `vendor/bin/phpunit --no-coverage`; 28 files/190 cases; no services; host PHP/Composer unavailable; baseline unavailable, so immutable base and pinned harness provide the oracle.

## Evidence
- `EVID-1`: harness/build/runtime commands, required.
- `EVID-2`: manifest self-test/base-fail/green/update/platform/validate/unlock, required.
- `EVID-3`: Pest/focused/migration-verifier self-test+green, required.
- `EVID-4`: format/analyse controlled failures+green, required.
- `EVID-5`: actionlint/CI verifier self-test+green, required.
- `EVID-6`: scope verifier self-test+post-commit green, required.

## Command specifications
- Defaults: cwd repo; Docker only; 2 CPU/2 GiB; 300s.
- `CMD-HARNESS`: add only `docker/php85/Dockerfile` before RED; build `oauth1-php85:local`; target stage installs `git unzip libonig-dev libxml2-dev`, runs `docker-php-ext-install mbstring dom xml xmlwriter`, copies Composer from `composer:2.8.11`; verify PHP 8.5.x, Composer 2.8.11, git, unzip, required extensions. This is test harness, not product behavior.
- `CMD-MANIFEST-SELF`: same image `php tools/verify-manifest.php --self-test`; in-memory mutations independently change PHP/runtime/dev constraint, add random_compat/direct PHPUnit, change a script, broaden/remove allow-plugins; each named diagnostic fails, canonical fixture passes.
- `CMD-MANIFEST-BASE`: same image `php tools/verify-manifest.php --git-show 0badc63c...:composer.json`; must fail with all applicable legacy diagnostics.
- `CMD-MANIFEST`: same image `php tools/verify-manifest.php composer.json`; requires exact REQ-1/REQ-3 plus `config.allow-plugins` equal only `{pestphp/pest-plugin:true}`.
- `CMD-RESOLVE`: same image `composer update --prefer-dist --no-interaction`; then `composer check-platform-reqs`; then remove generated lock via PHP; then `composer validate --strict`.
- `CMD-TEST`: same image `composer test -- --colors=never`; exactly 190 pass.
- `CMD-FOCUSED`: same image `composer test --` followed by the two Credentials and two Signature test paths; all original cases pass.
- `CMD-PRESERVE-SELF`: `php tools/verify-test-migration.php --self-test`; in-memory mutations change assertion argument, delete/change exception, change expects/method/with, remove discovery; each named failure, listed allowed transforms pass.
- `CMD-PRESERVE`: `php tools/verify-test-migration.php 0badc63c...`; baseline via `git show`; exact per-path allowlist: test attribute/public lifecycle, two assertion names, onlyMethods/toggle removal, two anonymous credential subclasses with same args, two anonymous classes using exact traits; every other normalized token/argument/interaction identical; 28/190 map.
- `CMD-PHPSTAN-LEVEL`: with paths fixed to `src` and `tests/Tooling/phpstan-fixtures`, no baseline/ignoreErrors/excludes; run levels 10 down to 0 without editing src, select and record first passing numeric level; receipt lists every higher failure count and selected level. Add passing fixture, then temporary exact defect `function expectsString(): string { return 1; }`; `composer analyse` must report return.type/nonzero; restore byte-identically and pass. Zero analyzed files is failure.
- `CMD-FORMAT`: add then restore one trailing-space/style defect in writer test; exact `composer format` nonzero then pass; Pint paths are writer PHP files only, protected paths excluded.
- `CMD-CI-SELF`: `php tools/verify-ci-workflow.php --self-test`; malformed YAML, missing job, changed image/build/update/validate/platform/script command each rejected, canonical fixture accepted.
- `CMD-CI`: actionlint 1.7.7 plus verifier on `.github/workflows/ci.yml`; jobs `validate|test|format|analyse`; each checkout, builds `oauth1-php85:local` from exact Dockerfile, runs update; validate runs manifest/Composer/platform/unlock/strict validation, others run exact `composer test|format|analyse`; no combined substitute.
- `CMD-SCOPE-SELF`: `php tools/verify-scope.php --self-test`; fixtures independently add disallowed tracked/untracked path, wrong mode, retain each legacy file/reference, remove composer.lock ignore, add/track/leave lock; all rejected.
- `CMD-SCOPE`: after implementation commit, `php tools/verify-scope.php 0badc63c...`; compare `git diff --name-status --find-renames` and modes to exact allowlist, include `git status --porcelain --untracked-files=all`, require protected immutable-base equality, four exact deletions and `.gitattributes` cleanup, composer.lock absent/untracked and `git check-ignore composer.lock` success, 28 files/190 markers, `git diff --check 0badc63c...` success.

## Logical commits
- `COMMIT-1`: test harness Dockerfile only; parent base; green=`CMD-HARNESS`; implementation-owned.
- `COMMIT-2`: all remaining writer paths; parent COMMIT-1; green=`all evidence`; implementation-owned.

## Steps

### STEP-1 — Establish harness and manifest contract
- Done when: harness works; manifest self-test passes; base manifest fails exact diagnostics; green manifest resolves and validates.
- Depends: none. Requirements/acceptance/evidence: `REQ-1,3,4,5,7 / AC-1,3,4,5,7 / EVID-1,2`.
- RED `TEST-1`: after `CMD-HARNESS`, add manifest verifier; run self-test then base; expected named legacy contract failures; forbid harness/network/plugin-prompt failures.
- GREEN: exact requirements/scripts/plugin allowlist/configs/legacy deletions/CI draft; run manifest and resolve sequence; no product identity change.
- Rollback: revert commits; no data/deploy; stop on source adaptation.

### STEP-2 — Preserve tests under Pest
- Done when: 190/focused pass and self-tested verifier proves only named transforms.
- Depends: STEP-1. Requirements/acceptance/evidence: `REQ-2,6 / AC-2,6 / EVID-3`.
- RED `TEST-2`: exact Composer test on legacy files fails removed discovery/lifecycle/assertion/mock/abstract/trait APIs; harness failures forbidden.
- GREEN: attributes, supported lifecycle/mocks/assertions, two anonymous abstract subclasses and two trait harnesses; verifier self-test then base comparison; src unchanged.
- Rollback: revert test migration; stop on semantic hunk.

### STEP-3 — Prove quality, CI, and post-commit scope
- Done when: format/analysis/CI/scope controlled mutations fail, restored tree passes, commit exists, immutable-base scope passes.
- Depends: STEP-2. Requirements/acceptance/evidence: `REQ-3..7 / AC-3..7 / EVID-4,5,6`.
- RED `TEST-3`: run named PHPStan/format defects and all verifier self-tests; require domain diagnostics/nonzero; no missing-tool/config failures.
- GREEN: restore exact bytes, record selected PHPStan level, finalize four jobs, commit, then run every green command including base-bound scope.
- Rollback: revert whole slice; no data/deploy.

## Traceability result
- Orphans: none. Contradictions: none.

## Whole-change rollback and residual risks
- Revert both commits before release; after release publish correction without mutating old tags.
- Residual: maximum source-byte-identical PHPStan level may be low; record it and raise during later source modernization; nonblocking.

## Self-check
- `SELF_CHECK`: result=`PASS`; failed_ids=`none`; dispositions=`none`; residual_blockers=`none`
