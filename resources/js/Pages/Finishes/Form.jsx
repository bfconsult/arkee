import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import InputError from '@/Components/InputError';
import { Head, Link, useForm } from '@inertiajs/react';

export default function Form({ material, finish }) {
    const isNew = !finish;
    const title = isNew ? 'Add Finish' : `Edit ${finish.name}`;

    const { data, setData, post, put, processing, errors } = useForm({
        name: finish?.name ?? '',
        code_supplier: finish?.code_supplier ?? '',
        notes: finish?.notes ?? '',
    });

    const submit = (e) => {
        e.preventDefault();
        isNew
            ? post(route('materials.finishes.store', material.id))
            : put(route('materials.finishes.update', [material.id, finish.id]));
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
                        <InputLabel htmlFor="code_supplier" value="Supplier Code" />
                        <TextInput
                            id="code_supplier"
                            className="mt-1 block w-full"
                            value={data.code_supplier}
                            onChange={(e) => setData('code_supplier', e.target.value)}
                        />
                        <InputError message={errors.code_supplier} className="mt-1" />
                    </div>

                    <div>
                        <InputLabel htmlFor="notes" value="Notes" />
                        <textarea
                            id="notes"
                            className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500"
                            rows={3}
                            value={data.notes}
                            onChange={(e) => setData('notes', e.target.value)}
                        />
                        <InputError message={errors.notes} className="mt-1" />
                    </div>

                    <div className="flex justify-end gap-4">
                        <Link
                            href={route('materials.finishes.index', material.id)}
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
