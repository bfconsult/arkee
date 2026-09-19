import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import DataTable from '@/Components/DataTable';
import { Head, Link } from '@inertiajs/react';

export default function Index({ item, component, materials }) {
    return (
        <AuthenticatedLayout title={`Materials — ${component.name}`}>
            <Head title="Materials" />

            <Link
                href={route('items.components.edit', [item.id, component.id])}
                className="text-sm text-green-700 hover:underline"
            >
                ← Back to Component
            </Link>

            <div className="mt-4">
                <DataTable
                    rows={materials}
                    addRoute={{ name: 'items.components.materials.create', params: [item.id, component.id] }}
                    addLabel="Add Material"
                    editRoute={{ name: 'items.components.materials.edit', params: [item.id, component.id] }}
                    destroyRoute={{ name: 'items.components.materials.destroy', params: [item.id, component.id] }}
                    emptyMessage="No materials yet."
                    columns={[
                        { key: 'name', label: 'Name' },
                        {
                            key: 'supplier',
                            label: 'Supplier',
                            render: (row) => row.supplier?.name ?? '—',
                        },
                        {
                            key: 'unit_cost',
                            label: 'Unit Cost',
                            render: (row) => (row.unit_cost != null ? `$${row.unit_cost}` : '—'),
                        },
                        { key: 'meterage', label: 'Meterage' },
                    ]}
                />
            </div>
        </AuthenticatedLayout>
    );
}
