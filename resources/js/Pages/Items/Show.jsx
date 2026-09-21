import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import ItemImages from '@/Components/ItemImages';
import { Head, Link } from '@inertiajs/react';

function field(label, value) {
    return (
        <div>
            <div className="text-xs uppercase text-gray-500">{label}</div>
            <div className="text-gray-900">{value ?? '—'}</div>
        </div>
    );
}

export default function Show({ item }) {
    const title = item.name ?? item.catalogue_no ?? item.item_category?.name ?? 'Item';
    const dimensions =
        item.height_mm || item.width_mm || item.depth_mm
            ? `${item.height_mm ?? '—'} x ${item.width_mm ?? '—'} x ${item.depth_mm ?? '—'} mm`
            : '—';

    return (
        <AuthenticatedLayout title={title}>
            <Head title={title} />

            <div className="flex justify-between items-start mb-4">
                <Link href={route('items.index')} className="text-sm text-green-700 hover:underline">
                    ← Back to Items
                </Link>
                <Link
                    href={route('items.edit', item.id)}
                    className="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700"
                >
                    Edit Item
                </Link>
            </div>

            <div className="bg-white rounded-lg shadow p-6 mb-6">
                <h2 className="text-lg font-semibold text-gray-900 mb-4">{title}</h2>
                <div className="grid grid-cols-2 md:grid-cols-5 gap-4">
                    {field('Catalogue No.', item.catalogue_no)}
                    {field('Item Category', item.item_category?.name)}
                    {field('Supplier', item.supplier?.name)}
                    {field('Packaging Type', item.packaging_type?.name)}
                    {field('Dimensions (H x W x D)', dimensions)}
                </div>
                {item.notes && (
                    <div className="mt-4">
                        <div className="text-xs uppercase text-gray-500">Notes</div>
                        <div className="text-gray-900 whitespace-pre-wrap">{item.notes}</div>
                    </div>
                )}
            </div>

            <ItemImages item={item} />

            <div className="bg-white rounded-lg shadow p-6 mb-6">
                <div className="flex justify-between items-center mb-4">
                    <h2 className="text-lg font-semibold text-gray-900">Components</h2>
                    <Link
                        href={route('items.components.create', item.id)}
                        className="px-3 py-1.5 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700"
                    >
                        Add Component
                    </Link>
                </div>

                {item.components.length === 0 ? (
                    <p className="text-gray-500 text-sm">No components yet.</p>
                ) : (
                    <div className="divide-y divide-gray-100">
                        {item.components.map((component) => {
                            const material = component.material;
                            const supplier = component.supplier ?? material?.supplier ?? item.supplier;
                            const unitCost = component.unit_cost ?? material?.unit_cost;
                            const codeSupplier = component.code_supplier ?? material?.code_supplier;
                            const sourcedOnComponent = component.supplier_id != null || component.unit_cost != null;
                            const supplierDiffersFromItem =
                                component.supplier_id != null &&
                                item.supplier_id != null &&
                                component.supplier_id !== item.supplier_id;

                            return (
                                <div key={component.id} className="py-4 first:pt-0 last:pb-0">
                                    <div className="flex justify-between items-start">
                                        <div>
                                            <div className="font-medium text-gray-900">{component.name}</div>
                                            {component.is_fabric ? (
                                                <span className="inline-block px-2 py-0.5 rounded-full text-xs bg-amber-100 text-amber-800">
                                                    Fabric — chosen per Project
                                                </span>
                                            ) : (
                                                material && (
                                                    <Link
                                                        href={route('materials.edit', material.id)}
                                                        className="text-sm text-green-700 hover:underline"
                                                    >
                                                        {material.name}
                                                    </Link>
                                                )
                                            )}
                                        </div>
                                        <Link
                                            href={route('items.components.edit', [item.id, component.id])}
                                            className="text-sm text-green-700 hover:text-green-900"
                                        >
                                            Edit
                                        </Link>
                                    </div>

                                    <div className="mt-2 grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                                        {field('Supplier', supplier?.name)}
                                        {field('Supplier Code', codeSupplier)}
                                        {field('Unit Cost', unitCost != null ? `$${unitCost}` : null)}
                                        {field('Meterage', component.meterage)}
                                    </div>
                                    {sourcedOnComponent && (
                                        <p className="mt-1 text-xs text-gray-400">Sourced on this Component, not the Material.</p>
                                    )}
                                    {supplierDiffersFromItem && (
                                        <p className="mt-1 text-xs text-amber-600">
                                            Different supplier to the Item's default ({item.supplier?.name}).
                                        </p>
                                    )}

                                    {material?.finishes?.length > 0 && (
                                        <div className="mt-3">
                                            <div className="text-xs uppercase text-gray-500 mb-1">Finishes</div>
                                            <div className="flex flex-wrap gap-2">
                                                {material.finishes.map((finish) => (
                                                    <span
                                                        key={finish.id}
                                                        className="inline-block px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-700"
                                                    >
                                                        {finish.name}
                                                    </span>
                                                ))}
                                            </div>
                                        </div>
                                    )}
                                </div>
                            );
                        })}
                    </div>
                )}
            </div>

            <div className="bg-white rounded-lg shadow p-6">
                <h2 className="text-lg font-semibold text-gray-900 mb-4">Used On</h2>

                {item.schedule_lines.length === 0 ? (
                    <p className="text-gray-500 text-sm">Not used on any project yet.</p>
                ) : (
                    <table className="w-full text-sm">
                        <thead>
                            <tr className="border-b border-gray-200 text-left text-xs uppercase text-gray-500">
                                <th className="px-2 py-2 font-medium">Project</th>
                                <th className="px-2 py-2 font-medium">Qty</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-100">
                            {item.schedule_lines.map((line) => (
                                <tr key={line.id}>
                                    <td className="px-2 py-2">
                                        <Link
                                            href={route('quotes.schedule-lines.index', line.quote_id)}
                                            className="text-green-700 hover:underline"
                                        >
                                            {line.quote?.project?.project_descriptor ?? `Project #${line.quote?.project_id}`}
                                        </Link>
                                    </td>
                                    <td className="px-2 py-2 text-gray-900">{line.quantity}</td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                )}
            </div>
        </AuthenticatedLayout>
    );
}
