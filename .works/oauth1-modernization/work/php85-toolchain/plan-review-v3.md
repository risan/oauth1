ARTIFACT_SCHEMA_VERSION: 2
ROLE: plan-review
REVIEWER_ID: /root/php85_plan_review_v3
PLAN: /home/risan/projects/code/oauth1/.codex/plans/php85-toolchain-v3.md#sha256:949e139447bb16821b1a8a04b4d89850b57c55ad4c0217c3bc74abad632cab04
BRIEF: /home/risan/projects/code/oauth1/.works/oauth1-modernization/work/php85-toolchain/spec.md#sha256:b79755dec6d49bb7a6cf21bd0f2584615c31ed5bbad173c7a3c7973a8fd8469f
REPO_CONTEXT: /home/risan/projects/code/oauth1/.works/oauth1-modernization/work/php85-toolchain/scout-oauth1.md#sha256:d67f4110468a0179b926c75034c90ba07fb4725ab5f4eb738819b7d9fcd0ddcf
REPOSITORY_IDENTITY: oauth1=/home/risan/projects/code/oauth1@0badc63c04361ecc8bce2f852c7569991721f37a
FROZEN_STATUS: .codex/ and .works/ untracked only
GOVERNING_BINDINGS: none
RISK_EVIDENCE_DECLARATION: next-major PHP8.5 tooling; exact src/README preservation; pinned image; Pest plugin allowlist; exact removed-helper replacements; bound-HEAD semantic verifier; no external writes.
OUTPUT_PATH_RESERVED: /home/risan/projects/code/oauth1/.works/oauth1-modernization/work/php85-toolchain/plan-review-v3.md
OUTPUT_REVISIONS: unchanged
THREAD_ID: /root/php85_plan_review_v3
CONTEXT_REQUESTED: none
CONTEXT_ACTUAL: unknown
CONTEXT_PROVENANCE: unknown
CONTEXT_EVIDENCE: none
ROUTING_REQUESTED: plan-review
ROUTING_ACTUAL: unknown
ROUTING_PROVENANCE: unknown
ROUTING_EVIDENCE: none
SANDBOX_REQUESTED: read-only
SANDBOX_ACTUAL: workspace-write
SANDBOX_PROVENANCE: runtime-metadata
SANDBOX_EVIDENCE: runtime permissions metadata declares sandbox_mode=workspace-write with /home/risan/projects/code/oauth1 writable
INVARIANT: This plan is NOT implemented yet. Judge the plan against the repository's current state; do not report a planned change as already missing.
DIGEST_VERIFICATION: PASS — plan, brief, and repo-context digests match their supplied bindings at return
REPOSITORY_VERIFICATION: PASS — live .git/HEAD resolves refs/heads/master=0badc63c04361ecc8bce2f852c7569991721f37a and the reserved output path remains nonexisting

id: PHP85-PR3-001
severity: Major
materiality: material:scope
plan_location: AC-6; CMD-SOURCE; CMD-SCOPE; COMMIT-BASELINE
repo_evidence: .codex/plans/php85-toolchain-v3.md:26,71-75; .works/oauth1-modernization/work/php85-toolchain/spec.md:47-60
failure_scenario: CMD-SOURCE compares protected paths with HEAD, and CMD-SCOPE runs an unbound git diff. Once COMMIT-BASELINE advances HEAD, an accidentally committed src/ or README.md change produces clean status and zero HEAD-relative diff, allowing the exact-bound-HEAD preservation requirement to pass falsely.
fix: Make the acceptance commands compare tracked content and modes directly with 0badc63c04361ecc8bce2f852c7569991721f37a, retain a separate all-untracked scan for src/ and README.md, run the checks after the implementation commit exists, and bind git diff --check to the same baseline commit.

id: PHP85-PR3-002
severity: Major
materiality: material:evidence-tests-commands
plan_location: CMD-BUILD; CMD-RESOLVE; CMD-PRESERVE; STEP-1 GREEN
repo_evidence: .codex/plans/php85-toolchain-v3.md:59-67,83-87; the [official PHP CLI image layer](https://hub.docker.com/layers/library/php/8-cli-bookworm/images/sha256-eb1a10b504af18e87503e6e91e4bff19160b10c7cdc5cf7800ba9f1c4e638ab4) does not establish git or an archive extractor in the target image, while the [Composer 2.8.11 image layer](https://hub.docker.com/layers/library/composer/2.8.11/images/sha256-855bfdf4adf8418863b19c60d8026e9d161e21a40be354e0c1b66558ddb73b57) installs those utilities in its own image
failure_scenario: Copying only the Composer executable does not copy git or unzip into the PHP target stage. Composer update --prefer-dist can therefore fail because it cannot extract distributions or fall back to source, and the bound-commit preservation verifier has no specified way to retrieve baseline test bytes.
fix: Specify the target-stage OS packages and installation commands, including git plus unzip or another declared Composer-supported archive extractor; add exact command -v/runtime assertions; and state whether CMD-PRESERVE reads the baseline through git show or a caller-materialized immutable baseline.

id: PHP85-PR3-003
severity: Major
materiality: material:risk-tier
plan_location: CMD-PRESERVE; STEP-2 GREEN/VERIFY; STEP-3 TEST-3
repo_evidence: .codex/plans/php85-toolchain-v3.md:67,89-106; tests/Unit/OAuth1Test.php:135-176; tests/Unit/Request/ProtocolParameterTest.php:383-392
failure_scenario: The new verifier is the principal evidence that 190 migrated tests preserve assertion arguments, exception expectations, and mock interactions, but every planned invocation expects it to pass. An over-broad normalization or incomplete comparison can therefore approve semantic test drift while the migrated suite remains green.
fix: Define the verifier's path-by-path transformation allowlist and add failure-capable checks that independently alter an assertion argument, remove or change an exception expectation, alter a mock method/with/expects constraint, and remove test discovery metadata; require CMD-PRESERVE to fail for each mutation with the expected diagnostic and restore exact pre-mutation bytes before final verification.

id: PHP85-PR3-004
severity: Major
materiality: material:behavior
plan_location: REQ-3/AC-3; REQ-4/AC-4; STEP-1 GREEN; STEP-3 TEST-3/VERIFY
repo_evidence: composer.json:41-47; .works/oauth1-modernization/work/php85-toolchain/scout-oauth1.md:21-36; .codex/plans/php85-toolchain-v3.md:23-24,59-72,83-104
failure_scenario: The plan does not state the exact composer.json script values or the required GitHub Actions job/check topology, and none of its verification commands parses or executes .github/workflows/ci.yml. Local Composer commands can all pass while CI is malformed, uses different commands or runtime setup, or collapses formatting, analysis, and tests into one check contrary to the brief.
fix: Specify the exact test, format, and analyse script payloads; name the distinct CI jobs and their exact Docker build, unlocked update, validation, platform, and Composer-script invocations; and add a pinned actionlint or equivalent deterministic workflow verifier plus a negative workflow mutation proving parity violations fail acceptance.

VERDICT: REQUEST CHANGES
NEXT_ACTION: amend
FINDINGS: PHP85-PR3-001,PHP85-PR3-002,PHP85-PR3-003,PHP85-PR3-004
UNRESOLVED_BLOCKING_IDS: PHP85-PR3-001,PHP85-PR3-002,PHP85-PR3-003,PHP85-PR3-004
OPEN_ADVISORY_IDS: none
BLOCKER: none
