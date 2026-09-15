<?php

declare(strict_types=1);

namespace Naf\I18n\Events;

use Naf\I18n\Core\Translator;
use Naf\I18n\Support\Language;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

use function Naf\app;
use function Naf\log;
use function Naf\request;

class CookieSetListener
{
    /**
     * Handles the response by setting a language cookie if necessary.
     *
     * Sets a language cookie when:
     * - A 'lang' query parameter is present in the request, OR
     * - No 'lang' cookie exists yet
     *
     * The cookie is set with a 30-day expiration and SameSite=Lax policy.
     *
     * @param ResponseInterface $response The HTTP response to modify.
     *
     * @return ResponseInterface|null The modified response with Set-Cookie header, or null if no cookie needs to be set.
     */
    public function handle(ResponseInterface $response): ?ResponseInterface
    {
        $translator = app()->container()->get(Translator::class);
        $lang       = $translator->getLanguage();

        if (!$lang || !app()->container()->has(RequestInterface::class)) {
            return null;
        }

        $request = request();
        $lang    = Language::normalize($lang);

        $queryLang  = $request->getQueryParams()['lang'] ?? null;
        $cookieLang = $request->getCookieParams()['lang'] ?? null;

        if ($queryLang || !$cookieLang) {
            log()->info('Setting cookie lang: ' . $lang);

            return $response->withAddedHeader(
                'Set-Cookie',
                sprintf(
                    'lang=%s; Path=/; Max-Age=%d; SameSite=Lax',
                    rawurlencode($lang),
                    60 * 60 * 24 * 30,
                ),
            );
        }

        return null;
    }
}
