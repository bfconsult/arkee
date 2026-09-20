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

            <div className="flex justify-end mb-4">
                <Link
                    href={route('projects.create')}
                    className="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700"
                >
                    Add Quote
                </Link>
            </div>

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
                                    {quotes.map((project) => (
                                        <div key={project.id} className="px-4 py-3 flex items-center justify-between">
                                            <div>
                                                <div className="font-medium text-gray-900">
                                                    {project.project_descriptor ?? project.quote_number ?? `Project #${project.id}`}
                                                </div>
                                                <div className="text-xs text-gray-500">
                                                    {project.quote_number ?? '—'} · {project.client?.company_name ?? '—'}
                                                </div>
                                            </div>
                                            <div className="flex items-center gap-4 text-sm">
                                                <Link
                                                    href={route('projects.schedule-lines.index', project.id)}
                                                    className="text-green-700 hover:underline"
                                                >
                                                    Furniture Items
                                                </Link>
                                                <Link
                                                    href={route('projects.edit', project.id)}
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
