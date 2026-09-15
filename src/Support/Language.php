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

    public const string EN = 'en';     // English
    public const string DE = 'de';     // German
    public const string FR = 'fr';     // French
    public const string ES = 'es';     // Spanish
    public const string IT = 'it';     // Italian
    public const string PT = 'pt';     // Portuguese
    public const string RU = 'ru';     // Russian
    public const string ZH = 'zh';     // Chinese (Mandarin)
    public const string JA = 'ja';     // Japanese
    public const string KO = 'ko';     // Korean
    public const string AR = 'ar';     // Arabic
    public const string HI = 'hi';     // Hindi
    public const string TR = 'tr';     // Turkish
    public const string PL = 'pl';     // Polish
    public const string NL = 'nl';     // Dutch
    public const string SV = 'sv';     // Swedish
    public const string CS = 'cs';     // Czech
    public const string RO = 'ro';     // Romanian
    public const string HU = 'hu';     // Hungarian
    public const string FA = 'fa';     // Persian (Farsi)
    public const string HE = 'he';     // Hebrew
    public const string UK = 'uk';     // Ukrainian
    public const string TH = 'th';     // Thai
    public const string VI = 'vi';     // Vietnamese

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
            $fallback = trim((string) (config('fallback_language', self::EN) ?? self::EN));

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
