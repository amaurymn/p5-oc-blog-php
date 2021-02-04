<?php

namespace App\Services;

use App\Core\Twig;
use App\Exception\EmailException;
use App\Exception\TwigException;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Symfony\Component\Yaml\Yaml;

class Mailer
{
    private PHPMailer $mail;
    private FlashBag $flashBag;
    private Twig $twig;

    /**
     * Mailer constructor.
     * @throws Exception
     */
    public function __construct()
    {
        $config         = Yaml::parseFile(CONF_DIR . '/config.yml');
        $this->flashBag = new FlashBag();
        $this->twig     = new Twig();
        //Server settings
        $this->mail            = new PHPMailer(true);
        $this->mail->SMTPDebug = SMTP::DEBUG_OFF;                        // Enable verbose debug output
        $this->mail->isSMTP();                                            // Send using SMTP
        $this->mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $this->mail->Host       = $config['mailer']['host'];              // Set the SMTP server to send through
        $this->mail->Username   = $config['mailer']['username'];          // SMTP username
        $this->mail->Password   = $config['mailer']['password'];          // SMTP password
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
        $this->mail->Port       = 465;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
        $this->mail->isHTML(true);
        $this->mail->setLanguage('fr');
        $this->mail->setFrom($config['mailer']['username'], $config['mailer']['mailer_name']);
        $this->mail->addAddress($config['mailer']['username'], $config['mailer']['mailer_name']);
        $this->mail->Subject = "Message de " . $config['siteName'];
    }

    /**
     * Send mail with templates
     * @param array $post
     * @throws EmailException
     * @throws TwigException
     */
    public function sendMail(array $post): void
    {
        $this->mail->Body = $this->twig->twigRender('@public/mail/mail.html.twig', [
            'username' => $post['nom'],
            'email'    => $post['email'],
            'subject'  => $post['subject'],
            'message'  => $post['message']
        ]);

        try {
            $this->mail->send();
            $this->flashBag->set(FlashBag::SUCCESS, 'Le message a été envoyé.');
        } catch (Exception $e) {
            throw new EmailException("Le message n'a pas pu être envoyé: " . str_replace("https://github.com/PHPMailer/PHPMailer/wiki/Troubleshooting", '', $this->mail->ErrorInfo));
        }
    }

}
