import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import DataTable from '@/Components/DataTable';
import { Head, Link } from '@inertiajs/react';

export default function Index({ item, component, material, colours }) {
    return (
        <AuthenticatedLayout title={`Colours — ${material.name}`}>
            <Head title="Colours" />

            <Link
                href={route('items.components.materials.edit', [item.id, component.id, material.id])}
                className="text-sm text-green-700 hover:underline"
            >
                ← Back to Material
            </Link>

            <div className="mt-4">
                <DataTable
                    rows={colours}
                    addRoute={{ name: 'items.components.materials.colours.create', params: [item.id, component.id, material.id] }}
                    addLabel="Add Colour"
                    editRoute={{ name: 'items.components.materials.colours.edit', params: [item.id, component.id, material.id] }}
                    destroyRoute={{ name: 'items.components.materials.colours.destroy', params: [item.id, component.id, material.id] }}
                    emptyMessage="No colours yet."
                    columns={[
                        { key: 'name', label: 'Name' },
                        { key: 'code_supplier', label: 'Supplier Code' },
                    ]}
                />
            </div>
        </AuthenticatedLayout>
    );
}
