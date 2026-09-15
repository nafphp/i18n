<?php

declare(strict_types=1);

namespace Tests\Unit;

use Naf\Core\Config;
use Naf\I18n\Core\Translator;
use Tests\NafTestCase;

use function Naf\app;
use function Naf\I18n\t;

class TranslatorTest extends NafTestCase
{
    public function testTranslationSuccess()
    {
        $translator = new Translator();
        $this->assertSame('translated', $translator->translate('translated'));
    }

    public function testTranslationMissingReturnsKey()
    {
        $translator = new Translator();
        $this->assertSame('does-not-exist', $translator->translate('does-not-exist'));
    }

    public function testTranslationWithDifferentLanguage()
    {
        $config = new Config(['language' => 'de', 'fallback_language' => 'en']);
        app()->container()->set(Config::class, $config);
        $translator = new Translator();
        $this->assertSame('übersetzt', $translator->translate('translated'));
    }

    public function testEmptyLanguageFileReturnsKey()
    {
        $config = new Config(['language' => 'es', 'fallback_language' => 'en']);
        app()->container()->set(Config::class, $config);
        $translator = new Translator();
        $this->assertSame('translated', $translator->translate('translated'));
        $this->assertSame('es', $translator->getLanguage());
    }

    public function testTranslationMissingLanguageFileDoesntThrowException()
    {
        $config = new Config(['language' => 'xx']);
        app()->container()->set(Config::class, $config);
        $translator = new Translator();
        $this->assertSame('translated', $translator->translate('translated'));
        $this->assertSame('xx', $translator->getLanguage());
    }

    public function testMissingLanguageFileKeepsSelectedLanguage()
    {
        $config = new Config(['language' => 'en', 'fallback_language' => 'en']);
        app()->container()->set(Config::class, $config);
        $translator = new Translator('xx');

        $this->assertSame('translated', $translator->translate('translated'));
        $this->assertSame('xx', $translator->getLanguage());
    }

    public function testTranslationWithParams()
    {
        $config = new Config(['language' => 'de']);
        app()->container()->set(Config::class, $config);
        $translator = new Translator();
        $this->assertSame('test output', $translator->translate('parameter', ['testKey' => 'output']));
    }

    public function testTranslationWithScalarParams()
    {
        $config = new Config(['language' => 'de']);
        app()->container()->set(Config::class, $config);
        $translator = new Translator();
        $this->assertSame('test 3', $translator->translate('parameter', ['testKey' => 3]));
    }

    public function testTranslationWithParamsAndMissingKey()
    {
        $config = new Config(['language' => 'de']);
        app()->container()->set(Config::class, $config);
        $translator = new Translator();
        $this->assertSame('test :testKey', $translator->translate('parameter', ['does-not-exist' => '']));
    }

    public function testHelperFunction()
    {
        $config = new Config(['language' => 'en']);
        app()->container()->set(Config::class, $config);
        $this->assertSame('translated', t('translated'));
    }
}
