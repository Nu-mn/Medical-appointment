<div class="main-content" id="main">
    <main>

       <!-- HERO SECTION -->
         <section class="hero-section text-white">
            <div class="container">
                <div class="row align-items-center">

                    <div class="col-lg-6">
                        <h1 class="display-4 fw-bold mb-3">
                            Health Care <br> For Whole Family
                        </h1>
                        <p class="mb-4">
                            In healthcare sector, service excellence is the facility of the hospital
                            as healthcare service provider to consistently…
                        </p>
                        <a href="index.php?nav=appointment" class="btn btn-light btn-lg">Make an appointment</a>
                    </div>

                </div>
            </div>
        </section>
    </main>
</div>


<style>
.hero-section {
    position: relative;
    padding: 250px 0;
    background-image: url('../images/bg_doctor.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
}

/* Lớp phủ đen nhẹ để chữ nổi bật */
.hero-section::before {
    content: "";
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.45); /* tăng giảm độ tối tùy thích */
}

.hero-section .container {
    position: relative; /* để nội dung nằm trên lớp phủ */
    z-index: 2;
}
</style>