import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import formatDate from '@/formatDate';
import { Head, Link } from '@inertiajs/react';

function field(label, value) {
    return (
        <div>
            <div className="text-xs uppercase text-gray-500">{label}</div>
            <div className="text-gray-900">{value ?? '—'}</div>
        </div>
    );
}

function ItemView({ quote, line }) {
    const item = line.item;
    const title = item.name ?? item.catalogue_no ?? item.item_category?.name ?? `Item #${item.id}`;
    const dimensions =
        item.height_mm || item.width_mm || item.depth_mm
            ? `${item.height_mm ?? '—'} x ${item.width_mm ?? '—'} x ${item.depth_mm ?? '—'} mm`
            : '—';
    const finishesByComponent = Object.fromEntries(
        (line.component_finishes ?? []).map((cf) => [cf.component_id, cf])
    );
    const images = (item.attachments ?? []).filter((a) => a.kind === 'image');

    return (
        <div className="bg-white rounded-lg shadow p-6">
            <div className="flex justify-between items-start mb-4">
                <h3 className="text-base font-semibold text-gray-900">{title}</h3>
                <Link
                    href={route('quotes.schedule-lines.edit', [quote.id, line.id])}
                    className="text-sm text-green-700 hover:underline"
                >
                    Edit →
                </Link>
            </div>

            {images.length > 0 && (
                <div className="flex flex-wrap gap-3 mb-4">
                    {images.map((image) => (
                        <img
                            key={image.id}
                            src={image.file_url}
                            alt=""
                            className="w-28 h-28 object-cover rounded-md border border-gray-200"
                        />
                    ))}
                </div>
            )}

            <div className="grid grid-cols-2 md:grid-cols-5 gap-4">
                {field('Catalogue No.', item.catalogue_no)}
                {field('Supplier', item.supplier?.name)}
                {field('Packaging Type', item.packaging_type?.name)}
                {field('Dimensions (H x W x D)', dimensions)}
                {field('Quantity', line.quantity)}
            </div>

            <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4 border-t pt-4">
                {field('Required By', formatDate(line.required_by))}
                {field('On PO', line.include_on_po ? 'Yes' : 'No')}
                {field('Price Override', line.price_override != null ? `$${line.price_override}` : null)}
                {field('Markup Target %', line.markup_target_pct != null ? `${line.markup_target_pct}%` : null)}
            </div>

            {line.fabric_notes && (
                <div className="mt-4">
                    <div className="text-xs uppercase text-gray-500">Fabric Notes</div>
                    <div className="text-gray-900 whitespace-pre-wrap">{line.fabric_notes}</div>
                </div>
            )}

            {item.components?.length > 0 && (
                <div className="mt-4 border-t pt-4">
                    <div className="text-xs uppercase text-gray-500 mb-2">Components</div>
                    <div className="divide-y divide-gray-100">
                        {item.components.map((component) => {
                            const cf = finishesByComponent[component.id];
                            const materialName = component.is_fabric ? cf?.material?.name : component.material?.name;

                            return (
                                <div
                                    key={component.id}
                                    className="py-2 first:pt-0 last:pb-0 flex items-center justify-between text-sm"
                                >
                                    <div className="text-gray-900">{component.name}</div>
                                    <div className="text-gray-700 text-right">
                                        {materialName ?? '—'}
                                        {cf?.finish && ` — ${cf.finish.name}`}
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                </div>
            )}
        </div>
    );
}

export default function View({ quote, groups }) {
    const projectLabel = quote.project?.project_descriptor ?? 'Project';

    return (
        <AuthenticatedLayout
            title={`Furniture Schedule View — ${projectLabel} (${quote.quote_number ?? `Quote #${quote.id}`})`}
        >
            <Head title="Furniture Schedule View" />

            <Link
                href={route('quotes.schedule-lines.index', quote.id)}
                className="text-sm text-green-700 hover:underline"
            >
                ← Back to Furniture Schedule
            </Link>

            <div className="mt-4 space-y-8">
                {groups.length === 0 ? (
                    <p className="text-gray-500 text-sm">No schedule lines yet.</p>
                ) : (
                    groups.map((group) => (
                        <div key={group.category}>
                            <h2 className="text-lg font-semibold text-gray-900 mb-3">{group.category}</h2>

                            <div className="space-y-6">
                                {group.lines.map((line) => (
                                    <ItemView key={line.id} quote={quote} line={line} />
                                ))}
                            </div>
                        </div>
                    ))
                )}
            </div>
        </AuthenticatedLayout>
    );
}
