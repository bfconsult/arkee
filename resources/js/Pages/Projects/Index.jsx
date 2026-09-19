import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import DataTable from '@/Components/DataTable';
import { Head } from '@inertiajs/react';

export default function Index({ projects }) {
    return (
        <AuthenticatedLayout title="Projects">
            <Head title="Projects" />

            <DataTable
                rows={projects}
                addRoute="projects.create"
                addLabel="Add Project"
                editRoute="projects.edit"
                destroyRoute="projects.destroy"
                emptyMessage="No projects yet."
                columns={[
                    { key: 'quote_number', label: 'Quote No.' },
                    {
                        key: 'client',
                        label: 'Client',
                        render: (row) => row.client?.company_name ?? '—',
                    },
                    { key: 'project_descriptor', label: 'Descriptor' },
                    { key: 'status', label: 'Status' },
                    { key: 'date', label: 'Date' },
                    {
                        key: 'pm_user',
                        label: 'PM',
                        render: (row) => row.pm_user?.name ?? '—',
                    },
                ]}
            />
        </AuthenticatedLayout>
    );
}
