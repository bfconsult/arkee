import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import DataTable from '@/Components/DataTable';
import { Head } from '@inertiajs/react';

export default function Index({ clients }) {
    return (
        <AuthenticatedLayout title="Clients">
            <Head title="Clients" />

            <DataTable
                rows={clients}
                addRoute="clients.create"
                addLabel="Add Client"
                editRoute="clients.edit"
                destroyRoute="clients.destroy"
                emptyMessage="No clients yet."
                columns={[
                    { key: 'company_name', label: 'Company' },
                    { key: 'contact_name', label: 'Contact' },
                    { key: 'contact_email', label: 'Email' },
                    { key: 'contact_phone', label: 'Phone' },
                ]}
            />
        </AuthenticatedLayout>
    );
}
