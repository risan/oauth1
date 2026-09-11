# Plan: PHP 8.5 toolchain baseline — v4

VERDICT: PLAN_READY_FOR_REVIEW

## Bindings
- Brief: `SPEC_REF=/home/risan/projects/code/oauth1/.works/oauth1-modernization/work/php85-toolchain/spec.md#sha256:b79755dec6d49bb7a6cf21bd0f2584615c31ed5bbad173c7a3c7973a8fd8469f`
- Repositories: `oauth1=root:/home/risan/projects/code/oauth1,base:0badc63c04361ecc8bce2f852c7569991721f37a,parent:a5a143baf4c25f8a881cccafe9eaa59a5c605eb9,head:0badc63c04361ecc8bce2f852c7569991721f37a`
- Governing instructions: `none`
- Repo context: `/home/risan/projects/code/oauth1/.works/oauth1-modernization/work/php85-toolchain/scout-oauth1.md#sha256:d67f4110468a0179b926c75034c90ba07fb4725ab5f4eb738819b7d9fcd0ddcf`
- Design input: `none`

## Intake
- Objective / done: establish a behavior-preserving PHP 8.5 baseline / a single pinned PHP image resolves dependencies and exact Composer/CI interfaces pass, while immutable-base checks prove source and test behavior preservation.
- `REQ-1`: require PHP `^8.5`, current dependencies/extensions, and no compatibility shims.
- `REQ-2`: preserve 190 test cases and every assertion, exception, mock interaction, and argument constraint under Pest 5.
- `REQ-3`: exact scripts are `test: pest`, `format: pint --test`, and `analyse: phpstan analyse --no-progress --memory-limit=1G`, each with independent failure propagation.
- `REQ-4`: GitHub Actions exposes distinct `validate`, `test`, `format`, and `analyse` jobs; each builds the same image, resolves unlocked dependencies, and invokes its named exact contract.
- `REQ-5`: obsolete executable/configuration/distribution references are removed; README is reserved for the documentation slice.
- `REQ-6`: `src/` and README content/modes/path sets equal immutable base `0badc63c...`; any required source adaptation stops for respec.
- `REQ-7`: no committed or accepted-tree lock file; next-major boundary; no data migration.
- `AC-1`: strict validation, noninteractive resolution, and platform check pass inside verified PHP 8.5; requirements=`REQ-1,REQ-7`.
- `AC-2`: 190 tests pass and a self-tested base-comparison tool accepts only enumerated harness transformations; requirements=`REQ-2,REQ-6`.
- `AC-3`: every exact script rejects its controlled defect and passes after restoration; requirements=`REQ-3`.
- `AC-4`: actionlint plus a self-tested CI contract verifier validate four named jobs and exact commands; requirements=`REQ-4`.
- `AC-5`: legacy files/attributes are absent and README equals base; requirements=`REQ-5,REQ-6`.
- `AC-6`: base-relative tracked diff/status checks detect content, mode, path, and untracked changes under protected paths even after commits; requirements=`REQ-6`.
- `AC-7`: lock remains ignored/untracked/absent; requirements=`REQ-7`.
- `ASSUMPTION-1`: compatible ranges are Guzzle `^8.2`, PSR-7 `^3.1`, PSR HTTP Message `^2.0`, Pest `^5.1`, Pint `^1.32`, PHPStan `^2.2`; reversibility=`reversible`; impact=`patch/minor solver drift remains intentional`.
- `ASSUMPTION-2`: `php:8.5.0-cli-bookworm`, `composer:2.8.11`, and actionlint `1.7.7` are version-pinned build/check inputs; reversibility=`reversible`; impact=`no host PHP tooling needed`.

## Outcome and boundaries
- Writer boundary: `composer.json`, `phpunit.xml`, `tests/Pest.php`, `tests/Unit/**`, `tests/Tooling/**`, `tools/verify-test-migration.php`, `tools/verify-ci-workflow.php`, `docker/php85/Dockerfile`, `pint.json`, `phpstan.neon`, `.github/workflows/ci.yml`, `.gitignore`, `.gitattributes`; delete five legacy configs.
- Protected boundary: compare `src/**` and `README.md` directly with base `0badc63c...`, never moving HEAD.
- Integrated-review boundary: all writer paths land together; acceptance runs after any implementation commit.
- Rollback/stop: revert slice before release; stop on verifier semantic drift or source incompatibility.
- Compatibility/deploy: next-major only, no deploy/data; old consumers pin prior major.

## Research digest
- `composer.json:34-47` — legacy packages, no scripts or plugin permission.
- `OAuth1Test.php:135-176` and `ProtocolParameterTest.php:383-392` — failure and mock constraints need token-level preservation.
- `CredentialsTest.php:15`, `ServerIssuedCredentialsTest.php:15` — abstract mock helpers removed.
- `CanBuildBaseStringTest.php:15`, `CanGetSigningKeyTest.php:17` — trait mock helpers removed.
- `.gitignore:1-4` — library remains unlocked.
- Data flow: pinned image build → Composer update/platform → exact scripts and verifiers → statuses; network only during image/dependency resolution.

## Test-stack preflight
- `TESTSTACK-UNIT`: layer=`unit/tooling`; framework/source=`legacy PHPUnit @ composer.json`; command=`CMD-TEST-LEGACY`; harness=`no services`; constraints=`28 files/190 @test`; tool_gaps=`host PHP/Composer`; baseline=`unavailable`; receipt_or_anchor=`HEAD static inventory`; reason_impact=`RED uses built image; immutable base supplies semantics`.

## Evidence
- `EVID-RUNTIME`: local, required, publication-gating; commands=`CMD-BUILD,CMD-RUNTIME`.
- `EVID-RESOLVE`: local, required, publication-gating; commands=`CMD-RESOLVE,CMD-PLATFORM,CMD-UNLOCK,CMD-VALIDATE`.
- `EVID-TEST`: local, required, publication-gating; commands=`CMD-TEST,CMD-FOCUSED,CMD-PRESERVE,CMD-PRESERVE-SELF`.
- `EVID-QUALITY`: local, required, publication-gating; commands=`CMD-FORMAT,CMD-ANALYSE`.
- `EVID-CI`: local, required, publication-gating; commands=`CMD-ACTIONLINT,CMD-CI-CONTRACT,CMD-CI-SELF`.
- `EVID-SCOPE`: local, required, publication-gating; commands=`CMD-PROTECTED,CMD-SCOPE`.

## Command specifications
- `CMD-DEFAULTS`: cwd=`/home/risan/projects/code/oauth1`; env_names=`none`; services=`Docker`; resources=`2 CPU/2 GiB`; timeout=`300s`.
- `CMD-BUILD`: `docker build -t oauth1-php85:local -f docker/php85/Dockerfile .`; Dockerfile=`FROM php:8.5.0-cli-bookworm; apt-get install -y --no-install-recommends git unzip libonig-dev libxml2-dev; docker-php-ext-install mbstring dom xml xmlwriter; COPY --from=composer:2.8.11 /usr/bin/composer /usr/local/bin/composer`; assertion=`build pass`.
- `CMD-RUNTIME`: run image `sh -lc 'php --version; composer --version; command -v git; command -v unzip; php -m'`; assertion=`PHP 8.5.x, Composer 2.8.11, git/unzip and required extensions`.
- `CMD-RESOLVE`: run image/mounted repo `composer update --prefer-dist --no-interaction`; assertion=`pass with config.allow-plugins exactly pestphp/pest-plugin:true`.
- `CMD-PLATFORM`: same image `composer check-platform-reqs`; assertion=`all pass`.
- `CMD-UNLOCK`: same image `php -r "if (is_file('composer.lock')) unlink('composer.lock');"`; assertion=`absent`.
- `CMD-VALIDATE`: same image `composer validate --strict`; assertion=`pass after unlock`.
- `CMD-TEST-LEGACY`: `vendor/bin/phpunit --no-coverage`; assertion=`historical anchor only`.
- `CMD-TEST`: same image `composer test -- --colors=never`; assertion=`190 pass`.
- `CMD-FOCUSED`: same image `composer test -- tests/Unit/Credentials/CredentialsTest.php tests/Unit/Credentials/ServerIssuedCredentialsTest.php tests/Unit/Signature/CanBuildBaseStringTest.php tests/Unit/Signature/CanGetSigningKeyTest.php`; assertion=`all original cases pass`.
- `CMD-PRESERVE`: same image `php tools/verify-test-migration.php 0badc63c04361ecc8bce2f852c7569991721f37a`; implementation=`uses git show <base>:<path> for baseline and current filesystem bytes; allowlist per path covers metadata/lifecycle, two assertion renames, onlyMethods/toggle removal, anonymous subclasses in two credential files, anonymous trait harnesses in two signature files`; assertion=`28/190 map and normalized tokens/arguments/interactions equal`.
- `CMD-PRESERVE-SELF`: same image `php tools/verify-test-migration.php --self-test`; assertion=`in-memory fixtures independently mutate assertion argument, delete/change expectException, alter expects/method/with constraints, and remove discovery metadata; each is rejected with its named diagnostic; allowed transforms pass`.
- `CMD-FORMAT`: same image `composer format`; assertion=`check-only pass on writer-boundary PHP files, excluding protected src`.
- `CMD-ANALYSE`: same image `composer analyse`; assertion=`pass without baseline/ignored project errors at recorded maximal byte-identical level`.
- `CMD-ACTIONLINT`: `docker run --rm -v /home/risan/projects/code/oauth1:/repo -w /repo rhysd/actionlint:1.7.7 -color=false .github/workflows/ci.yml`; assertion=`syntax/semantic pass`.
- `CMD-CI-CONTRACT`: same PHP image `php tools/verify-ci-workflow.php .github/workflows/ci.yml`; assertion=`exact jobs validate/test/format/analyse each contain docker build, update, required validation/platform steps, and exact named Composer script; no combined substitute`.
- `CMD-CI-SELF`: same image `php tools/verify-ci-workflow.php --self-test`; assertion=`in-memory malformed YAML/job deletion/command substitution/runtime-image mutation each rejected, canonical fixture accepted`.
- `CMD-PROTECTED`: argv=`git diff --exit-code 0badc63c04361ecc8bce2f852c7569991721f37a -- src README.md`; plus=`git diff --cached --exit-code 0badc63c... -- src README.md; git status --porcelain=v1 --untracked-files=all -- src README.md`; assertion=`all zero/empty after commit, detecting tracked content/mode/path and untracked additions against immutable base`.
- `CMD-SCOPE`: argv=`git diff --check 0badc63c04361ecc8bce2f852c7569991721f37a`; plus=`28 test files, 190 modern markers, lock absent and git ls-files --error-unmatch composer.lock fails`; assertion=`all pass after implementation commit`.

## Logical commits
- `COMMIT-BASELINE`: requirements=`REQ-1..REQ-7`; paths=`writer boundary`; parent=`0badc63c...`; focused_green=`GREEN-3`; mode=`implementation-owned`; reason=`partial runner state not reviewable`.

## Steps

### STEP-1 — Build and resolve the exact toolchain
- Done when: pinned image proves runtime/utilities and resolves/validates the secure manifest without retained lock.
- Depends on: none
- Requirements / acceptance / evidence: `REQ-1,REQ-3,REQ-4,REQ-5,REQ-7 / AC-1,AC-3,AC-4,AC-5,AC-7 / EVID-RUNTIME,EVID-RESOLVE,EVID-SCOPE`
- RED `TEST-1`: run=`CMD-BUILD,RUNTIME,RESOLVE`; missing=`legacy constraints/no plugin allowlist`; expected=`dependency or manifest-contract rejection`; forbidden=`Docker/DNS/volume failure`; receipt=`RED-1`.
- GREEN: exact manifest ranges; allow only `pestphp/pest-plugin`; exact scripts from REQ-3; Dockerfile OS/extensions; modern configs; four-job CI; delete legacy config/attributes; src/README untouched; receipt=`GREEN-1`.
- Migration/rollback: replace/revert whole tool contract; no data/deploy; stop if src edit needed.
- VERIFY: `CMD-RUNTIME,RESOLVE,PLATFORM,UNLOCK,VALIDATE,PROTECTED`.

### STEP-2 — Migrate tests and prove the verifier
- Done when: 190 tests pass and self-tested comparison accepts only named transformations.
- Depends on: STEP-1
- Requirements / acceptance / evidence: `REQ-2,REQ-6 / AC-2,AC-6 / EVID-TEST,EVID-SCOPE`
- RED `TEST-2`: run legacy files with `CMD-TEST,FOCUSED`; expected=`removed discovery/lifecycle/assertion/mock/abstract/trait APIs`; forbidden=`missing vendor/image`; receipt=`RED-2`.
- GREEN: attributes/public lifecycle, assertion/mock substitutions, two anonymous concrete credential subclasses, two anonymous trait classes; verifier exact path allowlist; production identity unchanged; receipt=`GREEN-2`.
- Migration/rollback: atomic test-only migration/revert; stop on semantic hunk.
- VERIFY: `CMD-TEST,FOCUSED,PRESERVE,PRESERVE-SELF,PROTECTED,SCOPE`; named must-not-change cases pass.

### STEP-3 — Prove quality and CI contracts
- Done when: exact scripts and both custom verifiers reject controlled defects, then all final checks pass after commit.
- Depends on: STEP-2
- Requirements / acceptance / evidence: `REQ-3,REQ-4,REQ-5,REQ-6 / AC-3,AC-4,AC-5,AC-6 / EVID-QUALITY,EVID-CI,EVID-SCOPE`
- RED `TEST-3`: sequential in-memory/self-test mutations plus temporary restored test/format/PHPStan defects; run=`CMD-TEST,FORMAT,ANALYSE,PRESERVE-SELF,CI-SELF`; expected=`each own diagnostic/nonzero`; forbidden=`tool/image/config missing`; receipt=`RED-3`.
- GREEN: restore bytes; no baselines/ignored project errors; CI exact four jobs and commands; protected paths clean; receipt=`GREEN-3`.
- Migration/rollback: land/revert whole slice; no data/deploy; record maximal analysis level.
- VERIFY: all EVID commands pass after COMMIT-BASELINE, with base SHA used explicitly.

## Traceability result
- Orphans: `none`; contradictions: `none`.

## Whole-change rollback and residual risks
- Rollback: revert slice before release; after release publish correction and preserve old tags.
- `RISK-1`: source incompatibility triggers respec; blocker=`conditional`.
- `RISK-2`: record maximal byte-identical PHPStan level for later raising; blocker=`no`.

## Self-check
- `SELF_CHECK`: result=`PASS`; failed_ids=`none`; dispositions=`none`; residual_blockers=`none`
