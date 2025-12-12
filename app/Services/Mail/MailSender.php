<?php

namespace App\Services\Mail;

require_once ROOTPATH . 'lib/PHPMailer/Exception.php';
require_once ROOTPATH . 'lib/PHPMailer/PHPMailer.php';
require_once ROOTPATH . 'lib/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailSender
{
    public static function sendActivationMail(string $email, string $token): bool
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'jdgomezcubillo2004@gmail.com'; // 🔴 CAMBIAR
            $mail->Password = 'uxqk liox nhyu uzua';    // 🔴 CAMBIAR
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('jdgomezcubillo2004@gmail.com', 'Aventones');
            $mail->addAddress($email);

            $activationLink = site_url("activate/$token");

            $mail->isHTML(true);
            $mail->Subject = 'Activate your Aventones account';
            $mail->Body = "
                <h2>Welcome to Aventones</h2>
                <p>Please activate your account by clicking the link below:</p>
                <a href='$activationLink'>Activate account</a>
            ";

            return $mail->send();
        } catch (Exception $e) {
            log_message('error', 'Mail error: ' . $mail->ErrorInfo);
            return false;
        }
    }

    public static function sendPasswordlessLoginMail(string $email, string $token): bool
    {
        $mail = new PHPMailer(true);

        try {
            // (tu config SMTP igual que en sendActivationMail)
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'jdgomezcubillo2004@gmail.com';
            $mail->Password = 'uxqk liox nhyu uzua';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom($mail->Username, 'Aventones');
            $mail->addAddress($email);

            $link = site_url("passwordless/login/$token");

            $mail->isHTML(true);
            $mail->Subject = 'Your Aventones login link';
            $mail->Body = "
            <h3>Passwordless Login</h3>
            <p>This link is one-time use and expires in 15 minutes.</p>
            <a href='$link'>$link</a>
        ";

            return $mail->send();
        } catch (\Throwable $e) {
            log_message('error', 'Passwordless mail error: ' . $e->getMessage());
            return false;
        }
    }

    public static function sendPendingBookingReminder(string $email, array $data): bool
    {
        try {
            $mail = new PHPMailer(true);

            // Configuración SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'jdgomezcubillo2004@gmail.com';
            $mail->Password = 'uxqk liox nhyu uzua';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            // Configuración adicional
            $mail->CharSet = 'UTF-8';
            $mail->SMTPDebug = 0; // Cambia a 2 para ver detalles de depuración
            $mail->Timeout = 30;

            // Remitente y destinatario
            $mail->setFrom('jdgomezcubillo2004@gmail.com', 'Aventones');
            $mail->addAddress($email);

            // Datos del email
            $first = htmlspecialchars($data['first_name'] ?? '');
            $last = htmlspecialchars($data['last_name'] ?? '');
            $origin = htmlspecialchars($data['origin'] ?? '');
            $dest = htmlspecialchars($data['destination'] ?? '');
            $bookingId = htmlspecialchars($data['booking_id'] ?? '');
            $createdAt = htmlspecialchars($data['created_at'] ?? '');

            // Contenido HTML
            $mail->isHTML(true);
            $mail->Subject = 'Tienes solicitudes de reserva pendientes - Aventones';

            $mail->Body = "
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset='UTF-8'>
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                        .header { background-color: #4CAF50; color: white; padding: 10px; text-align: center; }
                        .content { padding: 20px; background-color: #f9f9f9; }
                        .footer { margin-top: 20px; padding: 10px; text-align: center; color: #666; font-size: 12px; }
                        .info-box { background-color: white; border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 5px; }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>
                            <h2>Aventones - Recordatorio de Reserva</h2>
                        </div>
                        <div class='content'>
                            <h3>¡Hola {$first} {$last}!</h3>
                            <p>Tienes una solicitud de reserva pendiente por más tiempo del habitual.</p>
                            
                            <div class='info-box'>
                                <h4>📋 Detalles de la reserva:</h4>
                                <p><strong>ID de Reserva:</strong> #{$bookingId}</p>
                                <p><strong>Ruta:</strong> {$origin} → {$dest}</p>
                                <p><strong>Solicitado el:</strong> {$createdAt}</p>
                            </div>
                            
                            <p>Por favor, ingresa a la plataforma para revisar y responder a esta solicitud.</p>
                            <p><a href='" . base_url() . "' style='background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Ir a Aventones</a></p>
                        </div>
                        <div class='footer'>
                            <p>Este es un mensaje automático, por favor no responder.</p>
                            <p>© " . date('Y') . " Aventones. Todos los derechos reservados.</p>
                        </div>
                    </div>
                </body>
                </html>
            ";

            // Versión texto plano
            $mail->AltBody = "AVENTONES - Recordatorio de Reserva\n\n" .
                "Hola {$first} {$last},\n\n" .
                "Tienes una solicitud de reserva pendiente:\n\n" .
                "ID: #{$bookingId}\n" .
                "Ruta: {$origin} → {$dest}\n" .
                "Fecha: {$createdAt}\n\n" .
                "Ingresa a la plataforma para responder.\n" .
                base_url() . "\n\n" .
                "Este es un mensaje automático.";

            return $mail->send();

        } catch (Exception $e) {
            log_message('error', 'Error enviando email: ' . $e->getMessage());
            return false;
        }
    }

}
