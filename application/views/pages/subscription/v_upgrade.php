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

        /* --- ADD THIS RULE TO .pricing-box --- */
        height: 100%;
        /* Ensures the box fills the full height of its stretched parent column */
        /* --- REMOVE any 'display: flex', 'flex-direction', 'justify-content' from .pricing-box if you added them --- */

        transition: transform 0.3s ease, box-shadow 0.3s ease;
        z-index: 1;
        /* Ensures the hovered box is on top of its neighbors */
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
        padding-left: 5px !important;
        padding-right: 5px !important;
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

    .text-bariskode {
        color: #3f51b5;
    }
</style>

<section class="section" id="pricing">
    <div class="container">

        <div class="row">
            <div class="col-lg-12">
                <div class="title-box text-center">
                    <h3 class="title-heading mt-2">Pilihan Mahkota Terbaik untuk Kerajaan Anda</h3>
                    <p class="text-muted f-17 mt-2">
                        Persembahan khusus dari istana, paket-paket istimewa yang dirancang untuk
                        mengukuhkan kejayaan dan kekuasaan.<br> Pilih mahkota yang paling sesuai
                        untuk memimpin tahta Anda menuju era keemasan.
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
        <div class="row pt-2 justify-content-center">
            <div class="col-lg-3 nopadding">
                <div class="pricing-box mt-4 noborderradius-right">
                    <i class="mdi mdi-account h1"></i>
                    <h4 class="f-20">Bangsawan Muda</h4>

                    <!-- <p class="mt-4 pt-2 text-muted">Semper urna veal tempus pharetra elit habisse platea dictumst. </p> -->
                    <!-- <p class="mt-4 pt-2 text-muted">Dirancang untuk para bangsawan muda yang mencari landasan kokoh. Dapatkan kendali penuh atas wilayah kecil Anda dan bangunlah kekuatan.</p> -->
                    <div class="pricing-plan pt-2">
                        <!-- <h4 class="text-muted"><s> $9.99</s> <span class="plan pl-3 text-dark">$8.99 </span></h4> -->
                        <span class="plan text-bariskode">IDR. 300rb</span>
                        <p class="text-muted mb-0">Per Bulan</p>
                    </div>

                    <div class="mt-1 pt-2">
                        <p class="mb-2 f-18">Fitur</p>

                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Unlimited</b>
                            Jurnal Arus Kas</p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Unlimited</b>
                            Manajemen Penugasan (Tello)</p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>5000</b>
                            Invoice / Bulan</p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>1000</b>
                            Digital Memorandum / Bulan</p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>5000</b>
                            Pengajuan Multi Layer Approval / Bulan</p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>5000</b>
                            Nota Penjualan / Bulan</p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> Jumlah Cabang <b>3</b></p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>15</b> User</p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> Fitur
                            <b>Premium</b>
                        </p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Cuti Online</b></p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Absen Online (by Face Recognition & Geo-Location)</b></p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Surat-menyurat</b></p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Manajemen Aset & Otomasi Penyusutan</b></p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Fitur-fitur Premium Lainnya</b></p>
                    </div>




                    <div class="mt-4 pt-3">
                        <a href="" class="btn btn-primary btn-rounded">Pilih Tahta</a>
                        <!-- <a href="" class="btn btn-primary btn-rounded">Ambil Takhta Ini</a> -->
                        <!-- <a href="" class="btn btn-primary btn-rounded">Dapatkan Mahkota</a> -->
                    </div>
                </div>
            </div>


            <div class="col-lg-3 nopadding">
                <div class="pricing-box mt-4 noborderradius">
                    <i class="mdi mdi-account h1"></i>
                    <h4 class="f-20">Kesatria Sejati</h4>

                    <!-- <p class="mt-4 pt-2 text-muted">Semper urna veal tempus pharetra elit habisse platea dictumst. </p> -->
                    <!-- <p class="mt-4 pt-2 text-muted">Dirancang untuk para bangsawan muda yang mencari landasan kokoh. Dapatkan kendali penuh atas wilayah kecil Anda dan bangunlah kekuatan.</p> -->
                    <div class="pricing-plan pt-2">
                        <!-- <h4 class="text-muted"><s> $9.99</s> <span class="plan pl-3 text-dark">$8.99 </span></h4> -->
                        <span class="plan text-bariskode">IDR. 500rb</span>
                        <p class="text-muted mb-0">Per Bulan</p>
                    </div>

                    <div class="mt-1 pt-2">
                        <p class="mb-2 f-18">Fitur</p>

                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Unlimited</b>
                            Jurnal Arus Kas</p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Unlimited</b>
                            Manajemen Penugasan (Tello)</p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>10000</b>
                            Invoice / Bulan</p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>3000</b>
                            Digital Memorandum / Bulan</p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>10000</b>
                            Pengajuan Multi Layer Approval / Bulan</p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>10000</b>
                            Nota Penjualan / Bulan</p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> Jumlah Cabang <b>5</b></p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>30</b> User</p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> Fitur
                            <b>Premium</b>
                        </p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Cuti Online</b></p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Absen Online (by Face Recognition & Geo-Location)</b></p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Surat-menyurat</b></p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Manajemen Aset & Otomasi Penyusutan</b></p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Fitur-fitur Premium Lainnya</b></p>
                    </div>


                    <div class="mt-4 pt-3">
                        <a href="" class="btn btn-primary btn-rounded">Pilih Tahta</a>
                        <!-- <a href="" class="btn btn-primary btn-rounded">Ambil Takhta Ini</a> -->
                        <!-- <a href="" class="btn btn-primary btn-rounded">Dapatkan Mahkota</a> -->
                    </div>
                </div>
            </div>

            <div class="col-lg-3 nopadding">
                <div class="pricing-box mt-4 noborderradius">
                    <i class="mdi mdi-account h1"></i>
                    <h4 class="f-20">Raja Sultan</h4>

                    <!-- <p class="mt-4 pt-2 text-muted">Semper urna veal tempus pharetra elit habisse platea dictumst. </p> -->
                    <!-- <p class="mt-4 pt-2 text-muted">Dirancang untuk para bangsawan muda yang mencari landasan kokoh. Dapatkan kendali penuh atas wilayah kecil Anda dan bangunlah kekuatan.</p> -->
                    <div class="pricing-plan pt-2">
                        <!-- <h4 class="text-muted"><s> $9.99</s> <span class="plan pl-3 text-dark">$8.99 </span></h4> -->
                        <span class="plan text-bariskode">IDR. 1jt</span>
                        <p class="text-muted mb-0">Per Bulan</p>
                    </div>

                    <div class="mt-1 pt-2">
                        <p class="mb-2 f-18">Fitur</p>

                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Unlimited</b>
                            Jurnal Arus Kas</p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Unlimited</b>
                            Manajemen Penugasan (Tello)</p>
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
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Fitur-fitur Premium Lainnya</b></p>
                    </div>

                    <div class="mt-4 pt-3">
                        <a href="" class="btn btn-primary btn-rounded">Pilih Tahta</a>
                        <!-- <a href="" class="btn btn-primary btn-rounded">Ambil Takhta Ini</a> -->
                        <!-- <a href="" class="btn btn-primary btn-rounded">Dapatkan Mahkota</a> -->
                    </div>
                </div>
            </div>
            <div class="col-lg-3 nopadding">
                <div class="pricing-box mt-4 noborderradius-left">
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
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>Fitur-fitur Premium Lainnya</b></p> -->
                    </div>



                    <div class="mt-4 pt-3">
                        <a href="https://wa.me/6289625551238" target="_blank" class="btn btn-primary btn-rounded">Call For Detail</a>
                        <!-- <a href="" class="btn btn-primary btn-rounded">Ambil Takhta Ini</a> -->
                        <!-- <a href="" class="btn btn-primary btn-rounded">Dapatkan Mahkota</a> -->
                    </div>
                </div>
            </div>
            <div class="col-lg-3 nopadding mt-3">
                <div class="pricing-box mt-4">
                    <i class="mdi mdi-account h1"></i>
                    <!-- <h4 class="f-20">Paket Kesatria Perintis (Dasar Gratis)</h4> -->
                    <h4 class="f-20">Jiwa Pengembara</h4>


                    <!-- <p class="mt-4 pt-2 text-muted">Semper urna veal tempus pharetra elit habisse platea dictumst. </p> -->
                    <!-- <p class="mt-4 pt-2 text-muted">Dirancang untuk para bangsawan muda yang mencari landasan kokoh. Dapatkan kendali penuh atas wilayah kecil Anda dan bangunlah kekuatan.</p> -->
                    <div class="pricing-plan pt-2">
                        <!-- <h4 class="text-muted"><s> $9.99</s> <span class="plan pl-3 text-dark">$8.99 </span></h4> -->
                        <span class="plan text-dark">Free </span>
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
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>500</b>
                            Digital Memorandum / Bulan</p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>1000</b>
                            Pengajuan Multi Layer Approval / Bulan</p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>1000</b>
                            Nota Penjualan / Bulan</p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> Jumlah Cabang <b>1</b></p>
                        <p class="mb-2"><i class="fa-solid fa-check fa-lg" style="color: #3ad29f;"></i> <b>5</b> User</p>
                        <p class="mb-2"><i class="fa-solid fa-xmark fa-lg text-danger f-18 mr-2"></i>Fitur
                            <b>Premium</b>
                        </p>
                        <p class="mb-2"><i class="fa-solid fa-xmark fa-lg text-danger f-18 mr-2" style="color: #3ad29f;"></i> <b>Cuti Online</b></p>
                        <p class="mb-2"><i class="fa-solid fa-xmark fa-lg text-danger f-18 mr-2" style="color: #3ad29f;"></i> <b>Absen Online (by Face Recognition & Geo-Location)</b></p>
                        <p class="mb-2"><i class="fa-solid fa-xmark fa-lg text-danger f-18 mr-2" style="color: #3ad29f;"></i> <b>Surat-menyurat</b></p>
                        <p class="mb-2"><i class="fa-solid fa-xmark fa-lg text-danger f-18 mr-2" style="color: #3ad29f;"></i> <b>Manajemen Aset & Otomasi Penyusutan</b></p>
                        <p class="mb-2"><i class="fa-solid fa-xmark fa-lg text-danger f-18 mr-2" style="color: #3ad29f;"></i> <b>Fitur-fitur Premium Lainnya</b></p>
                    </div>



                    <!-- <div class="mt-4 pt-3"> -->
                    <!-- <a href="" class="btn btn-primary btn-rounded">Pilih Tahta</a> -->
                    <!-- <a href="" class="btn btn-primary btn-rounded">Ambil Takhta Ini</a> -->
                    <!-- <a href="" class="btn btn-primary btn-rounded">Dapatkan Mahkota</a> -->
                    <!-- </div> -->
                </div>
            </div>
        </div>
    </div>
</section>