import { Link, router } from '@inertiajs/react';

/**
 * Generic list table for a CRUD module - columns: [{ key, label, render? }].
 * `render`, when given, is called as render(row) instead of row[key].
 *
 * editRoute/destroyRoute/addRoute accept either a plain route name string
 * (simple top-level resource) or `{ name, params }` for a nested resource,
 * where `params` are the parent route params to prepend before the row id.
 */
function resolveRoute(def, id) {
    if (!def) return null;
    if (typeof def === 'string') return route(def, id);
    const base = Array.isArray(def.params) ? def.params : [def.params];
    return route(def.name, id !== undefined ? [...base, id] : base);
}

export default function DataTable({ columns, rows, editRoute, destroyRoute, addRoute, addLabel = 'Add New', emptyMessage = 'Nothing here yet.', renderRowExtra }) {
    const destroy = (row) => {
        if (confirm('Are you sure you want to delete this?')) {
            router.delete(resolveRoute(destroyRoute, row.id));
        }
    };

    return (
        <div className="bg-white rounded-lg shadow">
            {addRoute && (
                <div className="flex justify-end p-4 border-b border-gray-100">
                    <Link
                        href={resolveRoute(addRoute)}
                        className="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700"
                    >
                        {addLabel}
                    </Link>
                </div>
            )}

            {rows.length === 0 ? (
                <p className="text-center text-gray-500 py-12">{emptyMessage}</p>
            ) : (
                <div className="overflow-x-auto">
                    <table className="w-full text-sm">
                        <thead>
                            <tr className="border-b border-gray-200 text-left text-xs uppercase text-gray-500">
                                {columns.map((col) => (
                                    <th key={col.key} className="px-4 py-2 font-medium">{col.label}</th>
                                ))}
                                <th className="px-4 py-2" />
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-gray-100">
                            {rows.map((row) => (
                                <tr key={row.id} className="hover:bg-gray-50">
                                    {columns.map((col) => (
                                        <td key={col.key} className="px-4 py-2 text-gray-900">
                                            {col.render ? col.render(row) : (row[col.key] ?? '—')}
                                        </td>
                                    ))}
                                    <td className="px-4 py-2 text-right whitespace-nowrap">
                                        {renderRowExtra && renderRowExtra(row)}
                                        <Link
                                            href={resolveRoute(editRoute, row.id)}
                                            className="text-green-700 hover:text-green-900 mr-4"
                                        >
                                            Edit
                                        </Link>
                                        <button
                                            type="button"
                                            onClick={() => destroy(row)}
                                            className="text-red-600 hover:text-red-800"
                                        >
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            )}
        </div>
    );
}
