<script>
    const BASE_URL = "<?= base_url() ?>";
    const detailsContainer = document.getElementById('pricing-details-container');

    $(document).ready(function() {
        // Get the current URL path
        var currentPath = window.location.pathname; // e.g., /new-template/subscription/upgrade

        // Get the base URL part
        var base_url_path = '<?php echo $this->config->item('base_url'); ?>';
        var path_parts = base_url_path.split('/');
        var ci_subfolder = path_parts[path_parts.length - 2];

        // Construct the target URL path
        var target_relative_path = '/' + ci_subfolder + '/subscription/upgrade';

        if (ci_subfolder === '' || ci_subfolder === 'http:' || ci_subfolder === 'https:') {
            target_relative_path = '/subscription/upgrade';
        }

        // Check if the current URL path matches the target page and the screen width is greater than 480px
        if (currentPath === target_relative_path && $(window).width() > 980) {
            // Add the 'collapsed' class only if both conditions are true
            $(".vertical").addClass("collapsed");

        }

    });

    function formatDate(date) {
        const options = {
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        };
        return new Intl.DateTimeFormat('id-ID', options).format(date);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const pricingBoxes = document.querySelectorAll('.pricing-box');
        const pilihTahtaButtons = document.querySelectorAll('.pilih-tahta-btn');
        const pilihDonasiButtons = document.querySelectorAll('.pilih-donasi-btn');
        // const detailsContainer = document.getElementById('pricing-details-container');
        const pricingRow = document.querySelector('.row.justify-content-center');
        const BASE_URL = '<?php echo base_url(); ?>';
        const tahta_sekarang = '<?= $utility['nama_paket'] ?>';
        const is_premium = '<?= $utility['is_premium'] ?>';

        // Store the original columns to re-insert them later
        const originalCols = Array.from(document.querySelectorAll('.col-lg-3.nopadding'));

        function formatDateForDatabase(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        // Function to handle button click
        function handlePilihTahtaClick(event) {
            event.preventDefault(); // Prevent the default link behavior

            const selectedButton = event.currentTarget;
            const selectedPricingBox = selectedButton.closest('.pricing-box');
            const planName = selectedPricingBox.querySelector('h4').innerText;
            const basePrice = parseInt(selectedButton.getAttribute('data-price'));
            const planDetailsHTML = selectedPricingBox.querySelector('.mt-1.pt-2').innerHTML;

            // Hide other pricing boxes with animation
            // pricingBoxes.forEach(box => {
            //     if (box !== selectedPricingBox) {
            //         box.classList.add('hidden');
            //         box.closest('.col-lg-3').style.display = 'none';
            //     }
            // });

            const allPricingCols = document.querySelectorAll('.col-lg-3.nopadding');

            if (is_premium == '1') {
                if (planName != tahta_sekarang) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Paket Tidak Sesuai',
                        text: 'Anda masih memiliki paket aktif. Silakan lanjutkan pembayaran sesuai paket yang sedang berjalan atau tunggu hingga masa aktif berakhir. Jika tetap ingin mengganti silahkan hubungi admin',
                        confirmButtonText: 'Mengerti'
                    });
                    return;
                }
            }
            allPricingCols.forEach(col => {
                if (col.querySelector('.pricing-box') !== selectedPricingBox) {
                    col.querySelector('.pricing-box').classList.add('hidden');

                    setTimeout(() => {
                        col.classList.add('d-none');
                    }, 500);
                }


            });
            // setTimeout(() => {
            //     pricingBoxes.forEach(box => {
            //         if (box !== selectedPricingBox) {
            //             box.classList.add('hidden');
            //             box.closest('.col-lg-3').style.display = 'none';
            //         }
            //     });
            // }, 600);
            // After the animation (0.5s), display the detail container
            setTimeout(() => {
                // Adjust width of the selected box
                // detailsContainer.classList.remove('d-none');

                const selectedCol = selectedPricingBox.closest('.col-lg-3');
                selectedCol.classList.remove('col-lg-3', 'nopadding', 'noborderradius-right', 'noborderradius', 'noborderradius-left');
                selectedCol.classList.add('col-lg-3', 'd-flex', 'flex-column', 'justify-content-center');

                selectedPricingBox.classList.remove('nopadding', 'noborderradius-right', 'noborderradius', 'noborderradius-left');
                selectedPricingBox.classList.add('selected-col');

                selectedPricingBox.style.height = 'auto';
                selectedPricingBox.classList.remove('feature');

                // Generate and display the new payment details content
                //     const detailHTML = `
                //     <div class="detail-box mt-5">
                //         <h3 class="f-20">Rincian Pembayaran : ${planName}</h3>
                //         <p>Pilih jangka waktu pembayaran:</p>
                //         <div class="month-selection">
                //             <button class="month-btn" data-months="1">1 Bulan</button>
                //             <button class="month-btn" data-months="3">3 Bulan</button>
                //             <button class="month-btn" data-months="6">6 Bulan</button>
                //             <button class="month-btn" data-months="9">9 Bulan</button>
                //             <button class="month-btn" data-months="12">1 Tahun</button>
                //         </div>

                //         <div class="mt-4 text-center">
                //             <h4>Total Pembayaran: <span id="total-price" class="text-bariskode">${formatRupiah(basePrice)}</span></h4>
                //             <p class="text-muted">Untuk <span id="months-display">1</span> bulan</p>
                //         </div>

                //         <div class="mt-4 pt-3 d-flex justify-content-center">
                //             <button type="button" id="btn-pembayaran" class="btn btn-primary btn-rounded w-50">Lanjutkan Pembayaran</a>
                //         </div>
                //         <div class="mt-3 d-flex justify-content-center">
                //             <button id="back-button" class="btn btn-outline-secondary w-50">
                //                 <i class="fa-solid fa-arrow-left"></i> Kembali ke Pilihan Plan
                //             </button>
                //         </div>
                //     </div>
                // `;
                const detailHTML = `
    <div class="detail-box mt-5">
        <h3 class="f-20">Rincian Pembayaran : ${planName}</h3>
        <p>Pilih jangka waktu pembayaran:</p>
       <div class="month-selection">
    <button class="month-btn btn btn-outline-primary" data-months="1">1 Bulan</button>
    <button class="month-btn btn btn-outline-primary" data-months="3">3 Bulan</button>
    <button class="month-btn btn btn-outline-primary" data-months="6">6 Bulan</button>
    <button class="month-btn btn btn-outline-primary" data-months="9">9 Bulan</button>
    <button class="month-btn btn btn-outline-primary" data-months="12">1 Tahun</button>
</div>

        <div class="mt-4 text-center">
            <h4>Total Pembayaran: <span id="total-price" class="text-bariskode">${formatRupiah(basePrice)}</span></h4>
            <p class="text-muted">Untuk <span id="months-display">1</span> bulan</p>
        </div>

        <div class="mt-4 pt-3 d-flex justify-content-center">
            <button type="button" id="btn-pembayaran" class="btn btn-primary btn-rounded w-50">Lanjutkan Pembayaran</button>
        </div>
        <div class="mt-3 d-flex justify-content-center">
            <button id="back-button" class="btn btn-outline-secondary w-50">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Pilihan Plan
            </button>
        </div>
    </div>
`;
                detailsContainer.classList.remove('d-none');

                detailsContainer.innerHTML = detailHTML;
                setTimeout(() => {
                    detailsContainer.classList.add('visible');
                }, 300);


                // Add event listeners to the new month selection buttons
                const monthButtons = detailsContainer.querySelectorAll('.month-btn');
                monthButtons.forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        // Remove 'selected' class from all buttons
                        monthButtons.forEach(b => b.classList.remove('selected'));
                        // Add 'selected' class to the clicked button
                        e.currentTarget.classList.add('selected');

                        const months = parseInt(e.currentTarget.getAttribute('data-months'));
                        const totalPrice = basePrice * months;
                        document.getElementById('total-price').innerText = formatRupiah(totalPrice);
                        document.getElementById('months-display').innerText = months;
                    });
                });

                // Set the default button to selected (1 Month)
                if (monthButtons.length > 0) {
                    monthButtons[0].classList.add('selected');
                }

                // ADD THE BACK BUTTON LISTENER HERE
                document.getElementById('back-button').addEventListener('click', handleBackClick);
                // document.getElementById('btn-pembayaran').addEventListener('click', detailPembayaran);
                document.getElementById('btn-pembayaran').addEventListener('click', () => {
                    // Pass the necessary data to the new function
                    const selectedMonths = parseInt(detailsContainer.querySelector('.month-btn.selected').getAttribute('data-months'));
                    detailPembayaran(planName, basePrice, selectedMonths);
                });


            }, 500); // Wait for the zoom-out animation to finish
        }

        function handleBackClick() {
            // Hide the details container with an animation
            detailsContainer.classList.remove('visible');

            // Wait for the fade-out animation to finish before restoring the grid
            setTimeout(() => {
                // Remove the content from the details container and hide it again
                detailsContainer.innerHTML = '';
                detailsContainer.classList.add('d-none');

                // Restore the original columns
                originalCols.forEach(col => {
                    pricingRow.appendChild(col);
                    // Reset any 'hidden' or other animation classes
                    const pricingBox = col.querySelector('.pricing-box');
                    col.classList.remove('d-none');
                    pricingBox.classList.remove('selected-col');


                    setTimeout(() => {
                        pricingBox.classList.remove('hidden');
                    }, 500);

                });

                // Re-apply original classes and styling to the selected box
                const selectedCol = pricingRow.querySelector('.selected-col').closest('.col-lg-3');
                selectedCol.classList.remove('d-flex', 'flex-column', 'justify-content-center', 'selected-col');
                selectedCol.classList.add('col-lg-3', 'nopadding');

                // Re-add the feature class and hover effects
                pricingBoxes.forEach(box => {
                    if (box.classList.contains('feature-data')) { // You might need a new data attribute or class to identify the original feature box
                        box.classList.add('feature');
                    }
                });

            }, 500); // This duration must match the 'visible' class transition

            setTimeout(() => {
                location.reload();
            }, 1500);
        }


        // New function to handle the "Lanjutkan Pembayaran" button click
        function detailPembayaran(planName, basePrice, months) {
            // Hide the first detail box with animation
            detailsContainer.classList.remove('visible');

            const startDate = new Date();
            const endDate = new Date();
            endDate.setMonth(startDate.getMonth() + months);

            const totalPrice = basePrice * months;
            const randomDigits = Math.floor(Math.random() * 900) + 100; // Generate 3 random digits (100-999)
            const confirmationPrice = totalPrice + randomDigits;
            const OriginPrice = totalPrice;
            const formatDateForDatabase = (date) => {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            };
            const dbStartStr = formatDateForDatabase(startDate);
            const dbEndStr = formatDateForDatabase(endDate);

            const data = {
                planName: planName,
                months: months,
                startDate: dbStartStr,
                endDate: dbEndStr,
                confirmationPrice: confirmationPrice,
                OriginPrice: OriginPrice,
                id_perusahaan: "<?php echo $this->session->userdata('user_perusahaan_id'); ?>"
            };

            const thirdDetailHTML = `
    <div class="detail-box text-center mt-5">
        <h3 class="f-20">Pembayaran Terkirim!</h3>
        <p>Terima kasih. Permintaan pembayaran Anda telah berhasil kami terima. <br>
           Mohon tunggu beberapa saat, tim kami akan segera memprosesnya.
           Anda akan menerima pesan konfirmasi melalui WhatsApp setelah pembayaran Anda disetujui.
        </p>
        <div class="mt-4 pt-3">
            <a href="${BASE_URL}home" class="btn btn-primary btn-rounded w-75">Kembali ke Dashboard</a>
        </div>
    </div>
`;

            const fourthDetailHTML = `
    <div class="detail-box text-center mt-5">
        <h3 class="f-20">Pembayaran Menunggu Konfirmasi</h3>
        <p>Kami mendeteksi bahwa Anda sudah memiliki permintaan konfirmasi pembayaran sebelumnya.<br>
           Mohon bersabar, tim kami sedang memprosesnya. Anda akan mendapatkan notifikasi setelah verifikasi selesai.<br>
           Terima kasih atas kesabaran Anda.
        </p>
        <div class="mt-4 pt-3">
            <a href="${BASE_URL}home" class="btn btn-primary btn-rounded w-75">Kembali ke Dashboard</a>
        </div>
    </div>
`;

            swal.fire({
                title: 'Mohon Tunggu...',
                text: 'Sedang memproses pembayaran Anda',
                icon: 'info',
                showConfirmButton: false,
                allowOutsideClick: false,
                didOpen: () => {
                    swal.showLoading();
                }
            });

            // This is the Ajax call using the Fetch API (modern JavaScript)
            const url = `${BASE_URL}Subscription/proses_bayar`;

            // Fetch call using the dynamic URL
            fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                })
                .then(response => {
                    swal.close();
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status == "success") {

                        swal.fire({
                            customClass: 'slow-animation',
                            icon: 'success',
                            showConfirmButton: false,
                            title: 'Berhasil!',
                            text: data.message,
                            timer: 2000
                        }).then(() => {
                            setTimeout(() => {

                                const id_pembayaran = data.id_pembayaran;
                                const startDate = new Date(data.confirmation_detail.tanggal_mulai.split(' ')[0]);
                                const endDate = new Date(data.confirmation_detail.tanggal_selesai.split(' ')[0]);
                                const startStr = formatDate(startDate);
                                const endStr = formatDate(endDate);

                                // ── Jika server sudah kembalikan QR (pending QRIS lama) ──────
                                if (data.metode_bayar === 'qris' && data.qr_url) {
                                    tampilQRCode(id_pembayaran, data.qr_url, data.confirmation_detail, startStr, endStr, data.expire);
                                    return;
                                }

                                // ── Tampil pilihan metode pembayaran ─────────────────────────
                                const pilihanHTML = `
                <div class="detail-box mt-5">
                    <h3 class="f-20">Konfirmasi Pembayaran</h3>
                    <p>Terima kasih telah memilih Plan <strong>${data.confirmation_detail.paket}</strong>. Berikut rincian pesanan Anda:</p>
                    <ul class="list-unstyled mb-3">
                        <li><strong>Paket</strong> ${data.confirmation_detail.paket}</li>
                        <li><strong>Jangka Waktu</strong> ${data.confirmation_detail.total_bulan} Bulan</li>
                        <li><strong>Tanggal Mulai</strong> ${startStr}</li>
                        <li><strong>Tanggal Selesai</strong> ${endStr}</li>
                    </ul
                    <hr class="my-4">
                    <h5 class="text-center mb-3">Pilih Metode Pembayaran</h5>

                    <div class="payment-row">

    <!-- Kartu VA -->
    <div class="col-12 col-md-5">
        <div class="payment-card" id="card-va" onclick="pilihMetode('va')">
            <div class="pc-logo"><span class="pc-emoji">📱</span></div>
            <div class="pc-title">Virtual Account</div>

            <div class="pc-label">Total Tagihan</div>
            <div class="pc-price">${formatRupiah(Number(data.confirmation_detail.nominal_asli))}</div>
            <p class="pc-note">Belum termasuk biaya layanan</p>

            <div class="pc-divider"></div>
            <div class="pc-info">
                BCA &middot; Mandiri &middot; BNI &middot; BRI &middot; BSI<br>
                dan bank lainnya
            </div>

            <div class="pc-badge-wrap">
                <span class="pc-badge pc-badge-instan">&#9889; Otomatis &amp; Instan</span>
            </div>
        </div>
    </div>

    <!-- Kartu Transfer BSI -->
    <div class="col-12 col-md-5">
        <div class="payment-card" id="card-bsi" onclick="pilihMetode('bsi')">
            <div class="pc-logo">
                <img src="${BASE_URL}assets/images/bank/BSI_1.png" alt="BSI">
            </div>
            <div class="pc-title">Transfer BSI</div>

            <div class="pc-label">Total Tagihan</div>
            <div class="pc-price">${formatRupiah(data.confirmation_detail.nominal)}</div>
            <p class="pc-note">Sudah termasuk 3 digit unik untuk konfirmasi transaksi</p>

            <div class="pc-divider"></div>
            <div class="pc-info">
                Bank Syariah Indonesia<br>
                <span class="pc-rek">79 7070 7004</span>
            </div>

            <div class="pc-badge-wrap">
                <span class="pc-badge pc-badge-manual">&#128337; Konfirmasi Manual</span>
            </div>
        </div>
    </div>

</div>

                    <div class="d-flex justify-content-center">
                        <button type="button" id="btn-lanjut-bayar" class="btn btn-primary btn-rounded px-5"
                                disabled onclick="lanjutBayar(${id_pembayaran}, '${data.confirmation_detail.paket}')">
                            Lanjutkan Pembayaran
                        </button>
                    </div>
                </div>
                `;

                                detailsContainer.innerHTML = pilihanHTML;
                                detailsContainer.classList.add('visible');

                            }, 500);
                        });

                    } else if (data.status == "proses") {
                        swal.fire({
                            customClass: 'slow-animation',
                            icon: 'info',
                            showConfirmButton: false,
                            title: 'Proses!',
                            text: data.message,
                            timer: 3000
                        }).then(() => {
                            detailsContainer.classList.remove('visible');
                            setTimeout(() => {
                                detailsContainer.innerHTML = fourthDetailHTML;
                                detailsContainer.classList.add('visible');
                            }, 500);
                        });
                    } else {
                        swal.fire({
                            customClass: 'slow-animation',
                            icon: 'error',
                            showConfirmButton: false,
                            title: 'Gagal!',
                            text: data.message,
                            timer: 1500
                        });
                    }
                })
                .catch((error) => {
                    swal.close();

                    console.error('Error:', error);
                    // Handle errors, e.g., show an error message to the user
                    alert('Terjadi kesalahan saat mengonfirmasi pembayaran. Silakan coba lagi.');
                });

            // Wait for the fade-out animation to finish
        }

        <?php
        if ($this->session->flashdata('proses') == 'lanjut_bayar') {
        ?>
            lanjutBayar_link();
        <?php
        }
        ?>

        function lanjutBayar_link() {

            console.log('Masuk Lanjut Bayar');

            const url = `${BASE_URL}Subscription/proses_lanjut_bayar`;

            // Fetch call using the dynamic URL
            fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: '{}' // ← ganti dari JSON.stringify(data)
                })
                .then(response => {
                    swal.close();
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status == "success") {

                        swal.fire({
                            customClass: 'slow-animation',
                            icon: 'success',
                            showConfirmButton: false,
                            title: 'Berhasil!',
                            text: data.message,
                            timer: 2000
                        }).then(() => {

                            const allPricingCols = document.querySelectorAll('.col-lg-3.nopadding');

                            allPricingCols.forEach(col => {
                                col.querySelector('.pricing-box').classList.add('hidden');

                                setTimeout(() => {
                                    col.classList.add('d-none');
                                }, 500);
                            });

                            setTimeout(() => {

                                const id_pembayaran = data.id_pembayaran;
                                const startDate = new Date(data.confirmation_detail.tanggal_mulai.split(' ')[0]);
                                const endDate = new Date(data.confirmation_detail.tanggal_selesai.split(' ')[0]);
                                const startStr = formatDate(startDate);
                                const endStr = formatDate(endDate);

                                // ── Jika server sudah kembalikan QR (pending QRIS lama) ──────

                                // ── Tampil pilihan metode pembayaran ─────────────────────────
                                const pilihanHTML = `
                <div class="detail-box mt-5">
                    <h3 class="f-20">Konfirmasi Pembayaran</h3>
                    <p>Terima kasih telah memilih Plan <strong>${data.confirmation_detail.paket}</strong>. Berikut rincian pesanan Anda:</p>
                    <ul class="list-unstyled mb-3">
                        <li><strong>Paket</strong> ${data.confirmation_detail.paket}</li>
                        <li><strong>Jangka Waktu</strong> ${data.confirmation_detail.total_bulan} Bulan</li>
                        <li><strong>Tanggal Mulai</strong> ${startStr}</li>
                        <li><strong>Tanggal Selesai</strong> ${endStr}</li>
                    </ul>
                    <hr class="my-4">
                    <h5 class="text-center mb-3">Pilih Metode Pembayaran</h5>

                    <div class="payment-row">

    <!-- Kartu VA -->
    <div class="col-12 col-md-5">
        <div class="payment-card" id="card-va" onclick="pilihMetode('va')">
            <div class="pc-logo"><span class="pc-emoji">📱</span></div>
            <div class="pc-title">Virtual Account</div>

            <div class="pc-label">Total Tagihan</div>
            <div class="pc-price">${formatRupiah(Number(data.confirmation_detail.nominal_asli))}</div>
            <p class="pc-note">Belum termasuk biaya layanan</p>

            <div class="pc-divider"></div>
            <div class="pc-info">
                BCA &middot; Mandiri &middot; BNI &middot; BRI &middot; BSI<br>
                dan bank lainnya
            </div>

            <div class="pc-badge-wrap">
                <span class="pc-badge pc-badge-instan">&#9889; Otomatis &amp; Instan</span>
            </div>
        </div>
    </div>

    <!-- Kartu Transfer BSI -->
    <div class="col-12 col-md-5">
        <div class="payment-card" id="card-bsi" onclick="pilihMetode('bsi')">
            <div class="pc-logo">
                <img src="${BASE_URL}assets/images/bank/BSI_1.png" alt="BSI">
            </div>
            <div class="pc-title">Transfer BSI</div>

            <div class="pc-label">Total Tagihan</div>
            <div class="pc-price">${formatRupiah(data.confirmation_detail.nominal)}</div>
            <p class="pc-note">Sudah termasuk 3 digit unik untuk konfirmasi transaksi</p>

            <div class="pc-divider"></div>
            <div class="pc-info">
                Bank Syariah Indonesia<br>
                <span class="pc-rek">79 7070 7004</span>
            </div>

            <div class="pc-badge-wrap">
                <span class="pc-badge pc-badge-manual">&#128337; Konfirmasi Manual</span>
            </div>
        </div>
    </div>

</div>

                    <div class="d-flex justify-content-center">
                        <button type="button" id="btn-lanjut-bayar" class="btn btn-primary btn-rounded px-5"
                                disabled onclick="lanjutBayar(${id_pembayaran}, '${data.confirmation_detail.paket}')">
                            Lanjutkan Pembayaran
                        </button>
                    </div>
                </div>
                `;

                                detailsContainer.innerHTML = pilihanHTML;
                                detailsContainer.classList.remove('d-none');
                                detailsContainer.classList.add('visible');

                                console.log('DetailContainer Muncul');

                            }, 500);
                        });

                    } else if (data.status == "proses") {
                        swal.fire({
                            customClass: 'slow-animation',
                            icon: 'info',
                            showConfirmButton: false,
                            title: 'Proses!',
                            text: data.message,
                            timer: 3000
                        }).then(() => {
                            detailsContainer.classList.remove('visible');
                            setTimeout(() => {
                                detailsContainer.innerHTML = fourthDetailHTML;
                                detailsContainer.classList.add('visible');
                            }, 500);
                        });
                    } else {
                        swal.fire({
                            customClass: 'slow-animation',
                            icon: 'error',
                            showConfirmButton: false,
                            title: 'Gagal!',
                            text: data.message,
                            timer: 1500
                        });
                    }
                })
                .catch((error) => {
                    swal.close();

                    console.error('Error:', error);
                    // Handle errors, e.g., show an error message to the user
                    alert('Terjadi kesalahan saat mengonfirmasi pembayaran. Silakan coba lagi.');
                });

        }

        function handlePilihDonasiClick(event) {
            event.preventDefault(); // Prevent the default link behavior

            const selectedButton = event.currentTarget;
            const selectedPricingBox = selectedButton.closest('.pricing-box');
            const planName = selectedPricingBox.querySelector('h4').innerText;
            const basePrice = parseInt(selectedButton.getAttribute('data-price'));
            const planDetailsHTML = selectedPricingBox.querySelector('.mt-1.pt-2').innerHTML;

            // Hide other pricing boxes with animation
            // pricingBoxes.forEach(box => {
            //     if (box !== selectedPricingBox) {
            //         box.classList.add('hidden');
            //         box.closest('.col-lg-3').style.display = 'none';
            //     }
            // });

            const allPricingCols = document.querySelectorAll('.col-lg-3.nopadding');

            allPricingCols.forEach(col => {
                if (col.querySelector('.pricing-box') !== selectedPricingBox) {
                    col.querySelector('.pricing-box').classList.add('hidden');

                    setTimeout(() => {
                        col.classList.add('d-none');
                    }, 500);
                }


            });
            // setTimeout(() => {
            //     pricingBoxes.forEach(box => {
            //         if (box !== selectedPricingBox) {
            //             box.classList.add('hidden');
            //             box.closest('.col-lg-3').style.display = 'none';
            //         }
            //     });
            // }, 600);
            // After the animation (0.5s), display the detail container
            setTimeout(() => {
                // Adjust width of the selected box
                // detailsContainer.classList.remove('d-none');

                const selectedCol = selectedPricingBox.closest('.col-lg-3');
                selectedCol.classList.remove('col-lg-3', 'nopadding', 'noborderradius-right', 'noborderradius', 'noborderradius-left', 'mt-5');
                selectedCol.classList.add('col-lg-3', 'd-flex', 'flex-column', 'justify-content-center');

                selectedPricingBox.classList.remove('nopadding', 'noborderradius-right', 'noborderradius', 'noborderradius-left');
                selectedPricingBox.classList.add('selected-col');

                selectedPricingBox.style.height = 'auto';
                selectedPricingBox.classList.remove('feature');

                // Generate and display the new payment details content
                const detailHTML = `
                <div class="detail-box">
    <h3 class="f-20">Donasi</h3>
</ul>
    <hr>
    <h4 class="text-center">Silakan Donasi Ke Rekening Berikut:</h4>
    
    <div class="mt-4 pt-3 text-center">
        <img src="${BASE_URL}assets/images/bank/BSI_1.png" alt="Logo Bank BSI" class="mb-2 w-25">
        <h2 class="mt-3 text-bariskode">79 7070 7004 (BSI) - PT. Baris Kode Indonesia</h2>
    </div>
</div>
            `;
                detailsContainer.classList.remove('d-none');

                detailsContainer.innerHTML = detailHTML;
                setTimeout(() => {
                    detailsContainer.classList.add('visible');
                }, 300);


                // Add event listeners to the new month selection buttons
                const monthButtons = detailsContainer.querySelectorAll('.month-btn');
                monthButtons.forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        // Remove 'selected' class from all buttons
                        monthButtons.forEach(b => b.classList.remove('selected'));
                        // Add 'selected' class to the clicked button
                        e.currentTarget.classList.add('selected');

                        const months = parseInt(e.currentTarget.getAttribute('data-months'));
                        const totalPrice = basePrice * months;
                        document.getElementById('total-price').innerText = formatRupiah(totalPrice);
                        document.getElementById('months-display').innerText = months;
                    });
                });

                // Set the default button to selected (1 Month)
                if (monthButtons.length > 0) {
                    monthButtons[0].classList.add('selected');
                }

                // ADD THE BACK BUTTON LISTENER HERE
                document.getElementById('back-button').addEventListener('click', handleBackClick);
                // document.getElementById('btn-pembayaran').addEventListener('click', detailPembayaran);
                document.getElementById('btn-pembayaran').addEventListener('click', () => {
                    // Pass the necessary data to the new function
                    const selectedMonths = parseInt(detailsContainer.querySelector('.month-btn.selected').getAttribute('data-months'));
                    detailPembayaran(planName, basePrice, selectedMonths);
                });


            }, 500); // Wait for the zoom-out animation to finish
        }


        // Add click event listeners to all "Pilih Tahta" buttons
        pilihTahtaButtons.forEach(button => {
            button.addEventListener('click', handlePilihTahtaClick);
        });
        pilihDonasiButtons.forEach(button => {
            button.addEventListener('click', handlePilihDonasiClick);
        });

    });



    // A helper function to format the price in Rupiah
    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(number);
    }

    // ============================================================
    // FUNGSI-FUNGSI BARU — tambahkan di luar fetch, di scope global
    // ============================================================

    /** State metode yang dipilih */
    let metodeBayarDipilih = null;

    /** Handle klik kartu metode bayar */
    function pilihMetode(metode) {
        metodeBayarDipilih = metode;

        // Reset semua kartu
        document.querySelectorAll('.payment-card').forEach(el => {
            el.style.borderColor = '#dee2e6';
            el.style.background = '#fff';
            el.style.boxShadow = 'none';
        });

        // Highlight kartu terpilih
        const card = document.getElementById('card-' + metode);

        let warna;
        if (metode == 'qris') {
            warna = '#00C48C';
            card.style.background = '#f0fdf8';
        } else if (metode == 'va') {
            warna = '#006fc4ff';
            card.style.background = '#f0fdf8';
        } else {
            warna = '#1a7f37';
            card.style.background = '#f6fff0';

        }
        card.style.borderColor = warna;
        // card.style.background = metode === 'qris' ? '#f0fdf8' : '#f6fff0';
        card.style.boxShadow = `0 0 0 3px ${warna}33`;

        document.getElementById('btn-lanjut-bayar').disabled = false;
    }


    /** Lanjutkan sesuai metode yang dipilih */
    function lanjutBayar(id_pembayaran, nama_paket) {
        if (!metodeBayarDipilih) return;

        if (metodeBayarDipilih === 'qris') {
            prosesQRIS(id_pembayaran);
        } else if (metodeBayarDipilih === 'va') {
            prosesVA(id_pembayaran, nama_paket);
        } else {
            prosesBSI(id_pembayaran, nama_paket);
        }
    }

    // ============================================================
    // VIRTUAL ACCOUNT
    // ============================================================

    /** Daftar bank VA — kode mengikuti paymentMethod Duitku */
    const DAFTAR_BANK_VA = [{
            kode: 'BC',
            nama: 'BCA Virtual Account',
            logo: 'BCA.png'
        },
        {
            kode: 'M2',
            nama: 'Mandiri Virtual Account',
            logo: 'MANDIRI.png'
        },
        {
            kode: 'BR',
            nama: 'BRIVA',
            logo: 'BRI.png'
        },
        {
            kode: 'I1',
            nama: 'BNI Virtual Account',
            logo: 'BNI.png'
        },
        {
            kode: 'BV',
            nama: 'BSI Virtual Account',
            logo: 'BSI_1.png'
        },
        {
            kode: 'BT',
            nama: 'Permata Bank Virtual Account',
            logo: 'PERMATA.png'
        },
        {
            kode: 'B1',
            nama: 'CIMB Niaga Virtual Account',
            logo: 'CIMB.png'
        },
        {
            kode: 'DM',
            nama: 'Danamon Virtual Account',
            logo: 'DANAMON.png'
        },
        {
            kode: 'VA',
            nama: 'Maybank Virtual Account',
            logo: 'MAYBANK.png'
        },
        {
            kode: 'NC',
            nama: 'Bank Neo Commerce/BNC',
            logo: 'BNC.png'
        },
        {
            kode: 'AG',
            nama: 'Bank Artha Graha',
            logo: 'ARTHAGRAHA.png'
        },
        {
            kode: 'S1',
            nama: 'Bank Sahabat Sampoerna',
            logo: 'SAMPOERNA.png'
        },
        {
            kode: 'A1',
            nama: 'ATM Bersama',
            logo: 'ATMBERSAMA.png'
        },
    ];

    /** State bank yang dipilih */
    let bankVADipilih = null;

    /** Handle klik kartu bank */
    function pilihBankVA(kode) {
        bankVADipilih = kode;
        console.log('pilihBankVA : ' + bankVADipilih);

        document.querySelectorAll('.bank-card').forEach(el => {
            el.style.borderColor = '#dee2e6';
            el.style.background = '#fff';
            el.style.boxShadow = 'none';
        });

        const card = document.getElementById('bank-' + kode);
        if (card) {
            card.style.borderColor = '#006fc4';
            card.style.background = '#f0f7ff';
            card.style.boxShadow = '0 0 0 3px #006fc433';
        }

        const btn = document.getElementById('btn-buat-va');
        if (btn) btn.disabled = false;
    }

    /** ── VA: tampilkan kartu pilihan bank ── */
    function prosesVA(id_pembayaran, nama_paket) {
        // Simpan tampilan pilihan metode supaya tombol "Kembali" bisa memulihkannya
        window._htmlPilihanMetode = detailsContainer.innerHTML;
        bankVADipilih = null;

        const kartuBank = DAFTAR_BANK_VA.map(b => `
            <div class="col-6 col-md-4 col-lg-3 mb-2">
                <div class="bank-card" id="bank-${b.kode}" onclick="pilihBankVA('${b.kode}')"
                     style="border:2px solid #dee2e6; border-radius:12px; padding:14px 10px; cursor:pointer;
                            text-align:center; transition:all .2s; background:#fff; height:100%;
                            display:flex; flex-direction:column; align-items:center; justify-content:center;">
                    <img src="${BASE_URL}assets/images/bank/${b.logo}" alt="${b.nama}"
                         style="height:30px; object-fit:contain; margin-bottom:8px; max-width: 105px"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div style="display:none; height:30px; width:52px; margin-bottom:8px; border-radius:6px;
                                background:#eef2ff; color:#3f51b5; font-weight:700; font-size:13px;
                                align-items:center; justify-content:center;">${b.kode}</div>
                    <small class="fw-bold" style="line-height:1.25; font-size:12px;">${b.nama}</small>
                </div>
            </div>
        `).join('');

        const vaHTML = `
        <div class="detail-box mt-5">
            <h3 class="f-20">Pilih Bank Virtual Account</h3>
            <p class="text-muted">Paket <strong>${nama_paket}</strong> — silakan pilih bank tujuan transfer.
               Nomor Virtual Account akan dibuat otomatis setelah Anda menekan tombol di bawah.</p>

            <div class="row g-2 g-md-3 mt-3 mb-4">
                ${kartuBank}
            </div>

            <div class="d-flex justify-content-center">
                <button type="button" id="btn-buat-va" class="btn btn-primary btn-rounded px-5" disabled
                        onclick="buatVA(${id_pembayaran})">
                    Buat Virtual Account
                </button>
            </div>
            <div class="mt-3 d-flex justify-content-center">
                <button type="button" class="btn btn-outline-secondary btn-rounded px-4" onclick="kembaliKeMetode()">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke Pilihan Metode
                </button>
            </div>
        </div>`;

        detailsContainer.classList.remove('visible');
        setTimeout(() => {
            detailsContainer.innerHTML = vaHTML;
            detailsContainer.classList.add('visible');
        }, 500);
    }

    /** Kembali ke kartu pilihan metode pembayaran */
    function kembaliKeMetode() {
        if (!window._htmlPilihanMetode) return;

        detailsContainer.classList.remove('visible');
        setTimeout(() => {
            detailsContainer.innerHTML = window._htmlPilihanMetode;
            detailsContainer.classList.add('visible');
            if (metodeBayarDipilih) pilihMetode(metodeBayarDipilih);
        }, 500);
    }

    /** ── VA: minta nomor Virtual Account ke server ── */
    function buatVA(id_pembayaran) {
        if (!bankVADipilih) return;

        swal.fire({
            title: 'Membuat Virtual Account...',
            text: 'Mohon tunggu sebentar',
            icon: 'info',
            showConfirmButton: false,
            allowOutsideClick: false,
            didOpen: () => swal.showLoading()
        });

        fetch(`${BASE_URL}Subscription/buat_va_untuk_pembayaran/${id_pembayaran}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    bank_code: bankVADipilih
                })
            })
            .then(r => r.json())
            .then(res => {
                swal.close();
                if (res.status !== 'success') {
                    swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: res.message || 'Tidak dapat membuat Virtual Account.',
                        timer: 2500,
                        showConfirmButton: false
                    });
                    return;
                }
                tampilVA(id_pembayaran, res.qr_url, res.confirmation_detail, res.start_str, res.end_str, res.expire);
            })
            .catch(err => {
                swal.close();
                console.error('Fetch error:', err);
                swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Koneksi gagal. Coba lagi.',
                    timer: 2000,
                    showConfirmButton: false
                });
            });
    }

    /** Salin nomor VA ke clipboard */
    function salinVA() {
        const el = document.getElementById('va-number');
        if (!el) return;
        const nomor = el.innerText.trim();

        const selesai = () => swal.fire({
            icon: 'success',
            title: 'Tersalin!',
            text: nomor,
            timer: 1200,
            showConfirmButton: false
        });

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(nomor).then(selesai);
        } else {
            const tmp = document.createElement('textarea');
            tmp.value = nomor;
            tmp.style.position = 'fixed';
            tmp.style.opacity = '0';
            document.body.appendChild(tmp);
            tmp.select();
            document.execCommand('copy');
            document.body.removeChild(tmp);
            selesai();
        }
    }

    /** Countdown timer VA */
    function mulaiCountdownVA(detik, id_pembayaran) {
        clearInterval(window._vaCountdown);
        let s = detik;
        const el = document.getElementById('va-countdown');

        function tick() {
            if (!el) {
                clearInterval(window._vaCountdown);
                return;
            }
            const jam = String(Math.floor(s / 3600)).padStart(2, '0');
            const m = String(Math.floor((s % 3600) / 60)).padStart(2, '0');
            const sec = String(s % 60).padStart(2, '0');
            el.textContent = (s >= 3600 ? jam + ':' : '') + m + ':' + sec;

            if (s <= 300) el.closest('.badge').className = 'badge bg-danger fs-6 px-3 py-2';
            if (s <= 0) {
                clearInterval(window._vaCountdown);
                clearInterval(window._vaPolling);
                swal.fire({
                    icon: 'warning',
                    title: 'Virtual Account Kadaluarsa',
                    text: 'Nomor VA sudah kadaluarsa. Silakan mulai ulang proses pembayaran.',
                    confirmButtonText: 'OK'
                }).then(() => {
                    detailsContainer.classList.remove('visible');
                    setTimeout(() => {
                        detailsContainer.innerHTML = '';
                    }, 500);
                });
            }
            s--;
        }
        tick();
        window._vaCountdown = setInterval(tick, 1000);
    }

    /** Polling cek status VA */
    function cekStatusVA(id_pembayaran) {
        fetch(`${BASE_URL}Subscription/cek_status_va/${id_pembayaran}`)
            .then(r => r.json())
            .then(res => {
                if (res.status === 'settlement') {
                    clearInterval(window._vaPolling);
                    clearInterval(window._vaCountdown);

                    swal.fire({
                        customClass: 'slow-animation',
                        icon: 'success',
                        title: 'Pembayaran Berhasil! 🎉',
                        text: 'Paket Anda telah aktif. Terima kasih!',
                        showConfirmButton: false,
                        timer: 2500
                    }).then(() => {
                        detailsContainer.classList.remove('visible');
                        const suksesHTML = `
    <div class="detail-box text-center mt-5">
        <h3 class="f-20">Pembayaran Berhasil! 🎉</h3>
        <p>Terima kasih! Pembayaran Anda melalui Virtual Account telah berhasil dikonfirmasi secara otomatis.<br>
           Paket langganan Anda kini telah aktif dan siap digunakan.<br>
           Silakan logout dan login kembali untuk menikmati fitur premium Anda.
        </p>
        <div class="mt-4 pt-3">
            <a href="${BASE_URL}home" class="btn btn-primary btn-rounded w-75">Kembali ke Dashboard</a>
        </div>
    </div>`;
                        setTimeout(() => {
                            detailsContainer.innerHTML = suksesHTML;
                            detailsContainer.classList.add('visible');
                        }, 500);
                    });

                } else if (['expire', 'cancel', 'deny'].includes(res.status)) {
                    clearInterval(window._vaPolling);
                    clearInterval(window._vaCountdown);
                    swal.fire({
                        icon: 'error',
                        title: 'Pembayaran Gagal',
                        text: 'Status: ' + res.status + '. Silakan coba lagi.',
                        confirmButtonText: 'OK'
                    });
                }
                // 'pending' → lanjut polling
            })
            .catch(err => console.error('Polling VA error:', err));
    }

    /** Batalkan VA, kembali ke pilihan metode */
    function batalVA() {
        // clearInterval(window._vaPolling);
        // clearInterval(window._vaCountdown);
        swal.fire({
            icon: 'info',
            title: 'Dibatalkan',
            text: 'Silakan pilih metode pembayaran kembali.',
            confirmButtonText: 'OK'
        }).then(() => {
            // detailsContainer.classList.remove('visible');
            setTimeout(() => {
                lanjutBayar_link2();
            }, 500);
        });
    }

    /** ── QRIS: request QR Code ke server ── */
    function prosesQRIS(id_pembayaran) {
        swal.fire({
            title: 'Membuat QR Code...',
            text: 'Mohon tunggu sebentar',
            icon: 'info',
            showConfirmButton: false,
            allowOutsideClick: false,
            didOpen: () => swal.showLoading()
        });

        // Ambil data dari form (sesuaikan dengan variabel yang ada di scope kamu)
        // Kita re-POST ke proses_bayar dengan metode_bayar = 'qris'
        // Namun kita sudah punya id_pembayaran — langsung minta QR dari endpoint baru
        fetch(`${BASE_URL}Subscription/buat_qris_untuk_pembayaran/${id_pembayaran}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({})
            })
            .then(r => r.json()) // <-- kembalikan ke .json()

            .then(res => {
                swal.close();
                if (res.status !== 'success') {
                    swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: res.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    return;
                }

                // Ambil detail transaksi dari DOM (sudah ada di detailsContainer sebelumnya)
                // — pakai data yang sudah di-render sebelumnya —
                tampilQRCode(id_pembayaran, res.qr_url, res.confirmation_detail, res.start_str, res.end_str, res.expire);
            })
            .catch((err) => {
                swal.close();
                console.error('Fetch error:', err); // <-- tambah ini
                swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Koneksi gagal. Coba lagi.',
                    timer: 2000,
                    showConfirmButton: false
                });
            });
    }

    /** ── BSI: tampil info rekening + tombol konfirmasi (alur lama) ── */
    function prosesBSI(id_pembayaran, nama_paket) {
        // Ambil detail dari DOM yang sudah ada
        const detailItems = document.querySelectorAll('#detailsContainer .list-unstyled li');

        fetch(`${BASE_URL}Subscription/get_detail_pembayaran/${id_pembayaran}`, {
                method: 'GET'
            })
            .then(r => r.json())
            .then(res => {
                if (res.status !== 'success') return;

                const trx = res.data;
                const startDate = new Date(trx.tanggal_mulai.split(' ')[0]);
                const endDate = new Date(trx.tanggal_selesai.split(' ')[0]);

                const bsiHTML = `
        <div class="detail-box mt-5">
            <h3 class="f-20">Konfirmasi Pembayaran</h3>
            <p>Terima kasih telah memilih Plan <strong>${trx.paket}</strong>. Berikut rincian pesanan Anda:</p>
            <ul class="list-unstyled">
                <li><strong>Paket</strong> ${trx.paket}</li>
                <li><strong>Jangka Waktu</strong> ${trx.total_bulan} Bulan</li>
                <li><strong>Tanggal Mulai</strong> ${formatDate(startDate)}</li>
                <li><strong>Tanggal Selesai</strong> ${formatDate(endDate)}</li>
            </ul>
            <hr>
            <h4 class="text-center">Total Tagihan:</h4>
            <h2 class="text-bariskode text-center">${formatRupiah(trx.nominal)}</h2>
            <p class="text-center text-muted f-12">Total akhir sudah termasuk 3 digit unik untuk konfirmasi transaksi.</p>

            <div class="mt-4 pt-3 text-center">
                <img src="${BASE_URL}assets/images/bank/BSI_1.png" alt="Logo Bank BSI" class="mb-2 w-25">
                <h2 class="mt-3 text-bariskode">79 7070 7004 (BSI) - PT. Baris Kode Indonesia</h2>
            </div>

            <div class="mt-4 pt-3 d-flex justify-content-center">
                <button type="button" id="pay-now-btn" class="btn btn-primary btn-rounded w-50">Konfirmasi Pembayaran</button>
            </div>
        </div>`;

                detailsContainer.classList.remove('visible');
                setTimeout(() => {
                    detailsContainer.innerHTML = bsiHTML;
                    detailsContainer.classList.add('visible');

                    // Tombol konfirmasi BSI (alur lama persis seperti sebelumnya)
                    document.getElementById('pay-now-btn').addEventListener('click', () => {
                        swal.fire({
                            title: 'Mohon Tunggu...',
                            text: 'Sedang memproses pembayaran Anda',
                            icon: 'info',
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            didOpen: () => swal.showLoading()
                        });

                        fetch(`${BASE_URL}Subscription/proses_bayar_konfirmasi/${id_pembayaran}`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({})
                            })
                            .then(r => r.json())
                            .then(d => {
                                swal.close();
                                if (d.status == "success") {
                                    swal.fire({
                                            customClass: 'slow-animation',
                                            icon: 'success',
                                            showConfirmButton: false,
                                            title: 'Berhasil!',
                                            text: d.message,
                                            timer: 1500
                                        })
                                        .then(() => {
                                            detailsContainer.classList.remove('visible');
                                            setTimeout(() => {
                                                const thirdDetailHTML = `
    <div class="detail-box text-center mt-5">
        <h3 class="f-20">Pembayaran Terkirim!</h3>
        <p>Terima kasih. Permintaan pembayaran Anda telah berhasil kami terima. <br>
           Mohon tunggu beberapa saat, tim kami akan segera memprosesnya.
           Anda akan menerima pesan konfirmasi melalui WhatsApp setelah pembayaran Anda disetujui.
        </p>
        <div class="mt-4 pt-3">
            <a href="${BASE_URL}home" class="btn btn-primary btn-rounded w-75">Kembali ke Dashboard</a>
        </div>
    </div>
`;
                                                detailsContainer.innerHTML = thirdDetailHTML;
                                                detailsContainer.classList.add('visible');
                                            }, 500);
                                        });
                                } else if (d.status == "proses") {
                                    swal.fire({
                                            customClass: 'slow-animation',
                                            icon: 'info',
                                            showConfirmButton: false,
                                            title: 'Proses!',
                                            text: d.message,
                                            timer: 3000
                                        })
                                        .then(() => {
                                            detailsContainer.classList.remove('visible');
                                            setTimeout(() => {
                                                detailsContainer.innerHTML = fourthDetailHTML;
                                                detailsContainer.classList.add('visible');
                                            }, 500);
                                        });
                                } else {
                                    swal.fire({
                                        customClass: 'slow-animation',
                                        icon: 'error',
                                        showConfirmButton: false,
                                        title: 'Gagal!',
                                        text: d.message,
                                        timer: 1500
                                    });
                                }
                            });
                    });
                }, 500);
            });
    }

    /** ── Tampil QR Code + countdown + polling ── */
    function tampilQRCode(id_pembayaran, qr_url, trx, startStr, endStr, expire_time) {

        // Hitung sisa waktu countdown
        let sisaDetik = 900; // default 15 menit
        if (expire_time) {
            const expDate = new Date(expire_time.replace(' ', 'T'));
            sisaDetik = Math.max(0, Math.round((expDate - Date.now()) / 1000));
        }

        const qrHTML = `
<div class="detail-box mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h3 class="f-20 mb-0">Lanjutkan Pembayaran</h3>
        <span class="badge bg-warning text-dark fs-6 px-3 py-2">
            ⏳ <span id="qr-countdown">60:00</span>
        </span>
    </div>

    <div class="text-center mb-3">
        <div id="qrcode-canvas" style="width:100%;"></div>
    </div>

    <div class="row text-center mb-3">
        <div class="col">
            <small class="text-muted d-block">Paket</small>
            <strong>${trx ? trx.paket : '-'}</strong>
        </div>
        <div class="col">
            <small class="text-muted d-block">Total</small>
            <strong class="text-bariskode">${trx ? formatRupiah(trx.nominal) : '-'}</strong>
        </div>
    </div>

    <div style="background:#f8f9fa; border-radius:12px; padding:14px 16px; margin-bottom:16px;">
        <p class="fw-bold mb-2 f-12 text-muted">CARA BAYAR:</p>
        <ol class="mb-0 ps-3" style="font-size:13px; line-height:1.8;">
            <li>Klik tombol <strong>Bayar Sekarang via QRIS</strong> di atas</li>
            <li>Pilih metode pembayaran QRIS di halaman Duitku</li>
            <li>Scan QR Code menggunakan e-wallet kamu</li>
            <li>Konfirmasi nominal & selesaikan pembayaran</li>
        </ol>
    </div>

    <p class="text-center text-muted f-12">
        Mendukung semua e-wallet berlogo QRIS 🟥<br>
        Pembayaran akan terkonfirmasi otomatis.
    </p>

    <div class="text-center mt-3">
        <button class="btn btn-outline-secondary btn-sm" onclick="batalQRIS()">
            Ganti Metode Pembayaran
        </button>
    </div>
</div>`;

        detailsContainer.classList.remove('visible');
        setTimeout(() => {
            detailsContainer.innerHTML = qrHTML;
            detailsContainer.classList.add('visible');

            // ── Render QR Code ──────────────────────────────────────────
            // Cek apakah qr_url adalah URL gambar (Midtrans) atau qrString teks (Duitku)
            // const isImageUrl = qr_url.startsWith('http') || qr_url.startsWith('https');
            const isImageUrl = qr_url.startsWith('http') || qr_url.startsWith('https');

            //     if (isImageUrl) {
            //         // Duitku: paymentUrl → tampil sebagai tombol
            //         const canvas = document.getElementById('qrcode-canvas');
            //         canvas.innerHTML = `
            // <div style="padding:20px;">
            //     <p class="text-muted f-12 mb-3">Klik tombol di bawah untuk melanjutkan pembayaran QRIS</p>
            //     <a href="${qr_url}" target="_blank" class="btn btn-primary btn-rounded px-4">
            //         Bayar Sekarang via QRIS
            //     </a>
            // </div>`;
            //     } 
            if (isImageUrl) {
                const canvas = document.getElementById('qrcode-canvas');
                const isMobile = window.innerWidth <= 768;

                const scale = isMobile ? 0.55 : 0.75;
                const width = isMobile ? '180%' : '150%';
                const height = isMobile ? '750px' : '650px';
                const boxHeight = isMobile ? '420px' : '480px';

                canvas.style.cssText = 'width:100%;';
                canvas.innerHTML = `
        <div style="width:100%; height:${boxHeight}; overflow:hidden; 
                    border-radius:12px; position:relative;">
            <iframe 
                src="${qr_url}" 
                style="width:${width}; height:${height}; border:none;
                       transform: scale(${scale}); transform-origin: top left;
                       position:absolute; top:0; left:0;"
                allow="payment">
            </iframe>
        </div>`;
            } else {
                // Midtrans: qrString → render pakai qrcodejs
                _renderQRCode(qr_url);
            }
            // ── End Render QR Code ──────────────────────────────────────

            // Countdown
            mulaiCountdown(sisaDetik, id_pembayaran);

            // Polling status setiap 4 detik
            window._qrisPolling = setInterval(() => cekStatusQRIS(id_pembayaran), 4000);

        }, 500);
    }

    function tampilVA(id_pembayaran, qr_url, trx, startStr, endStr, expire_time) {

        // Hitung sisa waktu countdown
        let sisaDetik = 900; // default 15 menit
        if (expire_time) {
            const expDate = new Date(expire_time.replace(' ', 'T'));
            sisaDetik = Math.max(0, Math.round((expDate - Date.now()) / 1000));
        }

        const qrHTML = `
<div class="detail-box mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h3 class="f-20 mb-0">Lanjutkan Pembayaran</h3>
        <span class="badge bg-warning text-dark fs-6 px-3 py-2">
            ⏳ <span id="qr-countdown">60:00</span>
        </span>
    </div>

    <div class="text-center mb-3">
        <div id="qrcode-canvas" style="width:100%;"></div>
    </div>

    <div class="row text-center mb-3">
        <div class="col">
            <small class="text-muted d-block">Paket</small>
            <strong>${trx ? trx.paket : '-'}</strong>
        </div>
        <div class="col">
            <small class="text-muted d-block">Total</small>
            <strong class="text-bariskode">${trx ? formatRupiah(trx.nominal) : '-'}</strong>
        </div>
    </div>

    <div style="background:#f8f9fa; border-radius:12px; padding:14px 16px; margin-bottom:16px;">
        <p class="fw-bold mb-2 f-12 text-muted">CARA BAYAR:</p>
        <ol class="mb-0 ps-3" style="font-size:13px; line-height:1.8;">
            <li>Klik tombol <strong>Cek Transaksi</strong> di atas</li>
            <li>Pilih bank Virtual Account yang kamu inginkan (misal: BCA, Mandiri, BRI)</li>
            <li>Salin <strong>Nomor Virtual Account</strong> yang muncul di layar</li>
            <li>Buka aplikasi m-Banking / ATM, pilih menu <strong>Transfer / Pembayaran Virtual Account</strong></li>
            <li>Masukkan nomor VA, periksa nama & nominal, lalu selesaikan pembayaran</li>
        </ol>
    </div>

    <div class="text-center mt-3">
        <button class="btn btn-outline-secondary btn-sm" onclick="batalVA()">
            Ganti Metode Pembayaran
        </button>
    </div>
</div>`;

        detailsContainer.classList.remove('visible');
        setTimeout(() => {
            detailsContainer.innerHTML = qrHTML;
            detailsContainer.classList.add('visible');

            // ── Render QR Code ──────────────────────────────────────────
            // Cek apakah qr_url adalah URL gambar (Midtrans) atau qrString teks (Duitku)
            // const isImageUrl = qr_url.startsWith('http') || qr_url.startsWith('https');
            const isImageUrl = qr_url.startsWith('http') || qr_url.startsWith('https');

            //     if (isImageUrl) {
            //         // Duitku: paymentUrl → tampil sebagai tombol
            //         const canvas = document.getElementById('qrcode-canvas');
            //         canvas.innerHTML = `
            // <div style="padding:20px;">
            //     <p class="text-muted f-12 mb-3">Klik tombol di bawah untuk melanjutkan pembayaran QRIS</p>
            //     <a href="${qr_url}" target="_blank" class="btn btn-primary btn-rounded px-4">
            //         Bayar Sekarang via QRIS
            //     </a>
            // </div>`;
            //     } 
            if (isImageUrl) {
                const canvas = document.getElementById('qrcode-canvas');
                const isMobile = window.innerWidth <= 768;

                const scale = isMobile ? 0.55 : 0.75;
                const width = isMobile ? '180%' : '150%';
                const height = isMobile ? '750px' : '650px';
                const boxHeight = isMobile ? '420px' : '480px';

                canvas.style.cssText = 'width:100%;';
                canvas.innerHTML = `
        <div style="width:100%; height:${boxHeight}; overflow:hidden; 
                    border-radius:12px; position:relative;">
            <iframe 
                src="${qr_url}" 
                style="width:${width}; height:${height}; border:none;
                       transform: scale(${scale}); transform-origin: top left;
                       position:absolute; top:0; left:0;"
                allow="payment">
            </iframe>
        </div>`;
            } else {
                // Midtrans: qrString → render pakai qrcodejs
                _renderQRCode(qr_url);
            }
            // ── End Render QR Code ──────────────────────────────────────

            // Countdown
            mulaiCountdown(sisaDetik, id_pembayaran);

            // Polling status setiap 4 detik
            window._qrisPolling = setInterval(() => cekStatusQRIS(id_pembayaran), 4000);

        }, 500);
    }

    function bukaPopupDuitku(reference) {
        checkout.process(reference, {
            onSuccess: function(result) {
                clearInterval(window._qrisPolling);
                clearInterval(window._qrisCountdown);
                swal.fire({
                    icon: 'success',
                    title: 'Pembayaran Berhasil! 🎉',
                    text: 'Paket Anda telah aktif. Terima kasih!',
                    showConfirmButton: false,
                    timer: 2500
                }).then(() => {
                    detailsContainer.classList.remove('visible');
                    setTimeout(() => {
                        detailsContainer.innerHTML = `
                        <div class="detail-box text-center mt-5">
                            <h3 class="f-20">Pembayaran Berhasil! 🎉</h3>
                            <p>Terima kasih! Pembayaran Anda telah berhasil dikonfirmasi.<br>
                               Silakan logout dan login kembali untuk menikmati fitur premium Anda.
                            </p>
                            <div class="mt-4 pt-3">
                                <a href="${BASE_URL}home" class="btn btn-primary btn-rounded w-75">Kembali ke Dashboard</a>
                            </div>
                        </div>`;
                        detailsContainer.classList.add('visible');
                    }, 500);
                });
            },
            onPending: function(result) {
                console.log('Pending:', result);
            },
            onError: function(result) {
                swal.fire({
                    icon: 'error',
                    title: 'Pembayaran Gagal',
                    text: 'Silakan coba lagi.',
                    confirmButtonText: 'OK'
                });
            },
            onClose: function() {
                console.log('Popup ditutup');
            }
        });
    }

    /**
     * Render qrString ke canvas menggunakan qrcodejs
     * Load library dari CDN jika belum ada
     */
    function _renderQRCode(qrString) {
        const containerId = 'qrcode-canvas';

        function doRender() {
            const container = document.getElementById(containerId);
            if (!container) return;
            container.innerHTML = ''; // bersihkan dulu
            new QRCode(container, {
                text: qrString,
                width: 220,
                height: 220,
                colorDark: '#000000',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.M
            });
        }

        // Jika qrcodejs sudah ter-load, langsung render
        if (typeof QRCode !== 'undefined') {
            doRender();
            return;
        }

        // Belum ada — load dari CDN dulu
        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js';
        script.onload = doRender;
        script.onerror = () => {
            const container = document.getElementById(containerId);
            if (container) {
                container.innerHTML = `<p class="text-danger f-12">Gagal memuat QR Code.<br>Refresh halaman dan coba lagi.</p>`;
            }
        };
        document.head.appendChild(script);
    }

    /** Countdown timer QR */
    function mulaiCountdown(detik, id_pembayaran) {
        clearInterval(window._qrisCountdown);
        let s = detik;
        const el = document.getElementById('qr-countdown');

        function tick() {
            if (!el) {
                clearInterval(window._qrisCountdown);
                return;
            }
            const m = String(Math.floor(s / 60)).padStart(2, '0');
            const sec = String(s % 60).padStart(2, '0');
            el.textContent = m + ':' + sec;

            if (s <= 60) el.closest('.badge').className = 'badge bg-danger fs-6 px-3 py-2';
            if (s <= 0) {
                clearInterval(window._qrisCountdown);
                clearInterval(window._qrisPolling);
                swal.fire({
                    icon: 'warning',
                    title: 'QR Kadaluarsa',
                    text: 'QR Code sudah kadaluarsa. Silakan mulai ulang proses pembayaran.',
                    confirmButtonText: 'OK'
                }).then(() => {
                    detailsContainer.classList.remove('visible');
                    setTimeout(() => {
                        detailsContainer.innerHTML = '';
                    }, 500);
                });
            }
            s--;
        }
        tick();
        window._qrisCountdown = setInterval(tick, 1000);
    }

    /** Polling cek status QRIS */
    function cekStatusQRIS(id_pembayaran) {
        // const detailsContainer = document.getElementById('pricing-details-container');

        fetch(`${BASE_URL}Subscription/cek_status_qris/${id_pembayaran}`)
            .then(r => r.json())
            .then(res => {
                if (res.status === 'settlement') {
                    clearInterval(window._qrisPolling);
                    clearInterval(window._qrisCountdown);

                    swal.fire({
                        customClass: 'slow-animation',
                        icon: 'success',
                        title: 'Pembayaran Berhasil! 🎉',
                        text: 'Paket Anda telah aktif. Terima kasih!',
                        showConfirmButton: false,
                        timer: 2500
                    }).then(() => {
                        detailsContainer.classList.remove('visible');
                        const thirdDetailHTML = `
    <div class="detail-box text-center mt-5">
    <h3 class="f-20">Pembayaran Berhasil! 🎉</h3>
    <p>Terima kasih! Pembayaran Anda melalui QRIS telah berhasil dikonfirmasi secara otomatis.<br>
       Paket langganan Anda kini telah aktif dan siap digunakan.<br>
       Silakan logout dan login kembali untuk menikmati fitur premium Anda.
    </p>
    <div class="mt-4 pt-3">
        <a href="${BASE_URL}home" class="btn btn-primary btn-rounded w-75">Kembali ke Dashboard</a>
    </div>
</div>
`;
                        setTimeout(() => {
                            detailsContainer.innerHTML = thirdDetailHTML;
                            detailsContainer.classList.add('visible');
                        }, 500);
                    });

                } else if (['expire', 'cancel', 'deny'].includes(res.status)) {
                    clearInterval(window._qrisPolling);
                    clearInterval(window._qrisCountdown);
                    swal.fire({
                        icon: 'error',
                        title: 'Pembayaran Gagal',
                        text: 'Status: ' + res.status + '. Silakan coba lagi.',
                        confirmButtonText: 'OK'
                    });
                }
                // 'pending' → lanjut polling
            });
    }

    /** Batalkan QRIS, kembali ke pilihan metode */
    function batalQRIS() {
        clearInterval(window._qrisPolling);
        clearInterval(window._qrisCountdown);
        // Re-trigger tampil pilihan metode — reload halaman atau trigger ulang
        swal.fire({
            icon: 'info',
            title: 'Dibatalkan',
            text: 'Silakan pilih metode pembayaran kembali.',
            confirmButtonText: 'OK'
        }).then(() => {
            detailsContainer.classList.remove('visible');
            setTimeout(() => {
                // detailsContainer.innerHTML = '';
                lanjutBayar_link2();
            }, 500);
        });
    }

    function lanjutBayar_link2() {

        console.log('Masuk Lanjut Bayar');

        const url = `${BASE_URL}Subscription/proses_lanjut_bayar`;

        // Fetch call using the dynamic URL
        fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: '{}' // ← ganti dari JSON.stringify(data)
            })
            .then(response => {
                swal.close();
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.status == "success") {



                    const allPricingCols = document.querySelectorAll('.col-lg-3.nopadding');

                    allPricingCols.forEach(col => {
                        col.querySelector('.pricing-box').classList.add('hidden');

                        setTimeout(() => {
                            col.classList.add('d-none');
                        }, 500);
                    });

                    setTimeout(() => {

                        const id_pembayaran = data.id_pembayaran;
                        const startDate = new Date(data.confirmation_detail.tanggal_mulai.split(' ')[0]);
                        const endDate = new Date(data.confirmation_detail.tanggal_selesai.split(' ')[0]);
                        const startStr = formatDate(startDate);
                        const endStr = formatDate(endDate);

                        // ── Jika server sudah kembalikan QR (pending QRIS lama) ──────

                        // ── Tampil pilihan metode pembayaran ─────────────────────────
                        const pilihanHTML = `
                <div class="detail-box mt-5">
                    <h3 class="f-20">Konfirmasi Pembayaran</h3>
                    <p>Terima kasih telah memilih Plan <strong>${data.confirmation_detail.paket}</strong>. Berikut rincian pesanan Anda:</p>
                    <ul class="list-unstyled mb-3">
                        <li><strong>Paket</strong> ${data.confirmation_detail.paket}</li>
                        <li><strong>Jangka Waktu</strong> ${data.confirmation_detail.total_bulan} Bulan</li>
                        <li><strong>Tanggal Mulai</strong> ${startStr}</li>
                        <li><strong>Tanggal Selesai</strong> ${endStr}</li>
                    </ul>
                   
                    <hr class="my-4">
                    <h5 class="text-center mb-3">Pilih Metode Pembayaran</h5>

                    <div class="payment-row">

    <!-- Kartu VA -->
    <div class="col-12 col-md-5">
        <div class="payment-card" id="card-va" onclick="pilihMetode('va')">
            <div class="pc-logo"><span class="pc-emoji">📱</span></div>
            <div class="pc-title">Virtual Account</div>

            <div class="pc-label">Total Tagihan</div>
            <div class="pc-price">${formatRupiah(Number(data.confirmation_detail.nominal_asli))}</div>
            <p class="pc-note">Belum termasuk biaya layanan</p>

            <div class="pc-divider"></div>
            <div class="pc-info">
                BCA &middot; Mandiri &middot; BNI &middot; BRI &middot; BSI<br>
                dan bank lainnya
            </div>

            <div class="pc-badge-wrap">
                <span class="pc-badge pc-badge-instan">&#9889; Otomatis &amp; Instan</span>
            </div>
        </div>
    </div>

    <!-- Kartu Transfer BSI -->
    <div class="col-12 col-md-5">
        <div class="payment-card" id="card-bsi" onclick="pilihMetode('bsi')">
            <div class="pc-logo">
                <img src="${BASE_URL}assets/images/bank/BSI_1.png" alt="BSI">
            </div>
            <div class="pc-title">Transfer BSI</div>

            <div class="pc-label">Total Tagihan</div>
            <div class="pc-price">${formatRupiah(data.confirmation_detail.nominal)}</div>
            <p class="pc-note">Sudah termasuk 3 digit unik untuk konfirmasi transaksi</p>

            <div class="pc-divider"></div>
            <div class="pc-info">
                Bank Syariah Indonesia<br>
                <span class="pc-rek">79 7070 7004</span>
            </div>

            <div class="pc-badge-wrap">
                <span class="pc-badge pc-badge-manual">&#128337; Konfirmasi Manual</span>
            </div>
        </div>
    </div>

</div>

                    <div class="d-flex justify-content-center">
                        <button type="button" id="btn-lanjut-bayar" class="btn btn-primary btn-rounded px-5"
                                disabled onclick="lanjutBayar(${id_pembayaran}, '${data.confirmation_detail.paket}')">
                            Lanjutkan Pembayaran
                        </button>
                    </div>
                </div>
                `;

                        detailsContainer.innerHTML = pilihanHTML;
                        detailsContainer.classList.remove('d-none');
                        detailsContainer.classList.add('visible');

                        console.log('DetailContainer Muncul');

                    }, 500);


                } else if (data.status == "proses") {
                    swal.fire({
                        customClass: 'slow-animation',
                        icon: 'info',
                        showConfirmButton: false,
                        title: 'Proses!',
                        text: data.message,
                        timer: 3000
                    }).then(() => {
                        detailsContainer.classList.remove('visible');
                        setTimeout(() => {
                            detailsContainer.innerHTML = fourthDetailHTML;
                            detailsContainer.classList.add('visible');
                        }, 500);
                    });
                } else {
                    swal.fire({
                        customClass: 'slow-animation',
                        icon: 'error',
                        showConfirmButton: false,
                        title: 'Gagal!',
                        text: data.message,
                        timer: 1500
                    });
                }
            })
            .catch((error) => {
                swal.close();

                console.error('Error:', error);
                // Handle errors, e.g., show an error message to the user
                alert('Terjadi kesalahan saat mengonfirmasi pembayaran. Silakan coba lagi.');
            });

    }
</script>