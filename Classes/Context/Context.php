<?php
declare(strict_types=1);

namespace In2code\Groupmailer\Context;

class Context
{
    public const FRONTEND = 'fe';

    public const BACKEND = 'be';

    /**
     * @param string $value
     * @return bool
     */
    public static function isContextValid(string $value): bool
    {
        if ($value !== self::FRONTEND && $value !== self::BACKEND) {
            return false;
        }

        return true;
    }
}
