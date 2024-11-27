<?php

namespace Motekar\LaravelPdf\Support;

use finfo;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\View\ViewException;
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

function inlinedImage($url, $classes = '')
{
    $url = Str::of($url)->trim("'")->trim('"')->value();

    if (! Str::of($url)->isUrl()) {
        try {
            $content = file_get_contents($url);
        } catch (\Exception $exception) {
            throw new ViewException('Image not found: '.$exception->getMessage());
        }
    } else {
        $response = Http::get($url);

        if (! $response->successful()) {
            throw new ViewException('Failed to fetch the image: '.$response->toException());
        }

        $content = $response->body();
    }

    $mime = (new finfo(FILEINFO_MIME_TYPE))->buffer($content) ?: 'image/png';

    return '<img class="'.$classes.'" src="data:'.$mime.';base64,'.base64_encode($content).'">';
}
