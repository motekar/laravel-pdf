<?php

namespace Motekar\LaravelPdf\Support;

use Motekar\LaravelPdf\Facades\Pdf;
use Motekar\LaravelPdf\PdfBuilder;

function pdf(string $viewPath = '', array $data = []): PdfBuilder
{
    return Pdf::view($viewPath, $data);
}

function toInch(float $number, string $unit = 'mm'): float
{
    switch ($unit) {
        case 'in':
            return $number;
        case 'mm':
            return $number / 25.4;
        case 'cm':
            return $number / 2.54;
        case 'px':
            return $number / 96;
        default:
            throw new \InvalidArgumentException("Unsupported unit: $unit");
    }
}
