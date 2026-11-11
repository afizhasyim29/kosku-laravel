import './bootstrap';

import Alpine from 'alpinejs';
import QRCode from 'qrcode';

window.Alpine = Alpine;

Alpine.start();

document.querySelectorAll('[data-qrcode]').forEach(async (canvas) => {
    const status = canvas.parentElement.querySelector('[data-qrcode-status]');

    try {
        await QRCode.toCanvas(canvas, canvas.dataset.qrcode, {
            width: Number(canvas.dataset.qrcodeWidth || 180),
            margin: 1,
            errorCorrectionLevel: 'H',
        });

        canvas.classList.remove('hidden');
        status?.classList.add('hidden');
    } catch (error) {
        if (status) {
            status.textContent = 'QR tidak dapat dibuat. Gunakan kode tiket yang tercetak.';
        }

        console.error('Failed to generate ticket QR code.', error);
    }
});
