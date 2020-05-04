<?php
declare(strict_types=1);

namespace In2code\In2bemail\Service;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class AbstractService implements LoggerAwareInterface
{
    use LoggerAwareTrait;
}
