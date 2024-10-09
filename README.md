# Create PDFs in Laravel apps

[![Latest Version on Packagist](https://img.shields.io/packagist/v/motekar/laravel-pdf.svg?style=flat-square)](https://packagist.org/packages/motekar/laravel-pdf)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/motekar/laravel-pdf/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/motekar/laravel-pdf/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/motekar/laravel-pdf/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/motekar/laravel-pdf/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/motekar/laravel-pdf.svg?style=flat-square)](https://packagist.org/packages/motekar/laravel-pdf)

This package provides a simple way to create PDFs in Laravel apps. Under the hood it uses [Chromium](https://www.chromium.org/chromium-projects/) to generate PDFs from Blade views. You can use modern CSS features like grid and flexbox to create beautiful PDFs.

_Unlike [spatie/laravel-pdf](https://github.com/spatie/laravel-pdf) this package does not require **nodejs** and **puppeteer**, it uses [chrome-php/chrome](https://github.com/chrome-php/chrome) instead._

Here's a quick example:

```php
use Motekar\LaravelPdf\Facades\Pdf;

Pdf::view('pdfs.invoice', ['invoice' => $invoice])
    ->format('a4')
    ->save('invoice.pdf')
```

This will render the Blade view `pdfs.invoice` with the given data and save it as a PDF file.

You can also return the PDF as a response from your controller:

```php
use Motekar\LaravelPdf\Facades\Pdf;

class DownloadInvoiceController
{
    public function __invoke(Invoice $invoice)
    {
        return Pdf::view('pdfs.invoice', ['invoice' => $invoice])
            ->format('a4')
            ->name('your-invoice.pdf');
    }
}
```

You can use also test your PDFs:

```php
use Motekar\LaravelPdf\Facades\Pdf;

it('can render an invoice', function () {
    Pdf::fake();

    $invoice = Invoice::factory()->create();

    $this->get(route('download-invoice', $invoice))
        ->assertOk();

    Pdf::assertRespondedWithPdf(function (PdfBuilder $pdf) {
        return $pdf->contains('test');
    });
});
```

## Documentation

### Installation

You can install the package via composer:

```bash
composer require motekar/laravel-pdf
```

Under the hood this package uses [Google Chrome](https://www.google.com/chrome/) or [Chromium](https://www.chromium.org/chromium-projects/) to generate PDFs. You'll need to install any of these browsers on your system.

### Usage

This package supports almost every feature of Spatie's Laravel PDF package. For detailed usage documentation, we recommend referring to the comprehensive guide available on [Spatie's documentation site](https://spatie.be/docs/laravel-pdf). The usage patterns and methods are largely compatible, allowing you to leverage the extensive documentation provided by Spatie for this package as well.

## Testing

For running the testsuite, you'll need the `pdftotext` CLI which is part of the poppler-utils package. More info can be found in in the [spatie/pdf-to-text readme](https://github.com/spatie/pdf-to-text?tab=readme-ov-file#requirements). Usually `brew install poppler` will suffice.

Finally run the tests with:

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Fauzie Rofi](https://github.com/fauzie811)
- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
