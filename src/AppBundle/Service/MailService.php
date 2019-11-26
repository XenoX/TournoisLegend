<?php

namespace AppBundle\Service;

/**
 * Class MailService
 * @package AppBundle\Service
 */
class MailService
{
    /** @var \Swift_Mailer */
    protected $mailer;

    /** @var array */
    protected $website;

    /**
     * MailService constructor.
     *
     * @param \Swift_Mailer $mailer
     * @param array         $website
     */
    public function __construct(\Swift_Mailer $mailer, array $website)
    {
        $this->mailer = $mailer;
        $this->website = $website;
    }

    /**
     * @param array  $from
     * @param array  $to
     * @param string $subject
     * @param string $body
     */
    public function send(array $from = null, array $to = null, string $subject, string $body)
    {
        if (null === $from) {
            $from['email'] = $this->website['noreply_email'];
            $from['name'] = $this->website['name'];
        }

        if (null === $to) {
            $to[0]['email'] = $this->website['email'];
            $to[0]['name'] = $this->website['name'];
        }

        /** @var \Swift_Message $message */
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom([$from['email'] => $from['name']])
            ->setTo([$to[0]['email'] => $to[0]['name']])
            ->setBody($body, 'text/html')
        ;

        $this->mailer->send($message);
    }
}
