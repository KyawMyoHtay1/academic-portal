@extends('layouts.guest')

@section('title', 'Academic Policies & Guidelines')

@push('styles')
<style>
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.8s ease-out;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6 space-y-16">
    {{-- Hero Section --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[color:var(--portal-navy)] via-slate-800 to-[color:var(--portal-navy)] px-6 md:px-12 py-16 md:py-24 text-white shadow-2xl">
        <div class="absolute inset-0 opacity-20">
            <div class="absolute top-0 right-0 w-96 h-96 bg-[color:var(--portal-gold)] rounded-full mix-blend-multiply filter blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-96 h-96 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl"></div>
        </div>
        
        <div class="relative z-10 max-w-4xl mx-auto text-center space-y-6">
            <p class="inline-flex items-center gap-2 rounded-full bg-white/10 backdrop-blur-sm px-4 py-2 text-xs font-semibold uppercase tracking-wide text-amber-200 ring-1 ring-white/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Policies & Guidelines
            </p>
            <h1 class="text-5xl md:text-6xl lg:text-7xl font-bold leading-tight">
                Academic Policies
                <span class="block text-transparent bg-clip-text bg-gradient-to-r from-[color:var(--portal-gold)] to-amber-300 mt-2">
                    & Guidelines
                </span>
            </h1>
        </div>
    </section>

    {{-- Main Content --}}
    <section class="max-w-4xl mx-auto space-y-8">
        <div class="group relative overflow-hidden rounded-3xl border-2 border-slate-200 bg-gradient-to-br from-white to-slate-50 p-8 md:p-12 shadow-xl hover:shadow-2xl transition-all duration-300">
            <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-[color:var(--portal-navy)]/5 to-transparent rounded-full blur-3xl"></div>
            <div class="relative z-10 space-y-6">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-gradient-to-br from-[color:var(--portal-navy)] to-slate-700 text-white shadow-lg">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                
                <div class="prose prose-lg max-w-none">
                    <h2 class="text-3xl md:text-4xl font-bold text-[color:var(--portal-navy)] mb-6">Academic Policies & Guidelines</h2>
                    <p class="text-lg text-slate-700 leading-relaxed mb-6">
                        This section provides access to official academic policies, regulations, and guidelines that govern university activities. These include assessment rules, grading systems, attendance requirements, examination regulations, and academic integrity policies.
                    </p>
                    <p class="text-lg text-slate-700 leading-relaxed">
                        The portal ensures that all users can easily access up-to-date academic information to support transparency and compliance with university standards.
                    </p>
                </div>
            </div>
        </div>

        {{-- Policy Categories --}}
        <div class="grid gap-6 md:grid-cols-2">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-2">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 mb-2">Assessment Rules</h3>
                        <p class="text-slate-600">Guidelines for assignments, projects, and examinations.</p>
                    </div>
                </div>
            </div>
            
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-2">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 mb-2">Grading Systems</h3>
                        <p class="text-slate-600">Standardized grading criteria and evaluation methods.</p>
                    </div>
                </div>
            </div>
            
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-2">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 mb-2">Attendance Requirements</h3>
                        <p class="text-slate-600">Policies regarding class attendance and participation.</p>
                    </div>
                </div>
            </div>
            
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-md hover:shadow-xl transition-all duration-300 hover:-translate-y-2">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 mb-2">Academic Integrity</h3>
                        <p class="text-slate-600">Standards for honesty and ethical academic conduct.</p>
                    </div>
                </div>
            </div>

            {{-- Privacy Policy (Termly) - full policy link --}}
            <a href="{{ route('privacy-policy') }}" class="block rounded-2xl border-2 border-slate-200 bg-white p-6 shadow-md hover:shadow-xl hover:border-[color:var(--portal-navy)]/30 transition-all duration-300 hover:-translate-y-2 group">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-[color:var(--portal-navy)] to-slate-700 flex items-center justify-center text-white group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-slate-900 mb-2 group-hover:text-[color:var(--portal-navy)]">Privacy Policy</h3>
                        <p class="text-slate-600 mb-2">Our full Privacy Policy (how we collect, use, and protect your data).</p>
                        <span class="inline-flex items-center gap-1 text-sm font-semibold text-[color:var(--portal-navy)]">
                            View full Privacy Policy
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </span>
                    </div>
                </div>
            </a>
        </div>

        {{-- Privacy Policy (full text) --}}
        <div id="privacy-policy" class="scroll-mt-8 rounded-3xl border-2 border-slate-200 bg-white p-8 md:p-12 shadow-xl">
            <div class="flex items-center gap-3 mb-8">
                <div class="flex-shrink-0 w-14 h-14 rounded-2xl bg-gradient-to-br from-[color:var(--portal-navy)] to-slate-700 flex items-center justify-center text-white">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl md:text-3xl font-bold text-[color:var(--portal-navy)]">Privacy Policy</h2>
                    <p class="text-sm text-slate-500 mt-0.5">Last updated: 27 January 2026</p>
                </div>
            </div>

            <div class="prose prose-slate prose-lg max-w-none">
                <h3 class="text-xl font-bold text-slate-900 mt-8 mb-3">1. Introduction</h3>
                <p class="text-slate-700 leading-relaxed">
                    Welcome to the University Academic Portal (“we,” “our,” or “us”). We are committed to protecting your privacy and the security of your personal information. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our academic portal.
                </p>
                <p class="text-slate-700 leading-relaxed">
                    By accessing or using our portal, you agree to the collection and use of information in accordance with this policy. If you do not agree with these terms, please do not use our services.
                </p>

                <h3 class="text-xl font-bold text-slate-900 mt-8 mb-3">2. Information We Collect</h3>
                <h4 class="text-lg font-semibold text-slate-800 mt-4 mb-2">2.1 Personal information</h4>
                <p class="text-slate-700 leading-relaxed mb-2">We collect information you provide directly to us, including:</p>
                <ul class="list-disc pl-6 text-slate-700 space-y-1 mb-4">
                    <li>Name, email address, and contact details</li>
                    <li>Student identification numbers and academic records</li>
                    <li>Course enrollment and attendance data</li>
                    <li>Grades, assignments, and academic performance</li>
                    <li>Fee payment information and transaction records</li>
                    <li>Profile photos and identification documents</li>
                    <li>Emergency contact information</li>
                </ul>
                <h4 class="text-lg font-semibold text-slate-800 mt-4 mb-2">2.2 Automatically collected information</h4>
                <p class="text-slate-700 leading-relaxed mb-2">When you use our portal, we may automatically collect:</p>
                <ul class="list-disc pl-6 text-slate-700 space-y-1">
                    <li>IP address and device information</li>
                    <li>Browser type and version</li>
                    <li>Pages visited and time spent</li>
                    <li>Login timestamps and session data</li>
                    <li>System logs and error reports</li>
                </ul>

                <h3 class="text-xl font-bold text-slate-900 mt-8 mb-3">3. How We Use Your Information</h3>
                <p class="text-slate-700 leading-relaxed mb-2">We use the information we collect to:</p>
                <ul class="list-disc pl-6 text-slate-700 space-y-1">
                    <li><strong>Academic management:</strong> Manage course enrollments, track attendance, record grades, and maintain academic records</li>
                    <li><strong>Communication:</strong> Send notifications, announcements, and updates about your academic progress</li>
                    <li><strong>Fee processing:</strong> Process payments and maintain financial records</li>
                    <li><strong>Security:</strong> Protect against fraud, unauthorised access, and ensure system integrity</li>
                    <li><strong>Improvement:</strong> Analyse usage patterns and improve portal functionality</li>
                    <li><strong>Compliance:</strong> Fulfil legal obligations and institutional policies</li>
                </ul>

                <h3 class="text-xl font-bold text-slate-900 mt-8 mb-3">4. Data Sharing and Disclosure</h3>
                <p class="text-slate-700 leading-relaxed">
                    We do not sell, trade, or rent your personal information to third parties. We may share your information only when necessary, including with: authorised university staff, faculty, and administrators; trusted service providers (e.g. payment processors, hosting); and when required by law, court order, or regulation. We may also share information for institutional reporting or research, with appropriate anonymisation, or in emergency situations to protect safety and security.
                </p>

                <h3 class="text-xl font-bold text-slate-900 mt-8 mb-3">5. Data Security</h3>
                <p class="text-slate-700 leading-relaxed mb-2">We implement technical and organisational measures to protect your data, including encryption of sensitive data, secure authentication and access controls, regular security assessments, and staff training on data protection. No method of transmission or storage over the Internet is completely secure; we strive to use industry-standard safeguards but cannot guarantee absolute security.</p>

                <h3 class="text-xl font-bold text-slate-900 mt-8 mb-3">6. Your Rights and Choices</h3>
                <p class="text-slate-700 leading-relaxed mb-2">You have the right to:</p>
                <ul class="list-disc pl-6 text-slate-700 space-y-1">
                    <li>Request access to your personal information</li>
                    <li>Request correction of inaccurate or incomplete information</li>
                    <li>Request deletion (subject to legal and institutional retention requirements)</li>
                    <li>Object to certain processing of your information</li>
                    <li>Request a portable copy of your data</li>
                    <li>Withdraw consent where processing is based on consent</li>
                </ul>
                <p class="text-slate-700 leading-relaxed mt-4">To exercise these rights, please contact us using the details in the “Contact us” section below.</p>

                <h3 class="text-xl font-bold text-slate-900 mt-8 mb-3">7. Cookies and Tracking</h3>
                <p class="text-slate-700 leading-relaxed">
                    We use cookies and similar technologies to maintain your session, remember preferences, analyse usage, and provide security features (e.g. reCAPTCHA). You can control cookies via your browser settings; disabling them may affect portal functionality.
                </p>

                <h3 class="text-xl font-bold text-slate-900 mt-8 mb-3">8. Third-Party Services</h3>
                <p class="text-slate-700 leading-relaxed">
                    Our portal may integrate third-party services (e.g. payment processors, email services, translation tools, security services). These providers have their own privacy policies; we encourage you to review how they handle your information.
                </p>

                <h3 class="text-xl font-bold text-slate-900 mt-8 mb-3">9. Data Retention</h3>
                <p class="text-slate-700 leading-relaxed">
                    We retain your information for as long as needed for the purposes in this policy, or as required or permitted by law. Academic records may be kept in line with institutional and legal retention periods, which can extend beyond your enrollment.
                </p>

                <h3 class="text-xl font-bold text-slate-900 mt-8 mb-3">10. Children’s Privacy</h3>
                <p class="text-slate-700 leading-relaxed">
                    The portal is intended for university students, faculty, and staff. We do not knowingly collect personal information from anyone under 18 without appropriate parental or guardian consent where required by law.
                </p>

                <h3 class="text-xl font-bold text-slate-900 mt-8 mb-3">11. International Transfers</h3>
                <p class="text-slate-700 leading-relaxed">
                    Your information may be transferred to and processed in other countries. We put in place appropriate safeguards to protect your information in line with this policy and applicable data protection laws.
                </p>

                <h3 class="text-xl font-bold text-slate-900 mt-8 mb-3">12. Changes to This Policy</h3>
                <p class="text-slate-700 leading-relaxed">
                    We may update this Privacy Policy from time to time. We will indicate changes by updating this page and the “Last updated” date and may notify you of significant changes via the portal. Your continued use of the portal after changes constitutes acceptance of the updated policy.
                </p>

                <h3 class="text-xl font-bold text-slate-900 mt-8 mb-3">13. Contact Us</h3>
                <p class="text-slate-700 leading-relaxed mb-2">
                    For questions, concerns, or requests about this Privacy Policy or our data practices, please contact:
                </p>
                <p class="text-slate-700 leading-relaxed">
                    <strong>University Academic Portal</strong><br>
                    Email: privacy@university.edu<br>
                    Phone: +123 456 7890<br>
                    Address: 123 University Avenue, City, Country
                </p>
                <p class="text-slate-700 leading-relaxed mt-4">
                    For data protection enquiries, you may also contact our Data Protection Officer at: dpo@university.edu
                </p>

                <h3 class="text-xl font-bold text-slate-900 mt-8 mb-3">14. Governing Law</h3>
                <p class="text-slate-700 leading-relaxed">
                    This Privacy Policy is governed by the laws of the jurisdiction in which the university operates. Any disputes arising from this policy are subject to the exclusive jurisdiction of the courts in that jurisdiction.
                </p>
            </div>

            <div class="mt-10 pt-8 border-t border-slate-200">
                <a href="{{ route('privacy-policy') }}" class="inline-flex items-center gap-2 rounded-xl bg-[color:var(--portal-navy)] px-5 py-3 text-sm font-semibold text-white shadow-md hover:opacity-90 transition-opacity">
                    View full Privacy Policy (detailed version)
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </a>
                <p class="text-sm text-slate-500 mt-2">A detailed, legally formatted version is also available on our dedicated Privacy Policy page.</p>
            </div>
        </div>
    </section>
</div>
@endsection
