import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import Select from '@/Components/Select';
import InputError from '@/Components/InputError';
import { Head, Link, useForm } from '@inertiajs/react';

export default function Form({ item, component, materials, suppliers }) {
    const isNew = !component;
    const title = isNew ? 'Add Component' : `Edit ${component.name}`;

    const { data, setData, post, put, processing, errors } = useForm({
        is_fabric: component?.is_fabric ?? false,
        material_id: component?.material_id ?? '',
        name: component?.name ?? '',
        quantity: component?.quantity ?? '',
        notes: component?.notes ?? '',
        supplier_id: component?.supplier_id ?? '',
        code_supplier: component?.code_supplier ?? '',
        unit_cost: component?.unit_cost ?? '',
        meterage: component?.meterage ?? '',
    });

    const submit = (e) => {
        e.preventDefault();
        isNew
            ? post(route('items.components.store', item.id))
            : put(route('items.components.update', [item.id, component.id]));
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

                    <div className="flex items-center gap-2">
                        <input
                            id="is_fabric"
                            type="checkbox"
                            checked={data.is_fabric}
                            onChange={(e) => {
                                setData('is_fabric', e.target.checked);
                                if (e.target.checked) {
                                    setData('material_id', '');
                                }
                            }}
                            className="rounded border-gray-300 text-green-600 focus:ring-green-500"
                        />
                        <InputLabel htmlFor="is_fabric" value="This component is Fabric" className="!mb-0" />
                    </div>

                    {data.is_fabric ? (
                        <p className="text-sm text-gray-500">
                            The Material and Finish for a Fabric component aren't fixed here — they're
                            chosen per Project, when this Item is added to a Furniture Schedule.
                        </p>
                    ) : (
                        <div>
                            <InputLabel htmlFor="material_id" value="Material" />
                            <Select
                                id="material_id"
                                className="mt-1 block w-full"
                                value={data.material_id}
                                onChange={(e) => setData('material_id', e.target.value)}
                            >
                                <option value="">— Select —</option>
                                {materials.map((m) => (
                                    <option key={m.id} value={m.id}>
                                        {m.name}
                                    </option>
                                ))}
                            </Select>
                            <InputError message={errors.material_id} className="mt-1" />
                        </div>
                    )}

                    <div>
                        <InputLabel htmlFor="quantity" value="Quantity" />
                        <TextInput
                            id="quantity"
                            type="number"
                            className="mt-1 block w-full"
                            value={data.quantity}
                            onChange={(e) => setData('quantity', e.target.value)}
                        />
                        <InputError message={errors.quantity} className="mt-1" />
                    </div>

                    <div className="border-t pt-4">
                        <h3 className="text-sm font-medium text-gray-700 mb-2">
                            This Component's Own Sourcing (optional — leave blank if the Material above already carries this)
                        </h3>

                        <div className="space-y-4">
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
                        </div>
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
                            href={route('items.components.index', item.id)}
                            className="px-4 py-2 text-gray-700 hover:text-gray-900"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            disabled={processing}
                            className="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-50"
                        >
                            {isNew ? 'Add Component' : 'Save Changes'}
                        </button>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
