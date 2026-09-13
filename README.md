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

---

## 📦 Features

* Loads language files from `app/Resources/lang/`
* Configure translation file path via config key `app:translationPath` (use `language` to set the language)
* Supports `t('key')` with a fallback mechanism
* Replaces variables via `:name`, `:count`, etc.
* Language codes follow ISO 639-1 (e.g. `en`, `de`, `fr`)
* JSON-based – easy to edit, export, and manage

---

## 📥 Installation

```bash
composer require naf/i18n
```

The plugin auto-registers and makes a `t()` as well as a `translator()` helper available globally. 
The `t()` function accepts a key and an optional array of replacements, while `translator()` is a shortcut 
to access the translator directly.

---

## 🚀 Usage

>If you don't configure a language, the default language is English (`en`).

### 🔍 Translate

```php
echo t('welcome');
```

Assuming `app/Resources/lang/en.json` contains:

```json
{
  "welcome": "Welcome to our site!"
}
```

You’ll see:
`Welcome to our site!`

---

### ✨ With replacements

```php
echo t('greeting', ['name' => 'John']);
```

With this JSON entry:

```json
{
  "greeting": "Hello, :name!"
}
```

Result:
`Hello, John!`

---

### 🌍 Switch language

```php
use Naf\I18n\Support\Language;

t()->setLanguage(Language::DE);
```

Make sure `app/Resources/lang/de.json` exists.

#### Through query parameter

```php
/index.php?lang=de
```

> An event listener will set the language based on the query parameter within a cookie.


---

### 🔄 Fallback

If a key is missing, the key itself is returned:

```php
translator()->get('unknown_key');
// → "unknown_key"
```

This helps you spot missing translations during development.

---

## 📁 File structure

```
app/
└── Resources/
    └── lang/
        ├── en.json
        ├── de.json
        └── fr.json
```

Each file should be a flat key-value map using UTF-8 encoded JSON.

---

## ✅ Requirements

* `naf/framework` >= 0.1.0
* PHP >= 8.1

---

## 📄 License

MIT License.
