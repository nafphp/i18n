<?php

declare(strict_types=1);

namespace Tests\Unit;

use Naf\I18n\Core\Translator;
use Naf\I18n\Events\LocaleListener;
use Nyholm\Psr7\ServerRequest;
use Tests\NafTestCase;

use function Naf\app;

class LocaleListenerTest extends NafTestCase
{
    public function testQueryLanguageWinsOverCookieAndHeader()
    {
        $request = (new ServerRequest('GET', '/'))
            ->withQueryParams(['lang' => 'fr'])
            ->withCookieParams(['lang' => 'de'])
            ->withHeader('Accept-Language', 'es;q=1');

        (new LocaleListener())->handle($request);

        $this->assertSame('fr', app()->container()->get(Translator::class)->getLanguage());
    }

    public function testCookieLanguageWinsOverHeader()
    {
        $request = (new ServerRequest('GET', '/'))
            ->withCookieParams(['lang' => 'de'])
            ->withHeader('Accept-Language', 'fr;q=1');

        (new LocaleListener())->handle($request);

        $this->assertSame('de', app()->container()->get(Translator::class)->getLanguage());
    }

    public function testAcceptLanguageUsesHighestQualityValue()
    {
        $request = (new ServerRequest('GET', '/'))->withHeader('Accept-Language', 'fr;q=0.4, de;q=0.9, es;q=0.7');

        (new LocaleListener())->handle($request);

        $this->assertSame('de', app()->container()->get(Translator::class)->getLanguage());
    }

    public function testIgnoresRejectedAcceptLanguageValues()
    {
        $request = (new ServerRequest('GET', '/'))->withHeader('Accept-Language', 'fr;q=0, de;q=0.8');

        (new LocaleListener())->handle($request);

        $this->assertSame('de', app()->container()->get(Translator::class)->getLanguage());
    }

    public function testEmptyAcceptLanguageDoesNotChangeLanguage()
    {
        $request = (new ServerRequest('GET', '/'))->withHeader('Accept-Language', ', ;q=0.8,   ');

        (new LocaleListener())->handle($request);

        $this->assertSame('en', app()->container()->get(Translator::class)->getLanguage());
    }

    public function testFaviconRequestDoesNotChangeLanguage()
    {
        $request = (new ServerRequest('GET', '/favicon.ico'))
            ->withQueryParams(['lang' => 'de']);

        (new LocaleListener())->handle($request);

        $this->assertSame('en', app()->container()->get(Translator::class)->getLanguage());
    }

    public function testInvalidLanguageFallsBackToFallbackLanguage()
    {
        $request = (new ServerRequest('GET', '/'))->withQueryParams(['lang' => '../de']);

        (new LocaleListener())->handle($request);

        $this->assertSame('en', app()->container()->get(Translator::class)->getLanguage());
    }

    public function testRegionLanguageIsNormalizedToBaseLanguage()
    {
        $request = (new ServerRequest('GET', '/'))->withHeader('Accept-Language', 'de-DE,de;q=0.9');

        (new LocaleListener())->handle($request);

        $this->assertSame('de', app()->container()->get(Translator::class)->getLanguage());
    }
}
