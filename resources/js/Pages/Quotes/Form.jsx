import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import Select from '@/Components/Select';
import InputError from '@/Components/InputError';
import { Head, Link, useForm } from '@inertiajs/react';

export default function Form({ project, quote }) {
    const isNew = !quote;
    const title = isNew
        ? `Add Quote — ${project.project_descriptor ?? 'Project'}`
        : `Edit ${quote.quote_number ?? 'Quote'}`;

    const { data, setData, post, put, processing, errors } = useForm({
        quote_number: quote?.quote_number ?? '',
        version: quote?.version ?? 1,
        date: quote?.date ?? '',
        status: quote?.status ?? 'quote',
    });

    const submit = (e) => {
        e.preventDefault();
        isNew
            ? post(route('projects.quotes.store', project.id))
            : put(route('projects.quotes.update', [project.id, quote.id]));
    };

    return (
        <AuthenticatedLayout title={title}>
            <Head title={title} />

            <div className="max-w-lg bg-white rounded-lg shadow p-6">
                <form onSubmit={submit} className="space-y-4">
                    <div className="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel htmlFor="quote_number" value="Quote No." />
                            <TextInput
                                id="quote_number"
                                className="mt-1 block w-full"
                                value={data.quote_number}
                                onChange={(e) => setData('quote_number', e.target.value)}
                                autoFocus
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
                    </div>

                    <div className="grid grid-cols-2 gap-4">
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
                    </div>

                    {!isNew && (
                        <div className="border-t pt-4">
                            <Link
                                href={route('quotes.schedule-lines.index', quote.id)}
                                className="px-4 py-2 text-sm bg-gray-100 rounded-md hover:bg-gray-200"
                            >
                                Furniture Schedule →
                            </Link>
                        </div>
                    )}

                    <div className="flex justify-end gap-4">
                        <Link
                            href={route('projects.show', project.id)}
                            className="px-4 py-2 text-gray-700 hover:text-gray-900"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            disabled={processing}
                            className="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-50"
                        >
                            {isNew ? 'Add Quote' : 'Save Changes'}
                        </button>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
