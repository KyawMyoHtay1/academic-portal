<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import Breadcrumb from "@/Components/Breadcrumb.vue";
import { Head, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const page = usePage();
const isAuthenticated = computed(() => !!page.props.auth?.user);

const lastUpdated = new Date().toLocaleDateString('en-GB', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
});
</script>

<template>
    <Head title="Privacy Policy" />

    <AuthenticatedLayout v-if="isAuthenticated">
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-slate-900">
                Privacy Policy
            </h2>
        </template>

        <template #breadcrumb>
            <div class="mb-4">
                <Breadcrumb :items="[{ label: 'Privacy Policy' }]" />
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="portal-card p-8">
                    <div class="prose prose-slate max-w-none">
                        <h1 class="text-3xl font-bold text-slate-900 mb-2">Privacy Policy</h1>
                        <p class="text-sm text-slate-600 mb-8">
                            Last updated: {{ lastUpdated }}
                        </p>

                        <section class="mb-8">
                            <h2 class="text-2xl font-semibold text-slate-900 mb-4">1. Introduction</h2>
                            <p class="text-slate-700 leading-relaxed mb-4">
                                Welcome to the University Academic Portal ("we," "our," or "us"). We are committed to protecting your privacy and ensuring the security of your personal information. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our academic portal.
                            </p>
                            <p class="text-slate-700 leading-relaxed">
                                By accessing or using our portal, you agree to the collection and use of information in accordance with this policy. If you do not agree with our policies and practices, please do not use our services.
                            </p>
                        </section>

                        <section class="mb-8">
                            <h2 class="text-2xl font-semibold text-slate-900 mb-4">2. Information We Collect</h2>
                            
                            <h3 class="text-xl font-semibold text-slate-800 mb-3">2.1 Personal Information</h3>
                            <p class="text-slate-700 leading-relaxed mb-4">
                                We collect information that you provide directly to us, including:
                            </p>
                            <ul class="list-disc list-inside text-slate-700 space-y-2 mb-4 ml-4">
                                <li>Name, email address, and contact information</li>
                                <li>Student identification numbers and academic records</li>
                                <li>Course enrollment and attendance data</li>
                                <li>Grades, assignments, and academic performance</li>
                                <li>Fee payment information and transaction records</li>
                                <li>Profile photos and identification documents</li>
                                <li>Emergency contact information</li>
                            </ul>

                            <h3 class="text-xl font-semibold text-slate-800 mb-3">2.2 Automatically Collected Information</h3>
                            <p class="text-slate-700 leading-relaxed mb-4">
                                When you use our portal, we automatically collect certain information, including:
                            </p>
                            <ul class="list-disc list-inside text-slate-700 space-y-2 mb-4 ml-4">
                                <li>IP address and device information</li>
                                <li>Browser type and version</li>
                                <li>Pages visited and time spent on pages</li>
                                <li>Login timestamps and session data</li>
                                <li>System logs and error reports</li>
                            </ul>
                        </section>

                        <section class="mb-8">
                            <h2 class="text-2xl font-semibold text-slate-900 mb-4">3. How We Use Your Information</h2>
                            <p class="text-slate-700 leading-relaxed mb-4">
                                We use the collected information for the following purposes:
                            </p>
                            <ul class="list-disc list-inside text-slate-700 space-y-2 mb-4 ml-4">
                                <li><strong>Academic Management:</strong> To manage course enrollments, track attendance, record grades, and maintain academic records</li>
                                <li><strong>Communication:</strong> To send notifications, announcements, and important updates about your academic progress</li>
                                <li><strong>Fee Processing:</strong> To process fee payments and maintain financial records</li>
                                <li><strong>System Security:</strong> To protect against fraud, unauthorized access, and ensure system integrity</li>
                                <li><strong>Service Improvement:</strong> To analyze usage patterns and improve our portal's functionality</li>
                                <li><strong>Compliance:</strong> To comply with legal obligations and institutional policies</li>
                            </ul>
                        </section>

                        <section class="mb-8">
                            <h2 class="text-2xl font-semibold text-slate-900 mb-4">4. Data Sharing and Disclosure</h2>
                            <p class="text-slate-700 leading-relaxed mb-4">
                                We do not sell, trade, or rent your personal information to third parties. We may share your information only in the following circumstances:
                            </p>
                            <ul class="list-disc list-inside text-slate-700 space-y-2 mb-4 ml-4">
                                <li><strong>Authorized Personnel:</strong> With university staff, faculty, and administrators who need access to perform their duties</li>
                                <li><strong>Service Providers:</strong> With trusted third-party service providers who assist in operating our portal (e.g., payment processors, hosting services)</li>
                                <li><strong>Legal Requirements:</strong> When required by law, court order, or government regulation</li>
                                <li><strong>Institutional Needs:</strong> For academic research, accreditation, or institutional reporting (with appropriate anonymization)</li>
                                <li><strong>Emergency Situations:</strong> To protect the safety and security of students, staff, and the institution</li>
                            </ul>
                        </section>

                        <section class="mb-8">
                            <h2 class="text-2xl font-semibold text-slate-900 mb-4">5. Data Security</h2>
                            <p class="text-slate-700 leading-relaxed mb-4">
                                We implement appropriate technical and organizational security measures to protect your personal information, including:
                            </p>
                            <ul class="list-disc list-inside text-slate-700 space-y-2 mb-4 ml-4">
                                <li>Encryption of sensitive data in transit and at rest</li>
                                <li>Secure authentication and access controls</li>
                                <li>Regular security audits and vulnerability assessments</li>
                                <li>Employee training on data protection and privacy</li>
                                <li>Incident response procedures for data breaches</li>
                            </ul>
                            <p class="text-slate-700 leading-relaxed">
                                However, no method of transmission over the Internet or electronic storage is 100% secure. While we strive to use commercially acceptable means to protect your information, we cannot guarantee absolute security.
                            </p>
                        </section>

                        <section class="mb-8">
                            <h2 class="text-2xl font-semibold text-slate-900 mb-4">6. Your Rights and Choices</h2>
                            <p class="text-slate-700 leading-relaxed mb-4">
                                You have certain rights regarding your personal information:
                            </p>
                            <ul class="list-disc list-inside text-slate-700 space-y-2 mb-4 ml-4">
                                <li><strong>Access:</strong> Request access to your personal information held by us</li>
                                <li><strong>Correction:</strong> Request correction of inaccurate or incomplete information</li>
                                <li><strong>Deletion:</strong> Request deletion of your information (subject to legal and institutional retention requirements)</li>
                                <li><strong>Objection:</strong> Object to certain processing of your information</li>
                                <li><strong>Data Portability:</strong> Request a copy of your data in a portable format</li>
                                <li><strong>Withdrawal of Consent:</strong> Withdraw consent for processing where applicable</li>
                            </ul>
                            <p class="text-slate-700 leading-relaxed">
                                To exercise these rights, please contact us using the information provided in the "Contact Us" section below.
                            </p>
                        </section>

                        <section class="mb-8">
                            <h2 class="text-2xl font-semibold text-slate-900 mb-4">7. Cookies and Tracking Technologies</h2>
                            <p class="text-slate-700 leading-relaxed mb-4">
                                We use cookies and similar tracking technologies to enhance your experience on our portal. Cookies are small data files stored on your device that help us:
                            </p>
                            <ul class="list-disc list-inside text-slate-700 space-y-2 mb-4 ml-4">
                                <li>Maintain your login session</li>
                                <li>Remember your preferences and settings</li>
                                <li>Analyze portal usage and performance</li>
                                <li>Provide security features (e.g., reCAPTCHA)</li>
                            </ul>
                            <p class="text-slate-700 leading-relaxed">
                                You can control cookies through your browser settings. However, disabling cookies may affect the functionality of our portal.
                            </p>
                        </section>

                        <section class="mb-8">
                            <h2 class="text-2xl font-semibold text-slate-900 mb-4">8. Third-Party Services</h2>
                            <p class="text-slate-700 leading-relaxed mb-4">
                                Our portal may integrate with third-party services, including:
                            </p>
                            <ul class="list-disc list-inside text-slate-700 space-y-2 mb-4 ml-4">
                                <li><strong>Payment Processors:</strong> Stripe for fee payments (see Stripe's privacy policy)</li>
                                <li><strong>Email Services:</strong> For sending notifications and communications</li>
                                <li><strong>Translation Services:</strong> Google Translate for language translation</li>
                                <li><strong>Security Services:</strong> Google reCAPTCHA for bot protection</li>
                            </ul>
                            <p class="text-slate-700 leading-relaxed">
                                These services have their own privacy policies. We encourage you to review them to understand how your information is handled by these third parties.
                            </p>
                        </section>

                        <section class="mb-8">
                            <h2 class="text-2xl font-semibold text-slate-900 mb-4">9. Data Retention</h2>
                            <p class="text-slate-700 leading-relaxed mb-4">
                                We retain your personal information for as long as necessary to fulfill the purposes outlined in this policy, unless a longer retention period is required or permitted by law. Academic records are typically retained in accordance with institutional policies and legal requirements, which may extend beyond your enrollment period.
                            </p>
                        </section>

                        <section class="mb-8">
                            <h2 class="text-2xl font-semibold text-slate-900 mb-4">10. Children's Privacy</h2>
                            <p class="text-slate-700 leading-relaxed">
                                Our portal is intended for use by university students, faculty, and staff. We do not knowingly collect personal information from individuals under the age of 18 without appropriate parental or guardian consent, in accordance with applicable laws.
                            </p>
                        </section>

                        <section class="mb-8">
                            <h2 class="text-2xl font-semibold text-slate-900 mb-4">11. International Data Transfers</h2>
                            <p class="text-slate-700 leading-relaxed">
                                Your information may be transferred to and processed in countries other than your country of residence. We ensure that appropriate safeguards are in place to protect your information in accordance with this Privacy Policy and applicable data protection laws.
                            </p>
                        </section>

                        <section class="mb-8">
                            <h2 class="text-2xl font-semibold text-slate-900 mb-4">12. Changes to This Privacy Policy</h2>
                            <p class="text-slate-700 leading-relaxed mb-4">
                                We may update this Privacy Policy from time to time to reflect changes in our practices or for legal, operational, or regulatory reasons. We will notify you of any material changes by:
                            </p>
                            <ul class="list-disc list-inside text-slate-700 space-y-2 mb-4 ml-4">
                                <li>Posting the updated policy on this page</li>
                                <li>Updating the "Last updated" date</li>
                                <li>Sending notifications through the portal for significant changes</li>
                            </ul>
                            <p class="text-slate-700 leading-relaxed">
                                Your continued use of the portal after such changes constitutes acceptance of the updated policy.
                            </p>
                        </section>

                        <section class="mb-8">
                            <h2 class="text-2xl font-semibold text-slate-900 mb-4">13. Contact Us</h2>
                            <p class="text-slate-700 leading-relaxed mb-4">
                                If you have questions, concerns, or requests regarding this Privacy Policy or our data practices, please contact us:
                            </p>
                            <div class="bg-slate-50 rounded-lg p-4 mb-4">
                                <p class="text-slate-700 font-semibold mb-2">University Academic Portal</p>
                                <p class="text-slate-700 mb-1">Email: privacy@university.edu</p>
                                <p class="text-slate-700 mb-1">Phone: +123 456 7890</p>
                                <p class="text-slate-700">Address: 123 University Avenue, City, Country</p>
                            </div>
                            <p class="text-slate-700 leading-relaxed">
                                For data protection inquiries, you may also contact our Data Protection Officer at: <strong>dpo@university.edu</strong>
                            </p>
                        </section>

                        <section class="mb-8">
                            <h2 class="text-2xl font-semibold text-slate-900 mb-4">14. Governing Law</h2>
                            <p class="text-slate-700 leading-relaxed">
                                This Privacy Policy is governed by and construed in accordance with the laws of the jurisdiction in which the university operates. Any disputes arising from this policy will be subject to the exclusive jurisdiction of the courts in that jurisdiction.
                            </p>
                        </section>

                        <div class="mt-8 pt-6 border-t border-slate-200">
                            <p class="text-sm text-slate-600">
                                By using the University Academic Portal, you acknowledge that you have read and understood this Privacy Policy.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

    <GuestLayout v-else>
        <div class="w-full max-w-4xl px-6 py-8 sm:px-8">
            <div class="portal-card p-8">
                <div class="prose prose-slate max-w-none">
                    <h1 class="text-3xl font-bold text-slate-900 mb-2">Privacy Policy</h1>
                    <p class="text-sm text-slate-600 mb-8">
                        Last updated: {{ lastUpdated }}
                    </p>

                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-slate-900 mb-4">1. Introduction</h2>
                        <p class="text-slate-700 leading-relaxed mb-4">
                            Welcome to the University Academic Portal ("we," "our," or "us"). We are committed to protecting your privacy and ensuring the security of your personal information. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our academic portal.
                        </p>
                        <p class="text-slate-700 leading-relaxed">
                            By accessing or using our portal, you agree to the collection and use of information in accordance with this policy. If you do not agree with our policies and practices, please do not use our services.
                        </p>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-slate-900 mb-4">2. Information We Collect</h2>
                        
                        <h3 class="text-xl font-semibold text-slate-800 mb-3">2.1 Personal Information</h3>
                        <p class="text-slate-700 leading-relaxed mb-4">
                            We collect information that you provide directly to us, including:
                        </p>
                        <ul class="list-disc list-inside text-slate-700 space-y-2 mb-4 ml-4">
                            <li>Name, email address, and contact information</li>
                            <li>Student identification numbers and academic records</li>
                            <li>Course enrollment and attendance data</li>
                            <li>Grades, assignments, and academic performance</li>
                            <li>Fee payment information and transaction records</li>
                            <li>Profile photos and identification documents</li>
                            <li>Emergency contact information</li>
                        </ul>

                        <h3 class="text-xl font-semibold text-slate-800 mb-3">2.2 Automatically Collected Information</h3>
                        <p class="text-slate-700 leading-relaxed mb-4">
                            When you use our portal, we automatically collect certain information, including:
                        </p>
                        <ul class="list-disc list-inside text-slate-700 space-y-2 mb-4 ml-4">
                            <li>IP address and device information</li>
                            <li>Browser type and version</li>
                            <li>Pages visited and time spent on pages</li>
                            <li>Login timestamps and session data</li>
                            <li>System logs and error reports</li>
                        </ul>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-slate-900 mb-4">3. How We Use Your Information</h2>
                        <p class="text-slate-700 leading-relaxed mb-4">
                            We use the collected information for the following purposes:
                        </p>
                        <ul class="list-disc list-inside text-slate-700 space-y-2 mb-4 ml-4">
                            <li><strong>Academic Management:</strong> To manage course enrollments, track attendance, record grades, and maintain academic records</li>
                            <li><strong>Communication:</strong> To send notifications, announcements, and important updates about your academic progress</li>
                            <li><strong>Fee Processing:</strong> To process fee payments and maintain financial records</li>
                            <li><strong>System Security:</strong> To protect against fraud, unauthorized access, and ensure system integrity</li>
                            <li><strong>Service Improvement:</strong> To analyze usage patterns and improve our portal's functionality</li>
                            <li><strong>Compliance:</strong> To comply with legal obligations and institutional policies</li>
                        </ul>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-slate-900 mb-4">4. Data Sharing and Disclosure</h2>
                        <p class="text-slate-700 leading-relaxed mb-4">
                            We do not sell, trade, or rent your personal information to third parties. We may share your information only in the following circumstances:
                        </p>
                        <ul class="list-disc list-inside text-slate-700 space-y-2 mb-4 ml-4">
                            <li><strong>Authorized Personnel:</strong> With university staff, faculty, and administrators who need access to perform their duties</li>
                            <li><strong>Service Providers:</strong> With trusted third-party service providers who assist in operating our portal (e.g., payment processors, hosting services)</li>
                            <li><strong>Legal Requirements:</strong> When required by law, court order, or government regulation</li>
                            <li><strong>Institutional Needs:</strong> For academic research, accreditation, or institutional reporting (with appropriate anonymization)</li>
                            <li><strong>Emergency Situations:</strong> To protect the safety and security of students, staff, and the institution</li>
                        </ul>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-slate-900 mb-4">5. Data Security</h2>
                        <p class="text-slate-700 leading-relaxed mb-4">
                            We implement appropriate technical and organizational security measures to protect your personal information, including:
                        </p>
                        <ul class="list-disc list-inside text-slate-700 space-y-2 mb-4 ml-4">
                            <li>Encryption of sensitive data in transit and at rest</li>
                            <li>Secure authentication and access controls</li>
                            <li>Regular security audits and vulnerability assessments</li>
                            <li>Employee training on data protection and privacy</li>
                            <li>Incident response procedures for data breaches</li>
                        </ul>
                        <p class="text-slate-700 leading-relaxed">
                            However, no method of transmission over the Internet or electronic storage is 100% secure. While we strive to use commercially acceptable means to protect your information, we cannot guarantee absolute security.
                        </p>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-slate-900 mb-4">6. Your Rights and Choices</h2>
                        <p class="text-slate-700 leading-relaxed mb-4">
                            You have certain rights regarding your personal information:
                        </p>
                        <ul class="list-disc list-inside text-slate-700 space-y-2 mb-4 ml-4">
                            <li><strong>Access:</strong> Request access to your personal information held by us</li>
                            <li><strong>Correction:</strong> Request correction of inaccurate or incomplete information</li>
                            <li><strong>Deletion:</strong> Request deletion of your information (subject to legal and institutional retention requirements)</li>
                            <li><strong>Objection:</strong> Object to certain processing of your information</li>
                            <li><strong>Data Portability:</strong> Request a copy of your data in a portable format</li>
                            <li><strong>Withdrawal of Consent:</strong> Withdraw consent for processing where applicable</li>
                        </ul>
                        <p class="text-slate-700 leading-relaxed">
                            To exercise these rights, please contact us using the information provided in the "Contact Us" section below.
                        </p>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-slate-900 mb-4">7. Cookies and Tracking Technologies</h2>
                        <p class="text-slate-700 leading-relaxed mb-4">
                            We use cookies and similar tracking technologies to enhance your experience on our portal. Cookies are small data files stored on your device that help us:
                        </p>
                        <ul class="list-disc list-inside text-slate-700 space-y-2 mb-4 ml-4">
                            <li>Maintain your login session</li>
                            <li>Remember your preferences and settings</li>
                            <li>Analyze portal usage and performance</li>
                            <li>Provide security features (e.g., reCAPTCHA)</li>
                        </ul>
                        <p class="text-slate-700 leading-relaxed">
                            You can control cookies through your browser settings. However, disabling cookies may affect the functionality of our portal.
                        </p>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-slate-900 mb-4">8. Third-Party Services</h2>
                        <p class="text-slate-700 leading-relaxed mb-4">
                            Our portal may integrate with third-party services, including:
                        </p>
                        <ul class="list-disc list-inside text-slate-700 space-y-2 mb-4 ml-4">
                            <li><strong>Payment Processors:</strong> Stripe for fee payments (see Stripe's privacy policy)</li>
                            <li><strong>Email Services:</strong> For sending notifications and communications</li>
                            <li><strong>Translation Services:</strong> Google Translate for language translation</li>
                            <li><strong>Security Services:</strong> Google reCAPTCHA for bot protection</li>
                        </ul>
                        <p class="text-slate-700 leading-relaxed">
                            These services have their own privacy policies. We encourage you to review them to understand how your information is handled by these third parties.
                        </p>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-slate-900 mb-4">9. Data Retention</h2>
                        <p class="text-slate-700 leading-relaxed mb-4">
                            We retain your personal information for as long as necessary to fulfill the purposes outlined in this policy, unless a longer retention period is required or permitted by law. Academic records are typically retained in accordance with institutional policies and legal requirements, which may extend beyond your enrollment period.
                        </p>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-slate-900 mb-4">10. Children's Privacy</h2>
                        <p class="text-slate-700 leading-relaxed">
                            Our portal is intended for use by university students, faculty, and staff. We do not knowingly collect personal information from individuals under the age of 18 without appropriate parental or guardian consent, in accordance with applicable laws.
                        </p>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-slate-900 mb-4">11. International Data Transfers</h2>
                        <p class="text-slate-700 leading-relaxed">
                            Your information may be transferred to and processed in countries other than your country of residence. We ensure that appropriate safeguards are in place to protect your information in accordance with this Privacy Policy and applicable data protection laws.
                        </p>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-slate-900 mb-4">12. Changes to This Privacy Policy</h2>
                        <p class="text-slate-700 leading-relaxed mb-4">
                            We may update this Privacy Policy from time to time to reflect changes in our practices or for legal, operational, or regulatory reasons. We will notify you of any material changes by:
                        </p>
                        <ul class="list-disc list-inside text-slate-700 space-y-2 mb-4 ml-4">
                            <li>Posting the updated policy on this page</li>
                            <li>Updating the "Last updated" date</li>
                            <li>Sending notifications through the portal for significant changes</li>
                        </ul>
                        <p class="text-slate-700 leading-relaxed">
                            Your continued use of the portal after such changes constitutes acceptance of the updated policy.
                        </p>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-slate-900 mb-4">13. Contact Us</h2>
                        <p class="text-slate-700 leading-relaxed mb-4">
                            If you have questions, concerns, or requests regarding this Privacy Policy or our data practices, please contact us:
                        </p>
                        <div class="bg-slate-50 rounded-lg p-4 mb-4">
                            <p class="text-slate-700 font-semibold mb-2">University Academic Portal</p>
                            <p class="text-slate-700 mb-1">Email: privacy@university.edu</p>
                            <p class="text-slate-700 mb-1">Phone: +123 456 7890</p>
                            <p class="text-slate-700">Address: 123 University Avenue, City, Country</p>
                        </div>
                        <p class="text-slate-700 leading-relaxed">
                            For data protection inquiries, you may also contact our Data Protection Officer at: <strong>dpo@university.edu</strong>
                        </p>
                    </section>

                    <section class="mb-8">
                        <h2 class="text-2xl font-semibold text-slate-900 mb-4">14. Governing Law</h2>
                        <p class="text-slate-700 leading-relaxed">
                            This Privacy Policy is governed by and construed in accordance with the laws of the jurisdiction in which the university operates. Any disputes arising from this policy will be subject to the exclusive jurisdiction of the courts in that jurisdiction.
                        </p>
                    </section>

                    <div class="mt-8 pt-6 border-t border-slate-200">
                        <p class="text-sm text-slate-600">
                            By using the University Academic Portal, you acknowledge that you have read and understood this Privacy Policy.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
