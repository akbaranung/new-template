<script>
    const BASE_URL = '<?= base_url('wa_dashboard') ?>';
    let refreshTimer, countdownTimer, countdownVal = 10;

    // ----------------------------------------------------------------
    // Log
    // ----------------------------------------------------------------
    function addLog(msg, type = 'info') {
        const box = document.getElementById('log-box');
        const now = new Date().toLocaleTimeString('id-ID', {
            hour12: false
        });
        const cls = type === 'ok' ? 'log-ok' : type === 'err' ? 'log-err' : 'log-info';
        const div = document.createElement('div');
        div.innerHTML = `<span class="log-time">[${now}]</span><span class="${cls}">${msg}</span>`;
        box.prepend(div);
        if (box.children.length > 50) box.removeChild(box.lastChild);
    }

    function clearLog() {
        document.getElementById('log-box').innerHTML = '';
    }

    // ----------------------------------------------------------------
    // Countdown
    // ----------------------------------------------------------------
    function resetCountdown() {
        clearInterval(countdownTimer);
        countdownVal = 10;
        document.getElementById('countdown').textContent = `Refresh dalam ${countdownVal}s`;
        countdownTimer = setInterval(() => {
            countdownVal--;
            document.getElementById('countdown').textContent = `Refresh dalam ${countdownVal}s`;
            if (countdownVal <= 0) {
                clearInterval(countdownTimer);
            }
        }, 1000);
    }

    // ----------------------------------------------------------------
    // Cek status
    // ----------------------------------------------------------------
    async function checkStatus() {
        try {
            const res = await fetch(BASE_URL + '/status');
            const data = await res.json();
            const s = data.status || 'disconnected';

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

            document.getElementById('status-dot').className = 'dot ' + m.dot;
            document.getElementById('status-text').textContent = m.label;
            document.getElementById('status-badge').className = 'badge-status ' + m.badge;
            document.getElementById('status-badge').textContent = m.label;
            document.getElementById('qr-avail').textContent = data.hasQR ? '✅ Ada' : '—';
            document.getElementById('last-check').textContent = new Date().toLocaleTimeString('id-ID', {
                hour12: false
            });
            document.getElementById('gw-url').textContent = BASE_URL;

            addLog(`Status: ${m.label} | QR: ${data.hasQR ? 'ada' : 'tidak ada'}`, s === 'connected' ? 'ok' : 'info');
        } catch (e) {
            addLog('Gagal cek status: ' + e.message, 'err');
        }
    }

    // ----------------------------------------------------------------
    // Muat QR
    // ----------------------------------------------------------------
    async function loadQR() {
        const wrap = document.getElementById('qr-wrap');
        const btn = document.getElementById('btn-qr');
        btn.disabled = true;
        btn.textContent = 'Memuat…';
        wrap.innerHTML = '<div class="text-center text-muted"><div style="font-size:36px">&#8635;</div><small class="d-block mt-2">Menunggu QR dari gateway…</small></div>';
        addLog('Memuat QR code dari gateway…');

        try {
            const res = await fetch(BASE_URL + '/qr');
            const data = await res.json();

            if (data.success && data.base64) {
                wrap.innerHTML = `<img src="${data.base64}" alt="QR Code WhatsApp" />`;
                addLog('QR berhasil dimuat — silakan scan dengan HP', 'ok');
            } else {
                const msg = data.message || 'Gagal memuat QR';
                wrap.innerHTML = `<div class="text-center text-danger"><div style="font-size:36px">&#9888;</div><small class="d-block mt-2">${msg}</small></div>`;
                addLog(msg, 'err');
            }
        } catch (e) {
            wrap.innerHTML = '<div class="text-center text-danger"><small>Gagal koneksi ke gateway</small></div>';
            addLog('Error muat QR: ' + e.message, 'err');
        }

        btn.disabled = false;
        btn.innerHTML = '&#x21bb; Muat ulang QR';
    }

    // ----------------------------------------------------------------
    // Logout
    // ----------------------------------------------------------------
    async function doLogout() {
        if (!confirm('Yakin mau logout?\n\nSesi WA akan dicabut dan folder auth_info akan dihapus otomatis.\nKamu perlu scan QR ulang setelahnya.')) return;

        const btn = document.getElementById('btn-logout');
        btn.disabled = true;
        btn.textContent = 'Proses…';
        addLog('Mengirim perintah logout…');

        try {
            const res = await fetch(BASE_URL + '/logout', {
                method: 'POST'
            });
            const data = await res.json();

            if (data.success) {
                addLog('Logout berhasil — auth_info dihapus', 'ok');
                addLog('Klik Restart lalu Muat QR untuk scan ulang', 'info');
                document.getElementById('qr-wrap').innerHTML = '<div class="text-center text-muted"><small>Logout berhasil. Klik Restart lalu Muat QR.</small></div>';
            } else {
                addLog('Logout gagal: ' + (data.message || '-'), 'err');
            }
        } catch (e) {
            addLog('Error logout: ' + e.message, 'err');
        }

        btn.disabled = false;
        btn.innerHTML = '&#x2715; Logout';
        setTimeout(checkStatus, 1000);
    }

    // ----------------------------------------------------------------
    // Restart
    // ----------------------------------------------------------------
    async function doRestart() {
        const btn = document.getElementById('btn-restart');
        btn.disabled = true;
        btn.textContent = 'Merestart…';
        addLog('Mengirim perintah restart ke gateway…');

        try {
            const res = await fetch(BASE_URL + '/restart', {
                method: 'POST'
            });
            const data = await res.json();

            if (data.success) {
                addLog('Gateway sedang restart…', 'ok');
                addLog('Tunggu 3 detik lalu klik Muat QR', 'info');
                document.getElementById('qr-wrap').innerHTML = '<div class="text-center text-muted"><small>Gateway restart… tunggu lalu klik Muat QR</small></div>';
                setTimeout(() => {
                    checkStatus();
                }, 3000);
            } else {
                addLog('Restart gagal: ' + (data.message || '-'), 'err');
            }
        } catch (e) {
            addLog('Error restart: ' + e.message, 'err');
        }

        btn.disabled = false;
        btn.innerHTML = '&#x21bb; Restart';
    }

    // ----------------------------------------------------------------
    // Refresh manual
    // ----------------------------------------------------------------
    function doRefresh() {
        checkStatus();
        resetCountdown();
    }

    // ----------------------------------------------------------------
    // Auto-refresh setiap 10 detik
    // ----------------------------------------------------------------
    function startAutoRefresh() {
        clearInterval(refreshTimer);
        refreshTimer = setInterval(() => {
            checkStatus();
            resetCountdown();
        }, 10000);
    }

    // init
    checkStatus();
    resetCountdown();
    startAutoRefresh();
</script>