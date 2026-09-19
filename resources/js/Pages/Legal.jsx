import { Head, Link } from '@inertiajs/react';
import ApplicationLogo from '@/Components/ApplicationLogo';

function Section({ title, children }) {
    return (
        <section className="mb-8">
            <h2 className="text-lg font-semibold text-gray-900 mb-2">{title}</h2>
            <div className="text-sm leading-relaxed text-gray-700 space-y-3">{children}</div>
        </section>
    );
}

// Named generically (not "PrivacyPolicy") - Brave's built-in cookie-consent-
// notice blocking (and similar filter lists) blocks resources whose path
// matches common privacy/consent page names, which silently prevented this
// chunk from ever loading. The rendered content/title can still say
// "Privacy Policy" - only the file/component name needed to change.
export default function Legal() {
    return (
        <>
            <Head title="Privacy Policy" />
            <div className="min-h-screen bg-gray-100">
                <header className="border-b border-gray-200 bg-white">
                    <div className="mx-auto flex max-w-3xl items-center gap-2 px-6 py-4">
                        <Link href="/" className="flex items-center gap-2">
                            <ApplicationLogo className="h-8 w-8" />
                        </Link>
                    </div>
                </header>

                <main className="mx-auto max-w-3xl px-6 py-12 bg-white sm:rounded-lg sm:shadow sm:my-8">
                    <h1 className="text-3xl font-bold text-gray-900 mb-2">Privacy Policy</h1>
                    <p className="text-sm text-gray-500 mb-8">Placeholder - replace with the real policy before launch.</p>

                    <Section title="Overview">
                        <p>Describe what data this app collects and why here.</p>
                    </Section>

                    <Section title="Data We Collect">
                        <p>List the categories of personal data collected (account details, usage data, etc.) here.</p>
                    </Section>

                    <Section title="How We Use Your Data">
                        <p>Explain the purposes data is used for here.</p>
                    </Section>

                    <Section title="Contact">
                        <p>
                            Questions about this policy? <Link href={route('contact')} className="text-green-700 hover:underline">Contact us</Link>.
                        </p>
                    </Section>
                </main>

                <footer className="border-t border-gray-200 py-8 text-center text-sm text-gray-500">
                    <Link href="/" className="hover:text-green-700 hover:underline">Home</Link>
                </footer>
            </div>
        </>
    );
}
