import { Link, usePage } from '@inertiajs/react';
import ApplicationLogo from '@/Components/ApplicationLogo';
import Avatar from '@/Components/Avatar';
import SiteNotice from '@/Components/SiteNotice';

export default function AuthenticatedLayout({ title, children }) {
    const { auth, flash } = usePage().props;

    return (
        <div className="min-h-screen bg-gray-100">
            <nav className="bg-white border-b border-gray-200 fixed top-0 left-0 right-0 z-10">
                <div className="flex items-center justify-between px-4 h-14">
                    <div className="flex items-center gap-2">
                        <ApplicationLogo className="h-6 w-6" />
                        {title && (
                            <span className="text-gray-900 font-semibold text-base">{title}</span>
                        )}
                    </div>

                    <Link href={route('profile.edit')} aria-label="Account">
                        <Avatar user={auth.user} size="sm" />
                    </Link>
                </div>
            </nav>

            {/* Main content */}
            <main className="pt-14 px-4 pb-4">
                <SiteNotice className="-mx-4 mb-4" />

                {flash?.error && (
                    <div className="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                        {flash.error}
                    </div>
                )}
                {flash?.success && (
                    <div className="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
                        {flash.success}
                    </div>
                )}
                {children}
            </main>
        </div>
    );
}
