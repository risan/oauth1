# Plan: PHP 8.5 toolchain baseline — v1

VERDICT: PLAN_READY_FOR_REVIEW

## Bindings
- Brief: `SPEC_REF=/home/risan/projects/code/oauth1/.works/oauth1-modernization/work/php85-toolchain/spec.md#sha256:ce0e67171d34bf3898155aa3da2677a6ee83d48acb0eebc3718ab66fbf8b2377`
- Repositories: `oauth1=root:/home/risan/projects/code/oauth1,base:0badc63c04361ecc8bce2f852c7569991721f37a,parent:a5a143baf4c25f8a881cccafe9eaa59a5c605eb9,head:0badc63c04361ecc8bce2f852c7569991721f37a`
- Governing instructions: `none`
- Repo context: `/home/risan/projects/code/oauth1/.works/oauth1-modernization/work/php85-toolchain/scout-oauth1.md`
- Design input: `none`

## Intake
- Objective / done: establish a behavior-preserving PHP 8.5 development baseline / current dependencies resolve and Pest, Pint, PHPStan, strict Composer validation, and GitHub Actions all exercise the package successfully.
- `REQ-1`: require PHP `^8.5`, current direct runtime packages, and required extensions; remove compatibility shims.
- `REQ-2`: preserve all 190 discovered legacy test cases and their success, failure, and interaction assertions under Pest 5.
- `REQ-3`: expose independent `test`, check-only `format`, and `analyse` Composer scripts.
- `REQ-4`: run strict metadata validation, unlocked resolution, formatting, analysis, and tests in GitHub Actions on PHP 8.5.
- `REQ-5`: remove obsolete Travis, StyleCI, Scrutinizer, and PHP-CS-Fixer configuration and stale distribution references.
- `REQ-6`: preserve observable production behavior; production source edits are permitted only when dependency compatibility demands them and focused tests prove equivalence.
- `REQ-7`: keep this library unlocked and make the PHP 8.5 boundary suitable for the next major version; no persistent data migration exists.
- `AC-1`: strict Composer validation and PHP 8.5 dependency resolution succeed without `random_compat`; requirements=`REQ-1,REQ-7`.
- `AC-2`: Pest discovers and passes all 190 migrated test cases with their assertion and mock expectations retained; requirements=`REQ-2,REQ-6`.
- `AC-3`: the three Composer quality scripts pass and have distinct failure modes; requirements=`REQ-3`.
- `AC-4`: GitHub Actions declares and runs all PHP 8.5 checks; requirements=`REQ-4`.
- `AC-5`: obsolete tool files and their `.gitattributes` references are absent; requirements=`REQ-5`.
- `AC-6`: `src/` remains byte-identical unless a focused compatibility fix is required and proven; requirements=`REQ-6`.
- `AC-7`: `composer.lock` remains ignored/untracked and the manifest exposes the PHP 8.5 boundary; requirements=`REQ-7`.
- `ASSUMPTION-1`: Pest 5.1, PHPUnit 13.3, Pint 1.32, PHPStan 2.2, Guzzle 8.2, Guzzle PSR-7 3.1, and PSR HTTP Message 2 are the current compatible release lines identified by the scout; reversibility=`reversible`; impact=`Composer may select later compatible patch/minor releases within these ranges`.
- `ASSUMPTION-2`: Docker is available to provide PHP 8.5 and Composer because neither executable is installed on the host; reversibility=`reversible`; impact=`local receipts use containers and do not require host PHP installation`.

## Outcome and boundaries
- Writer boundary: `composer.json`, `phpunit.xml`, `tests/Unit/**/*.php`, `tests/Pest.php`, `pint.json`, `phpstan.neon`, `.github/workflows/ci.yml`, `.gitignore`, `.gitattributes`; delete `phpunit.xml.dist`, `.travis.yml`, `.php_cs.dist`, `.styleci.yml`, `.scrutinizer.yml`; touch `src/**/*.php` only for formatter/static-analysis compatibility after tests pin behavior.
- Integrated-review boundary: one tree containing dependency metadata, the completely discoverable migrated suite, local quality configs, and CI; partial migration is never a releasable state.
- Rollback unit / stop point: revert the complete slice before publishing the PHP 8.5-only major; stop if any legacy behavior assertion cannot be represented or current dependencies require an observable API change.
- Compatibility / deploy order: this is an opt-in next-major package release with no runtime deployment or persistent data; older consumers remain on the prior major.

## Research digest
- Contract: `composer.json:24-47` — PHP 5.6, Guzzle 6/PSR-7 1, `random_compat`, and PHPUnit 5/6 are the complete current dependency surface.
- Convention: `tests/Unit/OAuth1Test.php:33-207` — PHPUnit interaction tests pin factory/orchestration behavior, including callback mismatch before dispatch.
- Contract: `tests/Unit/Request/NonceGeneratorTest.php:18-57` — legacy test annotations and removed assertions require explicit modern equivalents.
- Contract: `tests/Unit/Request/RequestFactoryTest.php:39-45` — partial mocks use removed builder APIs and must move to `onlyMethods()` while retaining expectations.
- Governing rule: `.gitignore:1-4` — reusable-library convention intentionally ignores `composer.lock`.
- Contract: `.travis.yml:1-40` and `phpunit.xml.dist:1-33` — the only current automation and runner config target obsolete PHP/PHPUnit behavior.
- Data flow: `Composer resolve -> Pest loads vendor/autoload.php -> 28 unit files exercise src collaborators -> process status`; no database, service, credential, or external network is involved after resolution.

## Test-stack preflight
- `TESTSTACK-UNIT`: layer=`unit`; framework/source=`PHPUnit 5/6 @ composer.json:41-43 and phpunit.xml.dist:1-33`; command=`CMD-TEST-OLD @ .travis.yml:36-37`; harness=`vendor autoload plus PHPUnit mocks; no services`; constraints=`190 @test methods across 28 files, sequential-safe, timeout 120 seconds`; tool_gaps=`host PHP and Composer missing`; baseline=`unavailable`; receipt_or_anchor=`command -v php/composer returned empty on 2026-09-11`; reason_impact=`baseline cannot run locally until the containerized PHP 8.5 dependency step exists; static inventory fixes the required case count`.

## Evidence
- `EVID-RESOLVE`: kind=`local`; required=`yes`; publication_gating=`yes`; command_or_receipt=`CMD-RESOLVE`; determinants=`composer.json plus PHP 8.5 container image`.
- `EVID-TEST`: kind=`local`; required=`yes`; publication_gating=`yes`; command_or_receipt=`CMD-TEST`; determinants=`composer.json, phpunit.xml, tests/Pest.php, tests/Unit/**/*.php, src/**/*.php`.
- `EVID-FORMAT`: kind=`local`; required=`yes`; publication_gating=`yes`; command_or_receipt=`CMD-FORMAT`; determinants=`pint.json, composer.json, src/**/*.php, tests/**/*.php`.
- `EVID-ANALYSE`: kind=`local`; required=`yes`; publication_gating=`yes`; command_or_receipt=`CMD-ANALYSE`; determinants=`phpstan.neon, composer.json, src/**/*.php`.
- `EVID-METADATA`: kind=`local`; required=`yes`; publication_gating=`yes`; command_or_receipt=`CMD-VALIDATE`; determinants=`composer.json`.
- `EVID-SCOPE`: kind=`local`; required=`yes`; publication_gating=`yes`; command_or_receipt=`CMD-SCOPE`; determinants=`git diff and test inventory`.

## Command specifications
- `CMD-DEFAULTS`: cwd=`/home/risan/projects/code/oauth1`; env_names=`none`; services=`Docker daemon for containerized PHP/Composer only`; resource_envelope=`2 CPUs, 2 GiB memory, no external service credentials`; timeout=`300 seconds`.
- `CMD-RESOLVE`: argv=`["docker","run","--rm","-v","/home/risan/projects/code/oauth1:/app","-w","/app","composer:2","composer","update","--prefer-dist","--no-interaction"]`; determinant_sources=`composer.json`; overrides=`network=required for first dependency download`; assertion=`dependency solving exits 0 and writes only ignored composer.lock/vendor artifacts`.
- `CMD-TEST-OLD`: argv=`["vendor/bin/phpunit","--no-coverage"]`; determinant_sources=`.travis.yml`; overrides=`runtime=historical PHP 5.6-7.2`; assertion=`historical command identified only; unavailable in current environment`.
- `CMD-TEST`: argv=`["docker","run","--rm","-v","/home/risan/projects/code/oauth1:/app","-w","/app","php:8.5-cli","php","vendor/bin/pest","--colors=never"]`; determinant_sources=`composer.json,phpunit.xml,tests/Pest.php,tests/Unit/**/*.php`; overrides=`none`; assertion=`exit 0 and summary reports exactly 190 tests before later slices add cases`.
- `CMD-FORMAT`: argv=`["docker","run","--rm","-v","/home/risan/projects/code/oauth1:/app","-w","/app","php:8.5-cli","php","vendor/bin/pint","--test"]`; determinant_sources=`composer.json,pint.json,src/**/*.php,tests/**/*.php`; overrides=`none`; assertion=`exit 0 and no formatting changes requested`.
- `CMD-ANALYSE`: argv=`["docker","run","--rm","-v","/home/risan/projects/code/oauth1:/app","-w","/app","php:8.5-cli","php","vendor/bin/phpstan","analyse","--no-progress","--memory-limit=1G"]`; determinant_sources=`composer.json,phpstan.neon,src/**/*.php`; overrides=`memory=1 GiB`; assertion=`exit 0 with no reported errors at configured level`.
- `CMD-VALIDATE`: argv=`["docker","run","--rm","-v","/home/risan/projects/code/oauth1:/app","-w","/app","composer:2","composer","validate","--strict"]`; determinant_sources=`composer.json`; overrides=`none`; assertion=`exit 0 after ignored generated lock is removed`.
- `CMD-SCOPE`: argv=`["bash","-lc","test $(find tests/Unit -type f -name '*Test.php' | wc -l) -eq 28 && test $(rg -n '#\\[Test\\]|function test_' tests/Unit --glob '*.php' | wc -l) -eq 190 && test ! -e composer.lock && git diff --check && git status --short"]`; determinant_sources=`tests/Unit/**/*.php,.gitignore,complete diff`; overrides=`none`; assertion=`28 test files and 190 modern test markers remain, composer.lock is absent, whitespace passes, and status lists only intended files plus planning artifacts`.

## Logical commits
- `COMMIT-TOOLCHAIN`: requirements=`REQ-1,REQ-3,REQ-4,REQ-5,REQ-7`; paths=`composer.json,phpunit.xml,tests/Pest.php,pint.json,phpstan.neon,.github/workflows/ci.yml,.gitignore,.gitattributes,.travis.yml,.php_cs.dist,.styleci.yml,.scrutinizer.yml,phpunit.xml.dist`; parent=`0badc63c04361ecc8bce2f852c7569991721f37a`; focused_green=`GREEN-1`; mode=`delayed`; reason=`manifest and runner become reviewable only after the legacy suite is fully migrated`.
- `COMMIT-TESTS`: requirements=`REQ-2,REQ-6`; paths=`tests/Unit/**/*.php and source-only compatibility/formatting edits`; parent=`COMMIT-TOOLCHAIN`; focused_green=`GREEN-2`; mode=`implementation-owned`; reason=`none`.

## Steps

### STEP-1 — Resolve the PHP 8.5 runtime and quality toolchain
- Done when: current dependencies resolve on PHP 8.5 and future quality commands/configurations exist without a committed lock file.
- Depends on: `none`
- Requirements / acceptance / evidence: `REQ-1,REQ-3,REQ-4,REQ-5,REQ-7` / `AC-1,AC-3,AC-4,AC-5,AC-7` / `EVID-RESOLVE,EVID-METADATA`
- RED `TEST-1`: requirements_acceptance=`REQ-1/AC-1`; path_name=`composer.json dependency resolution`; arrange_act_assert=`run CMD-RESOLVE on the unmodified manifest; assert it cannot satisfy the PHP 8.5/current-tool contract`; run=`CMD-RESOLVE`; missing_behavior=`no PHP 8.5 boundary or maintained toolchain`; expected_failure=`dependency resolution rejects legacy PHPUnit on PHP 8.5 or the resolved manifest audit shows obsolete constraints`; forbidden_failure=`Docker, registry/DNS, or volume-permission failure`; receipt=`RED-1`.
- GREEN: paths_symbols_signatures=`composer.json requires php:^8.5, ext-json, ext-openssl, guzzlehttp/guzzle:^8.2, guzzlehttp/psr7:^3.1, psr/http-message:^2.0; require-dev Pest:^5.1, Pint:^1.32, PHPStan:^2.2; allow Pest plugin; add scripts, phpunit.xml, tests/Pest.php, pint.json, phpstan.neon, and ci.yml; remove legacy configs and stale export-ignore rows`; values_order_errors=`CI uses PHP 8.5, composer update, then validate/format/analyse/test; PHPStan analyzes src at the highest no-baseline level achievable, minimum 5`; production_identity_change=`metadata only; src unchanged`; receipt=`GREEN-1`.
- Migration/rollback: forward=`replace dependency and automation contract as one next-major change`; existing_data=`none`; reversal=`restore legacy manifest/configs before release`; deploy_order=`none until complete slice passes`; stop_point=`solver requires observable runtime API redesign outside this slice`.
- VERIFY: `CMD-RESOLVE/EVID-RESOLVE and CMD-VALIDATE/EVID-METADATA exit 0; remove composer.lock afterward`.
- Affected determinants: `composer.json,phpunit.xml,tests/Pest.php,pint.json,phpstan.neon,.github/workflows/ci.yml,.gitignore,.gitattributes,legacy configs`.

### STEP-2 — Preserve the complete unit suite under Pest
- Done when: all 28 files and 190 cases are discovered and pass with supported PHPUnit 13/Pest APIs.
- Depends on: `STEP-1`
- Requirements / acceptance / evidence: `REQ-2,REQ-6` / `AC-2,AC-6` / `EVID-TEST,EVID-SCOPE`
- RED `TEST-2`: requirements_acceptance=`REQ-2,REQ-6/AC-2,AC-6`; path_name=`tests/Unit/**/*.php complete legacy suite`; arrange_act_assert=`after STEP-1 resolution, run CMD-TEST before conversion; assert failures name removed discovery, lifecycle, assertion, or mock APIs while bootstrap loads`; run=`CMD-TEST`; missing_behavior=`legacy PHPUnit 5/6 tests are not a fully discoverable PHPUnit 13 suite`; expected_failure=`partial discovery or named errors for @test, setUp, assertInternalType/assertRegExp, setMethods, or removed mock toggles`; forbidden_failure=`missing autoload/Pest, introduced syntax error, Docker failure`; receipt=`RED-2`.
- GREEN: paths_symbols_signatures=`import PHPUnit\\Framework\\Attributes\\Test; protected setUp(): void; public #[Test] methods; current assertion names; onlyMethods() partial mocks without removed toggles; safe property types only where initialization permits`; values_order_errors=`retain every arrange/expect/act/assert body and exact exception/identity/count/argument expectation; Pest executes PHPUnit-style tests and later tests may use native Pest syntax`; production_identity_change=`none unless a focused Guzzle 8 compatibility adaptation is proven equivalent`; receipt=`GREEN-2`.
- Migration/rollback: forward=`convert discovery and removed test APIs atomically`; existing_data=`none`; reversal=`revert tests and dependencies together`; deploy_order=`complete before protocol work`; stop_point=`any case cannot be represented without weakening it`.
- VERIFY: `CMD-TEST/EVID-TEST exits 0 with exactly 190 tests; CMD-SCOPE/EVID-SCOPE confirms inventory and intended paths`.
- Affected determinants: `tests/Unit/**/*.php; src only for required dependency compatibility`.

### STEP-3 — Close formatting, analysis, and integrated checks
- Done when: the complete tree passes formatting, analysis, tests, metadata, and scope checks and CI invokes the same contracts.
- Depends on: `STEP-2`
- Requirements / acceptance / evidence: `REQ-3,REQ-4,REQ-5,REQ-6` / `AC-3,AC-4,AC-5,AC-6` / `EVID-FORMAT,EVID-ANALYSE,EVID-TEST,EVID-METADATA,EVID-SCOPE`
- RED `TEST-3`: requirements_acceptance=`REQ-3,REQ-4/AC-3,AC-4`; path_name=`pint.json,phpstan.neon,.github/workflows/ci.yml and PHP tree`; arrange_act_assert=`run CMD-FORMAT and CMD-ANALYSE on the migrated but unformatted/unanalyzed tree; require concrete findings rather than empty-path success`; run=`CMD-FORMAT then CMD-ANALYSE`; missing_behavior=`legacy tree has never met current Pint/PHPStan contracts`; expected_failure=`Pint lists files and/or PHPStan reports source findings`; forbidden_failure=`missing executable, invalid config, zero analyzed files, Docker failure`; receipt=`RED-3`.
- GREEN: paths_symbols_signatures=`apply Pint once to src/tests; correct analysis with native types or precise PHPDoc where behavior remains identical; no baseline or ignored project errors; finalize CI commands matching Composer scripts`; values_order_errors=`formatter covers src/tests and CI runs on push/pull_request`; production_identity_change=`types/docs only where compatible with the next-major API and tests`; receipt=`GREEN-3`.
- Migration/rollback: forward=`land formatted/analyzed tree with configs`; existing_data=`none`; reversal=`revert the slice before release`; deploy_order=`quality closure precedes signing`; stop_point=`analysis requires an observable public API decision outside spec; use only a narrow documented third-party exclusion`.
- VERIFY: `CMD-FORMAT/EVID-FORMAT, CMD-ANALYSE/EVID-ANALYSE, CMD-TEST/EVID-TEST, CMD-VALIDATE/EVID-METADATA, CMD-SCOPE/EVID-SCOPE all satisfy assertions`.
- Affected determinants: `all writer-boundary paths`.

## Traceability result
- Orphan IDs: `REQ=none;AC=none;STEP=none;TEST=none;CMD=none;EVID=none`
- Contradictions: `none`

## Whole-change rollback and residual risks
- Rollback: before a tagged release, revert both logical commits together; after release, publish a correcting version and leave older-PHP consumers on the previous major rather than mutating tags.
- Residual `RISK-1`: owner=`implementation`; decision_or_evidence=`verify resolved Guzzle 8 Uri/Client APIs with migrated suite`; blocker=`no unless behavior differs`.
- Residual `RISK-2`: owner=`implementation`; decision_or_evidence=`record selected PHPStan level and any narrow third-party exclusion; no baseline`; blocker=`no`.

## Self-check
- `SELF_CHECK`: result=`PASS`; failed_ids=`none`; dispositions=`none`; residual_blockers=`none`
