import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import DataTable from '@/Components/DataTable';
import { Head, Link } from '@inertiajs/react';

export default function Index({ material, finishes }) {
    return (
        <AuthenticatedLayout title={`Finishes — ${material.name}`}>
            <Head title="Finishes" />

            <Link href={route('materials.show', material.id)} className="text-sm text-green-700 hover:underline">
                ← Back to Material
            </Link>

            <div className="mt-4">
                <DataTable
                    rows={finishes}
                    addRoute={{ name: 'materials.finishes.create', params: material.id }}
                    addLabel="Add Finish"
                    editRoute={{ name: 'materials.finishes.edit', params: [material.id] }}
                    destroyRoute={{ name: 'materials.finishes.destroy', params: [material.id] }}
                    emptyMessage="No finishes yet."
                    columns={[
                        { key: 'name', label: 'Name' },
                        { key: 'code_supplier', label: 'Supplier Code' },
                    ]}
                />
            </div>
        </AuthenticatedLayout>
    );
}
