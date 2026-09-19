import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import DataTable from '@/Components/DataTable';
import { Head, Link } from '@inertiajs/react';

export default function Index({ item, components }) {
    return (
        <AuthenticatedLayout title={`Components — ${item.catalogue_no ?? item.item_type ?? 'Item'}`}>
            <Head title="Components" />

            <Link href={route('items.edit', item.id)} className="text-sm text-green-700 hover:underline">
                ← Back to Item
            </Link>

            <div className="mt-4">
                <DataTable
                    rows={components}
                    addRoute={{ name: 'items.components.create', params: item.id }}
                    addLabel="Add Component"
                    editRoute={{ name: 'items.components.edit', params: [item.id] }}
                    destroyRoute={{ name: 'items.components.destroy', params: [item.id] }}
                    emptyMessage="No components yet."
                    columns={[
                        { key: 'name', label: 'Name' },
                        { key: 'quantity', label: 'Quantity' },
                    ]}
                />
            </div>
        </AuthenticatedLayout>
    );
}
