<style>
    body {
        background: #f4f6f9;
    }

    .card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .08);
    }

    .card-header {
        background: #fff;
        border-bottom: 1px solid #eee;
        font-weight: 500;
        border-radius: 10px 10px 0 0 !important;
    }

    .status-badge {
        font-size: 14px;
        padding: 6px 14px;
        border-radius: 20px;
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

    .dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 6px;
    }

    .dot-connected {
        background: #28a745;
    }

    .dot-connecting {
        background: #ffc107;
    }

    .dot-disconnected {
        background: #dc3545;
    }

    #qr-container {
        min-height: 220px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1rem;
    }

    #qr-container img {
        max-width: 200px;
    }

    .log-box {
        background: #1e1e1e;
        color: #d4d4d4;
        border-radius: 8px;
        padding: 12px;
        font-size: 12px;
        font-family: monospace;
        height: 120px;
        overflow-y: auto;
    }

    .log-time {
        color: #888;
        margin-right: 8px;
    }

    .stat-card {
        border-radius: 10px;
        padding: 1rem 1.25rem;
        background: #fff;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .08);
    }

    .stat-label {
        font-size: 12px;
        color: #6c757d;
        margin-bottom: 4px;
    }

    .stat-value {
        font-size: 22px;
        font-weight: 600;
        color: #212529;
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
                        <div class="col-md-4 mb-3">
                            <div class="stat-card">
                                <div class="stat-label">Status Koneksi</div>
                                <div id="status-value" class="mt-1">
                                    <span class="dot dot-disconnected" id="status-dot"></span>
                                    <span id="status-text">Mengecek…</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="stat-card">
                                <div class="stat-label">QR Tersedia</div>
                                <div class="stat-value" id="qr-avail">—</div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="stat-card">
                                <div class="stat-label">Terakhir dicek</div>
                                <div class="stat-value" style="font-size:14px;" id="last-check">—</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <!-- QR Code -->
                        <div class="col-md-5 mb-3">
                            <div class="card h-100">
                                <div class="card-header d-flex align-items-center justify-content-between">
                                    <span>Scan QR Code</span>
                                    <span class="status-badge badge-disconnected" id="status-badge">Disconnected</span>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted small mb-3">
                                        WhatsApp &rarr; Pengaturan &rarr; Perangkat Tertaut &rarr; Tautkan Perangkat
                                    </p>
                                    <div id="qr-container">
                                        <div class="text-center text-muted">
                                            <div style="font-size: 48px;">&#9638;</div>
                                            <small>Klik "Muat QR" untuk menampilkan</small>
                                        </div>
                                    </div>
                                    <div class="mt-3 d-flex gap-2">
                                        <button class="btn btn-sm btn-outline-primary mr-2" onclick="loadQR()" id="btn-qr">
                                            &#9638; Muat QR
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="doLogout()">
                                            &#x2715; Logout
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Log -->
                        <div class="col-md-7 mb-3">
                            <div class="card h-100">
                                <div class="card-header">Log Aktivitas</div>
                                <div class="card-body">
                                    <div class="log-box" id="log-box"></div>
                                    <div class="mt-3">
                                        <p class="text-muted small mb-1">
                                            Dashboard ini auto-refresh setiap <strong>10 detik</strong>.
                                            Setelah scan QR berhasil, status akan berubah menjadi <strong>Connected</strong>.
                                        </p>
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