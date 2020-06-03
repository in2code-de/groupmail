<?php
declare(strict_types=1);

namespace In2code\Groupmailer\Workflow;

class Workflow
{
    const STATE_DRAFT = 1;
    const STATE_REVIEW = 2;
    const STATE_APPROVED = 3;
    const STATE_REJECTED = 4;

    /**
     * @param int $state
     * @return bool
     */
    public static function isValidWorkflowState(int $state): bool
    {
        if ($state !== self::STATE_DRAFT &&
            $state !== self::STATE_REVIEW &&
            $state !== self::STATE_APPROVED &&
            $state !== self::STATE_REJECTED) {
            return false;
        }

        return true;
    }
}
