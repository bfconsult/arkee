import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import DataTable from '@/Components/DataTable';
import NeedsFinishesIcon from '@/Components/NeedsFinishesIcon';
import { Head, Link } from '@inertiajs/react';

export default function Index({ quote, lines }) {
    const projectLabel = quote.project?.project_descriptor ?? 'Project';

    return (
        <AuthenticatedLayout title={`Furniture Schedule — ${projectLabel} (${quote.quote_number ?? `Quote #${quote.id}`})`}>
            <Head title="Furniture Schedule" />

            <div className="flex items-center justify-between">
                <Link
                    href={route('projects.quotes.edit', [quote.project_id, quote.id])}
                    className="text-sm text-green-700 hover:underline"
                >
                    ← Back to Quote
                </Link>
                <Link
                    href={route('quotes.schedule-lines.view', quote.id)}
                    className="text-sm text-green-700 hover:underline"
                >
                    View Schedule →
                </Link>
            </div>

            <div className="mt-4">
                <DataTable
                    rows={lines}
                    addRoute={{ name: 'quotes.schedule-lines.create', params: quote.id }}
                    addLabel="Add Schedule Line"
                    editRoute={{ name: 'quotes.schedule-lines.edit', params: [quote.id] }}
                    destroyRoute={{ name: 'quotes.schedule-lines.destroy', params: [quote.id] }}
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
