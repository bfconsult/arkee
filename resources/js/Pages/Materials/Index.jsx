import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import DataTable from '@/Components/DataTable';
import { Head } from '@inertiajs/react';

export default function Index({ materials }) {
    return (
        <AuthenticatedLayout title="Materials">
            <Head title="Materials" />

            <DataTable
                rows={materials}
                addRoute="materials.create"
                addLabel="Add Material"
                editRoute="materials.edit"
                destroyRoute="materials.destroy"
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
        </AuthenticatedLayout>
    );
}
