<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
    /* Existing CSS */
    .pricing-box {
        -webkit-box-shadow: 0px 5px 30px -10px rgba(0, 0, 0, 0.1);
        box-shadow: 0px 5px 30px -10px rgba(0, 0, 0, 0.1);
        padding: 35px 25px;
        border-radius: 20px;
        border: 1px solid #e9ecef;
        position: relative;
        height: 100%;
        /* Combine all transitions for a smooth effect */
        transition: transform 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
        z-index: 1;
        /* Default z-index for all boxes */
    }

    .pricing-box .plan {
        font-size: 34px;
    }

    .pricing-badge {
        position: absolute;
        top: 0;
        z-index: 999;
        right: 0;
        width: 100%;
        display: block;
        font-size: 15px;
        padding: 0;
        overflow: hidden;
        height: 100px;
    }

    .pricing-badge .badge {
        float: right;
        -webkit-transform: rotate(45deg);
        transform: rotate(45deg);
        right: -67px;
        top: 17px;
        position: relative;
        text-align: center;
        width: 200px;
        font-size: 13px;
        margin: 0;
        padding: 7px 10px;
        font-weight: 500;
        color: #ffffff;
        background: #fb7179;
    }

    .mb-2,
    .my-2 {
        margin-bottom: .5rem !important;
    }

    p {
        line-height: 1.7;
    }

    .nopadding {
        /* margin-top: 20px; */
        padding-left: 7px !important;
        padding-right: 7px !important;
        /* margin: 0 !important; */
    }

    @media (max-width: 990px) {
        .nopadding {
            margin-top: 20px;
        }
    }

    @media (min-width: 990px) {

        .noborderradius {
            border-radius: 0 !important;
            /* margin: 0 !important; */
        }

        .noborderradius-right {
            border-radius: 20px 0 0 20px !important;
            /* margin: 0 !important; */
        }

        .noborderradius-left {
            border-radius: 0 20px 20px 0 !important;
            /* margin: 0 !important; */
        }
    }

    /* --- ADD THIS NEW RULE FOR THE PARENT ROW --- */
    .row.justify-content-center {
        display: flex;
        /* Makes the row a flex container */
        flex-wrap: wrap;
        /* Allows columns to wrap to the next line */
        align-items: stretch;
        /* CRUCIAL: Makes all direct flex children (your col-lg-3) stretch to the tallest height */
    }

    @media (min-width: 1200px) {

        .container,
        .container-sm,
        .container-md,
        .container-lg,
        .container-xl {
            max-width: 1400px;
        }
    }

    .pricing-box:hover {
        transform: scale(1.05);
        /* Slightly enlarges the box on hover */
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
        /* Adds a shadow for a "lifted" effect */
        z-index: 1;
    }

    .pricing-box:hover .feature {
        transform: scale(1);
        transition: transform 0.3s ease;
        /* Add a smooth transition */
    }

    .text-bariskode {
        color: #3f51b5;
    }

    .pricing-box.feature {
        transform: scale(1.05);
        /* Featured box is larger by default */
        background-color: #e81f63;
        border: 1px solid #a81647;
        /* Corrected syntax */
        color: #ffffff;
        /* Sets default text color for the featured box */
        z-index: 2;
        /* Ensures it's slightly above others by default */
    }

    .pricing-box.feature h4,
    .pricing-box.feature .plan,
    .pricing-box.feature p,
    .pricing-box.feature .fa-solid {
        color: #ffffff !important;
    }

    /* Ensure SVG path color for featured box is also white */
    .pricing-box.feature .plan svg path {
        fill: #ffffff !important;
    }

    /* When any pricing box is hovered, it scales up a bit more and gets a stronger shadow. */
    .pricing-box:hover {
        transform: scale(1.07);
        /* Any hovered box scales up further */
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        /* Stronger shadow for the hovered box */
        z-index: 3;
        /* The actively hovered box is on top */
    }

    .row:has(.col-lg-3:hover) .col-lg-3:not(:hover) .pricing-box.feature {
        transform: scale(1);
        /* Scale down the featured box to normal size (1) */
        z-index: 1;
        /* Bring its z-index back down */
    }

    /* Add this new CSS to your existing stylesheet */

    /* Rule to hide and zoom out the unselected boxes */
    .pricing-box.hidden {
        transform: scale(0.8);
        opacity: 0;
        transition: all 0.5s ease-out;
    }

    /* New rule to hide the payment details container by default */
    .pricing-details {
        opacity: 0;
        visibility: hidden;
        /* Use visibility to remove it from screen readers */
        transition: opacity 0.5s ease-in, visibility 0.5s ease-in;
    }

    /* When the 'visible' class is added, the container will fade in */
    .pricing-details.visible {
        opacity: 1;
        visibility: visible;
    }

    /* Styles for the new pricing details content */
    .detail-box {
        padding: 35px 25px;
        border-radius: 20px;
        border: 1px solid #e9ecef;
        background: #fff;
        box-shadow: 0px 5px 30px -10px rgba(0, 0, 0, 0.1);
    }

    .detail-box h3 {
        margin-bottom: 20px;
    }

    .month-selection {
        display: flex !important;
        flex-wrap: wrap;
        justify-content: center;
        gap: 15px !important;
        margin-top: 30px;
    }

    .month-btn {
        min-width: 90px;
        border-radius: 50px !important;
        font-size: 13px;
        transition: all 0.2s;
    }

    .month-btn.selected {
        background-color: #3f51b5 !important;
        color: #fff !important;
        border-color: #3f51b5 !important;
    }

    .month-btn:hover {
        background-color: #3f51b5 !important;
        color: #fff !important;
        border-color: #3f51b5 !important;
    }

    .selected-col {
        transform: scale(1.07);
        /* Any hovered box scales up further */
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        /* Stronger shadow for the hovered box */
        z-index: 3;
        /* The actively hovered box is on top */
    }

    /* CSS to align the colons using pseudo-elements */
    .list-unstyled strong {
        display: inline-block;
        width: 150px;
        /* Adjust this width as needed */
        position: relative;
    }

    .list-unstyled strong::after {
        content: ":";
        position: absolute;
        right: 0;
    }

    .text_nominal {
        border-style: solid;
        border-radius: 0.25rem;
    }

    /* ===== Payment Method Cards ===== */
    .payment-row {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        align-items: stretch;
        gap: 20px;
        margin-bottom: 20px;
    }

    .payment-card {
        border: 2px solid #e9ecef;
        border-radius: 20px;
        padding: 28px 22px;
        background: #fff;
        box-shadow: 0px 5px 30px -10px rgba(0, 0, 0, 0.1);
        cursor: pointer;
        text-align: center;
        height: 100%;
        display: flex;
        flex-direction: column;
        position: relative;
        overflow: hidden;
        transition: transform .3s ease, box-shadow .3s ease, border-color .3s ease;
    }

    .payment-card:hover {
        transform: translateY(-6px);
        border-color: #3f51b5;
        box-shadow: 0 15px 30px rgba(63, 81, 181, .18);
    }

    .payment-card.active {
        border-color: #3f51b5;
        background: #f7f8ff;
        box-shadow: 0 15px 30px rgba(63, 81, 181, .2);
    }

    .payment-card.active::before {
        content: "\2713";
        position: absolute;
        top: 14px;
        right: 14px;
        width: 26px;
        height: 26px;
        line-height: 26px;
        border-radius: 50%;
        background: #3f51b5;
        color: #fff;
        font-size: 14px;
    }

    /* Logo disamakan tingginya biar sejajar */
    .pc-logo {
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 14px;
    }

    .pc-logo img {
        max-height: 100%;
        max-width: 150px;
        object-fit: contain;
    }

    .pc-logo .pc-emoji {
        font-size: 2.2rem;
        line-height: 1;
    }

    .pc-title {
        font-weight: 700;
        font-size: 1.05rem;
        color: #212529;
        margin-bottom: 16px;
    }

    .pc-label {
        font-size: .7rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #adb5bd;
        margin-bottom: 6px;
    }

    .pc-price {
        font-size: 1.6rem;
        font-weight: 700;
        color: #3f51b5;
        line-height: 1.2;
        margin-bottom: 8px;
    }

    .pc-note {
        font-size: .72rem;
        color: #adb5bd;
        line-height: 1.5;
        margin-bottom: 0;
        min-height: 34px;
    }

    .pc-divider {
        border-top: 1px dashed #e9ecef;
        margin: 18px 0 14px;
    }

    .pc-info {
        font-size: .8rem;
        color: #6c757d;
        line-height: 1.6;
    }

    .pc-rek {
        font-weight: 600;
        color: #495057;
        letter-spacing: 1px;
    }

    /* Badge didorong ke bawah biar sejajar di kedua kartu */
    .pc-badge-wrap {
        margin-top: auto;
        padding-top: 16px;
    }

    .pc-badge {
        display: inline-block;
        padding: 6px 16px;
        border-radius: 50px;
        font-size: .72rem;
        font-weight: 600;
    }

    .pc-badge-instan {
        background: #d7f5e3;
        color: #0f7a48;
    }

    .pc-badge-manual {
        background: #fff3cd;
        color: #8a6100;
    }

    @media (max-width: 767px) {
        .payment-card {
            padding: 24px 18px;
        }

        .pc-price {
            font-size: 1.4rem;
        }
    }
</style>

<section class="section" id="pricing">
    <div class="container">
        <a class="btn btn-primary" href="<?= base_url() ?>"><i class="fa-solid fa-arrow-left"></i> Back</a>
        <div class="row">
            <div class="col-lg-12">
                <div class="title-box text-center">
                    <h3 class="title-heading mt-2">Pilihan Mahkota Terbaik untuk Kerajaan Anda</h3>
                    <p class="text-muted f-17 mt-2">
                        Persembahan khusus dari istana, paket-paket istimewa yang dirancang untuk
                        mengukuhkan kejayaan dan kekuasaan.<br> Pilih mahkota yang paling sesuai
                        untuk memimpin tahta Anda menuju era keemasan.
                    </p>

                    <p class="text-muted f-13 mt-2">
                        *Harga paket dapat berubah sewaktu-waktu tanpa pemberitahuan
                    </p>
                    <!-- <h3 class="title-heading mt-4">Dekrit Kerajaan: Penawaran Harga Agung</h3>
                    <p class="text-muted f-17 mt-3">
                        Dari balairung agung, kami persembahkan paket-paket layanan pilihan
                        yang akan melayani setiap kebutuhan penguasa. Setiap dekrit ini
                        adalah janji untuk kemegahan, dirancang untuk memastikan
                        takhta Anda berdiri kokoh abadi.
                    </p> -->
                    <!-- <h3 class="title-heading mt-4">Takhta Terbaik Menanti: Paket Harga Raja</h3>
                    <p class="text-muted f-17 mt-3">
                        Jadilah penguasa sejati dengan paket-paket harga terbaik yang pernah ada.
                        Dapatkan kendali penuh, otoritas tak terbatas, dan semua fasilitas
                        yang layak bagi seorang raja. Kekuasaan ada di tangan Anda.
                    </p> -->
                </div>
            </div>
        </div>


        <!-- <div class="row pt-4 justify-content-center"> -->

        <!-- </div> -->
        <div class="row pt-2 justify-content-center" id="daftar_harga">

            <div class="col-lg-3 nopadding">
                <div class="pricing-box mt-4 noborderradius-right" id="pricing-1">
                    <i class="mdi mdi-account h1"></i>
                    <h4 class="f-20">Bangsawan Muda</h4>

                    <!-- <p class="mt-4 pt-2 text-muted">Semper urna veal tempus pharetra elit habisse platea dictumst. </p> -->
                    <!-- <p class="mt-4 pt-2 text-muted">Dirancang untuk para bangsawan muda yang mencari landasan kokoh. Dapatkan kendali penuh atas wilayah kecil Anda dan bangunlah kekuatan.</p> -->
                    <div class="pricing-plan pt-2">
                        <!-- <h4 class="text-muted"><s> $9.99</s> <span class="plan pl-3 text-dark">$8.99 </span></h4> -->
                        <span class="plan text-bariskode">IDR. 500K</span>
                        <p class="text-muted mb-0">Per Bulan</p>
                    </div>

                    <div class="mt-1 pt-2">
                        <p class="mb-2 f-18">Fitur</p>

                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Unlimited</b>
                            Jurnal Arus Kas</p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Unlimited</b>
                            Manajemen Penugasan (Tello)</p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Unlimited</b>
                            Nota Penjualan (POS)</p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>3000</b>
                            Invoice / Bulan</p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>500</b>
                            Digital Memorandum / Bulan</p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>3000</b>
                            Pengajuan Multi Layer Approval / Bulan</p>
                        <!-- <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>5000</b>
                            Nota Penjualan / Bulan</p> -->
                        <!-- <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> Jumlah Cabang <b>3</b></p> -->
                        <!-- <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>15</b> User</p> -->
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> Jumlah Cabang <b>2</b></p>

                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>15</b> User</p>

                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> Fitur
                            <b>Premium</b>
                        </p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Digital Memo</b></p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Cuti Online</b></p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Absen Online (by Face Recognition & Geo-Location)</b></p>
                        <!-- <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Surat-menyurat</b></p> -->
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Manajemen Aset & Otomasi Penyusutan</b></p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Notifikasi Whatsapp</b></p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Fitur-fitur Premium Paket Sebelumnya</b></p>
                    </div>




                    <div class="mt-4 pt-3">
                        <a href="" class="btn btn-primary btn-rounded pilih-tahta-btn" data-plan="1" data-price="500000">Pilih Tahta</a>
                        <!-- <a href="" class="btn btn-primary btn-rounded">Ambil Takhta Ini</a> -->
                        <!-- <a href="" class="btn btn-primary btn-rounded">Dapatkan Mahkota</a> -->
                    </div>
                </div>
            </div>
            <div class="col-lg-3 nopadding">
                <div class="pricing-box mt-4 noborderradius" id="pricing-2">
                    <i class="mdi mdi-account h1"></i>
                    <h4 class="f-20">Kesatria Sejati</h4>

                    <!-- <p class="mt-4 pt-2 text-muted">Semper urna veal tempus pharetra elit habisse platea dictumst. </p> -->
                    <!-- <p class="mt-4 pt-2 text-muted">Dirancang untuk para bangsawan muda yang mencari landasan kokoh. Dapatkan kendali penuh atas wilayah kecil Anda dan bangunlah kekuatan.</p> -->
                    <div class="pricing-plan pt-2">
                        <!-- <h4 class="text-muted"><s> $9.99</s> <span class="plan pl-3 text-dark">$8.99 </span></h4> -->
                        <span class="plan text-bariskode">IDR. 1000K</span>
                        <p class="text-muted mb-0">Per Bulan</p>
                    </div>

                    <div class="mt-1 pt-2">
                        <p class="mb-2 f-18">Fitur</p>

                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Unlimited</b>
                            Jurnal Arus Kas</p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Unlimited</b>
                            Manajemen Penugasan (Tello)</p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Unlimited</b>
                            Nota Penjualan (POS)</p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>5000</b>
                            Invoice / Bulan</p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>1000</b>
                            Digital Memorandum / Bulan</p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>5000</b>
                            Pengajuan Multi Layer Approval / Bulan</p>
                        <!-- <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>10000</b>
                            Nota Penjualan / Bulan</p> -->
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> Jumlah Cabang <b>5</b></p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>25</b> User</p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> Fitur
                            <b>Premium</b>
                        </p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Digital Memo</b></p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Cuti Online</b></p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Absen Online (by Face Recognition & Geo-Location)</b></p>
                        <!-- <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Surat-menyurat</b></p> -->
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Manajemen Aset & Otomasi Penyusutan</b></p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Notifikasi Whatsapp</b></p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Fitur-fitur Premium Paket Sebelumnya</b></p>
                    </div>


                    <div class="mt-4 pt-3">
                        <a href="" class="btn btn-primary btn-rounded pilih-tahta-btn" data-plan="2" data-price="1000000">Pilih Tahta</a>
                        <!-- <a href="" class="btn btn-primary btn-rounded">Ambil Takhta Ini</a> -->
                        <!-- <a href="" class="btn btn-primary btn-rounded">Dapatkan Mahkota</a> -->
                    </div>
                </div>
            </div>
            <div class="col-lg-3 nopadding">
                <div class="pricing-box mt-4 noborderradius feature" id="pricing-3">
                    <i class="mdi mdi-account h1"></i>
                    <h4 class="f-20">Raja Sultan</h4>

                    <!-- <p class="mt-4 pt-2 text-muted">Semper urna veal tempus pharetra elit habisse platea dictumst. </p> -->
                    <!-- <p class="mt-4 pt-2 text-muted">Dirancang untuk para bangsawan muda yang mencari landasan kokoh. Dapatkan kendali penuh atas wilayah kecil Anda dan bangunlah kekuatan.</p> -->
                    <div class="pricing-plan pt-2">
                        <!-- <h4 class="text-muted"><s> $9.99</s> <span class="plan pl-3 text-dark">$8.99 </span></h4> -->
                        <span class="plan text-bariskode">IDR. 1500K</span>
                        <!-- <p class="text-muted mb-0">Per Bulan</p> -->
                        <p class="mb-0">Per Bulan</p>
                    </div>

                    <div class="mt-1 pt-2">
                        <p class="mb-2 f-18">Fitur</p>

                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Unlimited</b>
                            Jurnal Arus Kas</p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Unlimited</b>
                            Manajemen Penugasan (Tello)</p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Unlimited</b>
                            Nota Penjualan (POS)</p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>10000</b>
                            Invoice / Bulan</p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>3000</b>
                            Digital Memorandum / Bulan</p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>10000</b>
                            Pengajuan Multi Layer Approval / Bulan</p>
                        <!-- <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>25000</b>
                            Nota Penjualan / Bulan</p> -->
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> Jumlah Cabang <b>10</b></p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>50</b> User</p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> Fitur
                            <b>Premium</b>
                        </p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Digital Memo</b></p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Cuti Online</b></p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Absen Online (by Face Recognition & Geo-Location)</b></p>
                        <!-- <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Surat-menyurat</b></p> -->
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Manajemen Aset & Otomasi Penyusutan</b></p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Notifikasi Whatsapp</b></p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Fitur-fitur Premium Paket Sebelumnya</b></p>
                    </div>

                    <div class="mt-4 pt-3">
                        <a href="" class="btn btn-primary btn-rounded pilih-tahta-btn" data-plan="3" data-price="1500000">Pilih Tahta</a>
                        <!-- <a href="" class="btn btn-primary btn-rounded">Ambil Takhta Ini</a> -->
                        <!-- <a href="" class="btn btn-primary btn-rounded">Dapatkan Mahkota</a> -->
                    </div>
                </div>
            </div>
            <div class="col-lg-3 nopadding">
                <div class="pricing-box mt-4 noborderradius-left" id="pricing-4">
                    <i class="mdi mdi-account h1"></i>
                    <h4 class="f-20">Kaisar Agung</h4>

                    <!-- <p class="mt-4 pt-2 text-muted">Semper urna veal tempus pharetra elit habisse platea dictumst. </p> -->
                    <!-- <p class="mt-4 pt-2 text-muted">Dirancang untuk para bangsawan muda yang mencari landasan kokoh. Dapatkan kendali penuh atas wilayah kecil Anda dan bangunlah kekuatan.</p> -->
                    <div class="pricing-plan pt-2">
                        <!-- <h4 class="text-muted"><s> $9.99</s> <span class="plan pl-3 text-dark">$8.99 </span></h4> -->
                        <span class="plan text-bariskode">IDR.
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" height="64" width="64" style="color: #3f51b5;"><!--!Font Awesome Free v7.0.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                                <path fill="#3f51b5" d="M0 320C0 231.6 71.6 160 160 160C210.4 160 257.8 183.7 288 224L320 266.7L352 224C382.2 183.7 429.6 160 480 160C568.4 160 640 231.6 640 320C640 408.4 568.4 480 480 480C429.6 480 382.2 456.3 352 416L320 373.3L288 416C257.8 456.3 210.4 480 160 480C71.6 480 0 408.4 0 320zM280 320L236.8 262.4C218.7 238.2 190.2 224 160 224C107 224 64 267 64 320C64 373 107 416 160 416C190.2 416 218.7 401.8 236.8 377.6L280 320zM360 320L403.2 377.6C421.3 401.8 449.8 416 480 416C533 416 576 373 576 320C576 267 533 224 480 224C449.8 224 421.3 238.2 403.2 262.4L360 320z" />
                            </svg>
                        </span>
                        <p class="text-muted mb-0">Per Bulan</p>
                    </div>

                    <div class="mt-1 pt-2">
                        <p class="mb-2 f-18">Fitur</p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Full Customize
                            </b></p>
                        <!-- <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Unlimited</b>
                            Jurnal Arus Kas</p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Unlimited</b>
                            Manajemen Penugasan (Tello)</p>
                            <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Unlimited</b>
                            Nota Penjualan (POS)</p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>25000</b>
                            Invoice / Bulan</p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>10000</b>
                            Digital Memorandum / Bulan</p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>25000</b>
                            Pengajuan Multi Layer Approval / Bulan</p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>25000</b>
                            Nota Penjualan / Bulan</p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> Jumlah Cabang <b>10</b></p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>50</b> User</p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> Fitur
                            <b>Premium</b>
                        </p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Cuti Online</b></p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Absen Online (by Face Recognition & Geo-Location)</b></p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Surat-menyurat</b></p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Manajemen Aset & Otomasi Penyusutan</b></p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Fitur-fitur Premium Paket Sebelumnya</b></p> -->
                    </div>



                    <div class="mt-4 pt-3">
                        <a href="https://wa.me/6289625551238" target="_blank" class="btn btn-primary btn-rounded">Call For Detail</a>
                        <!-- <a href="" class="btn btn-primary btn-rounded">Ambil Takhta Ini</a> -->
                        <!-- <a href="" class="btn btn-primary btn-rounded">Dapatkan Mahkota</a> -->
                    </div>
                </div>
            </div>

            <div class="col-lg-3 nopadding mt-5">
                <div class="pricing-box mt-4">
                    <i class="mdi mdi-account h1"></i>
                    <!-- <h4 class="f-20">Paket Kesatria Perintis (Dasar Gratis)</h4> -->
                    <h4 class="f-20">Jiwa Pengembara</h4>


                    <!-- <p class="mt-4 pt-2 text-muted">Semper urna veal tempus pharetra elit habisse platea dictumst. </p> -->
                    <!-- <p class="mt-4 pt-2 text-muted">Dirancang untuk para bangsawan muda yang mencari landasan kokoh. Dapatkan kendali penuh atas wilayah kecil Anda dan bangunlah kekuatan.</p> -->
                    <div class="pricing-plan pt-2">
                        <!-- <h4 class="text-muted"><s> $9.99</s> <span class="plan pl-3 text-dark">$8.99 </span></h4> -->
                        <span class="plan text-dark">Gratis </span>
                        <!-- <p class="text-muted mb-0">Per Bulan</p> -->
                    </div>

                    <div class="mt-1 pt-2">
                        <p class="mb-2 f-18">Fitur</p>

                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Unlimited</b>
                            Jurnal Arus Kas</p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Unlimited</b>
                            Manajemen Penugasan (Tello)</p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>1000</b>
                            Invoice / Bulan</p>
                        <!-- <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>1000</b>
                            Digital Memorandum / Bulan</p> -->
                        <!-- <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>1000</b>
                            Nota Penjualan / Bulan</p> -->
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> Jumlah Cabang <b>1</b></p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>1</b> User</p>
                        <p class="mb-2"><i class="fa-solid fa-xmark fa-lg text-danger" style="color: #3ad29f;"></i> <b>Unlimited</b>
                            Nota Penjualan (POS)</p>
                        <p class="mb-2"><i class="fa-solid fa-xmark fa-lg text-danger" style="color: #3ad29f;"></i> <b>1000</b>
                            Pengajuan Multi Layer Approval / Bulan</p>
                        <p class="mb-2"><i class="fa-solid fa-xmark fa-lg text-danger f-18 mr-2"></i>Fitur
                            <b>Premium</b>
                        </p>
                        <p class="mb-2"><i class="fa-solid fa-xmark fa-lg text-danger f-18 mr-2" style="color: #3ad29f;"></i> <b>Digital Memo</b></p>
                        <p class="mb-2"><i class="fa-solid fa-xmark fa-lg text-danger f-18 mr-2" style="color: #3ad29f;"></i> <b>Cuti Online</b></p>
                        <p class="mb-2"><i class="fa-solid fa-xmark fa-lg text-danger f-18 mr-2" style="color: #3ad29f;"></i> <b>Absen Online (by Face Recognition & Geo-Location)</b></p>
                        <!-- <p class="mb-2"><i class="fa-solid fa-xmark fa-lg text-danger f-18 mr-2" style="color: #3ad29f;"></i> <b>Surat-menyurat</b></p> -->
                        <p class="mb-2"><i class="fa-solid fa-xmark fa-lg text-danger f-18 mr-2" style="color: #3ad29f;"></i> <b>Manajemen Aset & Otomasi Penyusutan</b></p>
                        <p class="mb-2"><i class="fa-solid fa-xmark fa-lg text-danger f-18 mr-2" style="color: #3ad29f;"></i> <b>Fitur-fitur Premium Lainnya</b></p>
                    </div>



                    <!-- <div class="mt-4 pt-3"> -->
                    <!-- <a href="" class="btn btn-primary btn-rounded">Pilih Tahta</a> -->
                    <!-- <a href="" class="btn btn-primary btn-rounded">Ambil Takhta Ini</a> -->
                    <!-- <a href="" class="btn btn-primary btn-rounded">Dapatkan Mahkota</a> -->
                    <!-- </div> -->
                    <div class="mt-4 pt-3">
                        <a href="" class="btn btn-primary btn-rounded pilih-donasi-btn">Donasi</a>
                        <!-- <a href="" class="btn btn-primary btn-rounded">Ambil Takhta Ini</a> -->
                        <!-- <a href="" class="btn btn-primary btn-rounded">Dapatkan Mahkota</a> -->
                    </div>
                </div>
            </div>
            <div class="col-lg-3 nopadding mt-5">
                <div class="pricing-box mt-4">
                    <i class="mdi mdi-account h1"></i>
                    <!-- <h4 class="f-20">Paket Kesatria Perintis (Dasar Gratis)</h4> -->
                    <h4 class="f-20">Saudagar Kaya</h4>


                    <!-- <p class="mt-4 pt-2 text-muted">Semper urna veal tempus pharetra elit habisse platea dictumst. </p> -->
                    <!-- <p class="mt-4 pt-2 text-muted">Dirancang untuk para bangsawan muda yang mencari landasan kokoh. Dapatkan kendali penuh atas wilayah kecil Anda dan bangunlah kekuatan.</p> -->
                    <div class="pricing-plan pt-2">
                        <span class="plan text-bariskode">IDR. 100K</span>
                        <p class="text-muted mb-0">Per Bulan</p>
                    </div>

                    <div class="mt-1 pt-2">
                        <p class="mb-2 f-18">Fitur</p>

                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Unlimited</b>
                            Jurnal Arus Kas</p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Unlimited</b>
                            Manajemen Penugasan (Tello)</p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Unlimited</b>
                            Nota Penjualan (POS)</p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Unlimited</b>
                            Item</p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>1000</b>
                            Invoice / Bulan</p>
                        <!-- <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>1000</b>
                            Digital Memorandum / Bulan</p> -->
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>1000</b>
                            Pengajuan Multi Layer Approval / Bulan</p>
                        <!-- <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>1000</b>
                            Nota Penjualan / Bulan</p> -->
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> Jumlah Cabang <b>1</b></p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>5</b> User</p>
                        <p class="mb-2"><i class="fa-solid fa-xmark text-danger fa-lg f-18 mr-2" style="color: #3ad29f;"></i>Fitur
                            <b>Premium</b>
                        </p>
                        <p class="mb-2"><i class="fa-solid fa-xmark text-danger fa-lg f-18 mr-2" style="color: #3ad29f;"></i> <b>Digital Memo</b></p>
                        <p class="mb-2"><i class="fa-solid fa-xmark text-danger fa-lg f-18 mr-2" style="color: #3ad29f;"></i> <b>Cuti Online</b></p>
                        <p class="mb-2"><i class="fa-solid fa-xmark text-danger fa-lg f-18 mr-2" style="color: #3ad29f;"></i> <b>Absen Online (by Face Recognition & Geo-Location)</b></p>
                        <!-- <p class="mb-2"><i class="fa-solid fa-check fa-lg f-18 mr-2" style="color: #3ad29f;"></i> <b>Surat-menyurat</b></p> -->
                        <p class="mb-2"><i class="fa-solid fa-xmark text-danger fa-lg f-18 mr-2" style="color: #3ad29f;"></i> <b>Manajemen Aset & Otomasi Penyusutan</b></p>
                        <p class="mb-2"><i class="fa-solid fa-xmark text-danger fa-lg f-18 mr-2" style="color: #3ad29f;"></i> <b>Fitur-fitur Premium Lainnya</b></p>
                    </div>



                    <!-- <div class="mt-4 pt-3"> -->
                    <!-- <a href="" class="btn btn-primary btn-rounded">Pilih Tahta</a> -->
                    <!-- <a href="" class="btn btn-primary btn-rounded">Ambil Takhta Ini</a> -->
                    <!-- <a href="" class="btn btn-primary btn-rounded">Dapatkan Mahkota</a> -->
                    <!-- </div> -->
                    <div class="mt-4 pt-3">
                        <a href="" class="btn btn-primary btn-rounded pilih-tahta-btn" data-plan="4" data-price="100000">Pilih Tahta</a>
                        <!-- <a href="" class=" btn btn-primary btn-rounded">Ambil Takhta Ini</a> -->
                        <!-- <a href="" class="btn btn-primary btn-rounded">Dapatkan Mahkota</a> -->
                    </div>
                </div>
            </div>
            <div class="col-lg-9 pricing-details d-none" id="pricing-details-container"></div>
        </div>
    </div>
</section>