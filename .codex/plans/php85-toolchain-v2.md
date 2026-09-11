# Plan: PHP 8.5 toolchain baseline — v2

VERDICT: PLAN_READY_FOR_REVIEW

## Bindings
- Brief: `SPEC_REF=/home/risan/projects/code/oauth1/.works/oauth1-modernization/work/php85-toolchain/spec.md#sha256:ce0e67171d34bf3898155aa3da2677a6ee83d48acb0eebc3718ab66fbf8b2377`
- Repositories: `oauth1=root:/home/risan/projects/code/oauth1,base:0badc63c04361ecc8bce2f852c7569991721f37a,parent:a5a143baf4c25f8a881cccafe9eaa59a5c605eb9,head:0badc63c04361ecc8bce2f852c7569991721f37a`
- Governing instructions: `none`
- Repo context: `/home/risan/projects/code/oauth1/.works/oauth1-modernization/work/php85-toolchain/scout-oauth1.md#sha256:d67f4110468a0179b926c75034c90ba07fb4725ab5f4eb738819b7d9fcd0ddcf`
- Design input: `none`

## Intake
- Objective / done: establish a behavior-preserving PHP 8.5 baseline / one version-pinned PHP 8.5 image resolves dependencies and all public Composer quality scripts pass while the legacy suite's behavior is mechanically preserved.
- `REQ-1`: require PHP `^8.5`, current direct dependencies, and actual extensions; remove compatibility shims.
- `REQ-2`: preserve all 190 legacy cases and every assertion, exception expectation, mock interaction, and argument constraint under Pest 5.
- `REQ-3`: expose independently failing `composer test`, `composer format`, and `composer analyse` interfaces.
- `REQ-4`: GitHub Actions uses the same PHP 8.5 image and exact Composer script interfaces after strict validation and unlocked resolution.
- `REQ-5`: remove Travis, StyleCI, Scrutinizer, legacy PHP-CS-Fixer config, and stale distribution references.
- `REQ-6`: keep `src/` byte-identical unless a named resolved-dependency failure proves a minimal compatibility edit is required and a focused regression test proves equivalence.
- `REQ-7`: keep the library unlocked and make this an opt-in next-major boundary with no data migration.
- `AC-1`: manifest validates and resolves on the verified PHP 8.5 runtime with platform requirements satisfied; requirements=`REQ-1,REQ-7`.
- `AC-2`: Pest passes exactly 190 cases and a bound-HEAD preservation tool proves only enumerated test migrations occurred; requirements=`REQ-2,REQ-6`.
- `AC-3`: all three Composer scripts demonstrate their domain failure and then pass; requirements=`REQ-3`.
- `AC-4`: CI builds the same toolchain image and invokes validation, platform check, and exact Composer scripts; requirements=`REQ-4`.
- `AC-5`: all obsolete files/references are absent; requirements=`REQ-5`.
- `AC-6`: the final `src/` tree hash equals bound HEAD unless the dependency-exception gate is exercised; requirements=`REQ-6`.
- `AC-7`: no lock file is tracked or left in the accepted tree; requirements=`REQ-7`.
- `ASSUMPTION-1`: exact compatible package ranges are PHP `^8.5`, Guzzle `^8.2`, Guzzle PSR-7 `^3.1`, PSR HTTP Message `^2.0`, Pest `^5.1`, Pint `^1.32`, and PHPStan `^2.2`; reversibility=`reversible`; impact=`patch/minor versions remain solver-selected`.
- `ASSUMPTION-2`: `php:8.5.0-cli-bookworm` and `composer:2.8.11` provide version-pinned build inputs; reversibility=`reversible`; impact=`Docker supplies the missing host runtime and Composer`.

## Outcome and boundaries
- Writer boundary: `composer.json`, `phpunit.xml`, `tests/Pest.php`, `tests/Unit/**/*.php`, `tools/verify-test-migration.php`, `docker/php85/Dockerfile`, `pint.json`, `phpstan.neon`, `.github/workflows/ci.yml`, `.gitignore`, `.gitattributes`; delete `phpunit.xml.dist`, `.travis.yml`, `.php_cs.dist`, `.styleci.yml`, `.scrutinizer.yml`.
- Protected boundary: `src/**/*.php` must retain the exact bound-HEAD aggregate hash. An edit is allowed only after a named Guzzle/PSR compatibility test fails, with a focused test and minimal adapter edit recorded before continuing.
- Integrated-review boundary: dependency metadata, image, fully migrated suite, preservation verifier, quality configs, and CI land together.
- Rollback unit / stop point: revert the whole slice before publishing; stop on any lost test semantic or observable runtime incompatibility.
- Compatibility / deploy order: next major only; no deployment/persistent data; older PHP consumers pin the previous major.

## Research digest
- Contract: `composer.json:34-47` — all legacy runtime/dev constraints and no scripts.
- Contract: `tests/Unit/OAuth1Test.php:135-176` — callback-token comparison and exchange interactions must survive.
- Contract: `tests/Unit/Request/ProtocolParameterTest.php:44-390` — dense signing interactions make body-preservation evidence necessary.
- Contract: `tests/Unit/Request/NonceGeneratorTest.php:18-57` — removed assertions require exactly two named substitutions.
- Contract: `.gitignore:1-4` — lock file stays ignored.
- Data flow: `pinned Docker build -> composer update -> platform check -> Pest/Pint/PHPStan scripts -> exit statuses`; only resolution/build need network.

## Test-stack preflight
- `TESTSTACK-UNIT`: layer=`unit`; framework/source=`PHPUnit 5/6 @ composer.json:41-43`; command=`CMD-TEST-LEGACY @ .travis.yml:36-37`; harness=`autoload and mocks, no services`; constraints=`28 files, 190 @test cases, 120 seconds`; tool_gaps=`host PHP/Composer absent`; baseline=`unavailable`; receipt_or_anchor=`2026-09-11 command lookup plus static 28/190 inventory`; reason_impact=`RED begins after the pinned image is built; preservation is anchored to HEAD rather than a claimed green legacy run`.

## Evidence
- `EVID-RUNTIME`: kind=`local`; required=`yes`; publication_gating=`yes`; command_or_receipt=`CMD-RUNTIME`; determinants=`docker/php85/Dockerfile`.
- `EVID-RESOLVE`: kind=`local`; required=`yes`; publication_gating=`yes`; command_or_receipt=`CMD-RESOLVE,CMD-PLATFORM,CMD-UNLOCK`; determinants=`Dockerfile,composer.json`.
- `EVID-TEST`: kind=`local`; required=`yes`; publication_gating=`yes`; command_or_receipt=`CMD-TEST,CMD-PRESERVE`; determinants=`tests plus bound HEAD`.
- `EVID-QUALITY`: kind=`local`; required=`yes`; publication_gating=`yes`; command_or_receipt=`CMD-FORMAT,CMD-ANALYSE`; determinants=`Composer scripts and configs`.
- `EVID-METADATA`: kind=`local`; required=`yes`; publication_gating=`yes`; command_or_receipt=`CMD-VALIDATE,CMD-SCOPE`; determinants=`manifest and complete diff`.

## Command specifications
- `CMD-DEFAULTS`: cwd=`/home/risan/projects/code/oauth1`; env_names=`none`; services=`Docker`; resource_envelope=`2 CPUs, 2 GiB`; timeout=`300 seconds`.
- `CMD-BUILD`: argv=`["docker","build","--tag","oauth1-php85:local","--file","docker/php85/Dockerfile","."]`; determinant_sources=`docker/php85/Dockerfile`; overrides=`network=required`; assertion=`image builds from php:8.5.0-cli-bookworm and copies Composer 2.8.11, with DOM/mbstring/XML/XMLWriter available`.
- `CMD-RUNTIME`: argv=`["docker","run","--rm","oauth1-php85:local","sh","-lc","php --version && composer --version && php -m"]`; determinant_sources=`CMD-BUILD image`; overrides=`none`; assertion=`PHP reports 8.5.x, Composer 2.8.11, and required extensions are listed`.
- `CMD-RESOLVE`: argv=`["docker","run","--rm","-v","/home/risan/projects/code/oauth1:/app","-w","/app","oauth1-php85:local","composer","update","--prefer-dist","--no-interaction"]`; determinant_sources=`composer.json plus image`; overrides=`network=required`; assertion=`exit 0`.
- `CMD-PLATFORM`: argv=`["docker","run","--rm","-v","/home/risan/projects/code/oauth1:/app","-w","/app","oauth1-php85:local","composer","check-platform-reqs"]`; determinant_sources=`composer.lock,vendor,image`; overrides=`none`; assertion=`all platform requirements succeed on the same image`.
- `CMD-UNLOCK`: argv=`["docker","run","--rm","-v","/home/risan/projects/code/oauth1:/app","-w","/app","oauth1-php85:local","php","-r","if (is_file('composer.lock')) { unlink('composer.lock'); }"]`; determinant_sources=`generated composer.lock`; overrides=`none`; assertion=`composer.lock absent afterward`.
- `CMD-TEST-LEGACY`: argv=`["vendor/bin/phpunit","--no-coverage"]`; determinant_sources=`.travis.yml`; overrides=`unavailable legacy runtime`; assertion=`historical anchor only`.
- `CMD-TEST`: argv=`["docker","run","--rm","-v","/home/risan/projects/code/oauth1:/app","-w","/app","oauth1-php85:local","composer","test","--","--colors=never"]`; determinant_sources=`composer.json,phpunit.xml,tests`; overrides=`none`; assertion=`exit 0 and exactly 190 tests before later slices`.
- `CMD-FORMAT`: argv=`["docker","run","--rm","-v","/home/risan/projects/code/oauth1:/app","-w","/app","oauth1-php85:local","composer","format"]`; determinant_sources=`composer.json,pint.json,tests`; overrides=`none`; assertion=`check-only exit 0; configured paths exclude src for this behavior-neutral slice`.
- `CMD-ANALYSE`: argv=`["docker","run","--rm","-v","/home/risan/projects/code/oauth1:/app","-w","/app","oauth1-php85:local","composer","analyse"]`; determinant_sources=`composer.json,phpstan.neon,src`; overrides=`none`; assertion=`exit 0 at the highest no-baseline level that leaves src byte-identical, recorded in config`.
- `CMD-VALIDATE`: argv=`["docker","run","--rm","-v","/home/risan/projects/code/oauth1:/app","-w","/app","oauth1-php85:local","composer","validate","--strict"]`; determinant_sources=`composer.json,image`; overrides=`run after CMD-UNLOCK`; assertion=`exit 0`.
- `CMD-PRESERVE`: argv=`["docker","run","--rm","-v","/home/risan/projects/code/oauth1:/app","-w","/app","oauth1-php85:local","php","tools/verify-test-migration.php","0badc63c04361ecc8bce2f852c7569991721f37a"]`; determinant_sources=`bound HEAD,tests/Unit,verification script`; overrides=`none`; assertion=`all 28 files map; normalized method bodies, assertion arguments, exception expectations, mock counts/method/with/willReturn chains are identical except the enumerated assertion and mock API substitutions`.
- `CMD-SCOPE`: argv=`["bash","-lc","test $(find tests/Unit -type f -name '*Test.php' | wc -l) -eq 28 && test $(rg -n '#\\[Test\\]|function test_' tests/Unit --glob '*.php' | wc -l) -eq 190 && test ! -e composer.lock && test $(git diff 0badc63c04361ecc8bce2f852c7569991721f37a -- src | wc -c) -eq 0 && git diff --check"]`; determinant_sources=`bound HEAD,tests,src,complete diff`; overrides=`none`; assertion=`test inventory, unlocked state, byte-identical src, and whitespace all pass`.

## Logical commits
- `COMMIT-BASELINE`: requirements=`REQ-1..REQ-7`; paths=`complete writer boundary`; parent=`0badc63c04361ecc8bce2f852c7569991721f37a`; focused_green=`GREEN-3`; mode=`implementation-owned`; reason=`partial runner/tool states are not reviewable`.

## Steps

### STEP-1 — Build and resolve one PHP 8.5 toolchain
- Done when: one version-pinned image proves PHP/Composer/extensions and resolves the current manifest without leaving a lock.
- Depends on: `none`
- Requirements / acceptance / evidence: `REQ-1,REQ-3,REQ-4,REQ-5,REQ-7` / `AC-1,AC-3,AC-4,AC-5,AC-7` / `EVID-RUNTIME,EVID-RESOLVE,EVID-METADATA`
- RED `TEST-1`: requirements_acceptance=`REQ-1/AC-1`; path_name=`composer.json under CMD-RESOLVE`; arrange_act_assert=`build/run the image, resolve the untouched manifest, and audit it against the PHP 8.5/current-package contract`; run=`CMD-BUILD,CMD-RUNTIME,CMD-RESOLVE`; missing_behavior=`manifest still selects legacy platform/packages`; expected_failure=`dependency failure on PHP 8.5 or manifest audit names legacy constraints`; forbidden_failure=`Docker/DNS/volume failure`; receipt=`RED-1`.
- GREEN: paths_symbols_signatures=`Dockerfile pins PHP 8.5.0 and Composer 2.8.11 with required test extensions; composer.json sets current runtime/dev ranges, Pest plugin, and exact scripts; add modern phpunit.xml, Pest bootstrap, Pint/PHPStan config, CI; remove legacy configs/references`; values_order_errors=`CI builds the same Dockerfile and calls the same validation/platform/scripts; format is check-only`; production_identity_change=`none`; receipt=`GREEN-1`.
- Migration/rollback: forward=`replace tooling contract`; existing_data=`none`; reversal=`revert slice`; deploy_order=`complete before protocol changes`; stop_point=`resolution demands observable source behavior change`.
- VERIFY: `CMD-RUNTIME/EVID-RUNTIME; CMD-RESOLVE,CMD-PLATFORM,CMD-UNLOCK/EVID-RESOLVE; CMD-VALIDATE/EVID-METADATA`.
- Affected determinants: `manifest,image,configs,CI,legacy files`.

### STEP-2 — Migrate tests with semantic preservation
- Done when: Pest passes 190 tests and normalized comparison to bound HEAD proves test bodies/interactions were preserved.
- Depends on: `STEP-1`
- Requirements / acceptance / evidence: `REQ-2,REQ-6` / `AC-2,AC-6` / `EVID-TEST`
- RED `TEST-2`: requirements_acceptance=`REQ-2,REQ-6/AC-2,AC-6`; path_name=`tests/Unit/**/*.php`; arrange_act_assert=`run exact composer test before conversion`; run=`CMD-TEST`; missing_behavior=`PHPUnit 5/6 discovery/lifecycle/assertion/mock APIs`; expected_failure=`partial discovery or named removed APIs`; forbidden_failure=`missing vendor/Pest, Docker failure`; receipt=`RED-2`.
- GREEN: paths_symbols_signatures=`add #[Test], supported setUp signatures, public test methods, assertIsString/assertMatchesRegularExpression, onlyMethods, and remove unsupported mock toggles; add verifier comparing token-normalized bodies and interaction surfaces to bound HEAD`; values_order_errors=`the verifier allowlist contains only these exact substitutions; every other body token/argument must match`; production_identity_change=`none`; receipt=`GREEN-2`.
- Migration/rollback: forward=`atomic mechanical conversion`; existing_data=`none`; reversal=`revert tests with toolchain`; deploy_order=`before signing work`; stop_point=`verifier identifies any semantic hunk`.
- VERIFY: `CMD-TEST and CMD-PRESERVE/EVID-TEST; named passing cases include OAuth1 callback mismatch, CredentialsFactory callback confirmation, UriConfig base resolution, HMAC-SHA1, PLAINTEXT, and four provider presets`.
- Affected determinants: `tests,verifier,bound HEAD`.

### STEP-3 — Prove public quality interfaces and CI parity
- Done when: representative defects make each exact Composer script fail, restoration makes all pass, and scope is clean.
- Depends on: `STEP-2`
- Requirements / acceptance / evidence: `REQ-3,REQ-4,REQ-5,REQ-6` / `AC-3,AC-4,AC-5,AC-6` / `EVID-QUALITY,EVID-TEST,EVID-METADATA`
- RED `TEST-3`: requirements_acceptance=`REQ-3,REQ-4/AC-3,AC-4`; path_name=`Composer scripts plus configs`; arrange_act_assert=`temporarily introduce one failing assertion, one formatting defect in a migrated test, and one typed PHPStan fixture error in sequence; run the corresponding Composer script and restore each file before continuing`; run=`CMD-TEST,CMD-FORMAT,CMD-ANALYSE`; missing_behavior=`unproved script routing/failure propagation`; expected_failure=`each script exits nonzero with its domain diagnostic`; forbidden_failure=`tool/config/image missing or unrelated domain failure`; receipt=`RED-3`.
- GREEN: paths_symbols_signatures=`restore fixtures; apply Pint only to writer-boundary test/tool files; configure PHPStan so src passes byte-identical without a baseline or ignored project error; CI invokes composer test/format/analyse exactly`; values_order_errors=`all commands independent and check-only in accepted tree`; production_identity_change=`none`; receipt=`GREEN-3`.
- Migration/rollback: forward=`land validated configs and CI`; existing_data=`none`; reversal=`revert slice`; deploy_order=`none`; stop_point=`PHPStan cannot provide useful analysis without source edits; document the maximal byte-identical level rather than edit src`.
- VERIFY: `CMD-TEST,CMD-PRESERVE/EVID-TEST; CMD-FORMAT,CMD-ANALYSE/EVID-QUALITY; CMD-VALIDATE,CMD-SCOPE/EVID-METADATA`.
- Affected determinants: `all writer paths and protected src hash`.

## Traceability result
- Orphan IDs: `REQ=none;AC=none;STEP=none;TEST=none;CMD=none;EVID=none`
- Contradictions: `none`

## Whole-change rollback and residual risks
- Rollback: revert the one slice commit before release; after release publish a correcting version and leave legacy consumers pinned to the prior major.
- Residual `RISK-1`: owner=`implementation`; decision_or_evidence=`if Guzzle 8 produces a named compatibility failure, stop and amend the spec/plan before touching src`; blocker=`conditional`.
- Residual `RISK-2`: owner=`implementation`; decision_or_evidence=`record the useful PHPStan level achievable on byte-identical legacy src; later source-modernization slices may raise it`; blocker=`no`.

## Self-check
- `SELF_CHECK`: result=`PASS`; failed_ids=`none`; dispositions=`none`; residual_blockers=`none`
