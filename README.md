# TYPO3 Extension `in2bemail`

## Usage

### Generate Mailing via MailService

#### Parameter for generateMailing:

- $backendGroups [array]: an array with backend groups
- $subject [string]: the email subject. The max length is currently 255. This can be increased via the TCA
- $bodytext [string]: the email content
- optional $senderEmail [string]: the sender email. If not defined the fallback `$GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress']` is used
- optional $senderName [string]: the sender name. If not defined the fallback `$GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromName']` is used
- optional $mailFormat [string]: default `FluidEmail::FORMAT_BOTH` valid options are `FluidEmail::FORMAT_BOTH`,  `FluidEmail::FORMAT_HTML` or `FluidEmail::FORMAT_PLAIN`
- optional $attachments [array]: not implemented jet

#### How to use the mailService

```php
        $beGroupRepository = GeneralUtility::makeInstance(BackendUserGroupRepository::class);
        $beGroups = [
            $beGroupRepository->findByUid(2),
            $beGroupRepository->findByUid(4)
        ];

        $mailService = GeneralUtility::makeInstance(MailService::class);
        $mailService->generateMailing(
            $beGroups,
            'Betreff',
            'Inhalt',
            'sender@in2code.de',
            'Sender Name',
            FluidEmail::FORMAT_PLAIN
        );
```

### Generate the mail queue 

The mail queue can be generated via the `in2bemail:generateMailQueue` command.

```
./vendor/bin/typo3 in2bemail:generateMailQueue
```

This can be automated via an Scheduler task.

### Process the mail queue (sends the actual mails)

The mail queue can be generated via the `in2bemail:processMailQueue` command.

```
./vendor/bin/typo3 in2bemail:processMailQueue
```

This can be automated via an Scheduler task.

## Configuration

This extension can be configured in the "Extension Configuration" (Backend Module 'Settings' -> "Extension Configuration")

### Configuration options

- Storage Pid [integer]: Sets the PID on which the records are stored
- Emails to sent per execution [integer]: Defines how many mails should be sent on one execution of the process mail queue command
- Recursion Level [integer]: Defines the recursion level for the given backend user groups in a mailing
