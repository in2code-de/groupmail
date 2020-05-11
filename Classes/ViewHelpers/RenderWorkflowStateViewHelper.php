<?php
declare(strict_types=1);

namespace In2code\In2bemail\ViewHelpers;

use In2code\In2bemail\Workflow\Workflow;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class RenderWorkflowStateViewHelper extends AbstractViewHelper
{
    public function initializeArguments()
    {
        $this->registerArgument('workflowState', 'int', 'the current workflow state', true);
    }

    /**
     * @return string
     */
    public function render(): string
    {
        $state = '';

        switch ($this->arguments['workflowState']) {
            case Workflow::STATE_DRAFT:
                $state = 'warning';
                break;
            case Workflow::STATE_REVIEW:
                $state = 'info';
                break;
            case Workflow::STATE_APPROVED:
                $state = 'success';
                break;
            case Workflow::STATE_REJECTED:
                $state = 'danger';
                break;
        }

        return $state;
    }
}
