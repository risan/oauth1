ARTIFACT_SCHEMA_VERSION: 2
ROLE: plan-review
REVIEWER_ID: /root/php85_plan_review_v4
PLAN: /home/risan/projects/code/oauth1/.codex/plans/php85-toolchain-v4.md#sha256:2434cb655953dbabc6c3cc4c09eaa3e9e6e383c6a3d9302d0313aff80ad33aa6
BRIEF: /home/risan/projects/code/oauth1/.works/oauth1-modernization/work/php85-toolchain/spec.md#sha256:b79755dec6d49bb7a6cf21bd0f2584615c31ed5bbad173c7a3c7973a8fd8469f
REPO_CONTEXT: /home/risan/projects/code/oauth1/.works/oauth1-modernization/work/php85-toolchain/scout-oauth1.md#sha256:d67f4110468a0179b926c75034c90ba07fb4725ab5f4eb738819b7d9fcd0ddcf
REPOSITORY_IDENTITY: oauth1=/home/risan/projects/code/oauth1@0badc63c04361ecc8bce2f852c7569991721f37a
FROZEN_STATUS: .codex/ and .works/ untracked only; output path nonexisting
GOVERNING_BINDINGS: none
RISK_EVIDENCE_DECLARATION: next-major PHP8.5 tooling, exact immutable-base source/README protection, exact scripts and four CI jobs, self-tested test/CI verifiers, pinned image utilities, no external writes
OUTPUT_PATH_RESERVED: /home/risan/projects/code/oauth1/.works/oauth1-modernization/work/php85-toolchain/plan-review-v4.md
OUTPUT_REVISIONS: unchanged
THREAD_ID: /root/php85_plan_review_v4
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
SANDBOX_EVIDENCE: runtime permissions metadata reports sandbox_mode=workspace-write with /home/risan/projects/code/oauth1 writable
INVARIANT: This plan is NOT implemented yet. Judge the plan against the repository's current state; do not report a planned change as already missing.
DIGEST_VERIFICATION: PASS; plan, brief, and repo-context digests matched their supplied bindings immediately before return; reserved output path remained nonexisting.
REPOSITORY_VERIFICATION: PASS; live .git/HEAD resolves refs/heads/master=0badc63c04361ecc8bce2f852c7569991721f37a and load-bearing repository files were inspected.

id: PHP85-PR4-001
severity: Major
materiality: material:evidence-tests-commands
plan_location: CMD-BUILD through CMD-VALIDATE, lines 59-64; STEP-1 TEST-1 and GREEN, lines 83-90
repo_evidence: /home/risan/projects/code/oauth1/composer.json:34-47; /home/risan/projects/code/oauth1/.codex/plans/php85-toolchain-v4.md:59-64,83-90
failure_scenario: TEST-1 invokes CMD-BUILD before GREEN introduces the Dockerfile, so its first result is the expressly forbidden harness/configuration failure. Even if the Dockerfile is added as an unstated RED prerequisite, the base PHP constraint admits PHP 8.5 and the base manifest has no Pest dependency, so Composer resolution does not deterministically expose either the legacy constraint or missing Pest plugin permission. No specified check fails on the exact R1 manifest defects, and the final validation commands can pass without proving the required PHP range, dependency ranges, removed shim, script payloads, or exact plugin allowlist.
fix: Define a pre-RED harness step that creates and validates the pinned image independently of the manifest change. Add a deterministic, self-tested manifest-contract check covering the exact PHP/runtime/dev requirements, absence of random_compat and direct PHPUnit, exact Composer scripts, and exact allow-plugins map. Require the bound base manifest to fail with named diagnostics, controlled mutations to fail independently, and the restored GREEN manifest to pass.

id: PHP85-PR4-002
severity: Major
materiality: material:evidence-tests-commands
plan_location: CMD-ANALYSE, line 71; STEP-3 TEST-3, GREEN, and VERIFY, lines 101-108
repo_evidence: /home/risan/projects/code/oauth1/.works/oauth1-modernization/work/php85-toolchain/spec.md:20,43,56; /home/risan/projects/code/oauth1/.works/oauth1-modernization/work/php85-toolchain/scout-oauth1.md:21-24; /home/risan/projects/code/oauth1/.codex/plans/php85-toolchain-v4.md:71,101-108
failure_scenario: The plan never fixes PHPStan's analyzed paths or numeric level and provides no algorithm for establishing the claimed “maximal byte-identical level.” A narrow path or weak level can therefore pass CMD-ANALYSE and be recorded as maximal. TEST-3's unspecified “PHPStan defect” does not guarantee that production code or any intended test surface is analyzed, so AC3's independent static-analysis failure mode remains unproved.
fix: Specify the exact phpstan.neon paths and numeric level. If the level must be discovered during implementation, define a bounded level-by-level procedure and required receipt that proves the selected maximum. Name an exact temporary type defect inside an included path, its expected PHPStan diagnostic/nonzero result, and byte-exact restoration; also assert that no baseline, ignored project errors, or empty analysis scope is accepted.

id: PHP85-PR4-003
severity: Major
materiality: material:scope
plan_location: AC-5 through AC-7, lines 25-27; writer boundary, line 32; CMD-SCOPE, line 76; STEP-1 GREEN and STEP-3 VERIFY, lines 88,108
repo_evidence: /home/risan/projects/code/oauth1/.gitattributes:1-8; /home/risan/projects/code/oauth1/.gitignore:1-4; /home/risan/projects/code/oauth1/.travis.yml:1-40; /home/risan/projects/code/oauth1/.codex/plans/php85-toolchain-v4.md:25-27,32,76,88,108
failure_scenario: CMD-SCOPE checks whitespace, test-file/marker counts, lockfile absence, and whether composer.lock is tracked, but it does not enforce the writer-path allowlist, the exact five legacy-file deletions, removal of their .gitattributes references, or continued composer.lock ignore behavior. An implementation can change an unrelated tracked path, retain a retired configuration/reference, or remove the lock ignore rule while every stated final command still passes, despite EVID-SCOPE claiming AC5-AC7 coverage.
fix: Extend CMD-SCOPE with a deterministic base-relative changed-path and mode allowlist, including the exact permitted additions/modifications and five deletions. Add explicit assertions that every retired file and stale .gitattributes reference is absent and that composer.lock is absent, untracked, and matched by git check-ignore. Include all untracked paths in the scope check and run it after the implementation commit.

VERDICT: REQUEST CHANGES
NEXT_ACTION: amend
FINDINGS: PHP85-PR4-001,PHP85-PR4-002,PHP85-PR4-003
UNRESOLVED_BLOCKING_IDS: PHP85-PR4-001,PHP85-PR4-002,PHP85-PR4-003
OPEN_ADVISORY_IDS: none
BLOCKER: none
RATIONALE: Approval is unavailable because the plan lacks a valid RED and final contract for the exact Composer manifest, leaves the PHPStan baseline underdetermined, and does not make its declared scope and retirement checks failure-capable.
PERSISTENCE_BOUNDARY: Caller may persist exactly from ARTIFACT_SCHEMA_VERSION through this line with one terminal LF.
