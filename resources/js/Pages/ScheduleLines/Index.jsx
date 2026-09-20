import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import DataTable from '@/Components/DataTable';
import NeedsFinishesIcon from '@/Components/NeedsFinishesIcon';
import { Head, Link } from '@inertiajs/react';

export default function Index({ project, lines }) {
    return (
        <AuthenticatedLayout title={`Furniture Schedule — ${project.project_descriptor ?? project.quote_number ?? 'Project'}`}>
            <Head title="Furniture Schedule" />

            <Link href={route('projects.show', project.id)} className="text-sm text-green-700 hover:underline">
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
                            render: (row) => (
                                <span className="inline-flex items-center gap-1.5">
                                    {row.item?.catalogue_no ?? '—'}
                                    {row.needs_finishes && <NeedsFinishesIcon />}
                                </span>
                            ),
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
