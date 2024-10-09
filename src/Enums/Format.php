<?php

namespace Motekar\LaravelPdf\Enums;

enum Format: string
{
    case Letter = 'letter';
    case Legal = 'legal';
    case Tabloid = 'tabloid';
    case Ledger = 'ledger';
    case A0 = 'a0';
    case A1 = 'a1';
    case A2 = 'a2';
    case A3 = 'a3';
    case A4 = 'a4';
    case A5 = 'a5';
    case A6 = 'a6';

    public function toPaperSize(): array
    {
        return match ($this) {
            self::Letter => ['width' => 8.5, 'height' => 11, 'unit' => 'in'],
            self::Legal => ['width' => 8.5, 'height' => 14, 'unit' => 'in'],
            self::Tabloid => ['width' => 11, 'height' => 17, 'unit' => 'in'],
            self::Ledger => ['width' => 17, 'height' => 11, 'unit' => 'in'],
            self::A0 => ['width' => 841, 'height' => 1189, 'unit' => 'mm'],
            self::A1 => ['width' => 594, 'height' => 841, 'unit' => 'mm'],
            self::A2 => ['width' => 420, 'height' => 594, 'unit' => 'mm'],
            self::A3 => ['width' => 297, 'height' => 420, 'unit' => 'mm'],
            self::A4 => ['width' => 210, 'height' => 297, 'unit' => 'mm'],
            self::A5 => ['width' => 148, 'height' => 210, 'unit' => 'mm'],
            self::A6 => ['width' => 105, 'height' => 148, 'unit' => 'mm'],
        };
    }
}
