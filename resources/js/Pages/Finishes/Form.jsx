import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import Select from '@/Components/Select';
import InputError from '@/Components/InputError';
import { Head, Link, useForm } from '@inertiajs/react';

export default function Form({ finish }) {
    const isNew = !finish;
    const title = isNew ? 'Add Finish' : `Edit ${finish.name}`;

    const { data, setData, post, put, processing, errors } = useForm({
        name: finish?.name ?? '',
        type: finish?.type ?? 'other',
    });

    const submit = (e) => {
        e.preventDefault();
        isNew ? post(route('finishes.store')) : put(route('finishes.update', finish.id));
    };

    return (
        <AuthenticatedLayout title={title}>
            <Head title={title} />

            <div className="max-w-lg bg-white rounded-lg shadow p-6">
                <form onSubmit={submit} className="space-y-4">
                    <div>
                        <InputLabel htmlFor="name" value="Name" />
                        <TextInput
                            id="name"
                            className="mt-1 block w-full"
                            value={data.name}
                            onChange={(e) => setData('name', e.target.value)}
                            autoFocus
                        />
                        <InputError message={errors.name} className="mt-1" />
                    </div>

                    <div>
                        <InputLabel htmlFor="type" value="Type" />
                        <Select
                            id="type"
                            className="mt-1 block w-full"
                            value={data.type}
                            onChange={(e) => setData('type', e.target.value)}
                        >
                            <option value="timber_stain">Timber Stain</option>
                            <option value="fabric">Fabric</option>
                            <option value="other">Other</option>
                        </Select>
                        <InputError message={errors.type} className="mt-1" />
                    </div>

                    <div className="flex justify-end gap-4">
                        <Link
                            href={route('finishes.index')}
                            className="px-4 py-2 text-gray-700 hover:text-gray-900"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            disabled={processing}
                            className="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-50"
                        >
                            {isNew ? 'Add Finish' : 'Save Changes'}
                        </button>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
