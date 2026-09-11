# Plan: PHP 8.5 toolchain baseline — v3

VERDICT: PLAN_READY_FOR_REVIEW

## Bindings
- Brief: `SPEC_REF=/home/risan/projects/code/oauth1/.works/oauth1-modernization/work/php85-toolchain/spec.md#sha256:b79755dec6d49bb7a6cf21bd0f2584615c31ed5bbad173c7a3c7973a8fd8469f`
- Repositories: `oauth1=root:/home/risan/projects/code/oauth1,base:0badc63c04361ecc8bce2f852c7569991721f37a,parent:a5a143baf4c25f8a881cccafe9eaa59a5c605eb9,head:0badc63c04361ecc8bce2f852c7569991721f37a`
- Governing instructions: `none`
- Repo context: `/home/risan/projects/code/oauth1/.works/oauth1-modernization/work/php85-toolchain/scout-oauth1.md#sha256:d67f4110468a0179b926c75034c90ba07fb4725ab5f4eb738819b7d9fcd0ddcf`
- Design input: `none`

## Intake
- Objective / done: establish a behavior-preserving PHP 8.5 baseline / one pinned PHP 8.5 image resolves dependencies and the exact Composer quality interfaces pass while source bytes and legacy test semantics are preserved.
- `REQ-1`: require PHP `^8.5`, current direct dependencies, required extensions, and no compatibility shims.
- `REQ-2`: preserve all 190 legacy cases plus assertion arguments, exception expectations, mock interactions, and call constraints under Pest 5.
- `REQ-3`: expose independently failing `composer test`, `composer format`, and `composer analyse`.
- `REQ-4`: GitHub Actions uses the same PHP 8.5 image and exact Composer scripts after strict validation and unlocked resolution.
- `REQ-5`: remove obsolete executable/configuration and distribution references; README badge cleanup belongs to the roadmap's documentation slice.
- `REQ-6`: keep `src/` exactly identical to bound HEAD; stop and respec if a dependency requires source adaptation.
- `REQ-7`: keep the library unlocked and use an opt-in next-major boundary with no data migration.
- `AC-1`: manifest validates and resolves on verified PHP 8.5 with every platform requirement satisfied; requirements=`REQ-1,REQ-7`.
- `AC-2`: Pest passes exactly 190 cases and bound-HEAD normalization proves only enumerated test-harness migrations; requirements=`REQ-2,REQ-6`.
- `AC-3`: each Composer script fails for a representative domain defect and passes after restoration; requirements=`REQ-3`.
- `AC-4`: CI builds the same image and invokes exact Composer validation/platform/quality interfaces; requirements=`REQ-4`.
- `AC-5`: legacy tool files and `.gitattributes` references are absent while README is untouched; requirements=`REQ-5`.
- `AC-6`: tracked content/modes and any untracked entries under `src/` exactly match bound HEAD; requirements=`REQ-6`.
- `AC-7`: `composer.lock` remains ignored, untracked, and absent from the accepted tree; requirements=`REQ-7`.
- `ASSUMPTION-1`: current compatible ranges are Guzzle `^8.2`, PSR-7 `^3.1`, PSR HTTP Message `^2.0`, Pest `^5.1`, Pint `^1.32`, and PHPStan `^2.2`; reversibility=`reversible`; impact=`solver selects compatible patches/minors`.
- `ASSUMPTION-2`: `php:8.5.0-cli-bookworm` plus copied `composer:2.8.11` is the single pinned verification runtime; reversibility=`reversible`; impact=`Docker replaces unavailable host PHP/Composer`.

## Outcome and boundaries
- Writer boundary: `composer.json`, `phpunit.xml`, `tests/Pest.php`, `tests/Unit/**/*.php`, `tools/verify-test-migration.php`, `docker/php85/Dockerfile`, `pint.json`, `phpstan.neon`, `.github/workflows/ci.yml`, `.gitignore`, `.gitattributes`; delete `phpunit.xml.dist`, `.travis.yml`, `.php_cs.dist`, `.styleci.yml`, `.scrutinizer.yml`.
- Protected boundaries: `src/**` and `README.md` have no edits, mode changes, or untracked additions. Any dependency incompatibility in `src` is a stop point for a new spec/plan.
- Integrated-review boundary: manifest, image, complete suite migration, preservation verifier, configs, and CI land together.
- Rollback unit / stop point: revert the whole slice before release; stop on lost test semantics, source drift, or runtime incompatibility.
- Compatibility / deploy order: next major only; no deployment/data; older PHP consumers retain the prior major.

## Research digest
- Contract: `composer.json:34-47` — legacy dependencies and no scripts/plugin allowlist.
- Contract: `tests/Unit/OAuth1Test.php:135-176` — callback mismatch and exchange interactions.
- Contract: `tests/Unit/Request/ProtocolParameterTest.php:44-390` — dense signing mock constraints.
- Contract: `tests/Unit/Credentials/CredentialsTest.php:15` and `ServerIssuedCredentialsTest.php:15` — removed abstract-class helper must become anonymous concrete subclasses.
- Contract: `tests/Unit/Signature/CanBuildBaseStringTest.php:15` and `CanGetSigningKeyTest.php:17` — removed trait helper must become anonymous classes using the traits.
- Contract: `.gitignore:1-4` — lock remains ignored.
- Data flow: `pinned build -> update -> platform check -> Composer scripts -> process status`; only image/dependency resolution needs network.

## Test-stack preflight
- `TESTSTACK-UNIT`: layer=`unit`; framework/source=`PHPUnit 5/6 @ composer.json:41-43`; command=`CMD-TEST-LEGACY @ .travis.yml:36-37`; harness=`autoload/mocks, no services`; constraints=`28 files, 190 @test cases`; tool_gaps=`host PHP/Composer absent`; baseline=`unavailable`; receipt_or_anchor=`static inventory at HEAD 0badc63c`; reason_impact=`use the pinned image for RED and bind preservation to immutable HEAD`.

## Evidence
- `EVID-RUNTIME`: kind=`local`; required=`yes`; publication_gating=`yes`; command_or_receipt=`CMD-BUILD,CMD-RUNTIME`; determinants=`Dockerfile`.
- `EVID-RESOLVE`: kind=`local`; required=`yes`; publication_gating=`yes`; command_or_receipt=`CMD-RESOLVE,CMD-PLATFORM,CMD-UNLOCK`; determinants=`image,manifest`.
- `EVID-TEST`: kind=`local`; required=`yes`; publication_gating=`yes`; command_or_receipt=`CMD-TEST,CMD-PRESERVE,CMD-FOCUSED`; determinants=`tests,bound HEAD`.
- `EVID-QUALITY`: kind=`local`; required=`yes`; publication_gating=`yes`; command_or_receipt=`CMD-FORMAT,CMD-ANALYSE`; determinants=`scripts/configs`.
- `EVID-SCOPE`: kind=`local`; required=`yes`; publication_gating=`yes`; command_or_receipt=`CMD-VALIDATE,CMD-SOURCE,CMD-SCOPE`; determinants=`manifest,bound HEAD,diff`.

## Command specifications
- `CMD-DEFAULTS`: cwd=`/home/risan/projects/code/oauth1`; env_names=`none`; services=`Docker`; resource_envelope=`2 CPUs, 2 GiB`; timeout=`300 seconds`.
- `CMD-BUILD`: argv=`["docker","build","-t","oauth1-php85:local","-f","docker/php85/Dockerfile","."]`; determinant_sources=`Dockerfile`; overrides=`network=required`; assertion=`builds from php:8.5.0-cli-bookworm, copies Composer 2.8.11, installs DOM/mbstring/XML/XMLWriter`.
- `CMD-RUNTIME`: argv=`["docker","run","--rm","oauth1-php85:local","sh","-lc","php --version && composer --version && php -m"]`; determinant_sources=`image`; overrides=`none`; assertion=`PHP 8.5.x, Composer 2.8.11, required extensions`.
- `CMD-RESOLVE`: argv=`["docker","run","--rm","-v","/home/risan/projects/code/oauth1:/app","-w","/app","oauth1-php85:local","composer","update","--prefer-dist","--no-interaction"]`; determinant_sources=`manifest,image`; overrides=`network=required`; assertion=`exit 0 without plugin prompt because config.allow-plugins contains only pestphp/pest-plugin:true`.
- `CMD-PLATFORM`: argv=`["docker","run","--rm","-v","/home/risan/projects/code/oauth1:/app","-w","/app","oauth1-php85:local","composer","check-platform-reqs"]`; determinant_sources=`lock,vendor,image`; overrides=`none`; assertion=`all pass`.
- `CMD-UNLOCK`: argv=`["docker","run","--rm","-v","/home/risan/projects/code/oauth1:/app","-w","/app","oauth1-php85:local","php","-r","if (is_file('composer.lock')) unlink('composer.lock');"]`; determinant_sources=`generated lock`; overrides=`none`; assertion=`lock absent`.
- `CMD-TEST-LEGACY`: argv=`["vendor/bin/phpunit","--no-coverage"]`; determinant_sources=`Travis`; overrides=`historical`; assertion=`anchor only`.
- `CMD-TEST`: argv=`["docker","run","--rm","-v","/home/risan/projects/code/oauth1:/app","-w","/app","oauth1-php85:local","composer","test","--","--colors=never"]`; determinant_sources=`manifest,phpunit.xml,tests`; overrides=`none`; assertion=`190 pass`.
- `CMD-FOCUSED`: argv=`["docker","run","--rm","-v","/home/risan/projects/code/oauth1:/app","-w","/app","oauth1-php85:local","composer","test","--","tests/Unit/Credentials/CredentialsTest.php","tests/Unit/Credentials/ServerIssuedCredentialsTest.php","tests/Unit/Signature/CanBuildBaseStringTest.php","tests/Unit/Signature/CanGetSigningKeyTest.php"]`; determinant_sources=`four helper-migration tests`; overrides=`none`; assertion=`all original cases/assertions pass`.
- `CMD-PRESERVE`: argv=`["docker","run","--rm","-v","/home/risan/projects/code/oauth1:/app","-w","/app","oauth1-php85:local","php","tools/verify-test-migration.php","0badc63c04361ecc8bce2f852c7569991721f37a"]`; determinant_sources=`tests,bound HEAD,verifier`; overrides=`none`; assertion=`28 files and 190 methods map; normalized bodies/arguments/interactions match after only listed substitutions and four exact fixture-harness replacements`.
- `CMD-FORMAT`: argv=`["docker","run","--rm","-v","/home/risan/projects/code/oauth1:/app","-w","/app","oauth1-php85:local","composer","format"]`; determinant_sources=`manifest,pint.json,writer PHP files`; overrides=`none`; assertion=`check-only pass; src and README excluded`.
- `CMD-ANALYSE`: argv=`["docker","run","--rm","-v","/home/risan/projects/code/oauth1:/app","-w","/app","oauth1-php85:local","composer","analyse"]`; determinant_sources=`manifest,phpstan.neon,src`; overrides=`none`; assertion=`pass at maximum no-baseline level compatible with byte-identical src`.
- `CMD-VALIDATE`: argv=`["docker","run","--rm","-v","/home/risan/projects/code/oauth1:/app","-w","/app","oauth1-php85:local","composer","validate","--strict"]`; determinant_sources=`manifest`; overrides=`after unlock`; assertion=`pass and allow-plugins has exactly pestphp/pest-plugin:true`.
- `CMD-SOURCE`: argv=`["git","status","--porcelain=v1","--untracked-files=all","--","src","README.md"]`; determinant_sources=`bound HEAD,working tree`; overrides=`none`; assertion=`zero output`; plus=`git diff --quiet HEAD -- src README.md and git diff --cached --quiet HEAD -- src README.md both exit 0, covering tracked content/mode and untracked entries`.
- `CMD-SCOPE`: argv=`["bash","-lc","test $(find tests/Unit -type f -name '*Test.php' | wc -l) -eq 28 && test $(rg -n '#\\[Test\\]|function test_' tests/Unit --glob '*.php' | wc -l) -eq 190 && test ! -e composer.lock && git diff --check"]`; determinant_sources=`tests,lock,diff`; overrides=`none`; assertion=`inventory/unlocked/whitespace pass`.

## Logical commits
- `COMMIT-BASELINE`: requirements=`REQ-1..REQ-7`; paths=`complete writer boundary`; parent=`0badc63c04361ecc8bce2f852c7569991721f37a`; focused_green=`GREEN-3`; mode=`implementation-owned`; reason=`partial runner states are not reviewable`.

## Steps

### STEP-1 — Build and resolve the PHP 8.5 toolchain
- Done when: the single image proves runtime/extensions and resolves the current manifest without a retained lock.
- Depends on: `none`
- Requirements / acceptance / evidence: `REQ-1,REQ-3,REQ-4,REQ-5,REQ-7` / `AC-1,AC-3,AC-4,AC-5,AC-7` / `EVID-RUNTIME,EVID-RESOLVE,EVID-SCOPE`
- RED `TEST-1`: requirements_acceptance=`REQ-1/AC-1`; path_name=`composer.json resolution`; arrange_act_assert=`resolve untouched manifest and audit PHP/current-package/plugin security contract`; run=`CMD-BUILD,CMD-RUNTIME,CMD-RESOLVE`; missing_behavior=`legacy constraints and no allowlist`; expected_failure=`legacy incompatibility or audit mismatch`; forbidden_failure=`Docker/DNS/volume failure or plugin denial after GREEN`; receipt=`RED-1`.
- GREEN: paths_symbols_signatures=`Dockerfile pinned inputs/extensions; manifest current ranges; config.allow-plugins exactly {pestphp/pest-plugin:true}; scripts; modern configs/CI; delete legacy files/references`; values_order_errors=`CI builds same image and calls validation/platform plus Composer scripts`; production_identity_change=`none`; receipt=`GREEN-1`.
- Migration/rollback: forward=`replace tool contract`; existing_data=`none`; reversal=`revert slice`; deploy_order=`before protocol work`; stop_point=`source adaptation required`.
- VERIFY: `CMD-RUNTIME,RESOLVE,PLATFORM,UNLOCK,VALIDATE and SOURCE` satisfy bound evidence.
- Affected determinants: `image,manifest,configs,CI,legacy files,protected paths`.

### STEP-2 — Migrate all tests without semantic drift
- Done when: 190 tests and the four removed-helper groups pass and the preservation verifier accepts every change.
- Depends on: `STEP-1`
- Requirements / acceptance / evidence: `REQ-2,REQ-6` / `AC-2,AC-6` / `EVID-TEST,EVID-SCOPE`
- RED `TEST-2`: requirements_acceptance=`REQ-2,REQ-6/AC-2,AC-6`; path_name=`complete suite plus four named files`; arrange_act_assert=`run exact Composer test before conversion`; run=`CMD-TEST,CMD-FOCUSED`; missing_behavior=`removed discovery/lifecycle/assertion/mock/abstract/trait helpers`; expected_failure=`partial discovery or named removed API`; forbidden_failure=`missing vendor/Pest/image`; receipt=`RED-2`.
- GREEN: paths_symbols_signatures=`#[Test], supported setUp/public methods, two assertion renames, onlyMethods and removed toggles; Credentials fixtures instantiate anonymous concrete subclasses with the same constructor args; trait fixtures instantiate anonymous classes using the exact trait; verifier recognizes only those transformations`; values_order_errors=`no production edits and all original assertions remain`; production_identity_change=`none`; receipt=`GREEN-2`.
- Migration/rollback: forward=`mechanical atomic conversion`; existing_data=`none`; reversal=`revert slice`; deploy_order=`before protocol work`; stop_point=`semantic hunk detected`.
- VERIFY: `CMD-TEST,FOCUSED,PRESERVE,SOURCE,SCOPE` all pass; named callback confirmation/mismatch, URI resolution, signers, and providers pass in full suite.
- Affected determinants: `tests,verifier,bound HEAD`.

### STEP-3 — Prove script failure modes and CI parity
- Done when: each exact Composer script fails for its own temporary defect, passes after restoration, and CI uses the same commands.
- Depends on: `STEP-2`
- Requirements / acceptance / evidence: `REQ-3,REQ-4,REQ-5,REQ-6` / `AC-3,AC-4,AC-5,AC-6` / `EVID-QUALITY,EVID-TEST,EVID-SCOPE`
- RED `TEST-3`: requirements_acceptance=`REQ-3,REQ-4/AC-3,AC-4`; path_name=`Composer scripts/configs`; arrange_act_assert=`sequentially introduce then restore a failing assertion, formatting defect in a writer-boundary test, and PHPStan fixture error`; run=`CMD-TEST,CMD-FORMAT,CMD-ANALYSE`; missing_behavior=`unproved routing/status propagation`; expected_failure=`each exits nonzero with own diagnostic`; forbidden_failure=`missing tool/config/image or cross-domain failure`; receipt=`RED-3`.
- GREEN: paths_symbols_signatures=`restore defects; apply Pint only to writer-boundary PHP files; use no PHPStan baseline/ignored project errors; CI invokes exact scripts`; values_order_errors=`accepted tree check-only and source/README clean`; production_identity_change=`none`; receipt=`GREEN-3`.
- Migration/rollback: forward=`land quality config/CI`; existing_data=`none`; reversal=`revert slice`; deploy_order=`none`; stop_point=`useful analysis requires src edit; record maximum passing level`.
- VERIFY: `CMD-TEST,PRESERVE,FORMAT,ANALYSE,VALIDATE,SOURCE,SCOPE` pass.
- Affected determinants: `complete writer/protected boundaries`.

## Traceability result
- Orphan IDs: `REQ=none;AC=none;STEP=none;TEST=none;CMD=none;EVID=none`
- Contradictions: `none`

## Whole-change rollback and residual risks
- Rollback: revert the single slice before release; after release publish a correction and preserve old tags.
- Residual `RISK-1`: owner=`implementation`; decision_or_evidence=`source incompatibility triggers stop/respec`; blocker=`conditional`.
- Residual `RISK-2`: owner=`implementation`; decision_or_evidence=`record useful byte-identical PHPStan level for later raising`; blocker=`no`.

## Self-check
- `SELF_CHECK`: result=`PASS`; failed_ids=`none`; dispositions=`none`; residual_blockers=`none`
