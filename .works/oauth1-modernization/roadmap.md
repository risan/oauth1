# Modernize and verify the OAuth 1.0 client — roadmap

Status: complete

| # | Child | Route | After | Conflicts | Covers | Status |
|---|---|---|---|---|---|---|
| 1 | `work/php85-toolchain/` — establish the PHP 8.5 dependency, test, formatting, analysis, and CI baseline | bounded-change | — | 4: `composer.json` metadata | 1, 7, 8, 10 | complete |
| 2 | `work/rfc5849-signing/` — make parameter collection and all three RFC signature methods conformant | bounded-change | 1: current test/tool baseline | — | 2, 3, 4, 5 | complete |
| 3 | `work/oauth10a-integration/` — harden the complete authorization flow with deterministic HTTP integration coverage | bounded-change | 2: lossless request signing contract | — | 2, 6, 10 | complete |
| 4 | `work/docs-and-release/` — document the final API, providers, development workflow, and Packagist release process | bounded-change | 1–3: final public behavior and commands | 1: `composer.json` metadata | 4, 9 | complete |
