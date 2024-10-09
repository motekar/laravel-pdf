<?php

namespace Motekar\LaravelPdf;

use HeadlessChromium\BrowserFactory;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Motekar\LaravelPdf\Enums\Format;
use Motekar\LaravelPdf\Enums\Orientation;
use Motekar\LaravelPdf\Enums\Unit;

use function Motekar\LaravelPdf\Support\toInch;

class PdfBuilder implements Responsable
{
    const TIMEOUT = 60 * 1000;

    public string $viewName = '';

    public array $viewData = [];

    public string $html = '';

    public string $headerViewName = '';

    public array $headerData = [];

    public ?string $headerHtml = null;

    public string $footerViewName = '';

    public array $footerData = [];

    public ?string $footerHtml = null;

    public string $downloadName = '';

    public ?array $paperSize = null;

    public ?string $orientation = null;

    public ?array $margins = null;

    protected string $visibility = 'private';

    protected array $responseHeaders = [
        'Content-Type' => 'application/pdf',
    ];

    protected bool $onLambda = false;

    protected ?string $diskName = null;

    public function view(string $view, array $data = []): self
    {
        $this->viewName = $view;

        $this->viewData = $data;

        return $this;
    }

    public function headerView(string $view, array $data = []): self
    {
        $this->headerViewName = $view;

        $this->headerData = $data;

        return $this;
    }

    public function footerView(string $view, array $data = []): self
    {
        $this->footerViewName = $view;

        $this->footerData = $data;

        return $this;
    }

    public function landscape(): self
    {
        return $this->orientation(Orientation::Landscape);
    }

    public function portrait(): self
    {
        return $this->orientation(Orientation::Portrait);
    }

    public function orientation(string|Orientation $orientation): self
    {
        if ($orientation instanceof Orientation) {
            $orientation = $orientation->value;
        }

        $this->orientation = $orientation;

        return $this;
    }

    public function inline(string $downloadName = ''): self
    {
        $this->name($downloadName);

        $this->addHeaders([
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$this->downloadName.'"',
        ]);

        return $this;
    }

    public function html(string $html): self
    {
        $this->html = $html;

        return $this;
    }

    public function headerHtml(string $html): self
    {
        $this->headerHtml = $html;

        return $this;
    }

    public function footerHtml(string $html): self
    {
        $this->footerHtml = $html;

        return $this;
    }

    public function download(?string $downloadName = null): self
    {
        $this->downloadName ?: $this->name($downloadName ?? 'download');

        $this->addHeaders([
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$this->downloadName.'"',
        ]);

        return $this;
    }

    public function headers(array $headers): self
    {
        $this->addHeaders($headers);

        return $this;
    }

    public function name(string $downloadName): self
    {
        if (! str_ends_with(strtolower($downloadName), '.pdf')) {
            $downloadName .= '.pdf';
        }

        $this->downloadName = $downloadName;

        return $this;
    }

    public function base64(): string
    {
        return $this
            ->generatePdf(true);
    }

    public function margins(
        float $top = 0,
        float $right = 0,
        float $bottom = 0,
        float $left = 0,
        Unit|string $unit = 'mm'
    ): self {
        if ($unit instanceof Unit) {
            $unit = $unit->value;
        }

        $this->margins = compact(
            'top',
            'right',
            'bottom',
            'left',
            'unit',
        );

        return $this;
    }

    public function format(Format $format): self
    {
        $this->paperSize = $format->toPaperSize();

        return $this;
    }

    public function paperSize(float $width, float $height, Unit|string $unit = 'mm'): self
    {
        if ($unit instanceof Unit) {
            $unit = $unit->value;
        }

        $this->paperSize = compact(
            'width',
            'height',
            'unit',
        );

        return $this;
    }

    public function save(string $path): self
    {
        if ($this->diskName) {
            return $this->saveOnDisk($this->diskName, $path);
        }

        File::put($path, $this->generatePdf());

        return $this;
    }

    public function disk(string $diskName, string $visibility = 'private'): self
    {
        $this->diskName = $diskName;
        $this->visibility = $visibility;

        return $this;
    }

    protected function saveOnDisk(string $diskName, string $path): self
    {
        $pdfContent = $this->generatePdf();
        $visibility = $this->visibility;

        Storage::disk($diskName)->put($path, $pdfContent, $visibility);

        return $this;
    }

    protected function getHtml(): string
    {
        if ($this->viewName) {
            $this->html = view($this->viewName, $this->viewData)->render();
        }

        if ($this->html) {
            return $this->html;
        }

        return '&nbsp';
    }

    protected function getHeaderHtml(): ?string
    {
        if ($this->headerViewName) {
            $this->headerHtml = view($this->headerViewName, $this->headerData)->render();
        }

        if ($this->headerHtml) {
            return $this->headerHtml;
        }

        return null;
    }

    protected function getFooterHtml(): ?string
    {
        if ($this->footerViewName) {
            $this->footerHtml = view($this->footerViewName, $this->footerData)->render();
        }

        if ($this->footerHtml) {
            return $this->footerHtml;
        }

        return null;
    }

    protected function getAllHtml(): string
    {
        return implode(PHP_EOL, [
            $this->getHeaderHtml(),
            $this->getHtml(),
            $this->getFooterHtml(),
        ]);
    }

    public function generatePdf(bool $base64 = false): string
    {
        $tempPath = tempnam(sys_get_temp_dir(), Str::random());

        $factory = new BrowserFactory();
        $browser = $factory->createBrowser([
            'noSandbox' => true,
        ]);

        try {
            $page = $browser->createPage();
            $page->setHtml($this->getHtml());

            $options = [
                'printBackground' => true,
                'preferCSSPageSize' => true,
            ];

            $headerHtml = $this->getHeaderHtml();

            $footerHtml = $this->getFooterHtml();

            if ($headerHtml || $footerHtml) {
                $options['displayHeaderFooter'] = true;

                $options['headerTemplate'] = $headerHtml ?? '<p></p>';
                $options['footerTemplate'] = $footerHtml ?? '<p></p>';
            }

            if ($this->margins) {
                $options['marginTop'] = toInch($this->margins['top'], $this->margins['unit']);
                $options['marginRight'] = toInch($this->margins['right'], $this->margins['unit']);
                $options['marginBottom'] = toInch($this->margins['bottom'], $this->margins['unit']);
                $options['marginLeft'] = toInch($this->margins['left'], $this->margins['unit']);
            }

            if ($this->paperSize) {
                $options['paperWidth'] = toInch($this->paperSize['width'], $this->paperSize['unit']);
                $options['paperHeight'] = toInch($this->paperSize['height'], $this->paperSize['unit']);
            }

            if ($this->orientation === Orientation::Landscape->value) {
                $options['landscape'] = true;
            }

            $pdf = $base64
                ? $page->pdf($options)->getBase64(static::TIMEOUT)
                : $page->pdf($options)->saveToFile($tempPath, static::TIMEOUT);
        } finally {
            $browser->close();
        }

        return $base64 ? $pdf : File::get($tempPath);
    }

    public function toResponse($request): Response
    {
        if (! $this->hasHeader('Content-Disposition')) {
            $this->inline($this->downloadName);
        }

        $pdfContent = $this->generatePdf();

        return response($pdfContent, 200, $this->responseHeaders);
    }

    protected function addHeaders(array $headers): self
    {
        $this->responseHeaders = array_merge($this->responseHeaders, $headers);

        return $this;
    }

    protected function hasHeader(string $headerName): bool
    {
        return array_key_exists($headerName, $this->responseHeaders);
    }

    public function isInline(): bool
    {
        if (! $this->hasHeader('Content-Disposition')) {
            return false;
        }

        return str_contains($this->responseHeaders['Content-Disposition'], 'inline');
    }

    public function isDownload(): bool
    {
        if (! $this->hasHeader('Content-Disposition')) {
            return false;
        }

        return str_contains($this->responseHeaders['Content-Disposition'], 'attachment');
    }

    public function contains(string|array $text): bool
    {
        if (is_string($text)) {
            $text = [$text];
        }

        $html = $this->getAllHtml();

        foreach ($text as $singleText) {
            if (str_contains($html, $singleText)) {
                return true;
            }
        }

        return false;
    }
}
