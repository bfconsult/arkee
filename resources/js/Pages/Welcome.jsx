import { Head, Link } from '@inertiajs/react';
import ApplicationLogo from '@/Components/ApplicationLogo';
import SiteNotice from '@/Components/SiteNotice';

// Placeholder landing page - copy/layout is generic on purpose, swap in
// real marketing content once this app's actual pitch is settled.
export default function Welcome({ auth }) {
    return (
        <div className="min-h-screen bg-white">
            <Head title="Welcome" />
            <SiteNotice />

            <header className="max-w-5xl mx-auto px-6 py-6 flex items-center justify-between">
                <ApplicationLogo className="h-8 w-auto text-gray-900" />
                <nav className="flex items-center gap-4">
                    {auth?.user ? (
                        <Link href={route('dashboard')} className="text-sm font-medium text-gray-700 hover:text-gray-900">
                            Dashboard
                        </Link>
                    ) : (
                        <>
                            <Link href={route('login')} className="text-sm font-medium text-gray-700 hover:text-gray-900">
                                Log in
                            </Link>
                            <Link
                                href={route('register')}
                                className="text-sm font-medium px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-700"
                            >
                                Sign up
                            </Link>
                        </>
                    )}
                </nav>
            </header>

            <main className="max-w-5xl mx-auto px-6 py-24 text-center">
                <h1 className="text-4xl sm:text-5xl font-bold text-gray-900">
                    Placeholder headline goes here
                </h1>
                <p className="mt-4 text-lg text-gray-500 max-w-xl mx-auto">
                    A short description of what this product does goes here.
                </p>
                <div className="mt-8">
                    <Link
                        href={route('register')}
                        className="inline-block text-sm font-medium px-6 py-3 bg-gray-900 text-white rounded-lg hover:bg-gray-700"
                    >
                        Get started
                    </Link>
                </div>
            </main>
        </div>
    );
}
