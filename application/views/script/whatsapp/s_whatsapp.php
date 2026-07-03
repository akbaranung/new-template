<script>
    const BASE_URL = '<?= base_url('wa_dashboard') ?>';
    let timer;

    function addLog(msg) {
        const box = document.getElementById('log-box');
        const now = new Date().toLocaleTimeString('id-ID');
        const line = document.createElement('div');
        line.innerHTML = `<span class="log-time">[${now}]</span>${msg}`;
        box.prepend(line);
        if (box.children.length > 30) box.removeChild(box.lastChild);
    }

    async function checkStatus() {
        try {
            const res = await fetch(BASE_URL + '/status');
            const data = await res.json();
            const s = data.status || 'disconnected';

            const dot = document.getElementById('status-dot');
            const text = document.getElementById('status-text');
            const badge = document.getElementById('status-badge');
            const avail = document.getElementById('qr-avail');
            const lastck = document.getElementById('last-check');

            const map = {
                connected: {
                    dot: 'dot-connected',
                    badge: 'badge-connected',
                    label: 'Connected'
                },
                connecting: {
                    dot: 'dot-connecting',
                    badge: 'badge-connecting',
                    label: 'Connecting...'
                },
                disconnected: {
                    dot: 'dot-disconnected',
                    badge: 'badge-disconnected',
                    label: 'Disconnected'
                },
            };
            const m = map[s] || map.disconnected;

            dot.className = 'dot ' + m.dot;
            text.textContent = m.label;
            badge.className = 'status-badge ' + m.badge;
            badge.textContent = m.label;
            avail.textContent = data.hasQR ? '✅ Ada' : '—';
            lastck.textContent = new Date().toLocaleTimeString('id-ID');
            addLog(`Status: ${m.label} | QR: ${data.hasQR ? 'ada' : 'tidak ada'}`);
        } catch (e) {
            addLog('❌ Gagal cek status: ' + e.message);
        }
    }

    async function loadQR() {
        const box = document.getElementById('qr-container');
        const btn = document.getElementById('btn-qr');
        btn.disabled = true;
        btn.textContent = 'Memuat…';
        box.innerHTML = '<div class="text-center text-muted"><div style="font-size:32px">&#8635;</div><small>Menunggu QR…</small></div>';
        addLog('Memuat QR code dari gateway…');

        try {
            const res = await fetch(BASE_URL + '/qr');
            const data = await res.json();

            if (data.success && data.base64) {
                box.innerHTML = `<img src="${data.base64}" alt="QR Code WhatsApp" />`;
                addLog('✅ QR berhasil dimuat — silakan scan dengan HP');
            } else {
                box.innerHTML = `<div class="text-center text-danger"><div style="font-size:32px">&#9888;</div><small>${data.message || 'Gagal memuat QR'}</small></div>`;
                addLog('❌ ' + (data.message || 'Gagal memuat QR'));
            }
        } catch (e) {
            box.innerHTML = '<div class="text-center text-danger"><small>Gagal koneksi ke gateway</small></div>';
            addLog('❌ Error: ' + e.message);
        }

        btn.disabled = false;
        btn.textContent = '↺ Muat ulang QR';
    }

    async function doLogout() {
        if (!confirm('Yakin mau logout? Kamu perlu scan QR ulang.')) return;
        try {
            const res = await fetch(BASE_URL + '/logout', {
                method: 'POST'
            });
            const data = await res.json();
            if (data.success) {
                addLog('✅ Logout berhasil');
                document.getElementById('qr-container').innerHTML = '<div class="text-center text-muted"><small>Klik "Muat QR" untuk scan ulang</small></div>';
                setTimeout(checkStatus, 1000);
            } else {
                addLog('❌ Logout gagal: ' + data.message);
            }
        } catch (e) {
            addLog('❌ Error logout: ' + e.message);
        }
    }

    function doRefresh() {
        checkStatus();
    }

    // jalankan saat pertama load
    checkStatus();

    // auto-refresh setiap 10 detik
    timer = setInterval(checkStatus, 10000);
</script>