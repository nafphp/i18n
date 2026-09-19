<?php

declare(strict_types=1);

namespace Naf\I18n;

use Naf\I18n\Support\TranslationPathRegistry;

use function Naf\app;

if (!function_exists('Naf\I18n\translation_paths')) {
    /**
     * The registry of directories translations are read from.
     *
     * A plugin registers its own directory here; the application's directory is
     * always read last, so a host translation wins over a package's.
     */
    function translation_paths(): TranslationPathRegistry
    {
        $container = app()->container();

        if (!$container->has(TranslationPathRegistry::class)) {
            $container->set(TranslationPathRegistry::class, new TranslationPathRegistry());
        }

        return $container->get(TranslationPathRegistry::class);
    }
}
