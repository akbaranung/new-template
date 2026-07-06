<style>
    body {
        background: #f4f6f9;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    .navbar-brand {
        font-weight: 600;
        color: #25d366 !important;
    }

    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .08);
    }

    .card-header {
        background: #fff;
        border-bottom: 1px solid #f0f0f0;
        font-weight: 500;
        font-size: 14px;
        border-radius: 12px 12px 0 0 !important;
        padding: 12px 16px;
    }

    .stat-card {
        border-radius: 12px;
        padding: 1rem 1.25rem;
        background: #fff;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .08);
    }

    .stat-label {
        font-size: 12px;
        color: #6c757d;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: .5px;
    }

    .stat-value {
        font-size: 20px;
        font-weight: 600;
        color: #212529;
    }

    .dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 6px;
        flex-shrink: 0;
    }

    .dot-connected {
        background: #28a745;
        box-shadow: 0 0 0 3px rgba(40, 167, 69, .2);
    }

    .dot-connecting {
        background: #ffc107;
        box-shadow: 0 0 0 3px rgba(255, 193, 7, .2);
    }

    .dot-disconnected {
        background: #dc3545;
        box-shadow: 0 0 0 3px rgba(220, 53, 69, .2);
    }

    .badge-status {
        font-size: 12px;
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: 500;
    }

    .badge-connected {
        background: #d4edda;
        color: #155724;
    }

    .badge-connecting {
        background: #fff3cd;
        color: #856404;
    }

    .badge-disconnected {
        background: #f8d7da;
        color: #721c24;
    }

    #qr-wrap {
        min-height: 220px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1rem;
    }

    #qr-wrap img {
        max-width: 200px;
        border-radius: 8px;
    }

    .log-box {
        background: #1a1a2e;
        color: #a8b2d8;
        border-radius: 8px;
        padding: 12px 14px;
        font-size: 11.5px;
        font-family: 'Courier New', monospace;
        height: 160px;
        overflow-y: auto;
    }

    .log-box::-webkit-scrollbar {
        width: 4px;
    }

    .log-box::-webkit-scrollbar-thumb {
        background: #444;
        border-radius: 2px;
    }

    .log-time {
        color: #5a6a8a;
        margin-right: 8px;
    }

    .log-ok {
        color: #64ffda;
    }

    .log-err {
        color: #ff5370;
    }

    .log-info {
        color: #a8b2d8;
    }

    .btn-action {
        font-size: 12px;
        padding: 6px 12px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .countdown {
        font-size: 11px;
        color: #adb5bd;
    }

    .step-item {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 10px;
    }

    .step-num {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: #e9ecef;
        color: #495057;
        font-size: 11px;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-top: 1px;
    }

    .step-text {
        font-size: 13px;
        color: #495057;
        line-height: 1.4;
    }

    .action-strip {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 12px;
    }
</style>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="mb-0 font-weight-bold">
                            WA Gateway Dashboard
                        </h5>
                        <button class="btn btn-sm btn-outline-secondary" onclick="doRefresh()">
                            &#x21bb; Refresh
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-6 col-md-3 mb-3">
                            <div class="stat-card">
                                <div class="stat-label">Status</div>
                                <div class="d-flex align-items-center mt-1">
                                    <span class="dot dot-disconnected" id="status-dot"></span>
                                    <span id="status-text" style="font-size:14px;font-weight:600;">Mengecek…</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <div class="stat-card">
                                <div class="stat-label">QR Tersedia</div>
                                <div class="stat-value" id="qr-avail">—</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <div class="stat-card">
                                <div class="stat-label">Auto-refresh</div>
                                <div class="stat-value" style="font-size:16px;" id="last-check">—</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <div class="stat-card">
                                <div class="stat-label">Gateway</div>
                                <div style="font-size:12px;color:#6c757d;margin-top:4px;word-break:break-all;" id="gw-url">—</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">

                        <!-- Panel kiri: QR + aksi -->
                        <div class="col-md-5 mb-3">
                            <div class="card">
                                <div class="card-header d-flex align-items-center justify-content-between">
                                    <span>Scan QR Code</span>
                                    <span class="badge-status badge-disconnected" id="status-badge">Disconnected</span>
                                </div>
                                <div class="card-body">

                                    <!-- Langkah scan -->
                                    <div class="mb-3">
                                        <div class="step-item">
                                            <div class="step-num">1</div>
                                            <div class="step-text">Klik <strong>Muat QR</strong> di bawah</div>
                                        </div>
                                        <div class="step-item">
                                            <div class="step-num">2</div>
                                            <div class="step-text">Buka WhatsApp di HP &rarr; <strong>Perangkat Tertaut</strong></div>
                                        </div>
                                        <div class="step-item">
                                            <div class="step-num">3</div>
                                            <div class="step-text">Pilih <strong>Tautkan Perangkat</strong> lalu scan QR</div>
                                        </div>
                                    </div>

                                    <!-- QR Box -->
                                    <div id="qr-wrap">
                                        <div class="text-center text-muted">
                                            <div style="font-size:52px;line-height:1;">&#9638;</div>
                                            <small class="d-block mt-2">Klik "Muat QR" untuk menampilkan</small>
                                        </div>
                                    </div>

                                    <!-- Tombol aksi -->
                                    <div class="action-strip">
                                        <button class="btn btn-sm btn-outline-primary btn-action" onclick="loadQR()" id="btn-qr">
                                            &#9638; Muat QR
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning btn-action" onclick="doRestart()" id="btn-restart">
                                            &#x21bb; Restart
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger btn-action" onclick="doLogout()" id="btn-logout">
                                            &#x2715; Logout
                                        </button>
                                    </div>

                                    <div class="mb-3">
                                        <div class="h-100">
                                            <div class="">
                                                <div class="mt-3 p-3 rounded" style="background:#f8f9fa;font-size:12px;">
                                                    <div class="font-weight-500 mb-2">Alur setelah Logout:</div>
                                                    <div style="color:#495057;">
                                                        1. Klik <strong>Logout</strong> &rarr; sesi WA dicabut &amp; auth_info dihapus otomatis<br>
                                                        2. Klik <strong>Restart</strong> &rarr; gateway mulai ulang koneksi<br>
                                                        3. Klik <strong>Muat QR</strong> &rarr; scan QR baru dengan HP
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="col-md-7 mb-3">
                            <div class="card h-100">
                                <div class="card-header d-flex align-items-center justify-content-between">
                                    <span>&#9654; Log Aktivitas</span>
                                    <button class="btn btn-sm btn-danger text-white" style="font-size:12px;" onclick="clearLog()">Bersihkan</button>
                                </div>
                                <div class="card-body">
                                    <div class="log-box" id="log-box"></div>

                                    <div class="mt-3 p-3 rounded" style="background:#f8f9fa;font-size:12px;">
                                        <table class="table table-stripped">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th>No.</th>
                                                    <th>Tujuan</th>
                                                    <th>Pesan</th>
                                                    <th>Waktu</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $no = 1;
                                                foreach ($logs as $log) : ?>
                                                    <tr>
                                                        <td><?= $no++; ?></td>
                                                        <td><?= $log['nomor_tujuan'] ?></td>
                                                        <td><?= substr($log['isi_pesan'], 0, 15) ?> ...</td>
                                                        <td><?= $log['tanggal_waktu'] ?></td>
                                                        <td><?= $log['status'] ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div> <!-- .col-12 -->
    </div> <!-- .row -->
</div> <!-- .container-fluid -->