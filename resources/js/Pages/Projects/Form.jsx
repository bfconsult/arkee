import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import Select from '@/Components/Select';
import InputError from '@/Components/InputError';
import { Head, Link, useForm } from '@inertiajs/react';

export default function Form({ project, clients, users }) {
    const isNew = !project;
    const title = isNew ? 'Add Project' : `Edit ${project.project_descriptor ?? project.quote_number ?? 'Project'}`;

    const { data, setData, post, put, processing, errors } = useForm({
        quote_number: project?.quote_number ?? '',
        client_id: project?.client_id ?? '',
        pm_user_id: project?.pm_user_id ?? '',
        project_descriptor: project?.project_descriptor ?? '',
        version: project?.version ?? 1,
        date: project?.date ?? '',
        status: project?.status ?? 'quote',
        site_name: project?.site_name ?? '',
        site_address: project?.site_address ?? '',
        site_contact_name: project?.site_contact_name ?? '',
    });

    const submit = (e) => {
        e.preventDefault();
        isNew ? post(route('projects.store')) : put(route('projects.update', project.id));
    };

    return (
        <AuthenticatedLayout title={title}>
            <Head title={title} />

            <div className="max-w-2xl bg-white rounded-lg shadow p-6">
                <form onSubmit={submit} className="space-y-4">
                    <div className="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel htmlFor="client_id" value="Client" />
                            <Select
                                id="client_id"
                                className="mt-1 block w-full"
                                value={data.client_id}
                                onChange={(e) => setData('client_id', e.target.value)}
                            >
                                <option value="">— Select —</option>
                                {clients.map((c) => (
                                    <option key={c.id} value={c.id}>
                                        {c.company_name}
                                    </option>
                                ))}
                            </Select>
                            <InputError message={errors.client_id} className="mt-1" />
                        </div>

                        <div>
                            <InputLabel htmlFor="pm_user_id" value="Project Manager" />
                            <Select
                                id="pm_user_id"
                                className="mt-1 block w-full"
                                value={data.pm_user_id}
                                onChange={(e) => setData('pm_user_id', e.target.value)}
                            >
                                <option value="">— Select —</option>
                                {users.map((u) => (
                                    <option key={u.id} value={u.id}>
                                        {u.name}
                                    </option>
                                ))}
                            </Select>
                            <InputError message={errors.pm_user_id} className="mt-1" />
                        </div>
                    </div>

                    <div>
                        <InputLabel htmlFor="project_descriptor" value="Project Descriptor" />
                        <TextInput
                            id="project_descriptor"
                            className="mt-1 block w-full"
                            value={data.project_descriptor}
                            onChange={(e) => setData('project_descriptor', e.target.value)}
                            autoFocus
                        />
                        <InputError message={errors.project_descriptor} className="mt-1" />
                    </div>

                    <div className="grid grid-cols-3 gap-4">
                        <div>
                            <InputLabel htmlFor="quote_number" value="Quote No." />
                            <TextInput
                                id="quote_number"
                                className="mt-1 block w-full"
                                value={data.quote_number}
                                onChange={(e) => setData('quote_number', e.target.value)}
                            />
                            <InputError message={errors.quote_number} className="mt-1" />
                        </div>

                        <div>
                            <InputLabel htmlFor="version" value="Version" />
                            <TextInput
                                id="version"
                                type="number"
                                min="1"
                                className="mt-1 block w-full"
                                value={data.version}
                                onChange={(e) => setData('version', e.target.value)}
                            />
                            <InputError message={errors.version} className="mt-1" />
                        </div>

                        <div>
                            <InputLabel htmlFor="date" value="Date" />
                            <TextInput
                                id="date"
                                type="date"
                                className="mt-1 block w-full"
                                value={data.date}
                                onChange={(e) => setData('date', e.target.value)}
                            />
                            <InputError message={errors.date} className="mt-1" />
                        </div>
                    </div>

                    <div>
                        <InputLabel htmlFor="status" value="Status" />
                        <Select
                            id="status"
                            className="mt-1 block w-full"
                            value={data.status}
                            onChange={(e) => setData('status', e.target.value)}
                        >
                            <option value="quote">Quote</option>
                            <option value="complete">Complete</option>
                            <option value="approved">Approved</option>
                            <option value="cancelled">Cancelled</option>
                        </Select>
                        <InputError message={errors.status} className="mt-1" />
                    </div>

                    <div className="border-t pt-4">
                        <h3 className="text-sm font-medium text-gray-700 mb-2">Site Details</h3>

                        <div className="space-y-4">
                            <div>
                                <InputLabel htmlFor="site_name" value="Site Name" />
                                <TextInput
                                    id="site_name"
                                    className="mt-1 block w-full"
                                    value={data.site_name}
                                    onChange={(e) => setData('site_name', e.target.value)}
                                />
                                <InputError message={errors.site_name} className="mt-1" />
                            </div>

                            <div>
                                <InputLabel htmlFor="site_address" value="Site Address" />
                                <TextInput
                                    id="site_address"
                                    className="mt-1 block w-full"
                                    value={data.site_address}
                                    onChange={(e) => setData('site_address', e.target.value)}
                                />
                                <InputError message={errors.site_address} className="mt-1" />
                            </div>

                            <div>
                                <InputLabel htmlFor="site_contact_name" value="Site Contact Name" />
                                <TextInput
                                    id="site_contact_name"
                                    className="mt-1 block w-full"
                                    value={data.site_contact_name}
                                    onChange={(e) => setData('site_contact_name', e.target.value)}
                                />
                                <InputError message={errors.site_contact_name} className="mt-1" />
                            </div>
                        </div>
                    </div>

                    {!isNew && (
                        <div className="border-t pt-4 flex gap-4">
                            <Link
                                href={route('projects.show', project.id)}
                                className="px-4 py-2 text-sm bg-gray-100 rounded-md hover:bg-gray-200"
                            >
                                View Full Project →
                            </Link>
                            <Link
                                href={route('projects.schedule-lines.index', project.id)}
                                className="px-4 py-2 text-sm bg-gray-100 rounded-md hover:bg-gray-200"
                            >
                                Furniture Schedule →
                            </Link>
                            <Link
                                href={route('projects.purchase-orders.index', project.id)}
                                className="px-4 py-2 text-sm bg-gray-100 rounded-md hover:bg-gray-200"
                            >
                                Purchase Orders →
                            </Link>
                        </div>
                    )}

                    <div className="flex justify-end gap-4">
                        <Link
                            href={route('projects.index')}
                            className="px-4 py-2 text-gray-700 hover:text-gray-900"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            disabled={processing}
                            className="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-50"
                        >
                            {isNew ? 'Add Project' : 'Save Changes'}
                        </button>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
