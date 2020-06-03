<?php
declare(strict_types=1);

namespace In2code\Groupmailer\Service;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class AbstractService implements LoggerAwareInterface
{
    use LoggerAwareTrait;
}
