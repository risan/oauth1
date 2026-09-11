# Plan review — PHP 8.5 toolchain v1

PLAN: /home/risan/projects/code/oauth1/.codex/plans/php85-toolchain-v1.md#sha256:980d50630bce7dc95bb42fad2ab8dd1d6fa02fa8c5a9126b0db2a2c9e11b965a
BRIEF: /home/risan/projects/code/oauth1/.works/oauth1-modernization/work/php85-toolchain/spec.md#sha256:ce0e67171d34bf3898155aa3da2677a6ee83d48acb0eebc3718ab66fbf8b2377
SCOUT: /home/risan/projects/code/oauth1/.works/oauth1-modernization/work/php85-toolchain/scout-oauth1.md#sha256:d67f4110468a0179b926c75034c90ba07fb4725ab5f4eb738819b7d9fcd0ddcf
REPOSITORY: /home/risan/projects/code/oauth1
HEAD: 0badc63c04361ecc8bce2f852c7569991721f37a
FROZEN_STATUS: two untracked roots only: .codex/ and .works/
GOVERNING_BINDINGS: none
RISK_EVIDENCE: PHP 8.5 next-major compatibility, dependency resolution, test preservation, CI/tooling; no deployment/provider writes; local Docker receipts unavailable before implementation; current host lacks PHP/Composer.
OUTPUT: /home/risan/projects/code/oauth1/.works/oauth1-modernization/work/php85-toolchain/plan-review-v1.md
THREAD_ID: /root/php85_plan_review
ROUTING_REQUESTED: unknown
ROUTING_ACTUAL: unknown
ROUTING_PROVENANCE: unknown
ROUTING_EVIDENCE: none
CONTEXT_REQUESTED: none
CONTEXT_ACTUAL: unknown
CONTEXT_PROVENANCE: unknown
CONTEXT_EVIDENCE: none
SANDBOX_REQUESTED: read-only
SANDBOX_ACTUAL: workspace-write
SANDBOX_PROVENANCE: runtime-metadata
FINAL_DIGEST_CHECK: pass
OUTPUT_PATH_CHECK: nonexisting

> This plan is NOT implemented yet. Judge the plan against the repository's current state; do not report a planned change as already missing.

id: PR-PHP85-001
severity: Major
materiality: material:compatibility
plan_location: Command specifications / CMD-RESOLVE, CMD-TEST, CMD-FORMAT, CMD-ANALYSE; STEP-1/TEST-1
repo_evidence: composer.json:34-47; .works/oauth1-modernization/work/php85-toolchain/spec.md:54-57; .works/oauth1-modernization/work/php85-toolchain/scout-oauth1.md:32-36
failure_scenario: CMD-RESOLVE runs Composer in the mutable `composer:2` image and never asserts that its PHP runtime is 8.5, while subsequent checks reuse that resolved vendor tree in a separate `php:8.5-cli` image. Dependency resolution can therefore pass against a different PHP version or extension set and later fail in the target runtime, without satisfying AC1's PHP 8.5 resolution requirement.
fix: Define one pinned PHP 8.5 verification image containing Composer and every required runtime/tool extension, add explicit `php --version`, `composer --version`, and `composer check-platform-reqs` assertions, and run resolution plus all quality commands in that same image. Add any Dockerfile/configuration needed for this image to the writer boundary and determinants.

id: PR-PHP85-002
severity: Major
materiality: material:scope
plan_location: Outcome and boundaries / Writer boundary; STEP-3/GREEN
repo_evidence: .works/oauth1-modernization/work/php85-toolchain/spec.md:13-15,45,59; src/OAuth1.php:109-191
failure_scenario: The plan permits source edits for formatter or static-analysis compatibility, directs Pint across `src`, and permits native types or PHPDoc based merely on next-major compatibility. AC6 permits production changes only when a resolved dependency requires a minimal compatibility adaptation. An implementation can therefore rewrite production files or change public type behavior while still claiming conformance to STEP-3.
fix: Make `src/` byte-identical for formatting and analysis work. Permit a source edit only after a named resolved-dependency failure demonstrates it is necessary, bind that failure to a focused regression test, and restrict the edit to the minimal compatibility adaptation. Configure Pint/PHPStan scope or rules so ordinary quality closure does not require unrelated production rewrites or public type additions.

id: PR-PHP85-003
severity: Major
materiality: material:behavior
plan_location: EVID-SCOPE; CMD-SCOPE; STEP-2/GREEN and VERIFY
repo_evidence: .works/oauth1-modernization/work/php85-toolchain/spec.md:19,23,47-51,55; tests/Unit/OAuth1Test.php:33-303; tests/Unit/Request/ProtocolParameterTest.php:44-390
failure_scenario: CMD-SCOPE proves only 28 files and 190 modern test markers. A migration that deletes or weakens assertions, exception expectations, mock call counts, argument constraints, or must-not-change cases can retain all 190 test methods and pass every specified command. The claimed evidence therefore does not establish AC2's no-lost-assertion contract.
fix: Add a deterministic preservation receipt against bound HEAD that accounts for every legacy test and permits only enumerated mechanical migrations such as metadata, lifecycle signatures, assertion renames, and supported mock-builder substitutions. Require review or machine validation of all other test-body hunks, explicitly reconcile assertion, exception, and mock-expectation surfaces, and bind the must-not-change cases to named passing tests.

id: PR-PHP85-004
severity: Major
materiality: material:compatibility
plan_location: Command specifications / CMD-TEST, CMD-FORMAT, CMD-ANALYSE; STEP-2/TEST-2 and VERIFY; STEP-3/TEST-3 and VERIFY
repo_evidence: composer.json:41-47; .works/oauth1-modernization/work/php85-toolchain/spec.md:20-21,56-57; .works/oauth1-modernization/work/php85-toolchain/scout-oauth1.md:32-36
failure_scenario: The plan adds `composer test`, `composer format`, and `composer analyse`, but every local RED and GREEN command bypasses those interfaces and invokes `vendor/bin/*` directly. The Composer scripts can be absent, mutating, misrouted, or unable to propagate failure while all specified evidence still passes, so AC3 is unproved.
fix: Make CMD-TEST, CMD-FORMAT, and CMD-ANALYSE invoke the exact Composer entry points inside the unified PHP 8.5 toolchain. Use those same commands for their RED and GREEN receipts so each observes a representative domain failure and later exits successfully, and have CI invoke those exact scripts as distinct steps or jobs.

FINDINGS: PR-PHP85-001,PR-PHP85-002,PR-PHP85-003,PR-PHP85-004
VERDICT: REQUEST CHANGES
NEXT_ACTION: amend
BLOCKER: none
