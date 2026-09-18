<?php

declare(strict_types=1);

namespace Tests\Unit;

use LogicException;
use Naf\I18n\Core\Translator;
use Naf\I18n\Support\TranslationPathRegistry;
use Tests\NafTestCase;

use function Naf\I18n\translation_paths;

class TranslationPathRegistryTest extends NafTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        foreach (array_keys(translation_paths()->all()) as $id) {
            translation_paths()->remove($id);
        }
    }

    public function testTheHelperAlwaysReturnsTheSameRegistry()
    {
        $this->assertInstanceOf(TranslationPathRegistry::class, translation_paths());
        $this->assertSame(translation_paths(), translation_paths());
    }

    public function testRegisteredDirectoriesAreReadInIndexAndIdOrder()
    {
        translation_paths()->add('second', __DIR__ . '/../Fixtures/other-lang', 200);
        translation_paths()->add('first', __DIR__ . '/../Fixtures/plugin-lang', 100);

        $this->assertSame(['first', 'second'], array_keys(translation_paths()->all()));

        $translator = new Translator();
        $this->assertSame('the other plugin', $translator->translate('plugin-only'));
        $this->assertSame('only the other', $translator->translate('other-only'));
    }

    public function testTheApplicationWinsOverAPluginDirectory()
    {
        translation_paths()->add('plugin', __DIR__ . '/../Fixtures/plugin-lang');

        $translator = new Translator();

        $this->assertSame('translated', $translator->translate('translated'));
        $this->assertSame('only the plugin has this', $translator->translate('plugin-only'));
    }

    public function testAPluginCanOfferALanguageTheApplicationDoesNotHave()
    {
        translation_paths()->add('plugin', __DIR__ . '/../Fixtures/plugin-lang');

        $translator = new Translator();
        $translator->setLanguage('fr');

        $this->assertSame('traduit', $translator->translate('translated'));
    }

    public function testRegisteringLaterInvalidatesAnAlreadyLoadedTranslator()
    {
        $translator = new Translator();
        $this->assertSame('plugin-only', $translator->translate('plugin-only'));

        translation_paths()->add('plugin', __DIR__ . '/../Fixtures/plugin-lang');

        $this->assertSame('only the plugin has this', $translator->translate('plugin-only'));

        translation_paths()->remove('plugin');

        $this->assertSame('plugin-only', $translator->translate('plugin-only'));
    }

    public function testADuplicateIdNeedsAnExplicitReplacement()
    {
        translation_paths()->add('plugin', __DIR__ . '/../Fixtures/plugin-lang');

        $this->expectException(LogicException::class);
        translation_paths()->add('plugin', __DIR__ . '/../Fixtures/other-lang');
    }

    public function testRemovingAnUnknownIdIsFalse()
    {
        $this->assertFalse(translation_paths()->remove('never-registered'));
    }
}
