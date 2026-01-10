<?php

namespace App\Core\Mailer;

use Nette;
use Nette\Mail\SendmailMailer;

/**
 * Service for creating and sending simple emails
 * 
 * After the mail is sent these parameters will be cleared:
 * - Recipient
 * - Subject
 * - Parameters
 * - Template
 * 
 * Usage:
 * First set Recipinet (setRecipient)
 * Then set From (setFrom)
 * Then add Subject (setSubject)
 * After that set Template for the email (setTemplate)
 * And then add Parameters (addParameter) or set all Parameters at once (setParameters)
 * Finally send the email (send)
 */
final class MailService
{

	private $linkGenerator;
	private $templateFactory;
    private SendmailMailer $mailer;

	private $params = [];
	private $template;
	private $emailRecipient = "Not set";
	private $subject = "Not set";
	private $sender = "Not set";
	private $name = "Not set";

	public function __construct(
		Nette\Application\LinkGenerator $linkGenerator,
		Nette\Bridges\ApplicationLatte\TemplateFactory $templateFactory,
	)
	{
		$this->linkGenerator = $linkGenerator;
		$this->templateFactory = $templateFactory;
        $this->mailer = new SendmailMailer();
	}

	/**
	 * Adds a single parameter
	 *
	 * @param string $name
	 * @param mixed $value
	 * @return void
	 */
	public function addParam(string $name, mixed $value): void
	{
		$this->params[$name] = $value;
	}

	/**
	 * Sets all parameters at once
	 *
	 * @param array $params
	 * @return void
	 */
	public function setParams(array $params): void
	{
		foreach ($params as $name => $value) {
			$this->params[$name] = $value;
		}
	}

	/**
	 * Sets a template for the email
	 *
	 * @param string $templatePath
	 * @return void
	 */
	public function setTemplate(string $templatePath): void{
		$this->template = $templatePath;
	}

	/**
	 * Sets the email recipient
	 *
	 * @param string $emailRecipient
	 * @return void
	 */
	public function setRecipient(string $emailRecipient): void{
		$this->emailRecipient = $emailRecipient;
	}

	/**
	 * Sets the email subject
	 *
	 * @param string $subject
	 * @return void
	 */
	public function setSubject(string $subject): void{
		$this->subject = $subject;
	}

	/**
	 * Sets the email sender
	 *
	 * @param string $sender
	 * @param string $name
	 * @return void
	 */
	public function setFrom(string $sender, string $name = "Noreply"): void{
		$this->sender = $sender;
		$this->name = $name;
	}

	/**
	 * Sends the email
	 * @return void
	 */
    public function send(): void
    {

		$template = $this->templateFactory->createTemplate();
		$template->getLatte()->addProvider('uiControl', $this->linkGenerator);

		$mail = new Nette\Mail\Message;
		$mail->setFrom($this->sender, $this->name)
			->addTo($this->emailRecipient)
			->setSubject($this->subject)
			->setHtmlBody(
				$template->renderToString(__DIR__ . $this->template, $this->params)
			);
        $this->mailer->send($mail);

		$this->params = [];
		$this->template = null;
		$this->emailRecipient = "Not set";
		$this->subject = "Not set";
    }
}