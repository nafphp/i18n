<?php

declare(strict_types=1);

namespace Naf\I18n\Core;

use LogicException;
use Naf\I18n\Support\Language;
use Stringable;
use Throwable;
use function Naf\app;
use function Naf\config;
use function Naf\log;

class Translator
{
    private ?string $language;
    private array $data = [];

    /**
     * Initializes the Translator with an optional language and loads translation data.
     *
     * @param string|null $language The language code to use, or null to use default from config.
     *
     * @return void
     */
    public function __construct(?string $language = null)
    {
        $this->language = $language ? Language::normalize($language) : null;
        $this->reload();
    }

    /**
     * Reload translation files
     *
     * @return void
     */
    public function reload(): void
    {
        $this->language ??= Language::normalize(
            (string)(config('language') ?? config('fallback_language', Language::EN))
        );

        try {
            $this->data = $this->loadLanguageData($this->language);
        } catch (Throwable $t) {
            log()->info($t->getMessage());
            $this->data = [];
        }
    }

    /**
     * Translates the given key using the translation data and replaces placeholders with provided parameters.
     *
     * @param string $key    The key to translate.
     * @param array  $params An associative array of parameters for placeholder replacement.
     *
     * @return string The translated string with placeholders replaced.
     */
    public function translate(string $key, array $params = []): string
    {
        $result = $this->data[$key] ?? $key;

        if ($params === []) {
            return $result;
        }

        $replacements = [];

        foreach ($params as $k => $v) {
            if (!is_scalar($v) && !$v instanceof Stringable && $v !== null) {
                continue;
            }

            $replacements[':' . $k] = (string)$v;
        }

        return $replacements === [] ? $result : strtr($result, $replacements);
    }

    /**
     * Retrieves the language value.
     *
     * @return string|null The language value or null if not set.
     */
    public function getLanguage(): ?string
    {
        return $this->language;
    }

    /**
     * Sets the language value and reloads the related configuration.
     *
     * @param string $lang The language value to set.
     *
     * @return void
     */
    public function setLanguage(string $lang): void
    {
        $this->language = Language::normalize($lang);
        $this->reload();
    }

    /**
     * Loads the language data from the appropriate JSON file.
     *
     * Determines the language to load based on the current language value or configurations.
     * Reads the corresponding translation file, validates its contents, and sets the language data.
     * Throws an exception if the file is missing or its contents are invalid.
     *
     * @param string $lang The language code to load.
     *
     * @return array<string,string>
     * @throws LogicException If the language file is not found or contains invalid JSON.
     */
    private function loadLanguageData(string $lang): array
    {
        $filePath = app()->getBasePath() . config('app:translationPath', '/app/Resources/lang');
        $file     = sprintf('%s/%s.json', $filePath, $lang);

        if (!file_exists($file)) {
            throw new \LogicException('Language file not found: ' . $file);
        }

        $data = json_decode(file_get_contents($file), true);

        if (!is_array($data)) {
            throw new \LogicException('Invalid JSON in language file: ' . $file);
        }

        return $data;
    }

}
