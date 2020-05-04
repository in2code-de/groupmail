<?php
declare(strict_types=1);

namespace In2code\In2bemail\Utility;

class ConfigurationUtility
{
    /**
     * @return array
     */
    protected static function getConfiguration(): array
    {
        return $extensionConfiguration = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['in2bemail'];
    }

    /**
     * @return int
     */
    public static function getRecursionLevel(): int
    {
        $extConfiguration = self::getConfiguration();

        return (int)$extConfiguration['backendGroupRecursionLevel'];
    }

    /**
     * @return int
     */
    public static function getStoragePid(): int
    {
        $extConfiguration = self::getConfiguration();

        return (int)$extConfiguration['storagePid'];
    }

    /**
     * @return int
     */
    public static function getEmailProcessCount(): int
    {
        $extConfiguration = self::getConfiguration();

        return (int)$extConfiguration['emailProcessCount'];
    }
}
