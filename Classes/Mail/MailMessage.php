<?php

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace Extcode\MailConfOverride\Mail;

use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use TYPO3\CMS\Core\Mail\Mailer;
use TYPO3\CMS\Core\Mail\TransportFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Adapter for Symfony Mime to be used by TYPO3 extensions, also provides
 * some backwards-compatibility for previous TYPO3 installations where
 * send() was baked into the MailMessage object.
 */
class MailMessage extends \TYPO3\CMS\Core\Mail\MailMessage
{
    /**
     * @var \Swift_Transport
     */
    protected $transport;

    /**
     * @var Mailer
     */
    protected $mailer;

    /**
     * TRUE if the message has been sent.
     *
     * @var bool
     */
    protected $sent = false;

    private function initializeMailer(): void
    {
        $this->injectMailSettings();
        $this->mailer = GeneralUtility::makeInstance(Mailer::class);
    }

    /**
     * Sends the message.
     *
     * This is a short-hand method. It is however more useful to create
     * a Mailer instance which can be used via Mailer->send($message);
     *
     * @return bool whether the message was accepted or not
     */
    public function send(): bool
    {
        $this->initializeMailer();
        $this->sent = false;
        $this->mailer->send($this);
        $sentMessage = $this->mailer->getSentMessage();
        if ($sentMessage) {
            $this->sent = true;
        }
        return $this->sent;
    }

    /**
     * Overrride mail settings depending on the sender's address
     *
     * @internal
     */
    public function injectMailSettings()
    {
        if (is_array($this->getFrom()) && ($this->getFrom()[0] instanceof \Symfony\Component\Mime\Address)) {
            $from = $this->getFrom()[0]->getAddress();
        }

        if (!empty($GLOBALS['TYPO3_CONF_VARS']['MAIL']['overrides'][$from]) && is_array($GLOBALS['TYPO3_CONF_VARS']['MAIL']['overrides'][$from])) {
            $GLOBALS['TYPO3_CONF_VARS']['MAIL'] = array_replace(
                (array)$GLOBALS['TYPO3_CONF_VARS']['MAIL'],
                (array)$GLOBALS['TYPO3_CONF_VARS']['MAIL']['overrides'][$from]
            );
            unset($GLOBALS['TYPO3_CONF_VARS']['MAIL']['overrides']);
        }
    }
}
