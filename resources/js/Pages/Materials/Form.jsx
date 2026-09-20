import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import Select from '@/Components/Select';
import InputError from '@/Components/InputError';
import { Head, Link, useForm } from '@inertiajs/react';

export default function Form({ material, suppliers }) {
    const isNew = !material;
    const title = isNew ? 'Add Material' : `Edit ${material.name}`;

    const { data, setData, post, put, processing, errors } = useForm({
        name: material?.name ?? '',
        supplier_id: material?.supplier_id ?? '',
        code_supplier: material?.code_supplier ?? '',
        unit_cost: material?.unit_cost ?? '',
        meterage: material?.meterage ?? '',
        notes: material?.notes ?? '',
    });

    const submit = (e) => {
        e.preventDefault();
        isNew ? post(route('materials.store')) : put(route('materials.update', material.id));
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
                        <InputLabel htmlFor="supplier_id" value="Supplier" />
                        <Select
                            id="supplier_id"
                            className="mt-1 block w-full"
                            value={data.supplier_id}
                            onChange={(e) => setData('supplier_id', e.target.value)}
                        >
                            <option value="">— None —</option>
                            {suppliers.map((s) => (
                                <option key={s.id} value={s.id}>
                                    {s.name}
                                </option>
                            ))}
                        </Select>
                        <InputError message={errors.supplier_id} className="mt-1" />
                    </div>

                    <div className="grid grid-cols-2 gap-4">
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
                            <InputLabel htmlFor="unit_cost" value="Unit Cost" />
                            <TextInput
                                id="unit_cost"
                                type="number"
                                step="0.01"
                                className="mt-1 block w-full"
                                value={data.unit_cost}
                                onChange={(e) => setData('unit_cost', e.target.value)}
                            />
                            <InputError message={errors.unit_cost} className="mt-1" />
                        </div>
                    </div>

                    <div>
                        <InputLabel htmlFor="meterage" value="Meterage" />
                        <TextInput
                            id="meterage"
                            type="number"
                            step="0.01"
                            className="mt-1 block w-full"
                            value={data.meterage}
                            onChange={(e) => setData('meterage', e.target.value)}
                        />
                        <InputError message={errors.meterage} className="mt-1" />
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

                    {!isNew && (
                        <div className="border-t pt-4 flex gap-4">
                            <Link
                                href={route('materials.show', material.id)}
                                className="px-4 py-2 text-sm bg-gray-100 rounded-md hover:bg-gray-200"
                            >
                                View Full Material →
                            </Link>
                            <Link
                                href={route('materials.finishes.index', material.id)}
                                className="px-4 py-2 text-sm bg-gray-100 rounded-md hover:bg-gray-200"
                            >
                                Finishes →
                            </Link>
                        </div>
                    )}

                    <div className="flex justify-end gap-4">
                        <Link
                            href={route('materials.index')}
                            className="px-4 py-2 text-gray-700 hover:text-gray-900"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            disabled={processing}
                            className="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-50"
                        >
                            {isNew ? 'Add Material' : 'Save Changes'}
                        </button>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
