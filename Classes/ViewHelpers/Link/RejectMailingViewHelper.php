<?php
declare(strict_types=1);

namespace In2code\In2bemail\ViewHelpers\Link;

use In2code\In2bemail\Domain\Model\Mailing;
use In2code\In2bemail\Workflow\Workflow;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper;

class RejectMailingViewHelper extends AbstractTagBasedViewHelper
{
    /**
     * @var string
     */
    protected $tagName = 'a';

    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerUniversalTagAttributes();
        $this->registerArgument('uid', 'int', 'uid of the record that should deleted', true);
        $this->registerArgument(
            'returnUrl',
            'string',
            'return to this URL after deleting the content element',
            false,
            ''
        );
    }

    /**
     * @return string
     * @throws \TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException
     */
    public function render(): string
    {
        if (empty($this->arguments['returnUrl'])) {
            $this->arguments['returnUrl'] = GeneralUtility::getIndpEnv('REQUEST_URI');
        }

        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);

        $url = (string)$uriBuilder->buildUriFromRoute('tce_db', [
            'data' => [
                Mailing::TABLE => [
                    $this->arguments['uid'] => [
                        'workflow_state' => Workflow::STATE_REJECTED
                    ]
                ]
            ],
            'redirect' => $this->arguments['returnUrl']
        ]);

        $this->tag->addAttribute('href', $url);
        $this->tag->setContent($this->renderChildren());
        $this->tag->forceClosingTag(true);
        return $this->tag->render();
    }
}
