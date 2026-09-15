# Working on naf/i18n

NAF is a small PHP framework with optional Composer plugins. Its core owns boot,
configuration, the service container, routing, events and PSR-7 responses. Prefer existing
NAF helpers, services and extension interfaces; keep application business rules in the host.
This package declares `type: naf-plugin` and is discovered after installation in a NAF host.
The plugin repository itself is not the application's web root.

Before changing code, read the [shared contribution workflow](https://github.com/nafphp/docs/blob/main/AGENT_WORKFLOW.md)
and [release procedure](https://github.com/nafphp/docs/blob/main/RELEASING.md).
In the multi-repository workspace, the same documents are in the sibling `docs/` checkout;
use the linked copies when working from a standalone clone. Preserve other contributors' work.
Review and update user documentation with every behavior change. Source fixes use an RC branch;
verified documentation-only changes can be merged and published by the agent.

## What this plugin does

`naf/i18n` translates flat JSON catalogs and selects the request language. Install with
`composer require naf/i18n`. Helpers are `Naf\I18n\t`, `translator` and `lang`; they are loaded
from `src/view_helpers.php`. This is the PHP plugin, not the separate native-extension project.

## Use it

Create `app/Resources/lang/de.json` in the host, or use the configured `app:translationPath`:

```json
{"greeting": "Hallo :name"}
```

In a bootstrapped application:

```php
<?php
use function Naf\I18n\{t, translator};

translator()->setLanguage('de');
$text = t('greeting', ['name' => 'Ada']);
```

This returns `Hallo Ada`. Escape it with `Naf\View\s()` when rendering HTML. The translator
returns the key when missing; catalogs and substituted values are not automatically escaped.
`setLanguage()` reloads the catalog. Language selection is controlled by the request listeners
and application configuration, so avoid ad-hoc locale/cookie handling in controllers.

## Change it here

[Translator](src/Core/Translator.php) owns lookup, replacement and loading;
[Language](src/Support/Language.php) normalization; [listeners](src/Events/) request selection
and response cookies. Inspect [bootstrap](bootstrap.php) and the actual configuration reads
before adding an option. Do not assume fallback settings merge individual missing keys from
a second catalog. Preserve normalized language paths and non-cascading placeholder replacement.

## Verify

Run `composer test` and `composer validate --strict`. Use [tests](tests/) for catalogs,
normalization, missing keys and replacement. Test changed locale selection/cookies over HTTP.
Do not require the optional native extension. No `analyse` script is declared.

User docs: [Translations](https://nafphp.github.io/docs/translations/).

Follow the shared [PHP code style](https://github.com/nafphp/docs/blob/main/CODE_STYLE.md)
and `.php-cs-fixer.dist.php`. Run `composer style:check`; `composer style:fix` applies the rules.
Keep logical steps and local names readable, preserving public signatures and template output.
