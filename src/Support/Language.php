<?php

declare(strict_types=1);

namespace Naf\I18n\Support;

use ReflectionClass;
use function Naf\config;

/**
 * Represents supported languages using ISO 639-1 codes.
 * Extend this class as needed to support more locales.
 */
class Language
{
    private static array $cache = [];

    const string EN = 'en';     // English
    const string DE = 'de';     // German
    const string FR = 'fr';     // French
    const string ES = 'es';     // Spanish
    const string IT = 'it';     // Italian
    const string PT = 'pt';     // Portuguese
    const string RU = 'ru';     // Russian
    const string ZH = 'zh';     // Chinese (Mandarin)
    const string JA = 'ja';     // Japanese
    const string KO = 'ko';     // Korean
    const string AR = 'ar';     // Arabic
    const string HI = 'hi';     // Hindi
    const string TR = 'tr';     // Turkish
    const string PL = 'pl';     // Polish
    const string NL = 'nl';     // Dutch
    const string SV = 'sv';     // Swedish
    const string CS = 'cs';     // Czech
    const string RO = 'ro';     // Romanian
    const string HU = 'hu';     // Hungarian
    const string FA = 'fa';     // Persian (Farsi)
    const string HE = 'he';     // Hebrew
    const string UK = 'uk';     // Ukrainian
    const string TH = 'th';     // Thai
    const string VI = 'vi';     // Vietnamese

    private const array LABELS = [
        self::EN => 'English',
        self::DE => 'Deutsch',
        self::FR => 'Français',
        self::ES => 'Español',
        self::IT => 'Italiano',
        self::PT => 'Português',
        self::RU => 'Русский',
        self::ZH => '中文',
        self::JA => '日本語',
        self::KO => '한국어',
        self::AR => 'العربية',
        self::HI => 'हिन्दी',
        self::TR => 'Türkçe',
        self::PL => 'Polski',
        self::NL => 'Nederlands',
        self::SV => 'Svenska',
        self::CS => 'Čeština',
        self::RO => 'Română',
        self::HU => 'Magyar',
        self::FA => 'فارسی',
        self::HE => 'עברית',
        self::UK => 'Українська',
        self::TH => 'ไทย',
        self::VI => 'Tiếng Việt',
    ];

    public static function normalize(string $tag): string
    {
        $tag = trim($tag);

        if (str_contains($tag, ',')) {
            $tag = explode(',', $tag, 2)[0];
        }

        $tag  = str_replace('_', '-', $tag);
        $tag  = explode(';', $tag, 2)[0];
        $base = strtolower(explode('-', $tag, 2)[0]);

        if (!preg_match('/^[a-z]{2,3}$/', $base)) {
            $fallback = trim((string)(config('fallback_language', self::EN) ?? self::EN));

            if (str_contains($fallback, ',')) {
                $fallback = explode(',', $fallback, 2)[0];
            }

            $fallback = str_replace('_', '-', $fallback);
            $fallback = explode(';', $fallback, 2)[0];
            $base     = strtolower(explode('-', $fallback, 2)[0]);

            return preg_match('/^[a-z]{2,3}$/', $base) ? $base : self::EN;
        }

        return $base;
    }

    public static function normalizeLanguage(string $tag): string
    {
        return self::normalize($tag);
    }

    public function label(string $language, ?string $default = null): string
    {
        return self::LABELS[$language] ?? $default ?? self::LABELS[self::EN];
    }

    /**
     * @return array<string,string>
     */
    public static function labels(): array
    {
        return self::LABELS;
    }

    /**
     * @return array<string,string>
     */
    public static function options(): array
    {
        return self::labels();
    }

    public static function isSupported(string $language): bool
    {
        return in_array(self::normalize($language), self::all(), true);
    }

    /**
     * @return string[]
     */
    public static function all(): array
    {
        $class = static::class;

        if (isset(self::$cache[$class])) {
            return self::$cache[$class];
        }

        $ref = new ReflectionClass($class);
        return self::$cache[$class] = array_values(array_filter($ref->getConstants(), 'is_string'));
    }
}
