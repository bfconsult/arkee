import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import DataTable from '@/Components/DataTable';
import { Head } from '@inertiajs/react';

export default function Index({ itemCategories }) {
    return (
        <AuthenticatedLayout title="Item Categories">
            <Head title="Item Categories" />

            <DataTable
                rows={itemCategories}
                addRoute="item-categories.create"
                addLabel="Add Item Category"
                editRoute="item-categories.edit"
                destroyRoute="item-categories.destroy"
                emptyMessage="No item categories yet."
                columns={[{ key: 'name', label: 'Name' }]}
            />
        </AuthenticatedLayout>
    );
}
