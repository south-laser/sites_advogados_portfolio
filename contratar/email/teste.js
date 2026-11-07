const enviarEmail = require('./email');

async function teste() {
    const resultado = await enviarEmail({
        to: 'guilherme.rein.oabrs@gmail.com',
        subject: 'Teste de envio - South Laser',
        html: `
            <h1>Olá!</h1>
            <p>Este é um e-mail de teste enviado pela VPS.</p>
            <strong>South Laser</strong>
        `
    });

    console.log(resultado);
}

teste();