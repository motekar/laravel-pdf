<?php

namespace Motekar\LaravelPdf\Support;

use Motekar\LaravelPdf\Facades\Pdf;
use Motekar\LaravelPdf\PdfBuilder;

function pdf(string $viewPath = '', array $data = []): PdfBuilder
{
    return Pdf::view($viewPath, $data);
}
