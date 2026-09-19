import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import DataTable from '@/Components/DataTable';
import { Head, Link } from '@inertiajs/react';

export default function Index({ project, lines }) {
    return (
        <AuthenticatedLayout title={`Furniture Schedule — ${project.project_descriptor ?? project.quote_number ?? 'Project'}`}>
            <Head title="Furniture Schedule" />

            <Link href={route('projects.edit', project.id)} className="text-sm text-green-700 hover:underline">
                ← Back to Project
            </Link>

            <div className="mt-4">
                <DataTable
                    rows={lines}
                    addRoute={{ name: 'projects.schedule-lines.create', params: project.id }}
                    addLabel="Add Schedule Line"
                    editRoute={{ name: 'projects.schedule-lines.edit', params: [project.id] }}
                    destroyRoute={{ name: 'projects.schedule-lines.destroy', params: [project.id] }}
                    emptyMessage="No schedule lines yet."
                    columns={[
                        {
                            key: 'item',
                            label: 'Item',
                            render: (row) => row.item?.catalogue_no ?? '—',
                        },
                        {
                            key: 'row_type',
                            label: 'Type',
                            render: (row) => (row.row_type === 'parent' ? 'Parent' : 'Sub'),
                        },
                        { key: 'quantity', label: 'Qty' },
                        {
                            key: 'required_by',
                            label: 'Required By',
                        },
                        {
                            key: 'include_on_po',
                            label: 'On PO',
                            render: (row) => (row.include_on_po ? 'Yes' : 'No'),
                        },
                    ]}
                />
            </div>
        </AuthenticatedLayout>
    );
}
