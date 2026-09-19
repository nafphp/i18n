<?php

declare(strict_types=1);

namespace Tests\Unit;

use Naf\I18n\Core\Translator;
use Naf\I18n\Events\CookieSetListener;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use Psr\Http\Message\RequestInterface;
use Tests\NafTestCase;

use function Naf\app;

class CookieSetListenerTest extends NafTestCase
{
    public function testReturnsNullWithoutRequest()
    {
        app()->container()->get(Translator::class)->setLanguage('en');

        $this->assertNull((new CookieSetListener())->handle(new Response()));
    }

    public function testSetsCookieWhenQueryLanguageIsPresent()
    {
        app()->container()->get(Translator::class)->setLanguage('de');
        app()->container()->set(RequestInterface::class, (new ServerRequest('GET', '/'))->withQueryParams(['lang' => 'de']));

        $response = (new CookieSetListener())->handle(new Response());

        $this->assertNotNull($response);
        $this->assertStringContainsString('lang=de;', $response->getHeaderLine('Set-Cookie'));
        $this->assertStringContainsString('Path=/;', $response->getHeaderLine('Set-Cookie'));
        $this->assertStringContainsString('SameSite=Lax', $response->getHeaderLine('Set-Cookie'));
    }

    public function testSetsCookieWhenNoCookieExists()
    {
        app()->container()->get(Translator::class)->setLanguage('de');
        app()->container()->set(RequestInterface::class, new ServerRequest('GET', '/'));

        $response = (new CookieSetListener())->handle(new Response());

        $this->assertNotNull($response);
        $this->assertStringContainsString('lang=de;', $response->getHeaderLine('Set-Cookie'));
    }

    public function testReturnsNullWhenCookieAlreadyExists()
    {
        app()->container()->get(Translator::class)->setLanguage('de');
        app()->container()->set(RequestInterface::class, (new ServerRequest('GET', '/'))->withCookieParams(['lang' => 'de']));

        $this->assertNull((new CookieSetListener())->handle(new Response()));
    }
}
