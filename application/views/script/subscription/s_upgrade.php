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

        // Store the original columns to re-insert them later
        const originalCols = Array.from(document.querySelectorAll('.col-lg-3.nopadding'));

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
                    
                    <div class="mt-4 pt-3">
                        <a href="#" class="btn btn-primary btn-rounded w-100">Lanjutkan Pembayaran</a>
                    </div>
                    <div class="mt-3">
                        <button id="back-button" class="btn btn-outline-secondary w-100">
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