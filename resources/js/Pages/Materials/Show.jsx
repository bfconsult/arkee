import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';

function field(label, value) {
    return (
        <div>
            <div className="text-xs uppercase text-gray-500">{label}</div>
            <div className="text-gray-900">{value ?? '—'}</div>
        </div>
    );
}

export default function Show({ material }) {
    const title = material.name;

    return (
        <AuthenticatedLayout title={title}>
            <Head title={title} />

            <div className="flex justify-between items-start mb-4">
                <Link href={route('materials.index')} className="text-sm text-green-700 hover:underline">
                    ← Back to Materials
                </Link>
                <Link
                    href={route('materials.edit', material.id)}
                    className="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700"
                >
                    Edit Material
                </Link>
            </div>

            <div className="bg-white rounded-lg shadow p-6 mb-6">
                <h2 className="text-lg font-semibold text-gray-900 mb-4">{title}</h2>
                <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                    {field('Supplier', material.supplier?.name)}
                    {field('Supplier Code', material.code_supplier)}
                    {field('Unit Cost', material.unit_cost != null ? `$${material.unit_cost}` : null)}
                    {field('Meterage', material.meterage)}
                </div>
                {material.notes && (
                    <div className="mt-4">
                        <div className="text-xs uppercase text-gray-500">Notes</div>
                        <div className="text-gray-900 whitespace-pre-wrap">{material.notes}</div>
                    </div>
                )}
            </div>

            <div className="bg-white rounded-lg shadow p-6 mb-6">
                <div className="flex justify-between items-center mb-4">
                    <h2 className="text-lg font-semibold text-gray-900">Finishes</h2>
                    <Link
                        href={route('materials.finishes.create', material.id)}
                        className="px-3 py-1.5 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700"
                    >
                        Add Finish
                    </Link>
                </div>

                {material.finishes.length === 0 ? (
                    <p className="text-gray-500 text-sm">No finishes yet.</p>
                ) : (
                    <div className="divide-y divide-gray-100">
                        {material.finishes.map((finish) => (
                            <div key={finish.id} className="py-3 first:pt-0 last:pb-0 flex justify-between items-start">
                                <div>
                                    <div className="font-medium text-gray-900">{finish.name}</div>
                                    {finish.code_supplier && (
                                        <div className="text-sm text-gray-500">Supplier Code: {finish.code_supplier}</div>
                                    )}
                                    {finish.notes && (
                                        <div className="text-sm text-gray-500 whitespace-pre-wrap">{finish.notes}</div>
                                    )}
                                </div>
                                <Link
                                    href={route('materials.finishes.edit', [material.id, finish.id])}
                                    className="text-sm text-green-700 hover:text-green-900"
                                >
                                    Edit
                                </Link>
                            </div>
                        ))}
                    </div>
                )}
            </div>

            <div className="bg-white rounded-lg shadow p-6">
                <h2 className="text-lg font-semibold text-gray-900 mb-4">Used In</h2>

                {material.components.length === 0 ? (
                    <p className="text-gray-500 text-sm">Not used on any Item yet.</p>
                ) : (
                    <table className="w-full text-sm">
                        <thead>
                            <tr className="border-b border-gray-200 text-left text-xs uppercase text-gray-500">
                                <th className="px-2 py-2 font-medium">Item</th>
                                <th className="px-2 py-2 font-medium">Component</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-100">
                            {material.components.map((component) => (
                                <tr key={component.id}>
                                    <td className="px-2 py-2">
                                        <Link
                                            href={route('items.show', component.item_id)}
                                            className="text-green-700 hover:underline"
                                        >
                                            {component.item?.catalogue_no ?? `Item #${component.item_id}`}
                                        </Link>
                                    </td>
                                    <td className="px-2 py-2 text-gray-900">{component.name}</td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                )}
            </div>
        </AuthenticatedLayout>
    );
}
