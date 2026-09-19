<?php

declare(strict_types=1);

use Naf\Core\Event;
use Naf\I18n\Core\Translator;
use Naf\I18n\Events\CookieSetListener;
use Naf\I18n\Events\LocaleListener;

use function Naf\app;
use function Naf\event;

app()->container()->set(Translator::class, fn() => new Translator());

event()->listen(Event::REQUEST_START, [LocaleListener::class, 'handle']);
event()->listen(Event::RESPONSE_HEADER, [CookieSetListener::class, 'handle']);
