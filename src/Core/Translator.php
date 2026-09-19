<?php

declare(strict_types=1);

namespace Naf\I18n\Core;

use LogicException;
use Naf\I18n\Support\Language;
use Stringable;
use Throwable;

use function Naf\app;
use function Naf\config;
use function Naf\I18n\translation_paths;
use function Naf\log;

class Translator
{
    private ?string $language;
    private array $data       = [];
    private int $pathRevision = -1;

    /** Load translations for the supplied language, or the configured default. */
    public function __construct(?string $language = null)
    {
        $this->language = $language ? Language::normalize($language) : null;
        $this->reload();
    }

    /** Reload translations, logging unreadable or invalid language files. */
    public function reload(): void
    {
        $this->language ??= Language::normalize(
            (string) (config('language') ?? config('fallback_language', Language::EN)),
        );

        try {
            $this->data = $this->loadLanguageData($this->language);
        } catch (Throwable $exception) {
            log()->info($exception->getMessage());
            $this->data = [];
        }

        $this->pathRevision = translation_paths()->revision();
    }

    /** Replace placeholders with scalar or stringable parameter values. */
    public function translate(string $key, array $params = []): string
    {
        // A directory registered or removed after this translator was built
        // changes what the answer should be, so the data is read again.
        if ($this->pathRevision !== translation_paths()->revision()) {
            $this->reload();
        }

        $result = $this->data[$key] ?? $key;

        if ($params === []) {
            return $result;
        }

        $replacements = [];

        foreach ($params as $placeholder => $value) {
            if (!is_scalar($value) && !$value instanceof Stringable && $value !== null) {
                continue;
            }

            $replacements[':' . $placeholder] = (string) $value;
        }

        return $replacements === [] ? $result : strtr($result, $replacements);
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    /** Normalize the language and reload its translations. */
    public function setLanguage(string $lang): void
    {
        $this->language = Language::normalize($lang);
        $this->reload();
    }

    /**
     * @return array<string,string>
     * @throws LogicException If the language file is missing or contains invalid JSON.
     */
    private function loadLanguageData(string $lang): array
    {
        $directories = [
            ...array_values(translation_paths()->all()),
            app()->getBasePath() . config('app:translationPath', '/app/Resources/lang'),
        ];

        $data  = [];
        $found = false;

        foreach ($directories as $directory) {
            $file = sprintf('%s/%s.json', rtrim($directory, '/\\'), $lang);

            if (!file_exists($file)) {
                continue;
            }

            $decoded = json_decode(file_get_contents($file), true);

            if (!is_array($decoded)) {
                throw new LogicException('Invalid JSON in language file: ' . $file);
            }

            // The application's directory is read last, so its wording wins.
            $data  = [...$data, ...$decoded];
            $found = true;
        }

        if (!$found) {
            throw new LogicException('Language file not found for: ' . $lang);
        }

        return $data;
    }
}
