import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import DataTable from '@/Components/DataTable';
import { Head } from '@inertiajs/react';

export default function Index({ packagingTypes }) {
    return (
        <AuthenticatedLayout title="Packaging Types">
            <Head title="Packaging Types" />

            <DataTable
                rows={packagingTypes}
                addRoute="packaging-types.create"
                addLabel="Add Packaging Type"
                editRoute="packaging-types.edit"
                destroyRoute="packaging-types.destroy"
                emptyMessage="No packaging types yet."
                columns={[{ key: 'name', label: 'Name' }]}
            />
        </AuthenticatedLayout>
    );
}
