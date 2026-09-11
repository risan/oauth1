# Make OAuth request signing conform to RFC 5849

Status: ready
Type: bug
Route: bounded-change — a cohesive protocol correction across parameter parsing, normalization, signatures, and request assembly
Next: draft-spec

## Request
Implement the signing and transport-conformance portion of `.works/oauth1-modernization/brief.md` after the PHP 8.5 baseline.

## Goal
Ensure every OAuth signature is computed from the exact eligible request parameters as an ordered multiset, and ensure sent credentials and parameters cannot diverge from what was signed.

## Target
- `src/Signature/` — normalized parameter string, base string, HMAC-SHA1, PLAINTEXT, and RSA-SHA1
- `src/Request/` — lossless URI/query/body parameter collection and safe Authorization assembly
- `tests/Unit/Signature/`, `tests/Unit/Request/` — RFC vectors and edge/failure cases
- Pattern to follow: `../../scout-oauth1.md` — delivery seam 2 and collaborator boundaries

## Acceptance criteria
- [ ] A failing test reproduces the current loss of repeated/colliding parameters before the implementation changes.
- [ ] Normalization preserves repeated names and values, empty values, literal plus signs, percent-encoded octets, and URI/options collisions, sorting encoded names then encoded values.
- [ ] Base URI normalization handles scheme/host case, default ports, non-default ports, paths, and excludes query/fragment.
- [ ] Only RFC-eligible request parameters are signed; supported query/form option shapes have an unambiguous wire representation.
- [ ] Generated OAuth Authorization credentials cannot be overridden by caller headers.
- [ ] HMAC-SHA1 and PLAINTEXT pass RFC-derived vectors, and RSA-SHA1 is implemented through OpenSSL with success and failure coverage.
- [ ] Public exceptions make unsupported or ambiguous input fail before dispatch.

## Out of scope
- Changing the high-level OAuth authorization stages (`oauth10a-integration`).
- Documentation and release prose (`docs-and-release`).

## Assumptions
- The PHP 8.5 test/tool baseline from child 1 is complete before this work begins — `../../roadmap.md`.
- Supporting all three signature methods defined by RFC 5849 best matches the request for comprehensive standards support; providers still choose which method they accept.
