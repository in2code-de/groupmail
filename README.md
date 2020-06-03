# TYPO3 Extension `groupmailer`

## Usage

### Generate Mailing via MailService

#### Parameter for generateMailing:

- $backendGroups [array]: an array with backend groups
- $subject [string]: the email subject. The max length is currently 255. This can be increased via the TCA
- $bodytext [string]: the email content
- optional $senderEmail [string]: the sender email. If not defined the fallback `$GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress']` is used
- optional $senderName [string]: the sender name. If not defined the fallback `$GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromName']` is used
- optional $mailFormat [string]: default `FluidEmail::FORMAT_BOTH` valid options are `FluidEmail::FORMAT_BOTH`,  `FluidEmail::FORMAT_HTML` or `FluidEmail::FORMAT_PLAIN`
- optional $context [string]: default `Context::FRONTEND` valid options are `Context::FRONTEND`,  `Context::BACKEND`
- optional $workflowState [int]: default `Workflow::STATE_DRAFT` valid options are `Workflow::STATE_DRAFT`, `Workflow::STATE_REVIEW`, `Workflow::STATE_APPROVED`, `Workflow::STATE_REJECTED`
- optional $attachments [array]: an array with SysFile Objects

#### How to use the mailService

```php
        $beGroupRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Domain\Repository\BackendUserGroupRepository::class);
        $beGroups = [
            $beGroupRepository->findByUid(2),
            $beGroupRepository->findByUid(4)
        ];
        
        $fileRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Resource\FileRepository::class);
        $attachments = [
            $fileRepository->findByUid(2)
        ];

        $mailService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\In2code\Groupmailer\Service\MailService::class);
        $mailService->generateMailing(
            $beGroups,
            'Betreff',
            'Inhalt',
            'sender@in2code.de',
            'Sender Name',
            \TYPO3\CMS\Core\Mail\FluidEmail::FORMAT_PLAIN,
            \In2code\Groupmailer\Context\Context::BACKEND,
            \In2code\Groupmailer\Workflow\Workflow::STATE_DRAFT,
            $attachments
        );
```

### Generate the mail queue 

The mail queue can be generated via the `groupmailer:generateMailQueue` command.

```
./vendor/bin/typo3 groupmailer:generateMailQueue
```

This can be automated via an Scheduler task.

### Process the mail queue (sends the actual mails)

The mail queue can be generated via the `groupmailer:processMailQueue` command.

```
./vendor/bin/typo3 groupmailer:processMailQueue
```

This can be automated via an Scheduler task.

## Configuration

This extension can be configured in the "Extension Configuration" (Backend Module 'Settings' -> "Extension Configuration")

### Configuration options

- Storage Pid [integer]: Sets the PID on which the records are stored
- Emails to sent per execution [integer]: Defines how many mails should be sent on one execution of the process mail queue command
- Recursion Level [integer]: Defines the recursion level for the given backend user groups in a mailing

### Workflow

- DRAFT: mailings with this status will be ignored if mail queue entries will be generated
- REVIEW: mailings with this status will be ignored if mail queue entries will be generated
- REJECTED: mailings with this status will be moved into locked mailings and no mail queue entries will be generated
- APPROVED: queue entries will be generated on the next execution of the generateMailQueue command
