<?php

namespace Motekar\LaravelPdf\Facades;

use Illuminate\Support\Facades\Facade;
use Motekar\LaravelPdf\FakePdfBuilder;
use Motekar\LaravelPdf\PdfFactory;

/**
 * @mixin \Motekar\LaravelPdf\PdfBuilder
 * @mixin \Motekar\LaravelPdf\FakePdfBuilder
 */
class Pdf extends Facade
{
    protected static function getFacadeAccessor()
    {
        return PdfFactory::class;
    }

    public static function fake()
    {
        $fake = new FakePdfBuilder;

        static::swap($fake);
    }
}
