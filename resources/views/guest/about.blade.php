@extends('layouts.guest')

@section('title', 'About Us')

@push('styles')
<style>
    @keyframes softRise {
        from {
            opacity: 0;
            transform: translateY(16px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .about-rise {
        animation: softRise 0.6s ease-out both;
    }

    .about-rise:nth-child(2) { animation-delay: 0.08s; }
    .about-rise:nth-child(3) { animation-delay: 0.16s; }
    .about-rise:nth-child(4) { animation-delay: 0.24s; }

    .about-testimonial-side {
        opacity: 0.22;
        filter: grayscale(20%);
    }

    .about-testimonial-quote {
        line-height: 1.6;
    }

    .about-dot.is-active {
        background-color: rgb(249 115 22);
        opacity: 1;
        transform: scale(1.18);
    }

    .about-testimonial-slider .portal-slide {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .about-testimonial-slider .portal-slide > article {
        width: 100%;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto max-w-7xl space-y-14 px-4 py-8">
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-sky-900 via-[color:var(--portal-navy)] to-cyan-900 px-6 py-14 text-white shadow-2xl ring-2 ring-white/10 md:px-12 md:py-20">
        <div class="absolute -right-20 -top-20 h-72 w-72 rounded-full bg-[color:var(--portal-gold)]/25 blur-3xl"></div>
        <div class="absolute -bottom-24 -left-20 h-72 w-72 rounded-full bg-cyan-500/20 blur-3xl"></div>

        <div class="relative z-10 mx-auto max-w-4xl text-center">
            <p class="inline-flex items-center rounded-full bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.14em] text-amber-200 ring-1 ring-white/20">
                About The University
            </p>
            <h1 class="mt-4 text-4xl font-bold leading-tight md:text-6xl">
                Built for Academic Excellence
                <span class="mt-2 block text-transparent bg-gradient-to-r from-[color:var(--portal-gold)] to-amber-300 bg-clip-text">
                    Designed for Student Success
                </span>
            </h1>
            <p class="mx-auto mt-5 max-w-3xl text-base leading-relaxed text-slate-200 md:text-lg">
                We combine rigorous teaching, modern learning technology, and a deeply supportive campus culture to help learners develop strong knowledge, practical capability, and professional confidence. From orientation to graduation, our focus is on helping every student build a meaningful academic and career path.
            </p>
            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('guest.courses') }}" class="inline-flex items-center rounded-full bg-white px-6 py-3 text-sm font-semibold text-[color:var(--portal-navy)] shadow-md hover:bg-slate-100">
                    Explore Courses
                </a>
                <a href="{{ route('guest.contact') }}" class="inline-flex items-center rounded-full border border-white/40 bg-white/10 px-6 py-3 text-sm font-semibold text-white hover:bg-white/20">
                    Contact Us
                </a>
            </div>
        </div>
    </section>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="about-rise rounded-2xl border border-blue-200 bg-blue-50 p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-200/80 text-blue-800">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            </div>
            <p class="mt-3 text-xs font-semibold uppercase tracking-wide text-blue-700">Years Of Excellence</p>
            <p class="mt-1 text-3xl font-bold text-blue-900">{{ $stats['yearsOfExcellence'] > 0 ? number_format($stats['yearsOfExcellence']) . '+' : '50+' }}</p>
        </div>
        <div class="about-rise rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-200/80 text-emerald-800">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
            </div>
            <p class="mt-3 text-xs font-semibold uppercase tracking-wide text-emerald-700">Active Students</p>
            <p class="mt-1 text-3xl font-bold text-emerald-900">{{ $stats['totalStudents'] > 0 ? number_format($stats['totalStudents']) . '+' : '5,000+' }}</p>
        </div>
        <div class="about-rise rounded-2xl border border-indigo-200 bg-indigo-50 p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-200/80 text-indigo-800">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
            </div>
            <p class="mt-3 text-xs font-semibold uppercase tracking-wide text-indigo-700">Expert Faculty</p>
            <p class="mt-1 text-3xl font-bold text-indigo-900">{{ $stats['totalFaculty'] > 0 ? number_format($stats['totalFaculty']) . '+' : '200+' }}</p>
        </div>
        <div class="about-rise rounded-2xl border border-amber-200 bg-amber-50 p-5 shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-200/80 text-amber-800">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
            </div>
            <p class="mt-3 text-xs font-semibold uppercase tracking-wide text-amber-700">Programs Offered</p>
            <p class="mt-1 text-3xl font-bold text-amber-900">{{ $stats['totalPrograms'] > 0 ? number_format($stats['totalPrograms']) . '+' : '100+' }}</p>
        </div>
    </section>

    @include('guest.partials.quick-links-strip')

    @include('guest.partials.icon-highlights')

    <section class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[color:var(--portal-navy)]">Our Mission</p>
            <h2 class="mt-3 text-3xl font-bold text-[color:var(--portal-navy)]">Deliver quality education for all learners</h2>
            <p class="mt-4 text-sm leading-relaxed text-slate-600">
                We empower students through research-informed teaching, consistent mentorship, and practical learning experiences that connect theory to real-world challenges. Our teaching model emphasizes critical thinking, communication, and ethical responsibility so graduates can contribute with confidence in fast-changing environments.
            </p>
            <ul class="mt-5 space-y-2 text-sm text-slate-700">
                <li>Research-driven instruction</li>
                <li>Inclusive and supportive learning environments</li>
                <li>Career-focused academic development</li>
            </ul>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[color:var(--portal-navy)]">Our Vision</p>
            <h2 class="mt-3 text-3xl font-bold text-[color:var(--portal-navy)]">Lead innovation and global impact</h2>
            <p class="mt-4 text-sm leading-relaxed text-slate-600">
                We aim to be recognized for academic excellence, impactful research, and graduates who create practical solutions to local and global challenges. Our long-term direction is to strengthen interdisciplinary collaboration, expand international engagement, and advance a culture where innovation and inclusion grow together.
            </p>
            <ul class="mt-5 space-y-2 text-sm text-slate-700">
                <li>Global academic recognition</li>
                <li>Cross-disciplinary innovation</li>
                <li>Graduates who create positive change</li>
            </ul>
        </div>
    </section>

    <section class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-md ring-1 ring-slate-900/5">
        <div class="h-14 bg-gradient-to-r from-orange-500/90 via-amber-500/80 to-orange-500/90"></div>
        <div class="grid lg:grid-cols-2">
            <div class="bg-slate-100 px-6 py-10 md:px-10 md:py-12">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[color:var(--portal-navy)]">University Story</p>
                <h2 class="mt-3 text-3xl font-bold leading-tight text-slate-900 md:text-5xl">University Academic Portal for Modern Academic Management</h2>
                <div class="mt-6 space-y-4 text-base leading-relaxed text-slate-600">
                    <p>
                        Our institution has evolved through decades of academic commitment, guided by a clear mission: deliver accessible, high-quality education and prepare graduates for real-world impact.
                    </p>
                    <p>
                        Over time, we expanded programs, strengthened faculty expertise, and invested in modern campus and digital infrastructure to support learning, research, and student wellbeing.
                    </p>
                    <p>
                        Today, the University Academic Portal continues this legacy by bringing core academic services into one reliable, student-centered platform.
                    </p>
                </div>
            </div>
            <div class="relative min-h-[340px] md:min-h-[420px] lg:min-h-[520px]">
                <img
                    src="{{ asset('images/fox_images/about-2.jpg') }}"
                    alt="Students on campus"
                    class="absolute inset-0 h-full w-full object-cover"
                >
            </div>
        </div>
        <div class="h-14 bg-gradient-to-r from-orange-500/90 via-amber-500/80 to-orange-500/90"></div>
    </section>

    <section class="space-y-8 rounded-3xl border border-slate-200 bg-slate-100 px-6 py-12 shadow-sm ring-1 ring-slate-900/5 md:px-10 md:py-16">
        <div class="mx-auto max-w-3xl text-center">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[color:var(--portal-navy)]">Testimonials</p>
            <h2 class="mt-2 text-4xl font-bold text-slate-900 md:text-6xl">Student Says About Us</h2>
            <p class="mx-auto mt-4 max-w-2xl text-base leading-relaxed text-slate-600 md:text-xl">
                Hear from learners and families who experienced our teaching quality, student support, and academic growth environment.
            </p>
        </div>

        <div class="mx-auto max-w-6xl">
            <div class="portal-slider about-testimonial-slider" data-portal-slider data-autoplay="true" data-interval="6500">
                <div class="portal-slider-track h-[34rem] overflow-visible bg-transparent md:h-[26rem] lg:h-[22rem]">
                    <div class="portal-slide is-active" data-portal-slide>
                        <div class="absolute left-0 top-1/2 hidden w-56 -translate-y-1/2 text-slate-400 lg:block about-testimonial-side">
                            <p class="text-5xl font-bold leading-none text-slate-300">“</p>
                            <p class="about-testimonial-quote text-xl">Great learning support and clear communication from teachers and staff throughout every semester.</p>
                        </div>
                        <div class="absolute right-0 top-1/2 hidden w-56 -translate-y-1/2 text-slate-400 lg:block about-testimonial-side">
                            <p class="text-5xl font-bold leading-none text-slate-300">“</p>
                            <p class="about-testimonial-quote text-xl">A campus culture that really motivates students to stay focused and confident.</p>
                        </div>
                        <article class="mx-auto max-w-3xl rounded-3xl border border-slate-200 bg-white px-6 py-7 shadow-xl md:px-8">
                            <div class="flex flex-col items-start gap-4 md:flex-row md:items-center">
                                <img src="{{ asset('images/fox_images/person_1.jpg') }}" alt="Student testimonial avatar" class="h-24 w-24 rounded-full object-cover ring-4 ring-slate-100">
                                <div class="flex-1">
                                    <p class="text-5xl font-bold leading-none text-slate-200">“</p>
                                    <p class="about-testimonial-quote -mt-3 text-2xl text-slate-700">
                                        Far beyond basic administration, this university helped me grow academically and personally through strong mentoring and structured support.
                                    </p>
                                    <p class="mt-4 text-3xl font-semibold text-slate-900">Racky Henderson</p>
                                    <p class="mt-1 text-lg font-semibold text-orange-500">Final-Year Student</p>
                                </div>
                            </div>
                        </article>
                    </div>

                    <div class="portal-slide" data-portal-slide>
                        <div class="absolute left-0 top-1/2 hidden w-56 -translate-y-1/2 text-slate-400 lg:block about-testimonial-side">
                            <p class="text-5xl font-bold leading-none text-slate-300">“</p>
                            <p class="about-testimonial-quote text-xl">The portal made daily academic tasks faster and less stressful.</p>
                        </div>
                        <div class="absolute right-0 top-1/2 hidden w-56 -translate-y-1/2 text-slate-400 lg:block about-testimonial-side">
                            <p class="text-5xl font-bold leading-none text-slate-300">“</p>
                            <p class="about-testimonial-quote text-xl">Support teams responded quickly whenever I had schedule or fee questions.</p>
                        </div>
                        <article class="mx-auto max-w-3xl rounded-3xl border border-slate-200 bg-white px-6 py-7 shadow-xl md:px-8">
                            <div class="flex flex-col items-start gap-4 md:flex-row md:items-center">
                                <img src="{{ asset('images/fox_images/person_2.jpg') }}" alt="Parent testimonial avatar" class="h-24 w-24 rounded-full object-cover ring-4 ring-slate-100">
                                <div class="flex-1">
                                    <p class="text-5xl font-bold leading-none text-slate-200">“</p>
                                    <p class="about-testimonial-quote -mt-3 text-2xl text-slate-700">
                                        As a parent, I appreciate the transparency of progress, attendance, and communication. It gives me confidence in my child's learning journey.
                                    </p>
                                    <p class="mt-4 text-3xl font-semibold text-slate-900">Henry Dee</p>
                                    <p class="mt-1 text-lg font-semibold text-orange-500">Parent</p>
                                </div>
                            </div>
                        </article>
                    </div>

                    <div class="portal-slide" data-portal-slide>
                        <div class="absolute left-0 top-1/2 hidden w-56 -translate-y-1/2 text-slate-400 lg:block about-testimonial-side">
                            <p class="text-5xl font-bold leading-none text-slate-300">“</p>
                            <p class="about-testimonial-quote text-xl">A practical and student-first system that supports teaching quality.</p>
                        </div>
                        <div class="absolute right-0 top-1/2 hidden w-56 -translate-y-1/2 text-slate-400 lg:block about-testimonial-side">
                            <p class="text-5xl font-bold leading-none text-slate-300">“</p>
                            <p class="about-testimonial-quote text-xl">Class coordination became much easier with timetable and messaging tools.</p>
                        </div>
                        <article class="mx-auto max-w-3xl rounded-3xl border border-slate-200 bg-white px-6 py-7 shadow-xl md:px-8">
                            <div class="flex flex-col items-start gap-4 md:flex-row md:items-center">
                                <img src="{{ asset('images/fox_images/person_4.jpg') }}" alt="Teacher testimonial avatar" class="h-24 w-24 rounded-full object-cover ring-4 ring-slate-100">
                                <div class="flex-1">
                                    <p class="text-5xl font-bold leading-none text-slate-200">“</p>
                                    <p class="about-testimonial-quote -mt-3 text-2xl text-slate-700">
                                        The integrated workflow for attendance, grades, and announcements lets teachers focus more on learning outcomes than repetitive admin work.
                                    </p>
                                    <p class="mt-4 text-3xl font-semibold text-slate-900">Ariana Cole</p>
                                    <p class="mt-1 text-lg font-semibold text-orange-500">Faculty Member</p>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-center gap-3">
                    <button type="button" class="about-dot h-3 w-3 rounded-full bg-slate-300 opacity-80 transition-all" data-portal-slider-dot aria-label="Go to testimonial 1"></button>
                    <button type="button" class="about-dot h-3 w-3 rounded-full bg-slate-300 opacity-80 transition-all" data-portal-slider-dot aria-label="Go to testimonial 2"></button>
                    <button type="button" class="about-dot h-3 w-3 rounded-full bg-slate-300 opacity-80 transition-all" data-portal-slider-dot aria-label="Go to testimonial 3"></button>
                </div>
            </div>
        </div>
    </section>

    <section class="space-y-6">
        <div class="max-w-3xl">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[color:var(--portal-navy)]">Why Students Choose Us</p>
            <h2 class="mt-2 text-3xl font-bold text-[color:var(--portal-navy)]">A complete learning environment, not just a classroom</h2>
            <p class="mt-3 text-sm leading-relaxed text-slate-600 md:text-base">
                Our student experience combines strong academics, career mentorship, and modern campus services. From first-year orientation to graduation support, we focus on practical outcomes, wellbeing, and long-term employability.
            </p>
        </div>
        <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 text-blue-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-slate-900">Academic Depth</h3>
                <p class="mt-2 text-sm text-slate-600">Programs designed with clear progression, specialist pathways, and assessment practices aligned with quality assurance standards.</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5V4H2v16h5m10 0v-5a3 3 0 00-6 0v5m6 0H7" />
                    </svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-slate-900">Career Readiness</h3>
                <p class="mt-2 text-sm text-slate-600">Industry-focused projects, practical assignments, and employability support that connect classroom learning to real work contexts.</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100 text-amber-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636l-1.414 1.414M6.343 17.657l-1.414 1.414M12 8v4l2 2m6-2a8 8 0 11-16 0 8 8 0 0116 0z" />
                    </svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-slate-900">Student Support</h3>
                <p class="mt-2 text-sm text-slate-600">Dedicated academic advising, responsive support channels, and transparent communication throughout each academic term.</p>
            </div>
        </div>
    </section>

    <section class="space-y-6">
        <div class="max-w-2xl">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[color:var(--portal-navy)]">Core Values</p>
            <h2 class="mt-2 text-3xl font-bold text-[color:var(--portal-navy)]">Principles that guide every decision</h2>
        </div>
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100 text-amber-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" /></svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-slate-900">Excellence</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">We hold ourselves to high standards in teaching, research, and student support so every program and service meets the expectations of learners and stakeholders.</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 text-blue-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-slate-900">Integrity</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">Ethical leadership, accountability, and transparency guide how we make decisions, communicate with the community, and handle academic and administrative matters.</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-slate-900">Inclusion</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">We build a welcoming environment where every learner can thrive, regardless of background, and where diverse perspectives strengthen teaching and research.</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-violet-100 text-violet-700">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" /></svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-slate-900">Innovation</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">We encourage curiosity and practical solutions to real problems, in the classroom and in research, so our community stays at the forefront of knowledge and practice.</p>
            </div>
        </div>
    </section>

    <section class="space-y-6">
        <div class="max-w-2xl">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[color:var(--portal-navy)]">Milestones</p>
            <h2 class="mt-2 text-3xl font-bold text-[color:var(--portal-navy)]">Our Journey</h2>
        </div>

        @php
            $milestones = [
                ['year' => '1975', 'title' => 'Foundation', 'description' => 'Established with a mission to make quality higher education accessible.'],
                ['year' => '1990', 'title' => 'Academic Expansion', 'description' => 'New faculties and graduate programs introduced across disciplines.'],
                ['year' => '2005', 'title' => 'Digital Transformation', 'description' => 'Blended learning and digital services launched for students and staff.'],
                ['year' => '2020', 'title' => 'Global Partnerships', 'description' => 'Strengthened international collaboration, research, and exchange opportunities.'],
            ];
        @endphp

        <div class="grid gap-4 md:grid-cols-2">
            @foreach ($milestones as $milestone)
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm ring-1 ring-slate-900/5 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-100 text-amber-700 font-bold text-sm">{{ $milestone['year'] }}</div>
                    <h3 class="mt-4 text-xl font-semibold text-slate-900">{{ $milestone['title'] }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $milestone['description'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    @include('guest.partials.image-cards-about')

    <section class="rounded-3xl border border-amber-200 bg-gradient-to-r from-amber-100 via-amber-50 to-amber-100 p-8 text-center shadow-md ring-1 ring-amber-200/50 md:p-12">
        <h2 class="text-3xl font-bold text-[color:var(--portal-navy)]">Ready to start your journey?</h2>
        <p class="mx-auto mt-3 max-w-2xl text-sm text-slate-700 md:text-base">
            Join a university community focused on achievement, support, and real-world impact.
        </p>
        <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
            <a href="{{ route('guest.courses') }}" class="inline-flex items-center rounded-full bg-[color:var(--portal-navy)] px-6 py-3 text-sm font-semibold text-white hover:bg-slate-800">
                Browse Courses
            </a>
            <a href="{{ route('guest.contact') }}" class="inline-flex items-center rounded-full border border-[color:var(--portal-navy)] bg-white px-6 py-3 text-sm font-semibold text-[color:var(--portal-navy)] hover:bg-slate-50">
                Get In Touch
            </a>
        </div>
    </section>
</div>
@endsection
