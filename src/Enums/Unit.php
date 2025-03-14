<?php

namespace Motekar\LaravelPdf\Enums;

enum Unit: string
{
    case Pixel = 'px';
    case Inch = 'in';
    case Centimeter = 'cm';
    case Millimeter = 'mm';
}
