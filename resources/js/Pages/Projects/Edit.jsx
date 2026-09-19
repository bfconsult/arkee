import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router, useForm } from '@inertiajs/react';

export default function Edit({ project, isNewProject }) {
    const title = isNewProject ? 'Create New Project' : 'Edit Project';

    const { data, setData, patch, processing, errors } = useForm({
        name: project.name,
        address: project.address,
        email: project.email ?? '',
    });

    const submit = (e) => {
        e.preventDefault();
        patch(route('projects.update', project.id));
    };

    const discardNewProject = () => {
        if (confirm("Discard this new project? It hasn't been saved with any real details yet.")) {
            router.delete(route('projects.destroy', project.id));
        }
    };

    return (
        <AuthenticatedLayout>
            <Head title={isNewProject ? title : `Edit ${project.name}`} />

            <div className="py-12">
                <div className="max-w-2xl mx-auto sm:px-6 lg:px-8">
                    <h1 className="text-2xl font-semibold text-gray-900 mb-6">
                        {title}
                    </h1>

                    <div className="bg-white rounded-lg shadow p-6">
                        <form onSubmit={submit}>
                            <div className="mb-4">
                                <label className="block text-sm font-medium text-gray-700 mb-1">
                                    Name
                                </label>
                                <input
                                    type="text"
                                    value={data.name}
                                    onChange={(e) => setData('name', e.target.value)}
                                    className="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                                />
                                {errors.name && (
                                    <p className="mt-1 text-sm text-red-600">{errors.name}</p>
                                )}
                            </div>

                            <div className="mb-6">
                                <label className="block text-sm font-medium text-gray-700 mb-1">
                                    Address
                                </label>
                                <input
                                    type="text"
                                    value={data.address}
                                    onChange={(e) => setData('address', e.target.value)}
                                    className="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                                />
                                {errors.address && (
                                    <p className="mt-1 text-sm text-red-600">{errors.address}</p>
                                )}
                            </div>

                            {!isNewProject && (
                                <div className="mb-6">
                                    <label className="block text-sm font-medium text-gray-700 mb-1">
                                        Email
                                    </label>
                                    <input
                                        type="email"
                                        value={data.email}
                                        onChange={(e) => setData('email', e.target.value)}
                                        className="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                                    />
                                    {errors.email && (
                                        <p className="mt-1 text-sm text-red-600">{errors.email}</p>
                                    )}
                                </div>
                            )}

                            <div className="flex justify-end gap-4">
                                {isNewProject ? (
                                    <button
                                        type="button"
                                        onClick={discardNewProject}
                                        className="px-4 py-2 text-gray-700 hover:text-gray-900"
                                    >
                                        Cancel
                                    </button>
                                ) : (
                                    <Link
                                        href={route('projects.show', project.id)}
                                        className="px-4 py-2 text-gray-700 hover:text-gray-900"
                                    >
                                        Cancel
                                    </Link>
                                )}
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-50"
                                >
                                    {isNewProject ? 'Create Project' : 'Update Project'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}