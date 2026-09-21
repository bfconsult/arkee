import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import DataTable from '@/Components/DataTable';
import formatDate from '@/formatDate';
import { Head, Link } from '@inertiajs/react';

const STATUS_LABELS = {
    draft: 'Draft',
    sent: 'Sent',
    confirmed: 'Confirmed',
    received: 'Received',
};

export default function Index({ project, purchaseOrders }) {
    return (
        <AuthenticatedLayout title={`Purchase Orders — ${project.project_descriptor ?? 'Project'}`}>
            <Head title="Purchase Orders" />

            <Link href={route('projects.show', project.id)} className="text-sm text-green-700 hover:underline">
                ← Back to Project
            </Link>

            <div className="mt-4">
                <DataTable
                    rows={purchaseOrders}
                    addRoute={{ name: 'projects.purchase-orders.create', params: project.id }}
                    addLabel="Add Purchase Order"
                    editRoute={{ name: 'projects.purchase-orders.edit', params: [project.id] }}
                    destroyRoute={{ name: 'projects.purchase-orders.destroy', params: [project.id] }}
                    emptyMessage="No purchase orders yet."
                    columns={[
                        { key: 'po_number', label: 'PO Number' },
                        {
                            key: 'supplier',
                            label: 'Supplier',
                            render: (row) => row.supplier?.name ?? '—',
                        },
                        {
                            key: 'order_status',
                            label: 'Status',
                            render: (row) => STATUS_LABELS[row.order_status] ?? row.order_status,
                        },
                        {
                            key: 'date_issued',
                            label: 'Date Issued',
                            render: (row) => formatDate(row.date_issued) ?? '—',
                        },
                        {
                            key: 'assignee',
                            label: 'Assignee',
                            render: (row) => row.assignee?.name ?? '—',
                        },
                    ]}
                />
            </div>
        </AuthenticatedLayout>
    );
}
