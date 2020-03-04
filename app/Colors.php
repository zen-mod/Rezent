<?php

namespace App;

use MyCLabs\Enum\Enum;

/**
 * @method static self CANCELED()
 * @method static self FAILED()
 * @method static self PASSED()
 * @method static self PENDING()
 */
class Colors extends Enum
{
    private const CANCELED = 10329501;
    private const FAILED = 14370117;
    private const PASSED = 3779158;
    private const PENDING = 15588927;
}
