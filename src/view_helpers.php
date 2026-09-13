<?php

declare(strict_types=1);

namespace Naf\I18n;

use Naf\I18n\Core\Translator;
use function Naf\app;

/**
 * @param string $key
 * @param array  $params
 *
 * @return string
 */
function t(string $key, array $params = []): string
{
    return app()->container()->get(Translator::class)->translate($key, $params);
}

/**
 * @return Translator
 */
function translator(): Translator
{
    return app()->container()->get(Translator::class);
}

/**
 * @return string|null
 */
function lang(): ?string
{
    return app()->container()->get(Translator::class)->getLanguage();
}