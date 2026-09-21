import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';

const STATUS_SECTIONS = [
    { key: 'quote', label: 'Quote' },
    { key: 'complete', label: 'Complete' },
    { key: 'approved', label: 'Approved' },
    { key: 'cancelled', label: 'Cancelled' },
];

export default function Index({ quotesByStatus }) {
    return (
        <AuthenticatedLayout title="Quotes">
            <Head title="Quotes" />

            <div className="space-y-6">
                {STATUS_SECTIONS.map((section) => {
                    const quotes = quotesByStatus[section.key] ?? [];

                    return (
                        <div key={section.key} className="bg-white rounded-lg shadow">
                            <div className="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                                <h2 className="text-sm font-semibold text-gray-900">{section.label}</h2>
                                <span className="text-xs text-gray-400">{quotes.length}</span>
                            </div>

                            {quotes.length === 0 ? (
                                <p className="text-sm text-gray-500 px-4 py-6">No {section.label.toLowerCase()} quotes.</p>
                            ) : (
                                <div className="divide-y divide-gray-100">
                                    {quotes.map((quote) => (
                                        <div key={quote.id} className="px-4 py-3 flex items-center justify-between">
                                            <div>
                                                <div className="font-medium text-gray-900">
                                                    {quote.project?.project_descriptor ?? `Project #${quote.project_id}`}
                                                </div>
                                                <div className="text-xs text-gray-500">
                                                    {quote.quote_number ?? '—'} · {quote.project?.client?.company_name ?? '—'}
                                                </div>
                                            </div>
                                            <div className="flex items-center gap-4 text-sm">
                                                <Link
                                                    href={route('quotes.schedule-lines.index', quote.id)}
                                                    className="text-green-700 hover:underline"
                                                >
                                                    Furniture Items
                                                </Link>
                                                <Link
                                                    href={route('projects.quotes.edit', [quote.project_id, quote.id])}
                                                    className="text-gray-600 hover:text-gray-900"
                                                >
                                                    Edit
                                                </Link>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </div>
                    );
                })}
            </div>
        </AuthenticatedLayout>
    );
}
