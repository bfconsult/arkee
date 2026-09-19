import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import InputError from '@/Components/InputError';
import { Head, Link, useForm } from '@inertiajs/react';

export default function Form({ client }) {
    const isNew = !client;
    const title = isNew ? 'Add Client' : `Edit ${client.company_name}`;

    const { data, setData, post, put, processing, errors } = useForm({
        company_name: client?.company_name ?? '',
        contact_name: client?.contact_name ?? '',
        contact_email: client?.contact_email ?? '',
        contact_phone: client?.contact_phone ?? '',
        address: client?.address ?? '',
        notes: client?.notes ?? '',
    });

    const submit = (e) => {
        e.preventDefault();
        isNew ? post(route('clients.store')) : put(route('clients.update', client.id));
    };

    return (
        <AuthenticatedLayout title={title}>
            <Head title={title} />

            <div className="max-w-2xl bg-white rounded-lg shadow p-6">
                <form onSubmit={submit} className="space-y-4">
                    <div>
                        <InputLabel htmlFor="company_name" value="Company Name" />
                        <TextInput
                            id="company_name"
                            className="mt-1 block w-full"
                            value={data.company_name}
                            onChange={(e) => setData('company_name', e.target.value)}
                            autoFocus
                        />
                        <InputError message={errors.company_name} className="mt-1" />
                    </div>

                    <div>
                        <InputLabel htmlFor="contact_name" value="Contact Name" />
                        <TextInput
                            id="contact_name"
                            className="mt-1 block w-full"
                            value={data.contact_name}
                            onChange={(e) => setData('contact_name', e.target.value)}
                        />
                        <InputError message={errors.contact_name} className="mt-1" />
                    </div>

                    <div className="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel htmlFor="contact_email" value="Contact Email" />
                            <TextInput
                                id="contact_email"
                                type="email"
                                className="mt-1 block w-full"
                                value={data.contact_email}
                                onChange={(e) => setData('contact_email', e.target.value)}
                            />
                            <InputError message={errors.contact_email} className="mt-1" />
                        </div>
                        <div>
                            <InputLabel htmlFor="contact_phone" value="Contact Phone" />
                            <TextInput
                                id="contact_phone"
                                className="mt-1 block w-full"
                                value={data.contact_phone}
                                onChange={(e) => setData('contact_phone', e.target.value)}
                            />
                            <InputError message={errors.contact_phone} className="mt-1" />
                        </div>
                    </div>

                    <div>
                        <InputLabel htmlFor="address" value="Address" />
                        <TextInput
                            id="address"
                            className="mt-1 block w-full"
                            value={data.address}
                            onChange={(e) => setData('address', e.target.value)}
                        />
                        <InputError message={errors.address} className="mt-1" />
                    </div>

                    <div>
                        <InputLabel htmlFor="notes" value="Notes" />
                        <textarea
                            id="notes"
                            rows={3}
                            className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            value={data.notes}
                            onChange={(e) => setData('notes', e.target.value)}
                        />
                        <InputError message={errors.notes} className="mt-1" />
                    </div>

                    <div className="flex justify-end gap-4">
                        <Link
                            href={route('clients.index')}
                            className="px-4 py-2 text-gray-700 hover:text-gray-900"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            disabled={processing}
                            className="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-50"
                        >
                            {isNew ? 'Add Client' : 'Save Changes'}
                        </button>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
