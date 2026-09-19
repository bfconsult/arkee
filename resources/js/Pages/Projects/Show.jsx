import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import BackLink from '@/Components/BackLink';
import { Head, Link, router } from '@inertiajs/react';

export default function Show({ project, currentRole, canLeave }) {
    const isAdminOrManager = currentRole === 'admin' || currentRole === 'manager';

    const destroy = () => {
        if (confirm('Are you sure you want to delete this project?')) {
            router.delete(route('projects.destroy', project.id));
        }
    };

    const leave = () => {
        if (confirm(`Leave the team for ${project.name}? You'll lose access to this project.`)) {
            router.delete(route('projects.leave', project.id));
        }
    };

    return (
        <AuthenticatedLayout>
            <Head title={project.name} />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="flex justify-between items-center mb-6">
                        <h1 className="text-2xl font-semibold text-gray-900">
                            {project.name}
                        </h1>
                        <div className="flex items-center gap-4">
                            <Link
                                href={route('projects.edit', project.id)}
                                className="text-sm px-3 py-1 border border-green-600 text-green-600 rounded-lg"
                            >
                                Edit
                            </Link>
                            <BackLink href={route('dashboard')}>Back</BackLink>
                        </div>
                    </div>

                    <div className="bg-white rounded-lg shadow p-6 mb-6">
                        <h2 className="text-lg font-medium text-gray-900 mb-4">
                            Details
                        </h2>
                        <div className="grid grid-cols-2 gap-4">
                            <div>
                                <p className="text-sm text-gray-500">Name</p>
                                <p className="text-gray-900">{project.name}</p>
                            </div>
                            <div>
                                <p className="text-sm text-gray-500">Address</p>
                                <p className="text-gray-900">{project.address}</p>
                            </div>
                        </div>
                    </div>

                    {isAdminOrManager && (
                        <div className="bg-white rounded-lg shadow mb-6 divide-y divide-gray-100">
                            <Link
                                href={route('invitations.index')}
                                className="flex items-center justify-between px-6 py-4 hover:bg-gray-50"
                            >
                                <span className="text-gray-900">Team</span>
                                <span className="text-gray-400">›</span>
                            </Link>
                        </div>
                    )}

                    <div className="bg-white rounded-lg shadow p-6">
                        <h2 className="text-lg font-medium text-gray-900 mb-2">Leave Team</h2>
                        <p className="text-sm text-gray-500 mb-4">
                            {canLeave
                                ? "You'll lose access to this project, and will need a new invitation to rejoin."
                                : 'You are the last admin on this project, so you can\'t leave. Promote another member to admin first, or delete the project instead.'}
                        </p>
                        <button
                            onClick={leave}
                            disabled={!canLeave}
                            className="px-4 py-2 border border-red-300 text-red-600 rounded-md hover:bg-red-50 disabled:opacity-50 disabled:hover:bg-white disabled:cursor-not-allowed"
                        >
                            Leave Team
                        </button>
                    </div>

                    <button
                        onClick={destroy}
                        className="w-full mt-6 py-2 text-center text-sm text-red-600 border border-dotted border-red-400 rounded-lg"
                    >
                        Delete
                    </button>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}