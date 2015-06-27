<?php

namespace Libs;

use PHPMailer;

/**
 *
 * @author José María Valera Reales
 */
class Email {

	/**
	 * Return one new PHPMailer
	 *
	 * @param string|array $email
	 * @return PHPMailer
	 */
	private function getNewMailer($email = '') {
		$mail = new PHPMailer();
		$mail->isSMTP(); // Set mailer to use SMTP
		$mail->Host = 'smtp.mandrillapp.com'; // Specify main and backup SMTP servers
		$mail->SMTPAuth = true; // Enable SMTP authentication
		$mail->Username = 'smtp username'; // SMTP username
		$mail->Password = 'smtp-password'; // SMTP password
		$mail->SMTPSecure = 'tls'; // Enable TLS encryption, `ssl` also accepted
		$mail->Port = 587; // TCP port to connect to
		$mail->CharSet = 'utf-8';

		$mail->From = ADMIN_EMAIL;
		$mail->FromName = BLOG_TITLE;

		if (is_string($email)) {
			$mail->addAddress($email);
		} else if (is_array($email)) {
			foreach ( $email as $e ) {
				$mail->addAddress($e);
			}
		}
		return $mail;
	}

	/**
	 * Send new generic email
	 *
	 * @param array|string $email
	 *        	to
	 * @param string $subject
	 *        	Subject
	 * @param string $bodyHtml
	 *        	Body
	 * @param string $bodyHtmlAlt
	 * @return boolean
	 */
	public static function sendGenericEmail($email, $subject, $bodyHtml, $bodyHtmlAlt = '') {
		$mail = self::getNewMailer($email);
		$mail->WordWrap = 50; // Set word wrap to 50 characters
		$mail->isHTML(true); // Set email format to HTML
		$mail->Subject = $subject;
		$mail->Body = $bodyHtml;
		$mail->AltBody = $bodyHtmlAlt;
		return $mail->send();
	}
}