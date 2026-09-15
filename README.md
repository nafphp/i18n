<div style="text-align: center;" align="center">

![NAF](assets/naf-logo-small-square.png)

[![NAF I18n Plugin](https://github.com/nafphp/i18n/actions/workflows/php.yml/badge.svg)](https://github.com/nafphp/i18n/actions/workflows/php.yml)

</div>

[← Back to NAF](https://github.com/nafphp/framework)

---

# naf/i18n

> **Simple JSON-based translations for your NAF application.**

This plugin provides a lightweight translation system for multilingual apps.
It reads language files from disk, supports variable replacements, and falls back gracefully — all with minimal overhead.

> 🧩 Part of the official NAF plugin collection.
> Install it if you want clean, flexible localization without external libraries.

## Documentation

**[Translations →](https://nafphp.github.io/docs/translations/)**

Everything about this package — what it does, how it is configured and what it needs — lives
in the [NAF documentation](https://nafphp.github.io/docs/). Not sure which packages you need?
[Start here](https://nafphp.github.io/docs/choosing-packages/).

## Install

```bash
composer require naf/i18n
```

## License

MIT. Part of [NAF](https://github.com/nafphp/framework).

## PHP code style

Source, tests and PHP templates follow the shared [NAF code style](https://github.com/nafphp/docs/blob/main/CODE_STYLE.md)
(PER Coding Style 3.0 with the Nafinity readability rules). After `composer install`, run
`composer style:check` to verify formatting or `composer style:fix` to apply it. The formatter
is a development dependency. Review template output and run the package checks after changes.
