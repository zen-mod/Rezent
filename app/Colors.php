<?php

namespace App;

use MyCLabs\Enum\Enum;

/**
 * @method static self BROKEN()
 * @method static self CANCELED()
 * @method static self FAILED()
 * @method static self FIXED()
 * @method static self PASSED()
 * @method static self PENDING()
 * @method static self STILL_FAILING()
 */
class Colors extends Enum
{
    private const BROKEN = 14370117;
    private const CANCELED = 10329501;
    private const FAILED = 14370117;
    private const FIXED = 3779158;
    private const PASSED = 3779158;
    private const PENDING = 15588927;
    private const STILL_FAILING = 14370117;
}
