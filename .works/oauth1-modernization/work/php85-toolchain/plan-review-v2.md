ARTIFACT_SCHEMA_VERSION: 2
ROLE: plan-review
REVIEWER_ID: /root/php85_plan_review_v2
PLAN: /home/risan/projects/code/oauth1/.codex/plans/php85-toolchain-v2.md#sha256:6fbe2593faf2e421d14376ae61b6ee4a1b750b2180786340d56b87d341ad2e15
BRIEF: /home/risan/projects/code/oauth1/.works/oauth1-modernization/work/php85-toolchain/spec.md#sha256:ce0e67171d34bf3898155aa3da2677a6ee83d48acb0eebc3718ab66fbf8b2377
REPO_CONTEXT: /home/risan/projects/code/oauth1/.works/oauth1-modernization/work/php85-toolchain/scout-oauth1.md#sha256:d67f4110468a0179b926c75034c90ba07fb4725ab5f4eb738819b7d9fcd0ddcf
REPOSITORY_IDENTITY: /home/risan/projects/code/oauth1@0badc63c04361ecc8bce2f852c7569991721f37a
FROZEN_STATUS: two untracked roots only: .codex/ and .works/
GOVERNING_BINDINGS: none
RISK_EVIDENCE_DECLARATION: PHP 8.5 next-major dependency/test/tooling migration; exact src byte preservation; one pinned local image; bound-HEAD test semantic verification; no deployment or provider writes.
OUTPUT_PATH_RESERVED: /home/risan/projects/code/oauth1/.works/oauth1-modernization/work/php85-toolchain/plan-review-v2.md
OUTPUT_REVISIONS: unchanged
THREAD_ID: unknown
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
SANDBOX_EVIDENCE: session permissions metadata reports sandbox_mode=workspace-write
INVARIANT: This plan is NOT implemented yet. Judge the plan against the repository's current state; do not report a planned change as already missing.
DIGEST_VERIFICATION: PASS; plan, brief, and repo-context digests matched their bindings immediately before return; reserved output path remained nonexisting.
REPOSITORY_VERIFICATION: Live repository files were inspected against the caller-bound HEAD and frozen-status receipt; no implementation or verification command was executed.

id: PHP85-PR2-001
severity: Major
materiality: material:scope
plan_location: Intake REQ-5 and AC-5, lines 18 and 25; writer boundary, line 32; STEP-1 GREEN, line 81
repo_evidence: /home/risan/projects/code/oauth1/.works/oauth1-modernization/work/php85-toolchain/spec.md:13-15,22,58; /home/risan/projects/code/oauth1/README.md:4-7
failure_scenario: The brief excludes public usage and release documentation while R5 requires retired hosted tooling to stop being presented as active. The live README still presents Travis, Scrutinizer, and StyleCI badges, but README.md is outside the plan's writer boundary. Implementing the plan exactly therefore leaves retired systems publicly presented while claiming REQ-5 and AC-5 complete.
fix: Bind one scope resolution in the brief: either permit the narrow README badge cleanup, add README.md to the writer boundary, and verify the retired badges are absent, or explicitly narrow R5 and its acceptance criteria to configuration and distribution metadata.

id: PHP85-PR2-002
severity: Major
materiality: material:compatibility
plan_location: STEP-2 GREEN and preservation allowlist, line 91
repo_evidence: /home/risan/projects/code/oauth1/tests/Unit/Credentials/CredentialsTest.php:15; /home/risan/projects/code/oauth1/tests/Unit/Credentials/ServerIssuedCredentialsTest.php:15; /home/risan/projects/code/oauth1/tests/Unit/Signature/CanBuildBaseStringTest.php:15; /home/risan/projects/code/oauth1/tests/Unit/Signature/CanGetSigningKeyTest.php:17; /home/risan/projects/code/oauth1/.codex/plans/php85-toolchain-v2.md:28,91; [PHPUnit removal schedule](https://github.com/sebastianbergmann/phpunit/issues/5241)
failure_scenario: Pest 5 selects PHPUnit 13, but four live fixtures call getMockForAbstractClass() or getMockForTrait(), APIs removed before PHPUnit 13. The plan enumerates setMethods(), legacy toggles, and assertion replacements as the only permitted substitutions. Leaving these calls intact makes the migrated suite fail; replacing them with anonymous test doubles or trait harnesses violates the stated verifier allowlist.
fix: Enumerate exact test-only replacements for both abstract-class fixtures and both trait fixtures, add those transformations to the preservation verifier allowlist, and require focused RED/GREEN evidence that the four existing test groups retain their assertions without editing src/.

id: PHP85-PR2-003
severity: Major
materiality: material:compatibility
plan_location: ASSUMPTION-1, line 28; CMD-RESOLVE, line 60; STEP-1 GREEN, line 81
repo_evidence: /home/risan/projects/code/oauth1/composer.json:44-46; /home/risan/projects/code/oauth1/.codex/plans/php85-toolchain-v2.md:28,60,81; [Pest package metadata](https://packagist.org/packages/pestphp/pest); [Composer allow-plugins configuration](https://getcomposer.org/doc/06-config.md#allow-plugins)
failure_scenario: Pest 5 requires pestphp/pest-plugin, which is a Composer plugin. The current manifest has no allow-plugins entry, and the plan does not require one. Composer 2.8.11 defaults to an empty plugin allowlist and a first noninteractive update fails when a required plugin is not allowed, so CMD-RESOLVE can stop before any RED or GREEN test executes.
fix: Specify the exact composer.json security configuration allowing only pestphp/pest-plugin, include it in the manifest audit, and require the noninteractive resolver test to distinguish an allow-plugin rejection from the intended legacy-constraint RED failure.

id: PHP85-PR2-004
severity: Major
materiality: material:behavior
plan_location: AC-6, line 26; protected boundary, line 33; CMD-SCOPE, line 69
repo_evidence: /home/risan/projects/code/oauth1/.codex/plans/php85-toolchain-v2.md:26,33,69
failure_scenario: AC-6 requires the final src tree hash to equal bound HEAD, but CMD-SCOPE substitutes a git diff byte-count check. Git diff does not report untracked files, so an added untracked PHP file or other unexpected entry under src can pass while the live src tree is no longer byte-identical.
fix: Replace the proxy assertion with a bound-HEAD src manifest comparison covering every relative path, object type or mode, and content hash, or combine an exact bound-HEAD tracked-tree comparison with a clean git status check that includes all untracked src entries.

VERDICT: REQUEST CHANGES
NEXT_ACTION: needs-decision
FINDINGS: PHP85-PR2-001,PHP85-PR2-002,PHP85-PR2-003,PHP85-PR2-004
UNRESOLVED_BLOCKING_IDS: PHP85-PR2-001,PHP85-PR2-002,PHP85-PR2-003,PHP85-PR2-004
OPEN_ADVISORY_IDS: none
BLOCKER: PHP85-PR2-001 requires a caller-owned decision on whether the retired README badges are included in this phase or the brief's tooling-retirement requirement is narrowed.
RATIONALE: Approval is unavailable because four Major findings remain. Resolve the scope contradiction first, amend the executable dependency, test-migration, and source-preservation contracts, then submit the new plan bytes for a fresh review.
PERSISTENCE_BOUNDARY: Caller may persist exactly from ARTIFACT_SCHEMA_VERSION through this line with one terminal LF.
