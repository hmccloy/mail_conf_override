<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

(static function () {

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Core\Mail\MailMessage::class] = [
        'className' => \Extcode\MailConfOverride\Mail\MailMessage::class
    ];

    \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\Container\Container::class)
        ->registerImplementation(
            \TYPO3\CMS\Core\Mail\MailMessage::class,
            \Extcode\MailConfOverride\Mail\MailMessage::class
        );
})();
