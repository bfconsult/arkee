import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import NeedsFinishesIcon from '@/Components/NeedsFinishesIcon';
import { Head, Link } from '@inertiajs/react';

const STATUS_LABELS = {
    quote: 'Quote',
    complete: 'Complete',
    approved: 'Approved',
    cancelled: 'Cancelled',
};

const PO_STATUS_LABELS = {
    draft: 'Draft',
    sent: 'Sent',
    confirmed: 'Confirmed',
    received: 'Received',
};

function field(label, value) {
    return (
        <div>
            <div className="text-xs uppercase text-gray-500">{label}</div>
            <div className="text-gray-900">{value ?? '—'}</div>
        </div>
    );
}

export default function Show({ project }) {
    const title = project.project_descriptor ?? project.quote_number ?? 'Project';

    return (
        <AuthenticatedLayout title={title}>
            <Head title={title} />

            <div className="flex justify-between items-start mb-4">
                <Link href={route('projects.index')} className="text-sm text-green-700 hover:underline">
                    ← Back to Projects
                </Link>
                <Link
                    href={route('projects.edit', project.id)}
                    className="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700"
                >
                    Edit Project
                </Link>
            </div>

            <div className="bg-white rounded-lg shadow p-6 mb-6">
                <h2 className="text-lg font-semibold text-gray-900 mb-4">{title}</h2>
                <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                    {field('Quote No.', project.quote_number)}
                    {field('Client', project.client?.company_name)}
                    {field('Project Manager', project.pm_user?.name)}
                    {field('Status', STATUS_LABELS[project.status] ?? project.status)}
                    {field('Version', project.version)}
                    {field('Date', project.date)}
                </div>
                {(project.site_name || project.site_address || project.site_contact_name) && (
                    <div className="mt-4 pt-4 border-t border-gray-100 grid grid-cols-2 md:grid-cols-3 gap-4">
                        {field('Site Name', project.site_name)}
                        {field('Site Address', project.site_address)}
                        {field('Site Contact', project.site_contact_name)}
                    </div>
                )}
            </div>

            <div className="bg-white rounded-lg shadow p-6 mb-6">
                <div className="flex justify-between items-center mb-4">
                    <h2 className="text-lg font-semibold text-gray-900">Furniture Schedule</h2>
                    <Link
                        href={route('projects.schedule-lines.create', project.id)}
                        className="px-3 py-1.5 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700"
                    >
                        Add Schedule Line
                    </Link>
                </div>

                {project.furniture_schedule_lines.length === 0 ? (
                    <p className="text-gray-500 text-sm">No schedule lines yet.</p>
                ) : (
                    <div className="divide-y divide-gray-100">
                        {project.furniture_schedule_lines.map((line) => (
                            <div key={line.id} className="py-3 first:pt-0 last:pb-0 flex justify-between items-start">
                                <div>
                                    <div className="font-medium text-gray-900 flex items-center gap-1.5">
                                        {line.item?.catalogue_no ?? `Item #${line.item_id}`}
                                        <span className="text-gray-500 font-normal"> × {line.quantity}</span>
                                        {line.needs_finishes && <NeedsFinishesIcon />}
                                    </div>
                                    {line.include_on_po && <div className="text-sm text-gray-500">On Purchase Order</div>}
                                </div>
                                <Link
                                    href={route('projects.schedule-lines.edit', [project.id, line.id])}
                                    className="text-sm text-green-700 hover:text-green-900"
                                >
                                    Edit
                                </Link>
                            </div>
                        ))}
                    </div>
                )}
            </div>

            <div className="bg-white rounded-lg shadow p-6">
                <div className="flex justify-between items-center mb-4">
                    <h2 className="text-lg font-semibold text-gray-900">Purchase Orders</h2>
                    <Link
                        href={route('projects.purchase-orders.create', project.id)}
                        className="px-3 py-1.5 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700"
                    >
                        Add Purchase Order
                    </Link>
                </div>

                {project.purchase_orders.length === 0 ? (
                    <p className="text-gray-500 text-sm">No purchase orders yet.</p>
                ) : (
                    <div className="divide-y divide-gray-100">
                        {project.purchase_orders.map((po) => (
                            <div key={po.id} className="py-3 first:pt-0 last:pb-0 flex justify-between items-start">
                                <div>
                                    <div className="font-medium text-gray-900">{po.po_number ?? `PO #${po.id}`}</div>
                                    <div className="text-sm text-gray-500">
                                        {po.supplier?.name ?? '—'} · {PO_STATUS_LABELS[po.order_status] ?? po.order_status}
                                    </div>
                                </div>
                                <Link
                                    href={route('projects.purchase-orders.edit', [project.id, po.id])}
                                    className="text-sm text-green-700 hover:text-green-900"
                                >
                                    Edit
                                </Link>
                            </div>
                        ))}
                    </div>
                )}
            </div>
        </AuthenticatedLayout>
    );
}
