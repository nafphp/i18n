<?php

declare(strict_types=1);

namespace Tests;

use Naf\Core\Config;
use Naf\I18n\Core\Translator;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

use function Naf\app;

class NafTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        app()->container()->set(Config::class, new Config([
            'language'          => 'en',
            'fallback_language' => 'en',
        ]));
        app()->container()->set(Translator::class, fn() => new Translator());
        app()->container()->reset(RequestInterface::class);
    }
}
