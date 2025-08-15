<script>
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

    document.addEventListener('DOMContentLoaded', function() {
        const pricingBoxes = document.querySelectorAll('.pricing-box');
        const pilihTahtaButtons = document.querySelectorAll('.pilih-tahta-btn');
        const detailsContainer = document.getElementById('pricing-details-container');
        const pricingRow = document.querySelector('.row.justify-content-center');
        const BASE_URL = '<?php echo base_url(); ?>';

        // Store the original columns to re-insert them later
        const originalCols = Array.from(document.querySelectorAll('.col-lg-3.nopadding'));

        function formatDate(date) {
            const options = {
                day: '2-digit',
                month: 'long',
                year: 'numeric'
            };
            return new Intl.DateTimeFormat('id-ID', options).format(date);
        }

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
                const detailHTML = `
                <div class="detail-box">
                    <h3 class="f-20">Rincian Pembayaran : ${planName}</h3>
                    <p>Pilih jangka waktu pembayaran:</p>
                    <div class="month-selection">
                        <button class="month-btn" data-months="1">1 Bulan</button>
                        <button class="month-btn" data-months="3">3 Bulan</button>
                        <button class="month-btn" data-months="6">6 Bulan</button>
                        <button class="month-btn" data-months="9">9 Bulan</button>
                        <button class="month-btn" data-months="12">1 Tahun</button>
                    </div>
                    
                    <div class="mt-4 text-center">
                        <h4>Total Pembayaran: <span id="total-price" class="text-bariskode">${formatRupiah(basePrice)}</span></h4>
                        <p class="text-muted">Untuk <span id="months-display">1</span> bulan</p>
                    </div>
                    
                    <div class="mt-4 pt-3 d-flex justify-content-center">
                        <button type="button" id="btn-pembayaran" class="btn btn-primary btn-rounded w-50">Lanjutkan Pembayaran</a>
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

            // Wait for the fade-out animation to finish
            setTimeout(() => {
                const startDate = new Date();
                const endDate = new Date();
                endDate.setMonth(startDate.getMonth() + months);

                const totalPrice = basePrice * months;
                const randomDigits = Math.floor(Math.random() * 900) + 100; // Generate 3 random digits (100-999)
                const confirmationPrice = totalPrice + randomDigits;

                // Format dates
                // const formatDate = (date) => {
                //     const day = String(date.getDate()).padStart(2, '0');
                //     const month = String(date.getMonth() + 1).padStart(2, '0');
                //     const year = date.getFullYear();
                //     return `${day}-${month}-${year}`;
                // };
                // These lines now work correctly because formatDate is globally accessible
                const startStr = formatDate(startDate);
                const endStr = formatDate(endDate);

                const dbStartStr = formatDateForDatabase(startDate);
                const dbEndStr = formatDateForDatabase(endDate);
                const secondDetailHTML = `
                <div class="detail-box">
    <h3 class="f-20">Konfirmasi Pembayaran</h3>
    <p>Terima kasih telah memilih Plan ${planName}. Berikut rincian pesanan Anda:</p>
    <ul class="list-unstyled">
    <li><strong>Paket</strong> ${planName}</li>
    <li><strong>Jangka Waktu</strong> ${months} Bulan</li>
    <li><strong>Tanggal Mulai</strong> ${startStr}</li>
    <li><strong>Tanggal Selesai</strong> ${endStr}</li>
</ul>
    <hr>
    <h4 class="text-center">Total Tagihan:</h4>
    <h2 class="text-bariskode text-center">${formatRupiah(confirmationPrice)}</h2>
    <p class="text-center text-muted f-12">Total akhir sudah termasuk 3 digit unik untuk konfirmasi transaksi.</p>
    
    <div class="mt-4 pt-3 text-center">
        <img src="${BASE_URL}assets/images/bank/BSI_1.png" alt="Logo Bank BSI" class="mb-2 w-25">
        <h2 class="mt-3 text-bariskode">(NOMOR REKENING)</h2>
    </div>
    
    <div class="mt-4 pt-3 d-flex justify-content-center">
        <button type="button" id="pay-now-btn" class="btn btn-primary btn-rounded w-50">Bayar Sekarang</button>
    </div>
</div>
            `;

                detailsContainer.innerHTML = secondDetailHTML;
                detailsContainer.classList.add('visible'); // Show the new box with fade-in

                const payNowBtn = document.getElementById('pay-now-btn');
                payNowBtn.addEventListener('click', () => {
                    // Data to be sent via Ajax
                    const data = {
                        planName: planName,
                        months: months,
                        startDate: dbStartStr,
                        endDate: dbEndStr,
                        confirmationPrice: confirmationPrice,
                        id_perusahaan: "<?php echo $this->session->userdata('user_perusahaan_id'); ?>"
                    };

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
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.status == "success") {
                                // console.log('Success:', data);
                                // Handle a successful response from the server,
                                // e.g., show a success message or redirect the user.
                                swal.fire({
                                    customClass: 'slow-animation',
                                    icon: 'success',
                                    showConfirmButton: false,
                                    title: 'Berhasil!',
                                    text: data.message,
                                    timer: 1500
                                });
                                // alert('Pembayaran berhasil dikonfirmasi! Silakan lanjutkan.');
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
                            console.error('Error:', error);
                            // Handle errors, e.g., show an error message to the user
                            alert('Terjadi kesalahan saat mengonfirmasi pembayaran. Silakan coba lagi.');
                        });
                });

            }, 500); // This delay should match your transition time
        }


        // Add click event listeners to all "Pilih Tahta" buttons
        pilihTahtaButtons.forEach(button => {
            button.addEventListener('click', handlePilihTahtaClick);
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
</script>