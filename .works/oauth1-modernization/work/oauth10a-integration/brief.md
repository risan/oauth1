# Verify the complete OAuth 1.0a flow over HTTP

Status: ready
Type: feature
Route: bounded-change — one end-to-end protocol flow with related production hardening and deterministic fixture infrastructure
Next: draft-spec

## Request
Implement the complete-flow and integration-test portion of `.works/oauth1-modernization/brief.md` after conformant signing exists.

## Goal
Prove that temporary credentials, authorization redirect, verifier exchange, and protected-resource requests work together over HTTP and fail safely at every protocol boundary.

## Target
- `src/OAuth1.php`, `src/Credentials/`, `src/HttpClient*` — high-level OAuth 1.0a orchestration and response validation
- `tests/Integration/` and fixture support — deterministic local OAuth consumer/service exchange
- Focused unit tests for failure paths in the owned production files
- Pattern to follow: `../../scout-oauth1.md` — delivery seam 3 and preserved callback-token validation

## Acceptance criteria
- [ ] Tests cover request-token success with `oauth_callback_confirmed=true`, authorization URL construction, verifier exchange, and a signed protected-resource request.
- [ ] The fixture independently validates incoming OAuth parameters and signatures rather than reusing the production signer as its oracle.
- [ ] Callback-token mismatch fails before network dispatch.
- [ ] Missing/false callback confirmation and missing/malformed token response fields fail with clear exceptions.
- [ ] Token exchange signs and sends the verifier according to RFC 5849 without duplication ambiguity.
- [ ] Protected requests require token credentials and verify query/form handling across the real HTTP boundary.
- [ ] The deterministic suite needs no third-party credentials, callback host, or internet access.

## Out of scope
- Live provider E2E tests.
- Core normalization/signature semantics (`rfc5849-signing`).
- Public documentation (`docs-and-release`).

## Assumptions
- Child 2 supplies the final request representation and signing contract consumed here — `../../roadmap.md`.
