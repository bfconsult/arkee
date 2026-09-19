import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import DataTable from '@/Components/DataTable';
import { Head } from '@inertiajs/react';

export default function Index({ items }) {
    return (
        <AuthenticatedLayout title="Items">
            <Head title="Items" />

            <DataTable
                rows={items}
                addRoute="items.create"
                addLabel="Add Item"
                editRoute="items.edit"
                destroyRoute="items.destroy"
                emptyMessage="No items yet."
                columns={[
                    { key: 'catalogue_no', label: 'Catalogue No.' },
                    { key: 'item_type', label: 'Item Type' },
                    { key: 'packaging_type', label: 'Packaging' },
                    {
                        key: 'dimensions',
                        label: 'Dimensions (H x W x D mm)',
                        render: (row) =>
                            row.height_mm || row.width_mm || row.depth_mm
                                ? `${row.height_mm ?? '—'} x ${row.width_mm ?? '—'} x ${row.depth_mm ?? '—'}`
                                : '—',
                    },
                ]}
            />
        </AuthenticatedLayout>
    );
}
