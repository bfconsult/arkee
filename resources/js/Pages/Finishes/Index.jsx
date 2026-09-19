import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import DataTable from '@/Components/DataTable';
import { Head } from '@inertiajs/react';

const TYPE_LABELS = {
    timber_stain: 'Timber Stain',
    fabric: 'Fabric',
    other: 'Other',
};

export default function Index({ finishes }) {
    return (
        <AuthenticatedLayout title="Finishes">
            <Head title="Finishes" />

            <DataTable
                rows={finishes}
                addRoute="finishes.create"
                addLabel="Add Finish"
                editRoute="finishes.edit"
                destroyRoute="finishes.destroy"
                emptyMessage="No finishes yet."
                columns={[
                    { key: 'name', label: 'Name' },
                    { key: 'type', label: 'Type', render: (row) => TYPE_LABELS[row.type] ?? row.type },
                ]}
            />
        </AuthenticatedLayout>
    );
}
