# Modernize and verify the OAuth 1.0 client

Status: ready
Type: migration
Route: initiative — protocol conformance, runtime modernization, test infrastructure, and release documentation form distinct but coordinated workstreams
Next: decompose

## Request
Modernize this old PHP package for OAuth 1.0, verify and correct its standards conformance, target current PHP 8.5 only, upgrade runtime and development dependencies, consider Pest, add meaningful integration coverage, update the README, adopt current formatting, and clarify Packagist publishing.

## Goal
Release a maintainable PHP 8.5 OAuth 1.0a client whose signing and authorization flow conform to RFC 5849, whose important protocol behavior is protected by unit and deterministic integration tests, and whose installation, usage, contribution, and release documentation are current and easy to follow.

## Target
- `src/` — OAuth flow, parameter collection and normalization, signing, credentials, providers, and HTTP transport
- `tests/` — protocol vectors, failure behavior, and complete local signed-request flows
- `composer.json`, CI and formatter configuration — PHP 8.5 platform, current dependencies, automated quality checks
- `README.md` and release metadata — supported behavior, secure examples, development workflow, and Packagist publication guidance
- Pattern to follow: `src/OAuth1Factory.php` — preserve small collaborators while modernizing their contracts

## Acceptance criteria
- [ ] The package requires PHP 8.5 and current compatible runtime/development dependencies; obsolete compatibility shims and CI/tooling are removed.
- [ ] OAuth 1.0a temporary credentials, resource-owner authorization, token exchange, and signed protected-resource requests conform to RFC 5849.
- [ ] Signature base-string construction preserves repeated names and values, empty values, encoded sorting, query/body collisions, and RFC 3986 encoding, verified with published RFC examples.
- [ ] Supported signature methods are explicit; HMAC-SHA1 and PLAINTEXT are correct, and RSA-SHA1 is either implemented and tested or clearly scoped with a standards-based rationale.
- [ ] Callers cannot silently replace generated OAuth authorization credentials or send parameters that differ from those signed.
- [ ] Tests cover successful and failed protocol stages plus deterministic HTTP integration without third-party secrets; live-provider E2E limits are documented.
- [ ] The test suite uses a current maintained runner, with Pest adopted when it improves readability without weakening coverage.
- [ ] Current formatting and static checks run locally and in GitHub Actions on PHP 8.5.
- [ ] The README gives a complete, secure OAuth 1.0a walkthrough, API/options reference, supported signature methods/providers, testing guidance, and release/Packagist behavior.
- [ ] All automated tests and quality checks pass on the final tree.

## Out of scope
- Configuring external Packagist account/webhook settings without repository-visible credentials or authority.
- Live provider tests requiring consumer credentials, user authorization, or a public callback host.
- OAuth 2.0 or OpenID Connect support.

## Assumptions
- “Drop any old PHP support” means the next major version may require `php: ^8.5` and make deliberate public API changes — explicit PHP 8.5 target in the request.
- A deterministic local OAuth server fixture satisfies integration coverage without external credentials — no provider credentials or fixture server exists in the repository.
- Laravel Pint is preferred if it supports this standalone PHP 8.5 package cleanly; otherwise use a current equivalent with one documented command — the user prefers Pint but left the choice open.
- Packagist should update from tagged releases through its GitHub integration; repository work will document and validate package metadata, while account-side activation remains external.

## Known risks
- Correct multivalue normalization may require changing public parameter representations and therefore deserves a major-version migration note.
- Current Guzzle option passthrough permits ambiguous parameter and Authorization-header behavior; tightening it can expose previously accepted but incorrectly signed calls.
- PHP 8.5 and newest tool releases may not all be installed in the current environment, so containerized or CI verification may be needed.
