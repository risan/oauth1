# Scout: oauth1

## Repo rules
- No `AGENTS.md` or `CLAUDE.md` exists at or above this repository (checked with `find .. -name AGENTS.md -o -name CLAUDE.md`); the repository itself supplies no additional contributor rules.
- The current worktree was clean before this report; preserve any later user or agent changes (`git status --short`).

## Map
- `src/OAuth1.php` — public four-stage client flow and protected-resource convenience methods — `OAuth1::requestTemporaryCredentials`, `OAuth1::requestTokenCredentials`, `OAuth1::request`.
- `src/Request/{ProtocolParameter,AuthorizationHeader,RequestFactory,UriParser}.php` and `src/Signature/*` — OAuth parameters, header rendering, transport options, URI handling, and HMAC/PLAINTEXT signing — `ProtocolParameter::getSignature`, `BaseStringBuilder::build`.
- `src/Credentials/CredentialsFactory.php` — parses form-encoded token responses and enforces OAuth 1.0a callback confirmation — `CredentialsFactory::createTemporaryCredentialsFromResponse`.
- `src/{Config,Provider}/` — configuration plus Trello, Tumblr, Twitter, and Upwork endpoint presets — `ProviderFactory`, `ProviderInterface`.
- `tests/Unit/` — 28 PHPUnit unit-test files; no integration or end-to-end test directory was found (`find tests -type f -name '*Test.php'`).
- `composer.json`, `phpunit.xml.dist`, `.travis.yml`, `.php_cs.dist`, and `README.md` — dependency/platform policy, test runner, obsolete CI/formatting, and public package guidance.

## Current behavior
- `OAuth1Factory::create` wires `ConfigFactory`, a random nonce generator, HMAC-SHA1 by default, an authorization-header signer, Guzzle, and response credential parsing; public configuration is an associative array — `src/OAuth1Factory.php:26-48`, `src/Config/ConfigFactory.php:14-37`.
- The request-token call POSTs OAuth parameters in `Authorization`; if a callback is configured it is signed and sent as `oauth_callback`. Token exchange verifies the callback's `oauth_token` equals the stored temporary identifier, signs both `oauth_token` and form `oauth_verifier`, then POSTs the same verifier as form data — `src/OAuth1.php:127-137`, `src/Request/ProtocolParameter.php:106-139`, `src/Request/RequestFactory.php:63-95`.
- Protected requests require a `TokenCredentials` instance, sign URI query plus Guzzle `query` and `form_params`, and send the OAuth parameters only in `Authorization` — `src/OAuth1.php:183-191`, `src/Request/ProtocolParameter.php:145-193`, `src/Request/AuthorizationHeader.php:76-83`.
- The package supports only `HMAC-SHA1` and `PLAINTEXT`; RSA-SHA1 is absent — `src/Signature/HmacSha1Signer.php:13-37`, `src/Signature/PlainTextSigner.php:12-22`.
- The request-token response requires `oauth_token`, `oauth_token_secret`, and `oauth_callback_confirmed=true`; access-token parsing requires the two token fields — `src/Credentials/CredentialsFactory.php:12-49`.

## Pattern
- Use the existing small collaborators and interfaces (`Config` → `ProtocolParameter` → `AuthorizationHeader` → `RequestFactory`) for a compatibility-conscious modern API; tests currently mock each collaboration rather than exercise a real signed HTTP request — `src/OAuth1Factory.php:36-48`, `tests/Unit/Request/RequestFactoryTest.php:78-217`.

## Tests
- Framework: PHPUnit 5.7 or 6.4 (`composer.json:34-36`); 190 `@test` annotations were counted with `rg -n '@test' tests --glob '*.php'`.
- Command: `vendor/bin/phpunit --no-coverage` (the verified CI script); `.travis.yml` uses `vendor/bin/phpunit --coverage-text --coverage-clover coverage.xml` for its coverage job — `.travis.yml:36-40`.
- Closest test: `tests/Unit/Signature/BaseStringBuilderTest.php` — covers method case, base URI/default ports, simple encoding, arrays, and simple query signing, but not RFC 5849 reference vectors, duplicate parameters, empty values, `+` handling, or a complete HTTP exchange — `tests/Unit/Signature/BaseStringBuilderTest.php:37-164`.

## Contracts
- Installers currently receive PSR-4 package `risan/oauth1`, namespace `Risan\\OAuth1\\`, PHP `>=5.6.4`, Guzzle `~6.3`, PSR-7 v1, and `random_compat`; there is no committed lock file — `composer.json:1-39`, `.gitignore:1-4`.
- OAuth version is emitted as `1.0`, nonce uses `random_bytes`, and authorization-header fields are RFC 3986-percent-encoded then rendered as `OAuth key=\"value\"` — `src/Request/ProtocolParameter.php:76-100`, `src/Request/NonceGenerator.php:10-44`, `src/Request/AuthorizationHeader.php:76-83`.
- The README documents the four-leg authorization flow, configuration keys, two signature methods, and four provider presets; it also uses `serialize`/`unserialize` directly in its primary example — `README.md:41-103`, `README.md:105-136`, `README.md:138-336`.

## Must not change
- Preserve the OAuth 1.0a callback-token binding before access-token exchange: a mismatched callback token must fail before HTTP dispatch — `src/OAuth1.php:127-137`, `tests/Unit/OAuth1Test.php:136-146`.
- Preserve the mandatory request-token `oauth_callback_confirmed=true` validation and current relative-URI/base-URI behavior unless a documented major-version compatibility decision replaces them — `src/Credentials/CredentialsFactory.php:16-30`, `src/Config/UriConfig.php:206-227`.

## Open decisions
- **Standards boundary:** RFC 5849 defines the OAuth 1.0 protocol and HMAC-SHA1/PLAINTEXT/RSA-SHA1 methods; decide whether the next major release must implement RSA-SHA1 or explicitly document support as HMAC-SHA1 and PLAINTEXT only. The repository does not decide this — `src/Signature/HmacSha1Signer.php:13-37`, `src/Signature/PlainTextSigner.php:12-22`.
- **Confirmed conformance risks to fix/test:** `parse_str` collapses repeated query/body names and PHP-style array syntax, while the base-string builder accepts arrays and constructs bracketed keys; OAuth normalization needs an ordered multiset sorted by encoded name then value. Query parsing/merging also overwrites colliding names — `src/Signature/BaseStringBuilder.php:42-44`, `src/Signature/BaseStringBuilder.php:89-136`, `src/Request/UriParser.php:47-54`, `src/Request/ProtocolParameter.php:181-193`.
- **Transport/signature boundary:** Guzzle options permit `query` as formats other than the array handled here and let caller headers recursively replace the generated `Authorization` header, producing a request whose sent credentials can differ from the signed credentials — `src/Request/ProtocolParameter.php:181-193`, `src/Request/RequestFactory.php:101-107`.
- **Modernization boundary:** PHP 8.5 support requires a deliberate new minimum version and a dependency/API migration from Guzzle 6/PSR-7 v1/PHPUnit 5-6; current code has untyped public APIs and PHP 5-era PHPUnit mock calls — `composer.json:25-36`, `src/OAuth1.php:49-191`, `tests/Unit/OAuth1Test.php:33-44`.
- **Tooling/release boundary:** CI is Travis-only and tests old PHP 5.6–7.2; formatting is an unrequired legacy PHP-CS-Fixer config plus StyleCI/Scrutinizer badges, with no static analysis, Pint, GitHub Actions, release workflow, Packagist webhook, or Packagist auto-update configuration committed. Packagist auto-publishing cannot be established from this checkout; Packagist normally indexes VCS tags, but webhook/account settings are external — `.travis.yml:1-40`, `.php_cs.dist:1-20`, `.styleci.yml:1-20`, `.scrutinizer.yml:1-32`, `README.md:3-10`.
- **Integration approach:** no provider test credentials or local OAuth server exists in the repository. A deterministic local HTTP fixture/server can validate all three legs and exact Authorization/base-string behavior without external secrets; live-provider E2E testing needs provider credentials and callback-host authority — `tests/Unit/`, `README.md:41-103`.

## Delivery seams

### 1. PHP 8.5 toolchain and test-runner migration
- **Outcome:** establish the PHP `^8.5` package platform, current Guzzle/PSR dependencies, a maintained PHPUnit/Pest runner, formatter, static analysis, and PHP 8.5 GitHub Actions; remove Travis/legacy style integrations and compatibility dependencies.
- **Owns:** `composer.json`, generated `composer.lock` decision, `phpunit.xml.dist` or `phpunit.xml`, Pest bootstrap/configuration, formatter/static-analysis configs, `.github/workflows/*`, and retirement of `.travis.yml`, `.php_cs.dist`, `.styleci.yml`, `.scrutinizer.yml`.
- **Tests it migrates:** every `tests/Unit/*Test.php`, whose PHPUnit-5/6 namespace, `setUp()` visibility, `@test` annotations, and deprecated mock builder methods predate modern PHPUnit — `composer.json:34-36`, `tests/Unit/OAuth1Test.php:20-44`, `phpunit.xml.dist:1-39`.
- **Prerequisite:** none. **Unblocks:** all subsequent slices. Keep behavior assertions unchanged at this stage so failures identify runner migration versus protocol change.
- **Acceptance coverage:** PHP/dependencies/obsolete tooling; maintained runner and Pest decision; formatter/static checks on PHP 8.5; final automated-check foundation.

### 2. OAuth parameter model and signature normalization
- **Outcome:** replace PHP-array/query parsing with a lossless ordered parameter-pair representation and RFC 5849 normalization; make every request's signed parameter collection precisely match supported wire representations; protect generated OAuth Authorization fields from caller override.
- **Owns:** `src/Signature/BaseStringBuilder.php`, `src/Signature/BaseStringBuilderInterface.php`, `src/Request/ProtocolParameter.php`, `src/Request/ProtocolParameterInterface.php`, `src/Request/RequestFactory.php`, `src/Request/RequestFactoryInterface.php`, `src/Request/UriParser.php`, `src/Request/UriParserInterface.php`, and their focused tests under `tests/Unit/{Signature,Request}/`.
- **Tests to add/replace:** RFC 5849 signature/base-string vector; repeated names, repeated values, empty values, `+` versus `%20`, pre-encoded characters, query/body collisions, default/non-default ports, query option shapes, body-content eligibility, and attempted `Authorization` override — current coverage only exercises simplified arrays and simple query strings (`tests/Unit/Signature/BaseStringBuilderTest.php:73-164`, `tests/Unit/Request/ProtocolParameterTest.php:276-290`, `tests/Unit/Request/RequestFactoryTest.php:180-217`).
- **Prerequisite:** slice 1, because the test syntax/config is shared. **Unblocks:** the complete-flow integration harness and accurate README options contract.
- **Acceptance coverage:** RFC-conformant base string, supported signature-method behavior, and prevention of sent-versus-signed parameters/credential replacement.

### 3. OAuth 1.0a flow and deterministic HTTP integration
- **Outcome:** preserve and harden request-token → authorization redirect → verifier/token exchange → protected-resource behavior through a local deterministic OAuth fixture that inspects the received method, form/query, and authorization signature without external secrets.
- **Owns:** `src/OAuth1.php`, `src/OAuth1Interface.php`, `src/Credentials/CredentialsFactory.php`, `src/Credentials/CredentialsFactoryInterface.php`, `src/HttpClient.php`, `src/HttpClientInterface.php`, and new focused integration support/tests (for example `tests/Integration/*`), plus their existing unit counterparts.
- **Tests to add/replace:** success flow; callback-token mismatch before dispatch; missing/false `oauth_callback_confirmed`; malformed/missing credential fields; signature/verifier on token exchange; protected call with and without token; exact request signature validated by the local fixture — current tests use mocks for dispatch and response parsing (`tests/Unit/OAuth1Test.php:93-207`, `tests/Unit/Credentials/CredentialsFactoryTest.php:57-123`).
- **Prerequisite:** slice 2 for lossless signing; slice 1 for the runner. **Unblocks:** only final documentation/release evidence.
- **Acceptance coverage:** OAuth 1.0a four stages, failed stages, deterministic integration coverage, and documented live-provider E2E limits.

### 4. Public documentation, providers, and release metadata
- **Outcome:** publish the final PHP 8.5 API, secure OAuth 1.0a walkthrough, options/signature-method/provider matrix, contributor commands, local-integration boundary, versioning/release procedure, and Packagist behavior.
- **Owns:** `README.md`, provider classes/tests only if endpoint/support claims change (`src/Provider/*`, `tests/Unit/Provider/*Test.php`), `composer.json` package metadata only if needed, and release notes/changelog if introduced.
- **Tests/evidence:** executable README snippets or a documentation smoke test where practical; provider preset assertions; CI documentation/quality commands established by slice 1. Existing README’s direct `serialize`/`unserialize` example and stale external service badges need replacement — `README.md:3-10`, `README.md:41-103`, `README.md:119-136`.
- **Prerequisite:** slices 1–3, because it must describe the final runner, request-option constraints, signatures, and flow. **Acceptance coverage:** comprehensive, secure README and Packagist/release guidance; document account-side Packagist activation as external.

## Shared-file conflicts and sequencing
- `composer.json` is shared by slices 1 and 4; slice 1 owns platform/dependency/tool scripts, while slice 4 may only make a final metadata/doc-link adjustment after rebasing on it — `composer.json:1-39`.
- Test configuration and all existing test files are shared during slice 1 migration. Slices 2 and 3 should add/modify only their named test subtrees after the runner baseline lands; do not parallel-convert the same legacy test files — `phpunit.xml.dist:1-39`, `tests/Unit/`.
- `RequestFactory` and `ProtocolParameter` are the core boundary: slice 2 owns their production edits; slice 3 consumes their final request representation and must not revise signature semantics — `src/Request/ProtocolParameter.php:145-193`, `src/Request/RequestFactory.php:101-107`.
- `README.md` has a late, single owner in slice 4. Slices 1–3 should supply exact commands/API receipts rather than making competing prose changes — `README.md:14-340`.

## Parent acceptance coverage
- Slice 1: criteria 1, 7, 8, and the quality-check portion of 10.
- Slice 2: criteria 2–5 and the signature-method scope decision in criterion 4.
- Slice 3: criteria 2, 6, and the runtime/integration portion of 10.
- Slice 4: criterion 9, provider/support disclosures in criterion 4, and the Packagist portion of criterion 9.
- Combined sequencing covers every parent criterion; the only external dependency remains Packagist account/webhook activation, which stays documented rather than performed.
