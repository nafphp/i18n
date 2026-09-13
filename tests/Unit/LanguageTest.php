<?php

declare(strict_types=1);

namespace Tests\Unit;

use Naf\Core\Config;
use Naf\I18n\Support\Language;
use Tests\NafTestCase;
use function Naf\app;

class LanguageTest extends NafTestCase
{

    public function testAllReturnsLanguageCodesOnly()
    {
        $this->assertContains(Language::EN, Language::all());
        $this->assertContains(Language::DE, Language::all());

        foreach (Language::all() as $language) {
            $this->assertIsString($language);
            $this->assertMatchesRegularExpression('/^[a-z]{2}$/', $language);
        }
    }

    public function testLabelReturnsLocalizedLanguageName()
    {
        $this->assertSame('Deutsch', (new Language())->label(Language::DE));
    }

    public function testLabelReturnsDefaultForUnknownLanguage()
    {
        $this->assertSame('Unknown', (new Language())->label('xx', 'Unknown'));
    }

    public function testLabelFallsBackToEnglishLabel()
    {
        $this->assertSame('English', (new Language())->label('xx'));
    }

    public function testLabelsAreKeyedByLanguageCode()
    {
        $this->assertSame('English', Language::labels()[Language::EN]);
        $this->assertSame('Deutsch', Language::labels()[Language::DE]);
    }

    public function testOptionsAliasLabels()
    {
        $this->assertSame(Language::labels(), Language::options());
    }

    public function testIsSupportedAcceptsBaseAndRegionTags()
    {
        $this->assertTrue(Language::isSupported('de'));
        $this->assertTrue(Language::isSupported('de-DE'));
        $this->assertTrue(Language::isSupported('de_DE'));
        $this->assertFalse(Language::isSupported('xx'));
    }

    public function testNormalizeHandlesLocaleHeadersAndInvalidValues()
    {
        $this->assertSame('de', Language::normalize('de-DE,de;q=0.9'));
        $this->assertSame('de', Language::normalize('de_DE'));
        $this->assertSame('en', Language::normalize('../de'));
    }

    public function testNormalizeUsesValidatedFallbackLanguage()
    {
        app()->container()->set(Config::class, new Config([
            'fallback_language' => ' DE-de ',
        ]));

        $this->assertSame('de', Language::normalize('../de'));

        app()->container()->set(Config::class, new Config([
            'fallback_language' => '../de',
        ]));

        $this->assertSame('en', Language::normalize('../de'));
    }

    public function testNormalizeLanguageAliasStillWorks()
    {
        $this->assertSame(Language::normalize('de-DE'), Language::normalizeLanguage('de-DE'));
    }
}
