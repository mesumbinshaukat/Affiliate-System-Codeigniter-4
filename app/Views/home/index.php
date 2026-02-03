<?= $this->extend('layouts/landing') ?>

<?php
$heroBackground = base_url('media/braakbal10/' . rawurlencode('Frame 93.png'));
$stepsIllustrations = [
    base_url('media/braakbal10/' . rawurlencode('Frame 1000011522.png')),
    base_url('media/braakbal10/' . rawurlencode('Frame 1000011520.png')),
    base_url('media/braakbal10/' . rawurlencode('Frame 1000011521.png')),
];
$whyPhotos = [
    'front' => base_url('media/braakbal10/' . rawurlencode('Rectangle 4.png')),
    'back' => base_url('media/braakbal10/' . rawurlencode('Rectangle 3.png')),
];
$ctaIllustration = base_url('media/braakbal10/' . rawurlencode('g1.png'));
$ctaSprinkles = base_url('media/braakbal10/' . rawurlencode('Group 3.png'));
$blogImages = [
    base_url('media/braakbal10/' . rawurlencode('Rectangle 5.png')),
    base_url('media/braakbal10/' . rawurlencode('Rectangle 6.png')),
    base_url('media/braakbal10/' . rawurlencode('Rectangle 7.png')),
];
?>

<?= $this->section('styles') ?>
<style>
    .hero-wrapper {
        background-color: #E7F2FF;
        background-image: url('<?= $heroBackground ?>');
        background-size: cover;
        background-position: center bottom;
        background-repeat: no-repeat;
        padding: 6rem 0 4rem;
        position: relative;
        overflow: hidden;
        min-height: 100vh;
        margin-bottom: 3rem;
    }

    .hero-content {
        position: relative;
        max-width: 950px;
        margin: 0 auto;
        text-align: center;
    }

    .hero-preamble {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.45rem 1.75rem;
        border-radius: 999px;
        background: #dbeafe;
        color: #1d4ed8;
        font-weight: 600;
        margin-bottom: 1.25rem;
        font-size: 0.95rem;
    }

    .hero-heading {
        font-size: 4rem;
        font-weight: 800;
        color: #050C3E;
        line-height: 1.2;
        margin-bottom: 1.25rem;
    }

    .hero-text {
        font-size: 1.05rem;
        color: #4a5d8f;
        max-width: 560px;
        margin: 0 auto;
    }

    .hero-cta {
        margin-top: 2rem;
        gap: 1rem;
        justify-content: center;
    }

    .hero-cta .btn {
        border-radius: 999px;
        padding: 0.95rem 1.9rem;
        font-weight: 600;
        min-width: 230px;
        font-size: 1rem;
    }

    .hero-cta .btn-primary {
        background: #3479CD;
        border: 1px solid #3479CD;
        box-shadow: 0 18px 40px rgba(52, 121, 205, 0.35);
        color: #fff;
    }

    .hero-cta .btn-success,
    .steps-cta,
    .why-cta {
        background: linear-gradient(135deg, #28a745, #20c997);
        border: 1px solid #28a745;
        box-shadow: 0 18px 40px rgba(40, 167, 69, 0.35);
        color: #fff;
        border-radius: 999px;
        padding: 0.95rem 1.9rem;
        font-weight: 600;
        min-width: 230px;
        font-size: 1rem;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
    }

    .hero-cta .btn-success:hover,
    .steps-cta:hover,
    .why-cta:hover {
        background: linear-gradient(135deg, #218838, #1aa179);
        box-shadow: 0 20px 45px rgba(40, 167, 69, 0.45);
        transform: translateY(-2px);
    }

    .hero-cta .btn-outline {
        border: 2px solid #3479CD;
        /* background: rgba(255,255,255,0.65); */
        color: #3479CD;
    }


    .stat-band-wrapper {
        padding: 0 0 3rem;
    }

    .stat-band {
        position: relative;
        background: linear-gradient(120deg, #3479CD, #3479CD);
        border-radius: 28px;
        padding-top: 3rem;
        padding-bottom:3rem;
        box-shadow: 0 25px 60px rgba(25, 67, 188, 0.35);
        overflow: hidden;
    }

    .stat-band::before,
    .stat-band::after {
        content: '';
        position: absolute;
        width: 120px;
        height: 120px;
        border: 2px solid rgba(255,255,255,0.18);
        border-radius: 45% 55% 50% 50%;
        opacity: 0.5;
    }

    .stat-band::before {
        top: -60px;
        left: -40px;
    }

    .stat-band::after {
        bottom: -50px;
        right: -20px;
        border-radius: 60% 40% 55% 45%;
    }

    .stat-band .stat-card {
        text-align: center;
        color: white;
    }

    .stat-band .stat-card h3 {
        font-size: 4rem;
        font-weight: 700;
        margin-bottom: 0.35rem;
        background: linear-gradient(176.66deg, #FFFFFF 34.27%, rgba(255, 255, 255, 0) 84.49%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        color: transparent;
        display: inline-block;
    }

    .stat-band .stat-card p {
        margin-bottom: 0;
        color: rgba(255,255,255,0.85);
        font-weight: 500;
    }

    .section-wrapper {
        padding: 4rem 0;
    }

    .section-heading {
        font-size: 3rem;
        font-weight: 700;
        color: #071146;
    }

    .section-subtitle {
        color: #6070A3;
        max-width: 700px;
        margin: 0 auto 2.5rem;
    }

    .steps-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 1.5rem;
    }

    .step-card {
        background: #3479CD;
        border-radius: 26px;
        padding: 2rem 1.75rem;
        color: white;
        text-align: left;
        box-shadow: 0 25px 60px rgba(16, 46, 130, 0.25);
    }

    .step-card.light {
        background: #3479CD;
    }

    .step-card h4 {
        font-weight: 700;
        font-size: 1.15rem;
        margin-bottom: 0.75rem;
    }

    .step-card p {
        color: rgba(255,255,255,0.85);
        margin-bottom: 1.25rem;
        font-size: 0.98rem;
    }

    .step-card .number {
        font-size: 4.5rem;
        font-weight: 700;
        color: rgba(255,255,255,0.95);
        margin-right: 0.75rem;
        flex-shrink: 0;
        background: linear-gradient(176.66deg, #FFFFFF 34.27%, rgba(255, 255, 255, 0) 84.49%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        color: transparent;
        display: inline-block;
    }

    .step-card .heading-line {
        display: flex;
        align-items: center;
        margin-bottom: 0.75rem;
    }

    .step-card .heading-line h4 {
        margin-bottom: 0;
    }

    .card-illustration {
        border-radius: 22px;
        padding: 1rem;
        min-height: 190px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .card-illustration img {
        max-width: 100%;
        height: auto;
    }

    .steps-cta {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        padding: 0.85rem 1.9rem;
        font-weight: 600;
        border: 2px solid rgba(47, 76, 241, 0.3);
        color: #3479CD;
        background: transparent;
    }

    .steps-cta:hover {
        border-color: #3479CD;
        color: #3479CD;
        text-decoration: none;
    }

    .why-gradient {
        background: linear-gradient(180deg, #F4F6FF 0%, #EAF2FF 100%);
        border-radius: 34px;
        padding: 3.2rem;
        border: 1px solid rgba(52, 121, 205, 0.15);
    }

    .why-section {
        background: transparent;
        padding: 0;
        box-shadow: none;
        border: none;
    }

    .why-points li {
        margin-bottom: 0.75rem;
        font-weight: 500;
        color: #536398;
    }

    .why-points li i {
        color: #3479CD;
        margin-right: 0.6rem;
    }

    .why-copy {
        color: #4F5D8D;
        font-size: 1rem;
        line-height: 1.8;
    }

    .why-cta {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        padding: 0.85rem 1.9rem;
        font-weight: 600;
        color: #3479CD;
        border: 2px solid rgba(47, 76, 241, 0.4);
        background: #FFFFFF;
        box-shadow: 0 12px 28px rgba(47, 76, 241, 0.15);
        transition: all 0.2s ease;
    }

    .why-cta:hover {
        color: #3479CD;
        border-color: rgba(27, 47, 171, 0.6);
        text-decoration: none;
    }

    .why-stack {
        position: relative;
        width: 100%;
        max-width: 380px;
        min-height: 360px;
        margin-left: auto;
        margin-right: auto;
    }

    .photo-card {
        position: absolute;
        width: 78%;
        max-width: 340px;
        aspect-ratio: 3 / 4;
        border-radius: 32px;
        /* border: 12px solid #fff; */
        /* box-shadow: 0 30px 65px rgba(9, 28, 80, 0.25); */
        overflow: hidden;
        /* background: #fff; */
    }

    .photo-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .photo-card.back {
        right: -40%;
        top: -30px;
        transform: rotate(9deg);
    }

    .photo-card.front {
        transform: rotate(-6deg);
    }

    .cta-banner {
        background-color: #3479CD;
        background-image: url('<?= $ctaSprinkles ?>');
        background-size: cover;
        background-position: center;
        border-radius: 32px;
        padding-top: 2rem;
        padding-bottom: 2rem;
        padding-left: 4.5rem;
        color: white;
        margin-top: 4rem;
        box-shadow: 0 30px 80px rgba(26, 56, 166, 0.35);
        position: relative;
        overflow: hidden;
    }

    .cta-banner::after {
        content: '';
        position: absolute;
        inset: 0;
        background-image: radial-gradient(circle, rgba(255,255,255,0.25) 1px, transparent 1px);
        background-size: 24px 24px;
        opacity: 0.35;
        pointer-events: none;
    }

    .cta-content {
        position: relative;
        z-index: 1;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 2rem;
    }

    .cta-text {
        flex: 1 1 320px;
    }

    .cta-text h2 {
        font-weight: 700;
        font-size: 3rem;
        margin-bottom: 0.9rem;
    }

    .cta-copy {
        color: rgba(255,255,255,0.9);
        max-width: 420px;
        margin-bottom: 1.75rem;
        font-size: 1.02rem;
    }

    .cta-btn {
        border-radius: 999px;
        background: white;
        color: #3479CD;
        font-weight: 600;
        padding: 0.85rem 1.9rem;
        border: none;
        box-shadow: 0 12px 30px rgba(6, 24, 70, 0.2);
    }

    .cta-illustration {
        flex: 1 1 340px;
        min-height: 260px;
        position: relative;
        display: flex;
        align-items: flex-end;
        justify-content: center;
        padding-bottom: 1.5rem;
    }

    .cta-illustration::after {
        content: '';
        position: absolute;
        width: 75%;
        height: 35px;
        /* background: linear-gradient(90deg, rgba(20,58,141,0.2), rgba(255,255,255,0.45), rgba(20,58,141,0.2)); */
        border-radius: 50%;
        bottom: 12px;
        filter: blur(1px);
        opacity: 0.95;
        z-index: 1;
    }

    .cta-illustration img {
        position: relative;
        z-index: 2;
        max-width: 100%;
        width: 100%;
        height: auto;
        display: block;
    }

    .stories-heading {
        text-align: center;
    }

    .blog-card {
        /* background: #ffffff; */
        border-radius: 28px;
        overflow: hidden;
        border: 1px solid rgba(16, 36, 94, 0.08);
        box-shadow: 0 25px 65px rgba(6, 18, 68, 0.12);
        text-align: left;
        height: auto;
    }

    .blog-image {
        position: relative;
        height: 190px;
    }

    .blog-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .blog-chip {
        position: absolute;
        bottom: 15px;
        left: 24px;
        background: white;
        color: #0d1c4d;
        padding: 0.25rem 0.95rem;
        border-radius: 999px;
        font-weight: 600;
        font-size: 0.85rem;
        box-shadow: 0 10px 30px rgba(6, 18, 68, 0.22);
    }

    .blog-body {
        background: #3479CD;
        color: white;
        padding: 2.75rem 1.9rem 2.15rem;
        border-radius: 0 0 28px 28px;
        margin-top: 0.35rem;
    }

    .blog-body h5 {
        font-weight: 700;
        font-size: 1.5rem;
        margin-bottom: 0.9rem;
    }

    .blog-body p {
        color: rgba(255,255,255,0.9);
        font-size: 0.98rem;
        margin-bottom: 1.1rem;
    }

    .blog-link {
        background: transparent;
        border: none;
        padding: 0;
        color: white;
        font-weight: 600;
        text-decoration: underline;
    }

    @media (max-width: 992px) {
        .hero-cta {
            flex-direction: column;
            width: 100%;
        }
        .hero-cta .btn {
            width: 100%;
            text-align: center;
        }
        .why-gallery {
            grid-template-columns: 1fr;
        }
        .why-section {
            text-align: center;
        }
        .why-section .col-lg-6:first-child {
            order: 2;
        }
        .why-section .col-lg-6:last-child {
            order: 1;
        }
        .why-stack {
            max-width: 300px;
            min-height: 280px;
        }
        .photo-card.back {
            right: -20%;
            top: -20px;
        }
        .cta-banner {
            padding: 2rem;
            text-align: center;
            margin-left: auto;
            margin-right: auto;
        }
        .cta-content {
            flex-direction: column;
            align-items: center;
            gap: 1.5rem;
        }
        .cta-text {
            flex: 1 1 auto;
        }
        .cta-text h2,
        .cta-copy {
            text-align: center;
        }
        .cta-illustration {
            width: 100%;
            justify-content: center;
            padding-bottom: 0;
            min-height: unset;
        }
        .cta-illustration img {
            max-width: 260px;
        }
    }

    @media (max-width: 576px) {
        .why-stack {
            max-width: 250px;
            min-height: 240px;
        }
        .photo-card.back {
            right: 0;
            top: -10px;
        }
        .photo-card.front {
            transform: rotate(-3deg);
        }
        .photo-card.back {
            transform: rotate(5deg);
        }
    }

    #waarom-remcom{
        background: linear-gradient(180deg, #E9F4FF -44.28%, rgba(233, 244, 255, 0) 100%);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="hero-wrapper">
    <div class="container">
        <div class="hero-content">
            <!-- <span class="hero-preamble">Gratis te gebruiken voor families & vriendengroepen</span> -->
            <h1 class="hero-heading">Maak Cadeaus Geven Leuk, Eerlijk en Gemakkelijk.</h1>
            <p class="hero-text">Maak online een verlanglijstje of verloot met familie en vrienden. Geen stress. Geen verwarring. Volledig privé.</p>
            <div class="d-flex hero-cta flex-wrap">
                <a href="<?= base_url('register') ?>" class="btn btn-primary">Maak Een Verlanglijstje Aan</a>
                <a href="<?= base_url('drawings') ?>" class="btn btn-outline">Begin Met Lootjes Trekken</a>
            </div>
            <!-- <div class="hero-art">
                <div class="hero-character">Illustratie placeholder<br>karakter</div>
                <div class="hero-gifts">
                    <div class="gift-box short"></div>
                    <div class="gift-box"></div>
                    <div class="gift-box tall"></div>
                    <div class="gift-box"></div>
                </div>
            </div> -->
        </div>
    </div>
</section>

<section class="stat-band-wrapper">
    <div class="container">
        <div class="stat-band">
            <div class="row text-center gy-4 gy-md-0">
                <div class="col-md-4">
                    <div class="stat-card">
                        <h3>10K+</h3>
                        <p>Aangemaakte verlanglijstjes</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <h3>5K+</h3>
                        <p>Succesvolle lotingen</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <h3>8K+</h3>
                        <p>Verjaardagen deze maand</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-wrapper" id="hoe-het-werkt">
    <div class="container text-center">
        <h2 class="section-heading">Hoe Het Werkt</h2>
        <p class="section-subtitle">Aan de slag gaan is eenvoudig. Kies wat je wilt doen, nodig anderen uit en geniet van stressvrij cadeaus geven.</p>
        <div class="steps-grid">
            <div class="step-card">
                <div class="heading-line">
                    <div class="number">1</div>
                    <h4>Maak een verlanglijstje aan of start een loterij.</h4>
                </div>
                <p>Kies of je een persoonlijke verlanglijst wilt maken of een cadeauswens wilt organiseren door te loten.</p>
                <div class="card-illustration">
                    <img src="<?= $stepsIllustrations[0] ?>" alt="Illustratie verlanglijst maken">
                </div>
            </div>
            <div class="step-card light">
                <div class="heading-line">
                    <div class="number">2</div>
                    <h4>Nodig familie en vrienden uit</h4>
                </div>
                <p>Deel een simpele link met de mensen die je wilt uitnodigen. Alleen uitgenodigde personen hebben toegang tot je lijst of groep.</p>
                <div class="card-illustration">
                    <img src="<?= $stepsIllustrations[1] ?>" alt="Illustratie familie uitnodigen">
                </div>
            </div>
            <div class="step-card">
                <div class="heading-line">
                    <div class="number">3</div>
                    <h4>Loting en verlanglijstjes bekijken</h4>
                </div>
                <p>De loting vindt eerlijk en in het geheim plaats. Iedereen ziet voor wie hij of zij koopt en wat die persoon wil hebben.</p>
                <div class="card-illustration">
                    <img src="<?= $stepsIllustrations[2] ?>" alt="Illustratie loting en lijsten">
                </div>
            </div>
        </div>
        <a href="<?= base_url('register') ?>" class="btn btn-success steps-cta mt-4">Maak Een Verlanglijstje Aan</a>
    </div>
</section>

<section class="section-wrapper" id="waarom-remcom">
    <div class="container">
        <div class="why-section row g-4 align-items-center">
            <div class="col-lg-6">
                <h2 class="section-heading">Waarom Mensen Graag Remcom Gebruiken</h2>
                <p class="why-copy">Remcom gebruikt een volledig willekeurig systeem om mensen binnen je groep te matchen, waardoor elke trekking eerlijk en onpartijdig is. Zodra iedereen zich heeft aangemeld, worden de lotingen automatisch uitgevoerd, waardoor er geen handmatige selectie plaatsvindt en er geen kans is op voorkeursbeleid.</p>
                <p class="why-copy mt-3">Aan elke deelnemer wordt willekeurig één persoon toegewezen, waardoor het proces eenvoudig en transparant blijft. Privacy staat centraal. Elke match wordt alleen getoond aan de persoon die deze moet zien.</p>
                <a href="<?= base_url('register') ?>" class="btn btn-success why-cta mt-3">Maak Een Verlanglijstje Aan</a>
            </div>
            <div class="col-lg-6">
                <div class="why-stack">
                    <div class="photo-card back">
                        <img src="<?= $whyPhotos['back'] ?>" alt="Collage achtergrond">
                    </div>
                    <div class="photo-card front">
                        <img src="<?= $whyPhotos['front'] ?>" alt="Familie foto">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-wrapper">
    <div class="container">
        <div class="cta-banner">
            <div class="cta-content">
                <div class="cta-text">
                    <h2>Begin Vandaag Nog</h2>
                    <p class="cta-copy">Maak verlanglijstjes, verloot en geniet van eerlijke en besloten cadeausuitwisselingen in slechts een paar stappen.</p>
                    <a href="<?= base_url('register') ?>" class="btn cta-btn">Begin nu met het maken van uw lijst</a>
                </div>
                <div class="cta-illustration">
                    <img src="<?= $ctaIllustration ?>" alt="Illustratie Begin Vandaag Nog">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-wrapper" id="verhalen">
    <div class="container text-center">
        <h2 class="section-heading stories-heading">Verhalen en Tips Voor<br>Stressvrij Cadeaus Geven</h2>
        <p class="section-subtitle">Eenvoudige ideeën, handige tips en inspiratie om het geven van cadeaus gemakkelijker, eerlijker en leuker te maken voor familie en vrienden.</p>
        <div class="row g-4 mt-1">
            <div class="col-md-4">
                <div class="blog-card">
                    <div class="blog-image">
                        <img src="<?= $blogImages[0] ?>" alt="Waarom Loting Cadeaus Uitwisselen Eerlijker Maakt">
                        <span class="blog-chip">Leestijd: 5 minuten</span>
                    </div>
                    <div class="blog-body">
                        <h5>Waarom Loting Cadeaus Uitwisselen Eerlijker Maakt</h5>
                        <p>Hoe online loterijen het uitwisselen van cadeaus eenvoudig, eerlijk en zonder ongemakkelijke momenten maken.</p>
                        <button class="blog-link">Lees de Blog</button>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="blog-card">
                    <div class="blog-image">
                        <img src="<?= $blogImages[1] ?>" alt="Stressvrij Cadeaus Geven Voor Het Hele Gezin">
                        <span class="blog-chip">Leestijd: 5 minuten</span>
                    </div>
                    <div class="blog-body">
                        <h5>Stressvrij Cadeaus Geven Voor Het Hele Gezin</h5>
                        <p>Eenvoudige tips om verjaardagen en feestdagen te organiseren zonder verrassingen of budgetstress.</p>
                        <button class="blog-link">Lees de Blog</button>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="blog-card">
                    <div class="blog-image">
                        <img src="<?= $blogImages[2] ?>" alt="Vakanties Leuker Maken Voor Iedereen">
                        <span class="blog-chip">Leestijd: 5 minuten</span>
                    </div>
                    <div class="blog-body">
                        <h5>Vakanties Leuker Maken Voor Iedereen</h5>
                        <p>Hoe online loterijen het uitwisselen van cadeaus eenvoudig, eerlijk en zonder ongemakkelijke momenten maken.</p>
                        <button class="blog-link">Lees de Blog</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
