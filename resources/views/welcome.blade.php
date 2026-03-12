<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if(app()->getLocale() === 'cs')
        <title>Robin Hrdlička — Tvorba webů Louny | Web Developer & Designer</title>
        <meta name="description"
              content="Tvorba webových stránek a aplikací v Lounech. Robin Hrdlička — full-stack vývojář a designer. Laravel, moderní design, rychlé dodání. Dostupný pro nové projekty.">
        <meta name="keywords"
              content="Robin Hrdlička, tvorba webů Louny, webdesign Louny, web developer Louny, webové stránky Louny, Laravel developer, PHP vývojář, Ústecký kraj, full-stack vývojář">
    @else
        <title>Robin Hrdlička — Web Design & Development, Louny</title>
        <meta name="description"
              content="Web design and development based in Louny, Czech Republic. Robin Hrdlička — full-stack developer & designer specialising in Laravel, Livewire, and modern interfaces.">
        <meta name="keywords"
              content="Robin Hrdlička, web design Louny, web developer Louny, webdesign Louny, Laravel developer, full-stack developer Czech Republic, Ústí nad Labem region, portfolio">
    @endif
    <meta name="author" content="Robin Hrdlička">
    <meta name="robots" content="index, follow">

    <link rel="canonical" href="{{ url('/') }}">

    <link rel="alternate" hreflang="cs" href="{{ url('/') }}">
    <link rel="alternate" hreflang="en" href="{{ url('/') }}">
    <link rel="alternate" hreflang="x-default" href="{{ url('/') }}">

    <meta name="geo.region" content="CZ-US">
    <meta name="geo.placename" content="Louny">
    <meta name="geo.position" content="50.3571;13.7993">
    <meta name="ICBM" content="50.3571, 13.7993">

    <meta property="og:type" content="website">
    <meta property="og:locale" content="{{ app()->getLocale() === 'cs' ? 'cs_CZ' : 'en_US' }}">
    <meta property="og:locale:alternate" content="{{ app()->getLocale() === 'cs' ? 'en_US' : 'cs_CZ' }}">
    <meta property="og:url" content="{{ url('/') }}">
    @if(app()->getLocale() === 'cs')
        <meta property="og:title" content="Robin Hrdlička — Tvorba webů Louny | Web Developer & Designer">
        <meta property="og:description"
              content="Tvorba webových stránek a aplikací v Lounech. Full-stack vývojář a designer. Laravel, moderní design, rychlé dodání.">
    @else
        <meta property="og:title" content="Robin Hrdlička — Web Design & Development, Louny">
        <meta property="og:description"
              content="Web design and development based in Louny, Czech Republic. Full-stack developer & designer specialising in Laravel and modern interfaces.">
    @endif
    <meta property="og:image" content="{{ asset('robin-2.jpg') }}">
    <meta property="og:image:alt" content="Robin Hrdlička — Full-Stack Developer & Designer">
    <meta property="og:site_name" content="Robin Hrdlička">

    @if(app()->getLocale() === 'cs')
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="Robin Hrdlička — Tvorba webů Louny">
        <meta name="twitter:description"
              content="Tvorba webových stránek a aplikací v Lounech. Full-stack vývojář a designer.">
    @else
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="Robin Hrdlička — Web Design & Development, Louny">
        <meta name="twitter:description"
              content="Web design and development based in Louny, Czech Republic. Full-stack developer & designer.">
    @endif
    <meta name="twitter:image" content="{{ asset('robin-2.jpg') }}">
    <meta name="twitter:image:alt" content="Robin Hrdlička — Full-Stack Developer & Designer">

    <link rel="sitemap" type="application/xml" href="{{ url('/sitemap.xml') }}">

    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:ital,wght@0,700;0,800;0,900;1,700&family=Crimson+Pro:ital,wght@0,300;0,400;1,300;1,400&family=IBM+Plex+Mono:wght@400;500&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}?v={{ filemtime(public_path('css/welcome.css')) }}">

    <script type="application/ld+json">
        [
          {
            "@@context": "https://schema.org",
    "@@type": "Person",
    "name": "Robin Hrdlička",
    "url": "{{ url('/') }}",
    "jobTitle": "Full-Stack Developer & Designer",
    "email": "{{ $settings['contact_email'] ?? 'robinhrdlickadev@gmail.com' }}",
    "address": {
      "@@type": "PostalAddress",
      "addressLocality": "Louny",
      "addressRegion": "Ústecký kraj",
      "addressCountry": "CZ"
    },
    "knowsAbout": ["Laravel", "PHP", "Livewire", "Alpine.js", "Tailwind CSS", "TypeScript", "PostgreSQL", "Redis", "Figma", "Adobe Creative Cloud"],
    "sameAs": [
      "{{ $settings['github_url'] ?? 'https://github.com/Hrob2113' }}",
      "https://www.linkedin.com/in/robin-hrdli%C4%8Dka-653a22351/"
    ]
  },
  {
    "@@context": "https://schema.org",
    "@@type": "LocalBusiness",
    "name": "Robin Hrdlička — Web Design & Development",
    "url": "{{ url('/') }}",
    "email": "{{ $settings['contact_email'] ?? 'robinhrdlickadev@gmail.com' }}",
    "address": {
      "@@type": "PostalAddress",
      "addressLocality": "Louny",
      "addressRegion": "Ústecký kraj",
      "addressCountry": "CZ"
    },
    "geo": {
      "@@type": "GeoCoordinates",
      "latitude": 50.3571,
      "longitude": 13.7993
    },
    "areaServed": [
      { "@@type": "City", "name": "Louny" },
      { "@@type": "AdministrativeArea", "name": "Ústecký kraj" },
      { "@@type": "Country", "name": "Česká republika" }
    ],
    "description": "Tvorba webových stránek a aplikací v Lounech. Full-stack vývojář a designer — Laravel, moderní design, rychlé dodání.",
    "priceRange": "$$",
    "sameAs": [
      "{{ $settings['github_url'] ?? 'https://github.com/Hrob2113' }}",
      "https://www.linkedin.com/in/robin-hrdli%C4%8Dka-653a22351/"
    ]
  }
]
    </script>
</head>
<body>

<canvas id="bg"></canvas>
<div id="cur"></div>
<div id="cur-r"></div>

<canvas id="hero-neat" aria-hidden="true"></canvas>

<header>
    <nav role="navigation" aria-label="Primary navigation">
        <a href="#hero" class="n-brand">
            <svg class="n-icon" viewBox="0 0 32 44" fill="none" stroke="currentColor" stroke-width="1.7"
                 stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M6 41 L6 16 Q6 3 16 3 Q26 3 26 16 L26 41"/>
                <line x1="6" y1="25" x2="26" y2="25"/>
                <line x1="16" y1="25" x2="26" y2="41"/>
                <line x1="2" y1="41" x2="30" y2="41"/>
            </svg>
            <span class="n-label">HROB</span>
        </a>
        <ul class="n-links">
            <li><a href="#works">{{ __('nav.works') }}</a></li>
            <li><a href="#services">{{ __('nav.services') }}</a></li>
            <li><a href="#about">{{ __('nav.about') }}</a></li>
        </ul>
        <a href="#contact" class="n-cta">{{ __('nav.contact') }}</a>
    </nav>
</header>

<button class="ham" id="ham" aria-label="Menu">
    <span class="ham-line"></span>
    <span class="ham-line"></span>
</button>

<div class="mob-menu" id="mob-menu">
    <div class="mob-menu-bg" id="mob-menu-bg"></div>
    <div class="mob-menu-content">
        <a href="#works" class="mob-link" data-i="1">{{ __('nav.works') }}</a>
        <a href="#services" class="mob-link" data-i="2">{{ __('nav.services') }}</a>
        <a href="#about" class="mob-link" data-i="3">{{ __('nav.about') }}</a>
        <a href="#contact" class="mob-link mob-link" data-i="4">{{ __('nav.contact') }}</a>
    </div>
</div>

<div class="lang-toggle" data-active="{{ app()->getLocale() }}">
    <div class="lang-toggle-track"></div>
    <button type="button" class="lang-btn {{ app()->getLocale() === 'en' ? 'active' : '' }}" data-locale="en">EN
    </button>
    <button type="button" class="lang-btn {{ app()->getLocale() === 'cs' ? 'active' : '' }}" data-locale="cs">CZ
    </button>
</div>

<div class="locale-overlay{{ session('locale_switched') ? ' active' : '' }}" id="locale-overlay">
    <div class="locale-overlay-inner">
        <svg class="locale-tomb" viewBox="0 0 32 44">
            <defs>
                <mask id="tomb-mask">
                    <rect x="0" y="0" width="32" height="44" fill="black"/>
                    <g stroke="white" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 41 L6 16 Q6 3 16 3 Q26 3 26 16 L26 41"/>
                        <line x1="6" y1="25" x2="26" y2="25"/>
                        <line x1="16" y1="25" x2="26" y2="41"/>
                        <line x1="2" y1="41" x2="30" y2="41"/>
                    </g>
                </mask>
            </defs>
            <g class="tomb-stroke" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 41 L6 16 Q6 3 16 3 Q26 3 26 16 L26 41"/>
                <line x1="6" y1="25" x2="26" y2="25"/>
                <line x1="16" y1="25" x2="26" y2="41"/>
                <line x1="2" y1="41" x2="30" y2="41"/>
            </g>
            <rect class="tomb-fill-rect" mask="url(#tomb-mask)" x="0" y="44" width="32" height="44"
                  fill="var(--amber)"/>
        </svg>
    </div>
</div>
@if(session('locale_switched'))
    <script>
        requestAnimationFrame(() => {
            setTimeout(() => {
                document.getElementById('locale-overlay')?.classList.remove('active');
            }, 150);
        });
    </script>
@endif

<main>

    <section id="hero">
        <div class="h-portrait">
            <div class="h-portrait-frame">
                <img src="{{ asset('robin-2.jpg') }}"
                     alt="Portrait of Robin Hrdlička, full-stack developer and designer" fetchpriority="high"
                     width="600" height="800">
            </div>
        </div>
        <div class="h-mark" aria-hidden="true">
            <svg viewBox="0 0 32 44" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <g filter="url(#logo-distort)" stroke="#ffffff" stroke-width="0.6" opacity="0.9">
                    <path d="M6 41 L6 16 Q6 3 16 3 Q26 3 26 16 L26 41"/>
                    <line x1="6" y1="25" x2="26" y2="25"/>
                    <line x1="16" y1="25" x2="26" y2="41"/>
                    <line x1="2" y1="41" x2="30" y2="41"/>
                </g>
            </svg>
        </div>
        <h1 class="h-name" aria-label="Robin Hrdlička">
            <span class="h-a">ROBIN</span>
            <span class="h-c">HRDLIČKA</span>
        </h1>
        <div class="h-foot">
            <div class="h-copy rv">
                <h2>{{ __('hero.tagline') }}</h2>
                <p>{{ __('hero.desc') }}</p>
            </div>
            <div class="h-acts rv" style="transition-delay:.14s">
                <a href="#works" class="btn-p">{{ __('hero.cta1') }}</a>
                <a href="#contact" class="btn-g">{{ __('hero.cta2') }}</a>
            </div>
        </div>
        <div class="h-scroll" aria-hidden="true">
            <div class="s-line"></div>
            <span>{{ __('hero.scroll') }}</span>
        </div>
    </section>

    <section id="stack">
        <div class="sk-inner">
            <div class="sk-group">
                <span class="sk-head">{{ __('stack.backend') }}</span>
                <div class="sk-list">
                    <span class="sk-item sk-item--hi">Laravel</span>
                    <span class="sk-item sk-item--hi">PHP 8.4</span>
                    <span class="sk-item">REST APIs</span>
                    <span class="sk-item">Queues &amp; Jobs</span>
                </div>
            </div>
            <div class="sk-group">
                <span class="sk-head">{{ __('stack.frontend') }}</span>
                <div class="sk-list">
                    <span class="sk-item sk-item--hi">Livewire</span>
                    <span class="sk-item sk-item--hi">Alpine js</span>
                    <span class="sk-item">Tailwind CSS</span>
                    <span class="sk-item">TypeScript</span>
                </div>
            </div>
            <div class="sk-group">
                <span class="sk-head">{{ __('stack.design') }}</span>
                <div class="sk-list">
                    <span class="sk-item sk-item--hi">Figma</span>
                    <span class="sk-item sk-item--hi">Adobe CC</span>
                    <span class="sk-item">Blender</span>
                    <span class="sk-item">Brand Systems</span>
                </div>
            </div>
            <div class="sk-group">
                <span class="sk-head">{{ __('stack.data') }}</span>
                <div class="sk-list">
                    <span class="sk-item sk-item--hi">PostgreSQL</span>
                    <span class="sk-item sk-item--hi">Redis</span>
                    <span class="sk-item">Docker</span>
                    <span class="sk-item">Git / CI</span>
                </div>
            </div>
        </div>
    </section>

    <section id="works">
        <div class="works-top rv">
            <div>
                <div class="sl">{{ __('works.label') }}</div>
                <h2 class="works-hed">{!! __('works.heading') !!}</h2>
            </div>
            <p class="works-sub">{{ __('works.sub') }}</p>
        </div>

        <div class="filter-bar rv" style="transition-delay:.1s">
            <button class="f-btn active" data-filter="all">{{ __('works.filterAll') }}</button>
            <button class="f-btn" data-filter="web">{{ __('works.filterWeb') }}</button>
            <button class="f-btn" data-filter="graphic">{{ __('works.filterGraphic') }}</button>
            <button class="f-btn" data-filter="brand">{{ __('works.filterBrand') }}</button>
            <button class="f-btn" data-filter="ui">{{ __('works.filterUi') }}</button>
        </div>

        <div class="pg rv" style="transition-delay:.18s" id="portfolio-grid">
            <div class="pg-track">

                <a href="https://www.kavarnalouny.cz/" target="_blank" rel="noopener noreferrer"
                   class="pc pc--featured" data-cat="web">
                    <img src="{{ asset('skald.png') }}" alt="Skald Coffee — website screenshot" loading="lazy"
                         decoding="async">
                    <div class="pc-ov"></div>
                    <div class="pc-arrow">→</div>
                    <span class="pc-yr">2026</span>
                    <div class="pc-info">
                        <span class="pc-cat">{{ __('card.skald.cat') }}</span>
                        <div class="pc-title">SKALD COFFEE</div>
                        <p class="pc-desc">{{ __('card.skald.desc') }}</p>
                        <div class="pc-tags">
                            <span class="pc-tag">Laravel</span><span class="pc-tag">Tailwind CSS</span><span
                                class="pc-tag">JS</span>
                        </div>
                    </div>
                </a>

                <a href="https://www.louny-instalater.cz/" target="_blank" rel="noopener noreferrer"
                   class="pc pc--tall" data-cat="web">
                    <img src="{{ asset('graupner.png') }}" alt="Graupner - Instalater" loading="lazy"
                         decoding="async">
                    <div class="pc-ov"></div>
                    <div class="pc-arrow">→</div>
                    <span class="pc-yr">2026</span>
                    <div class="pc-info">
                        <span class="pc-cat">{{ __('card.skald.cat') }}</span>
                        <div class="pc-title">{{ __('card.graupner.title') }}</div>
                        <p class="pc-desc">{{ __('card.graupner.desc') }}</p>
                        <div class="pc-tags">
                            <span class="pc-tag">Frontend</span><span class="pc-tag">Backend</span><span
                                class="pc-tag">Statamic</span>
                        </div>
                    </div>
                </a>

                <a href="https://www.studio-pebe.cz/" target="_blank" rel="noopener noreferrer" class="pc pc--wide"
                   data-cat="ui">
                    <img src="{{ asset('studio-pebe.webp') }}" alt="Beauty Studio PeBe — website design" loading="lazy"
                         decoding="async">
                    <div class="pc-ov"></div>
                    <div class="pc-arrow">→</div>
                    <span class="pc-yr">2025</span>
                    <div class="pc-info">
                        <span class="pc-cat">{{ __('card.pebe.cat') }}</span>
                        <div class="pc-title">BEATY STUDIO PeBe</div>
                        <p class="pc-desc">{{ __('card.pebe.desc') }}</p>
                        <div class="pc-tags">
                            <span class="pc-tag">Frontend</span><span class="pc-tag">Prototype</span><span
                                class="pc-tag">Design System</span>
                        </div>
                    </div>
                </a>

                <div class="pc pc--sq" data-cat="graphic">
                    <img src="{{ asset('skald-ilustrations.png') }}" alt="Skald ilustrations" loading="lazy"
                         decoding="async">
                    <div class="pc-ov"></div>
                    <div class="pc-arrow">→</div>
                    <span class="pc-yr">2026</span>
                    <div class="pc-info">
                        <span class="pc-cat">{{ __('card.signal.cat') }}</span>
                        <div class="pc-title">SKALD COFFEE ILUSTRATIONS</div>
                        <p class="pc-desc">{{ __('card.signal.desc') }}</p>
                        <div class="pc-tags">
                            <span class="pc-tag">Artwork</span><span class="pc-tag">Print</span>
                        </div>
                    </div>
                </div>

                <div class="pc pc--wide2" data-cat="ui">
                    <img src="{{ asset('hokejova-skola.png') }}"
                         alt="Hokejová škola Radka Gardoně — UI/UX design concept"
                         loading="lazy" decoding="async">
                    <div class="pc-ov"></div>
                    <div class="pc-arrow">→</div>
                    <span class="pc-yr">2022</span>
                    <div class="pc-info">
                        <span class="pc-cat">{{ __('card.kasida.cat') }}</span>
                        <div class="pc-title">HOKEJOVÁ ŠKOLA RADKA GARDONĚ</div>
                        <p class="pc-desc">{{ __('card.kasida.desc') }}</p>
                        <div class="pc-tags">
                            <span class="pc-tag">Figma</span><span class="pc-tag">UX/UI</span>
                        </div>
                    </div>
                </div>

                <div class="pc pc--sq" data-cat="brand">
                    <img src="{{ asset('med-kvetovy.png') }}" alt="Med - Květový" loading="lazy" decoding="async">
                    <div class="pc-ov"></div>
                    <div class="pc-arrow">→</div>
                    <span class="pc-yr">2024</span>
                    <div class="pc-info">
                        <span class="pc-cat">{{ __('card.med.cat') }}</span>
                        <div class="pc-title">Med Květový</div>
                        <p class="pc-desc">{{ __('card.med.desc') }}</p>
                        <div class="pc-tags">
                            <span class="pc-tag">Logo</span><span class="pc-tag">Typography</span><span
                                class="pc-tag">Print</span>
                        </div>
                    </div>
                </div>

        </div>
        </div>
    </section>

    <section id="services">
        <div class="sv-wrap">
            <div class="sv-left rl">
                <div class="sl">{{ __('services.label') }}</div>
                <div class="sv-big">{!! __('services.heading') !!}</div>
            </div>
            <div class="sv-grid rr">
                <div class="sc"><span class="sc-icon">[ ]</span>
                    <div class="sc-name">{{ __('services.backends') }}</div>
                    <p class="sc-desc">{{ __('services.backendsDesc') }}</p></div>
                <div class="sc"><span class="sc-icon">◈</span>
                    <div class="sc-name">{{ __('services.frontends') }}</div>
                    <p class="sc-desc">{{ __('services.frontendsDesc') }}</p></div>
                <div class="sc"><span class="sc-icon">⬡</span>
                    <div class="sc-name">{{ __('services.visual') }}</div>
                    <p class="sc-desc">{{ __('services.visualDesc') }}</p></div>
                <div class="sc"><span class="sc-icon">✦</span>
                    <div class="sc-name">{{ __('services.consulting') }}</div>
                    <p class="sc-desc">{{ __('services.consultingDesc') }}</p></div>
            </div>
        </div>
    </section>

    <section id="about">
        <div class="ab-wrap">
            <div class="ab-img rl">
                <div class="ab-ghost" aria-hidden="true">ROBIN</div>
                <div class="ab-port">
                    <img src="{{ asset('robin.jpeg') }}" alt="Robin Hrdlička — developer and designer" loading="lazy"
                         decoding="async">
                </div>
            </div>
            <div class="rr">
                <div class="sl">{{ __('about.label') }}</div>
                <h2 class="ab-hed">{!! __('about.heading') !!}</h2>
                <p class="ab-copy">{{ __('about.copy1') }}</p>
                <p class="ab-copy" style="margin-bottom:0">{{ __('about.copy2') }}</p>
                <div class="ab-mantra">{!! __('about.mantra') !!}</div>
                <div class="bdg-row">
                    <div class="bdg"><span class="bdg-d"></span><span>{{ __('about.bdg1') }}</span></div>
                    <div class="bdg bdg-h"><span class="bdg-d"></span><span>{{ __('about.bdg2') }}</span></div>
                </div>
            </div>
        </div>
    </section>

    <section id="process">
        <div class="rv">
            <div class="sl">{{ __('process.label') }}</div>
            <h2 class="pr-hed">{!! __('process.heading') !!}</h2>
        </div>
        <div class="steps rv" style="transition-delay:.1s">
            <div class="st"><span class="st-n">01</span>
                <div class="st-name">{{ __('process.s1name') }}</div>
                <p class="st-desc">{{ __('process.s1desc') }}</p></div>
            <div class="st"><span class="st-n">02</span>
                <div class="st-name">{{ __('process.s2name') }}</div>
                <p class="st-desc">{{ __('process.s2desc') }}</p></div>
            <div class="st"><span class="st-n">03</span>
                <div class="st-name">{{ __('process.s3name') }}</div>
                <p class="st-desc">{{ __('process.s3desc') }}</p></div>
            <div class="st"><span class="st-n">04</span>
                <div class="st-name">{{ __('process.s4name') }}</div>
                <p class="st-desc">{{ __('process.s4desc') }}</p></div>
        </div>
    </section>

    <section id="contact">
        <div class="ct-ghost" aria-hidden="true">HELLO</div>
        <div class="ct-in">
            <div class="ct-eye">{{ __('contact.eye') }}</div>
            <h2 class="ct-hed">{!! __('contact.heading') !!}</h2>
            <p class="ct-sub">{{ __('contact.sub') }}</p>
            <a href="mailto:{{ $settings['contact_email'] ?? 'robinhrdlickadev@gmail.com' }}"
               class="ct-email">{{ $settings['contact_email'] ?? 'robinhrdlickadev@gmail.com' }}</a>
            <div class="cf-anchor" id="cf-anchor">
                <div class="cf-popup" id="cf-popup">
                    <div class="cf-accent"></div>
                    <button type="button" class="cf-close" id="cf-close" aria-label="Close">&times;</button>
                    <div class="cf-head">{{ __('contact.formHead') }}</div>
                    <p class="cf-subhead">{{ __('contact.formSub') }}</p>
                    <form id="cf-form" action="{{ route('contact.store') }}" method="post">
                        <div class="cf-row">
                            <div class="cf-field">
                                <label class="cf-label" for="cf-name">{{ __('contact.labelName') }}</label>
                                <input class="cf-input" type="text" id="cf-name" name="name"
                                       placeholder="{{ __('contact.phName') }}" required>
                            </div>
                            <div class="cf-field">
                                <label class="cf-label" for="cf-email">{{ __('contact.labelEmail') }}</label>
                                <input class="cf-input" type="email" id="cf-email" name="email"
                                       placeholder="you@company.com" required>
                            </div>
                        </div>
                        <div class="cf-row">
                            <div class="cf-field full">
                                <label class="cf-label" for="cf-subject">{{ __('contact.labelSubject') }}</label>
                                <input class="cf-input" type="text" id="cf-subject" name="subject"
                                       placeholder="{{ __('contact.phSubject') }}">
                            </div>
                        </div>
                        <div class="cf-row">
                            <div class="cf-field full">
                                <label class="cf-label" for="cf-msg">{{ __('contact.labelMsg') }}</label>
                                <textarea class="cf-textarea" id="cf-msg" name="message"
                                          placeholder="{{ __('contact.phMsg') }}" required></textarea>
                            </div>
                        </div>
                        <button type="submit" class="cf-submit" id="cf-submit">{{ __('contact.send') }}</button>
                    </form>
                    <div class="cf-status" id="cf-status"
                         style="display:none;padding:12px 16px;margin-top:12px;border-radius:8px;font-family:var(--fm);font-size:12px;letter-spacing:.04em"></div>
                    <div class="cf-note">{{ __('contact.note') }}</div>
                </div>
                <button type="button" class="btn-ct" id="open-cf">{{ __('contact.writeBtn') }}</button>
            </div>
            <div class="so-links">
                <a href="{{ $settings['github_url'] ?? 'https://github.com/Hrob2113' }}" target="_blank"
                   rel="noopener noreferrer" aria-label="Robin Hrdlička on GitHub">GitHub</a><a
                    href="{{ $settings['linkedin_url'] ?? 'https://www.linkedin.com/in/robin-hrdli%C4%8Dka-653a22351/' }}"
                    target="_blank" rel="noopener noreferrer" aria-label="Robin Hrdlička on LinkedIn">LinkedIn</a>
            </div>
            <div class="ct-sign">{{ __('contact.sign') }}</div>
        </div>
    </section>

</main>

<div class="footer-wrap">
    <footer>
        <div class="ft-brand">
            <svg style="width:14px;height:19px;margin-right:7px;vertical-align:middle" viewBox="0 0 32 44" fill="none"
                 stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"
                 aria-hidden="true">
                <path d="M6 41 L6 16 Q6 3 16 3 Q26 3 26 16 L26 41"/>
                <line x1="6" y1="25" x2="26" y2="25"/>
                <line x1="16" y1="25" x2="26" y2="41"/>
                <line x1="2" y1="41" x2="30" y2="41"/>
            </svg>
            <span>HROB</span>&ensp;<span style="color:var(--low)">/ Robin Hrdlicka</span>
        </div>
        <div class="ft-links">
            <a href="https://github.com/Hrob2113" target="_blank" rel="noopener noreferrer"
               aria-label="Robin Hrdlička on GitHub">GitHub</a>
            <a href="https://www.linkedin.com/in/robin-hrdli%C4%8Dka-653a22351/" target="_blank"
               rel="noopener noreferrer" aria-label="Robin Hrdlička on LinkedIn">LinkedIn</a>
        </div>
        <div class="ft-copy">{{ __('footer.copy') }}</div>
        <a href="#hero" class="ft-top">{{ __('footer.top') }}</a>
    </footer>
</div>

<script src="{{ asset('js/welcome.js') }}?v={{ filemtime(public_path('js/welcome.js')) }}" defer></script>

<script type="importmap">
    { "imports": { "three": "https://cdn.jsdelivr.net/npm/three@0.183.0/build/three.module.js" } }
</script>
<script type="module"
        src="{{ asset('js/hero-shader.js') }}?v={{ filemtime(public_path('js/hero-shader.js')) }}"></script>
</body>
</html>
