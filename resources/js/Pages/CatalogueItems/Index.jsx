import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import DataTable from '@/Components/DataTable';
import { Head } from '@inertiajs/react';

export default function Index({ items }) {
    return (
        <AuthenticatedLayout title="Catalogue Items">
            <Head title="Catalogue Items" />

            <DataTable
                rows={items}
                addRoute="catalogue-items.create"
                addLabel="Add Catalogue Item"
                editRoute="catalogue-items.edit"
                destroyRoute="catalogue-items.destroy"
                emptyMessage="No catalogue items yet."
                columns={[
                    { key: 'catalogue_no', label: 'Catalogue No.' },
                    {
                        key: 'row_type',
                        label: 'Type',
                        render: (row) => (row.row_type === 'parent' ? 'Parent' : 'Sub'),
                    },
                    { key: 'item_type', label: 'Item Type' },
                    {
                        key: 'supplier',
                        label: 'Supplier',
                        render: (row) => row.supplier?.name ?? '—',
                    },
                    {
                        key: 'parent_item',
                        label: 'Parent Item',
                        render: (row) => row.parent_item?.catalogue_no ?? '—',
                    },
                    {
                        key: 'unit_cost',
                        label: 'Unit Cost',
                        render: (row) => (row.unit_cost != null ? `$${row.unit_cost}` : '—'),
                    },
                ]}
            />
        </AuthenticatedLayout>
    );
}
