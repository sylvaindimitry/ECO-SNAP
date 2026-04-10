<?php
/**
 * Email Utility Class
 * Envoi d'emails de confirmation et de vérification
 */
class Email {
    private static $fromEmail = 'noreply@ecosnap.com';
    private $to;
    private $subject;
    private $body;
    private $headers;

    public function __construct($to, $subject, $body) {
        $this->to = $to;
        $this->subject = $subject;
        $this->body = $body;
        $this->headers = self::getHeaders();
    }

    /**
     * Obtenir les headers email
     */
    private static function getHeaders() {
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: ECO-SNAP <" . self::$fromEmail . ">\r\n";
        $headers .= "Reply-To: " . self::$fromEmail . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();
        return $headers;
    }

    /**
     * Envoyer l'email
     */
    public function send() {
        try {
            // En développement, log l'email au lieu de l'envoyer
            $config = require __DIR__ . '/../config/config.php';
            if ($config['app_debug']) {
                error_log("Email envoyé à: {$this->to}");
                error_log("Sujet: {$this->subject}");
                error_log("Corps: {$this->body}");
                return true; // Simuler l'envoi en dev
            }

            return mail($this->to, $this->subject, $this->body, $this->headers);
        } catch (Exception $e) {
            error_log("Erreur envoi email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Envoyer l'email de vérification
     */
    public static function sendVerification($to, $name, $token) {
        $config = require __DIR__ . '/../config/config.php';
        $appUrl = rtrim($config['app_url'], '/');
        $verificationLink = "{$appUrl}/verify-email?token={$token}";

        $subject = "✅ Vérifiez votre email - ECO-SNAP";
        $body = self::getVerificationTemplate($name, $verificationLink);

        $email = new self($to, $subject, $body);
        return $email->send();
    }

    /**
     * Template HTML pour l'email de vérification
     */
    private static function getVerificationTemplate($name, $link) {
        return "
        <!DOCTYPE html>
        <html lang='fr'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #2c5f2d, #1e3c1f); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .header h1 { margin: 0; font-size: 24px; }
                .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }
                .btn { display: inline-block; background: #28a745; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: bold; margin: 20px 0; }
                .btn:hover { background: #218838; }
                .footer { text-align: center; padding: 20px; color: #6c757d; font-size: 12px; }
                .link { word-break: break-all; color: #007bff; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🌍 ECO-SNAP</h1>
                </div>
                <div class='content'>
                    <h2>Bonjour {$name} !</h2>
                    <p>Merci de vous être inscrit sur ECO-SNAP. Pour activer votre compte, veuillez vérifier votre adresse email en cliquant sur le bouton ci-dessous :</p>
                    
                    <div style='text-align: center;'>
                        <a href='{$link}' class='btn'>✅ Vérifier mon email</a>
                    </div>
                    
                    <p>Si le bouton ne fonctionne pas, copiez-collez ce lien dans votre navigateur :</p>
                    <p class='link'>{$link}</p>
                    
                    <hr style='border: none; border-top: 1px solid #dee2e6; margin: 20px 0;'>
                    <p><strong>Ce lien expire dans 24 heures.</strong></p>
                    <p>Si vous n'avez pas créé de compte sur ECO-SNAP, ignorez simplement cet email.</p>
                </div>
                <div class='footer'>
                    <p>© " . date('Y') . " ECO-SNAP - Localisez. Signalez. Protégez.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    /**
     * Envoyer l'email de bienvenue
     */
    public static function sendWelcome($to, $name) {
        $subject = "🎉 Bienvenue sur ECO-SNAP !";
        $body = self::getWelcomeTemplate($name);

        $email = new self($to, $subject, $body);
        return $email->send();
    }

    /**
     * Template HTML pour l'email de bienvenue
     */
    private static function getWelcomeTemplate($name) {
        return "
        <!DOCTYPE html>
        <html lang='fr'>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #2c5f2d, #1e3c1f); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }
                .btn { display: inline-block; background: #28a745; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: bold; }
                .footer { text-align: center; padding: 20px; color: #6c757d; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🎉 Bienvenue {$name} !</h1>
                </div>
                <div class='content'>
                    <h2>Votre compte est activé !</h2>
                    <p>Merci d'avoir vérifié votre email. Vous pouvez maintenant :</p>
                    <ul>
                        <li>📍 Signaler des dépôts d'ordures</li>
                        <li>📊 Suivre l'évolution de vos signalements</li>
                        <li>🔔 Recevoir des notifications en temps réel</li>
                    </ul>
                    <div style='text-align: center; margin-top: 30px;'>
                        <a href='" . rtrim((require __DIR__ . '/../config/config.php')['app_url'], '/') . "/dashboard' class='btn'>Accéder à mon dashboard</a>
                    </div>
                </div>
                <div class='footer'>
                    <p>© " . date('Y') . " ECO-SNAP - Localisez. Signalez. Protégez.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    /**
     * Envoyer l'email de réinitialisation de mot de passe
     */
    public static function sendPasswordReset($to, $name, $token) {
        $config = require __DIR__ . '/../config/config.php';
        $appUrl = rtrim($config['app_url'], '/');
        $resetLink = "{$appUrl}/reset-password?token={$token}";

        $subject = "🔑 Réinitialisation de votre mot de passe - ECO-SNAP";
        $body = "
        <!DOCTYPE html>
        <html lang='fr'>
        <head><meta charset='UTF-8'><style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #2c5f2d, #1e3c1f); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }
            .btn { display: inline-block; background: #ffc107; color: #333; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: bold; }
            .footer { text-align: center; padding: 20px; color: #6c757d; font-size: 12px; }
        </style></head>
        <body>
            <div class='container'>
                <div class='header'><h1>🔑 ECO-SNAP</h1></div>
                <div class='content'>
                    <h2>Bonjour {$name},</h2>
                    <p>Vous avez demandé la réinitialisation de votre mot de passe. Cliquez sur le bouton ci-dessous :</p>
                    <div style='text-align: center;'>
                        <a href='{$resetLink}' class='btn'>Réinitialiser mon mot de passe</a>
                    </div>
                    <p><strong>Ce lien expire dans 1 heure.</strong></p>
                </div>
                <div class='footer'><p>© " . date('Y') . " ECO-SNAP</p></div>
            </div>
        </body>
        </html>
        ";

        $email = new self($to, $subject, $body);
        return $email->send();
    }
}
