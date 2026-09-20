import { Link, usePage } from '@inertiajs/react';
import ApplicationLogo from '@/Components/ApplicationLogo';
import Avatar from '@/Components/Avatar';
import SiteNotice from '@/Components/SiteNotice';

const TASK_NAV_ITEMS = [{ label: 'Quotes', route: 'quotes.index' }];

const DATA_NAV_ITEMS = [
    { label: 'Clients', route: 'clients.index' },
    { label: 'Suppliers', route: 'suppliers.index' },
    { label: 'Delivery Locations', route: 'delivery-locations.index' },
    { label: 'Packaging Types', route: 'packaging-types.index' },
    { label: 'Item Categories', route: 'item-categories.index' },
    { label: 'Materials', route: 'materials.index' },
    { label: 'Items', route: 'items.index' },
    { label: 'Projects', route: 'projects.index' },
];

function isActive(item) {
    return route().current(item.route) || route().current(item.route.replace('.index', '.*'));
}

function NavLink({ item }) {
    return (
        <Link
            href={route(item.route)}
            className={`block px-4 py-2 text-sm ${
                isActive(item)
                    ? 'bg-green-50 text-green-700 font-medium border-r-2 border-green-600'
                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
            }`}
        >
            {item.label}
        </Link>
    );
}

function NavSection({ label, items }) {
    return (
        <div className="mt-4 first:mt-0">
            <div className="px-4 mb-1 text-xs font-semibold text-gray-400 uppercase tracking-wide">{label}</div>
            {items.length === 0 ? (
                <div className="px-4 py-2 text-sm text-gray-400 italic">Coming soon</div>
            ) : (
                items.map((item) => <NavLink key={item.route} item={item} />)
            )}
        </div>
    );
}

export default function AuthenticatedLayout({ title, children }) {
    const { auth, flash } = usePage().props;

    const dataItems =
        auth.user.role === 'admin'
            ? [...DATA_NAV_ITEMS, { label: 'Users', route: 'users.index' }]
            : DATA_NAV_ITEMS;

    return (
        <div className="min-h-screen bg-gray-100 flex">
            {/* Sidebar - computer-first layout, not the mobile bottom-nav
                pattern this app started with. */}
            <aside className="w-56 flex-shrink-0 bg-white border-r border-gray-200 flex flex-col fixed inset-y-0 left-0">
                <div className="h-14 flex items-center gap-2 px-4 border-b border-gray-200">
                    <ApplicationLogo className="h-6 w-6" />
                    <span className="text-gray-900 font-semibold text-sm">Arkee</span>
                </div>

                <nav className="flex-1 overflow-y-auto py-3">
                    <NavLink item={{ label: 'Dashboard', route: 'dashboard' }} />
                    <NavSection label="Tasks" items={TASK_NAV_ITEMS} />
                    <NavSection label="Data" items={dataItems} />
                </nav>

                <Link
                    href={route('profile.edit')}
                    className="flex items-center gap-2 px-4 h-14 border-t border-gray-200 hover:bg-gray-50"
                >
                    <Avatar user={auth.user} size="sm" />
                    <span className="text-sm text-gray-700 truncate">{auth.user.name}</span>
                </Link>
            </aside>

            {/* Main content */}
            <div className="flex-1 ml-56 flex flex-col min-w-0">
                {title && (
                    <div className="h-14 flex items-center px-6 border-b border-gray-200 bg-white">
                        <h1 className="text-lg font-semibold text-gray-900">{title}</h1>
                    </div>
                )}

                <main className="flex-1 p-6">
                    <SiteNotice className="-mx-6 -mt-6 mb-6" />

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
        </div>
    );
}
