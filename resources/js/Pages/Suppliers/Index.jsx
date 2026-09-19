import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import DataTable from '@/Components/DataTable';
import { Head } from '@inertiajs/react';

export default function Index({ suppliers }) {
    return (
        <AuthenticatedLayout title="Suppliers">
            <Head title="Suppliers" />

            <DataTable
                rows={suppliers}
                addRoute="suppliers.create"
                addLabel="Add Supplier"
                editRoute="suppliers.edit"
                destroyRoute="suppliers.destroy"
                emptyMessage="No suppliers yet."
                columns={[
                    { key: 'name', label: 'Name' },
                    { key: 'contact_name', label: 'Contact' },
                    { key: 'contact_email', label: 'Email' },
                    { key: 'contact_phone', label: 'Phone' },
                ]}
            />
        </AuthenticatedLayout>
    );
}
