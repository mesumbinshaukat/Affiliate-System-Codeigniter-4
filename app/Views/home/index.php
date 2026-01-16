<?= $this->extend('layouts/landing') ?>

<?= $this->section('styles') ?>
<style>
    .hero-wrapper {
        background: linear-gradient(180deg, #E1F0FF 0%, #F8FBFF 72%, #FFFFFF 100%);
        padding: 4.75rem 0 2.5rem;
        position: relative;
        overflow: hidden;
    }

    .hero-wrapper::after {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at 30% -10%, rgba(52,121,205,0.18), transparent 60%);
        pointer-events: none;
    }

    .hero-content {
        position: relative;
        max-width: 760px;
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
        font-size: clamp(2.6rem, 4vw, 3.9rem);
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

    .hero-cta .btn-outline {
        border: 2px solid #3479CD;
        background: rgba(255,255,255,0.65);
        color: #3479CD;
    }

    .hero-art {
        position: relative;
        margin-top: 3.25rem;
        display: flex;
        align-items: flex-end;
        justify-content: center;
        gap: 1.5rem;
        flex-wrap: wrap;
        z-index: 1;
    }

    .hero-character {
        width: 240px;
        height: 270px;
        border-radius: 32px;
        border: 2px dashed rgba(7,17,70,0.2);
        background: linear-gradient(180deg, #FFE7E0 0%, #F9EEFF 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6B7CA5;
        font-weight: 600;
        text-align: center;
        padding: 1.5rem;
    }

    .hero-gifts {
        display: flex;
        gap: 1rem;
        flex-wrap: nowrap;
        align-items: flex-end;
    }

    .gift-box {
        width: 110px;
        height: 160px;
        border-radius: 28px;
        background: linear-gradient(180deg, #F4F8FF, #DDE6FF);
        border: 6px solid #fff;
        box-shadow: 0 20px 45px rgba(7, 17, 70, 0.15);
        position: relative;
    }

    .gift-box::after {
        content: '';
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 10px;
        height: 100%;
        background: rgba(255,255,255,0.6);
        border-radius: 999px;
    }

    .gift-box.short {
        height: 120px;
    }

    .gift-box.tall {
        height: 190px;
    }

    .stat-band-wrapper {
        padding: 0 0 3rem;
    }

    .stat-band {
        position: relative;
        background: linear-gradient(120deg, #3479CD, #3479CD);
        border-radius: 28px;
        padding: 1.75rem;
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
        font-size: 2.2rem;
        font-weight: 700;
        margin-bottom: 0.35rem;
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
        font-size: 2.2rem;
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
        font-size: 2.5rem;
        font-weight: 700;
        color: rgba(255,255,255,0.95);
        margin-right: 0.75rem;
        flex-shrink: 0;
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
        background: linear-gradient(180deg, #F6F9FF 0%, #EDF3FF 100%);
        padding: 1rem;
        min-height: 150px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #5A6EA7;
        font-weight: 600;
    }

    .steps-cta {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        padding: 0.85rem 1.9rem;
        font-weight: 600;
        border: 2px solid rgba(47, 76, 241, 0.3);
        color: #2F4CF1;
        background: transparent;
    }

    .steps-cta:hover {
        border-color: #2F4CF1;
        color: #1c2fa8;
        text-decoration: none;
    }

    .why-section {
        background: white;
        border-radius: 28px;
        padding: 2.8rem;
        box-shadow: 0 30px 70px rgba(7, 17, 70, 0.15);
        border: 1px solid var(--landing-border);
    }

    .why-points li {
        margin-bottom: 0.75rem;
        font-weight: 500;
        color: #536398;
    }

    .why-points li i {
        color: #2F4CF1;
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
        color: #14215C;
        border: 1px solid rgba(20, 33, 92, 0.2);
        background: #FFFFFF;
        box-shadow: 0 12px 30px rgba(10, 23, 70, 0.12);
        transition: all 0.2s ease;
    }

    .why-cta:hover {
        color: #2F4CF1;
        border-color: rgba(47, 76, 241, 0.4);
        text-decoration: none;
    }

    .why-stack {
        position: relative;
        width: 100%;
        max-width: 360px;
        min-height: 320px;
        margin-left: auto;
        margin-right: auto;
    }

    .photo-card {
        position: absolute;
        width: 70%;
        max-width: 320px;
        aspect-ratio: 3 / 4;
        border-radius: 24px;
        border: 8px solid #fff;
        box-shadow: 0 30px 60px rgba(4, 17, 63, 0.2);
        background: linear-gradient(180deg, #FCE9E3, #F8F1FF);
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: #5B6B98;
        font-weight: 600;
    }

    .photo-card.back {
        right: 8%;
        top: 20px;
        transform: rotate(6deg);
        background: linear-gradient(180deg, #F4FAFF, #E8F0FF);
    }

    .photo-card.front {
        left: 2%;
        top: 60px;
        transform: rotate(-6deg);
    }

    .cta-banner {
        background: #3479CD;
        border-radius: 32px;
        padding: 3rem;
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
        font-size: clamp(2rem, 2.8vw, 2.65rem);
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
        flex: 1 1 280px;
        min-height: 220px;
        border-radius: 28px;
        border: 2px dashed rgba(255,255,255,0.6);
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        font-weight: 600;
        color: rgba(255,255,255,0.85);
        background: rgba(255,255,255,0.1);
    }

    .blog-card {
        background: #3479CD;
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid var(--landing-border);
        box-shadow: 0 20px 55px rgba(8, 20, 66, 0.12);
        text-align: left;
    }

    .blog-image {
        height: 190px;
        border-bottom: 1px solid var(--landing-border);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6B7CA5;
        font-weight: 600;
        background: linear-gradient(135deg, #F2F6FF, #E6EEFF);
    }

    .blog-card .card-body {
        padding: 1.75rem;
    }

    .blog-card .eyebrow {
        font-size: 0.9rem;
        font-weight: 600;
        color: white;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .blog-card h5 {
        font-weight: 700;
        color: white;
        margin-bottom: 0.6rem;
    }

    .blog-card p {
        color: white;
        font-size: 0.97rem;
    }

    .blog-card .btn-link {
        font-weight: 600;
        color: white;
        text-decoration: underline;
        padding: 0;
        text-decoration: none;
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
                <a href="<?= base_url('index.php/register') ?>" class="btn btn-primary">Maak Een Verlanglijstje Aan</a>
                <a href="<?= base_url('index.php/drawings') ?>" class="btn btn-outline">Begin Met Lootjes Trekken</a>
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
                <div class="card-illustration">Illustratie placeholder</div>
            </div>
            <div class="step-card light">
                <div class="heading-line">
                    <div class="number">2</div>
                    <h4>Nodig familie en vrienden uit</h4>
                </div>
                <p>Deel een simpele link met de mensen die je wilt uitnodigen. Alleen uitgenodigde personen hebben toegang tot je lijst of groep.</p>
                <div class="card-illustration">Illustratie placeholder</div>
            </div>
            <div class="step-card">
                <div class="heading-line">
                    <div class="number">3</div>
                    <h4>Loting en verlanglijstjes bekijken</h4>
                </div>
                <p>De loting vindt eerlijk en in het geheim plaats. Iedereen ziet voor wie hij of zij koopt en wat die persoon wil hebben.</p>
                <div class="card-illustration">Illustratie placeholder</div>
            </div>
        </div>
        <a href="<?= base_url('index.php/register') ?>" class="steps-cta mt-4">Maak Een Verlanglijstje Aan</a>
    </div>
</section>

<section class="section-wrapper" id="waarom-remcom">
    <div class="container">
        <div class="why-section row g-4 align-items-center">
            <div class="col-lg-6">
                <h2 class="section-heading">Waarom Mensen Graag Remcom Gebruiken</h2>
                <p class="why-copy">Remcom gebruikt een volledig willekeurig systeem om mensen binnen je groep te matchen, waardoor elke trekking eerlijk en onpartijdig is. Zodra iedereen zich heeft aangemeld, worden de lotingen automatisch uitgevoerd, waardoor er geen handmatige selectie plaatsvindt en er geen kans is op voorkeursbeleid.</p>
                <p class="why-copy mt-3">Aan elke deelnemer wordt willekeurig één persoon toegewezen, waardoor het proces eenvoudig en transparant blijft. Privacy staat centraal. Elke match wordt alleen getoond aan de persoon die deze moet zien.</p>
                <a href="<?= base_url('index.php/register') ?>" class="why-cta mt-3">Maak Een Verlanglijstje Aan</a>
            </div>
            <div class="col-lg-6">
                <div class="why-stack">
                    <div class="photo-card back">Collage afbeelding</div>
                    <div class="photo-card front">Familie foto placeholder</div>
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
                    <a href="<?= base_url('index.php/register') ?>" class="btn cta-btn">Begin nu met het maken van uw lijst</a>
                </div>
                <div class="cta-illustration">Illustratie placeholder<br>cadeau stapel</div>
            </div>
        </div>
    </div>
</section>

<section class="section-wrapper" id="verhalen">
    <div class="container text-center">
        <h2 class="section-heading">Verhalen en Tips Voor Stressvrij Cadeaus Geven</h2>
        <p class="section-subtitle">Blijf geïnspireerd met verhalen van Remcom gebruikers en handige gidsen.</p>
        <div class="row g-4 mt-2">
            <div class="col-md-4">
                <div class="blog-card h-100">
                    <div class="blog-image">Artikel afbeelding</div>
                    <div class="card-body">
                        <!-- <p class="eyebrow">Verhalen</p> -->
                        <h5>Waarom Loting Cadeaus Uiteindelijk Iedereen Blijer Maakt</h5>
                        <p>Zo zorg je ervoor dat ieder gezin eerlijk cadeaus verdeelt en iedereen een glimlach houdt.</p>
                        <button class="btn btn-link">Lees artikel <i class="fa-solid fa-arrow-right ms-1"></i></button>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="blog-card h-100">
                    <div class="blog-image">Artikel afbeelding</div>
                    <div class="card-body">
                        <!-- <p class="eyebrow">Tips &amp; Tricks</p> -->
                        <h5>Stressvrij Cadeaus Geven Voor Grote Families</h5>
                        <p>Handige stappen om met veel mensen alsnog overzicht te houden en dubbele cadeaus te voorkomen.</p>
                        <button class="btn btn-link">Lees artikel <i class="fa-solid fa-arrow-right ms-1"></i></button>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="blog-card h-100">
                    <div class="blog-image">Artikel afbeelding</div>
                    <div class="card-body">
                        <!-- <p class="eyebrow">Inspiratie</p> -->
                        <h5>Vakanties Leuker Maken Voor Iedereen</h5>
                        <p>Gebruik Remcom om feestdagen gezellig, eerlijk en ontspannen te houden voor de hele groep.</p>
                        <button class="btn btn-link">Lees artikel <i class="fa-solid fa-arrow-right ms-1"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
