
require('dotenv').config();

const nodemailer = require('nodemailer');

const transporter = nodemailer.createTransport({
    host: process.env.SMTP_HOST || '127.0.0.1',
    port: Number(process.env.SMTP_PORT || 25),
    secure: process.env.SMTP_SECURE === 'true',

    // SMTP local sem autenticação
    tls: {
        rejectUnauthorized: false
    }
});

async function enviarEmail({ to, subject, html, text }) {
    try {
        const info = await transporter.sendMail({
            from: process.env.MAIL_FROM,
            to,
            subject,
            html,
            text
        });

        console.log('E-mail enviado:', info.messageId);

        return {
            success: true,
            messageId: info.messageId
        };

    } catch (error) {
        console.error('Erro ao enviar e-mail:', error);

        return {
            success: false,
            error: error.message
        };
    }
}

module.exports = enviarEmail;
