import { Link, router, usePage } from '@inertiajs/react';
import { useEffect, useRef, useState } from 'react';
import ApplicationLogo from '@/Components/ApplicationLogo';
import Avatar from '@/Components/Avatar';
import SiteNotice from '@/Components/SiteNotice';

export default function AuthenticatedLayout({ title, children }) {
    const { auth, properties, currentProperty, currentUserRole, flash } = usePage().props;
    // Always available with no current property to fall back to. Otherwise
    // admin or approver - an approver is often a relatively hands-off
    // owner rather than a day-to-day worker, and needing a second account
    // just to add another property they own had no real reason behind it.
    const canAddProperty = !currentProperty || currentUserRole === 'admin' || currentUserRole === 'approver';

    const [showPropertyMenu, setShowPropertyMenu] = useState(false);
    const [showChangeList, setShowChangeList] = useState(false);
    const propertyMenuRef = useRef(null);

    useEffect(() => {
        if (!showPropertyMenu) return undefined;

        const closeOnOutsideClick = (e) => {
            if (propertyMenuRef.current && !propertyMenuRef.current.contains(e.target)) {
                setShowPropertyMenu(false);
                setShowChangeList(false);
            }
        };
        document.addEventListener('mousedown', closeOnOutsideClick);
        return () => document.removeEventListener('mousedown', closeOnOutsideClick);
    }, [showPropertyMenu]);

    const togglePropertyMenu = () => {
        // Expanded by default when the menu opens - Change is the more
        // common reason to open this at all, so requiring an extra tap to
        // reveal it defeated the point of having it in the menu.
        setShowChangeList(true);
        setShowPropertyMenu((v) => !v);
    };

    const selectProperty = (propertyId) => {
        setShowPropertyMenu(false);
        setShowChangeList(false);

        // Edit/Settings are pinned to a specific property by URL, not to
        // whichever one is "current" (correct - you shouldn't get yanked
        // onto a different property's data mid-edit just because the nav
        // switched) - but leaving it at that meant the nav showed the newly
        // selected property while the page underneath kept showing the old
        // one. Jump to the same kind of page for the new property instead.
        // Edit requires admin, which this user may not have there, so fall
        // back to Settings (open to any role) rather than a 403.
        const onPropertyPage = route().current('properties.edit') || route().current('properties.show');
        if (onPropertyPage) {
            const isAdminOnTarget = properties.find((p) => p.id === propertyId)?.pivot?.type === 'admin';
            const targetRoute = route().current('properties.edit') && isAdminOnTarget
                ? 'properties.edit'
                : 'properties.show';

            router.post(route('property.select'), { property_id: propertyId }, {
                onSuccess: () => router.visit(route(targetRoute, propertyId)),
            });
            return;
        }

        router.post(route('property.select'), { property_id: propertyId });
    };

    const addProperty = () => {
        setShowPropertyMenu(false);
        setShowChangeList(false);
        router.post(route('properties.store'));
    };

    return (
        <div className="min-h-screen bg-gray-100">
            <nav className="bg-white border-b border-gray-200 fixed top-0 left-0 right-0 z-10">
                <div className="flex items-center justify-between px-4 h-14">
                    {/* Left: logo + page title */}
                    <div className="flex items-center gap-2">
                        <ApplicationLogo className="h-6 w-6" />
                        {title && (
                            <span className="text-gray-900 font-semibold text-base">{title}</span>
                        )}
                    </div>

                    {/* Right: property picker. With only one property there's
                        nothing to switch to, so the name is a plain link
                        straight to its settings page. Otherwise it opens a
                        small menu (Settings / Change) instead, since "go to
                        settings" and "switch property" are both plausible
                        things to want from tapping the name and a single
                        link/arrow split was easy to hit by mistake. */}
                    <div className="flex items-center gap-3">
                    {properties.length === 0 ? (
                        <button onClick={addProperty} className="text-sm text-green-600">
                            Add Property
                        </button>
                    ) : properties.length === 1 && currentProperty ? (
                        <Link
                            href={route('properties.show', currentProperty.id)}
                            className="text-sm font-medium text-green-700"
                        >
                            {currentProperty.name}
                        </Link>
                    ) : (
                        <div className="relative flex items-center" ref={propertyMenuRef}>
                            <button
                                type="button"
                                onClick={togglePropertyMenu}
                                aria-expanded={showPropertyMenu}
                                className={`text-sm font-medium ${currentProperty ? 'text-green-700' : 'text-gray-500'}`}
                            >
                                {currentProperty ? currentProperty.name : 'Select Property'}
                                <span className="ml-1 text-xs">▾</span>
                            </button>

                            {showPropertyMenu && (
                                <div className="absolute right-0 top-full mt-1 w-52 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-20">
                                    {currentProperty && (
                                        <Link
                                            href={route('properties.show', currentProperty.id)}
                                            className="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                                        >
                                            Settings
                                        </Link>
                                    )}
                                    <button
                                        type="button"
                                        onClick={() => setShowChangeList((v) => !v)}
                                        aria-expanded={showChangeList}
                                        className="flex w-full items-center justify-between px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                                    >
                                        Change
                                        <span className="text-xs">{showChangeList ? '▴' : '▾'}</span>
                                    </button>
                                    {showChangeList && (
                                        <div className="pb-1">
                                            {properties
                                                .filter((property) => property.id !== currentProperty?.id)
                                                .map((property) => (
                                                    <button
                                                        key={property.id}
                                                        type="button"
                                                        onClick={() => selectProperty(property.id)}
                                                        className="block w-full py-2 pl-8 pr-4 text-left text-sm text-gray-700 hover:bg-gray-50"
                                                    >
                                                        {property.name}
                                                    </button>
                                                ))}
                                            {canAddProperty && (
                                                <button
                                                    type="button"
                                                    onClick={addProperty}
                                                    className="block w-full py-2 pl-8 pr-4 text-left text-sm text-green-600"
                                                >
                                                    + Add Property
                                                </button>
                                            )}
                                        </div>
                                    )}
                                </div>
                            )}
                        </div>
                    )}

                    <Link href={route('profile.edit')} aria-label="Account">
                        <Avatar user={auth.user} size="sm" />
                    </Link>
                    </div>
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
