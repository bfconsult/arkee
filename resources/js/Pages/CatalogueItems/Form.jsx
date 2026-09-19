import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import Select from '@/Components/Select';
import InputError from '@/Components/InputError';
import { Head, Link, useForm } from '@inertiajs/react';

export default function Form({ item, suppliers, finishes, parentOptions }) {
    const isNew = !item;
    const title = isNew ? 'Add Catalogue Item' : `Edit ${item.catalogue_no ?? 'Catalogue Item'}`;

    const { data, setData, post, put, processing, errors } = useForm({
        catalogue_no: item?.catalogue_no ?? '',
        row_type: item?.row_type ?? 'parent',
        item_type: item?.item_type ?? '',
        supplier_id: item?.supplier_id ?? '',
        code_supplier: item?.code_supplier ?? '',
        notes_supplier: item?.notes_supplier ?? '',
        unit_cost: item?.unit_cost ?? '',
        meterage: item?.meterage ?? '',
        parent_item_id: item?.parent_item_id ?? '',
        height_mm: item?.height_mm ?? '',
        width_mm: item?.width_mm ?? '',
        depth_mm: item?.depth_mm ?? '',
        packaging_type: item?.packaging_type ?? '',
        finish_id: item?.finish_id ?? '',
    });

    const submit = (e) => {
        e.preventDefault();
        isNew
            ? post(route('catalogue-items.store'))
            : put(route('catalogue-items.update', item.id));
    };

    return (
        <AuthenticatedLayout title={title}>
            <Head title={title} />

            <div className="max-w-2xl bg-white rounded-lg shadow p-6">
                <form onSubmit={submit} className="space-y-4">
                    <div className="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel htmlFor="catalogue_no" value="Catalogue No." />
                            <TextInput
                                id="catalogue_no"
                                className="mt-1 block w-full"
                                value={data.catalogue_no}
                                onChange={(e) => setData('catalogue_no', e.target.value)}
                                autoFocus
                            />
                            <InputError message={errors.catalogue_no} className="mt-1" />
                        </div>

                        <div>
                            <InputLabel htmlFor="row_type" value="Row Type" />
                            <Select
                                id="row_type"
                                className="mt-1 block w-full"
                                value={data.row_type}
                                onChange={(e) => setData('row_type', e.target.value)}
                            >
                                <option value="parent">Parent</option>
                                <option value="sub">Sub</option>
                            </Select>
                            <InputError message={errors.row_type} className="mt-1" />
                        </div>
                    </div>

                    {data.row_type === 'sub' && (
                        <div>
                            <InputLabel htmlFor="parent_item_id" value="Parent Item" />
                            <Select
                                id="parent_item_id"
                                className="mt-1 block w-full"
                                value={data.parent_item_id}
                                onChange={(e) => setData('parent_item_id', e.target.value)}
                            >
                                <option value="">— None —</option>
                                {parentOptions.map((p) => (
                                    <option key={p.id} value={p.id}>
                                        {p.catalogue_no ?? `#${p.id}`}
                                        {p.item_type ? ` — ${p.item_type}` : ''}
                                    </option>
                                ))}
                            </Select>
                            <InputError message={errors.parent_item_id} className="mt-1" />
                        </div>
                    )}

                    <div>
                        <InputLabel htmlFor="item_type" value="Item Type" />
                        <TextInput
                            id="item_type"
                            className="mt-1 block w-full"
                            value={data.item_type}
                            onChange={(e) => setData('item_type', e.target.value)}
                        />
                        <InputError message={errors.item_type} className="mt-1" />
                    </div>

                    <div className="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel htmlFor="supplier_id" value="Supplier" />
                            <Select
                                id="supplier_id"
                                className="mt-1 block w-full"
                                value={data.supplier_id}
                                onChange={(e) => setData('supplier_id', e.target.value)}
                            >
                                <option value="">— Select —</option>
                                {suppliers.map((s) => (
                                    <option key={s.id} value={s.id}>
                                        {s.name}
                                    </option>
                                ))}
                            </Select>
                            <InputError message={errors.supplier_id} className="mt-1" />
                        </div>

                        <div>
                            <InputLabel htmlFor="finish_id" value="Finish" />
                            <Select
                                id="finish_id"
                                className="mt-1 block w-full"
                                value={data.finish_id}
                                onChange={(e) => setData('finish_id', e.target.value)}
                            >
                                <option value="">— None —</option>
                                {finishes.map((f) => (
                                    <option key={f.id} value={f.id}>
                                        {f.name}
                                    </option>
                                ))}
                            </Select>
                            <InputError message={errors.finish_id} className="mt-1" />
                        </div>
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
                            <InputLabel htmlFor="packaging_type" value="Packaging Type" />
                            <TextInput
                                id="packaging_type"
                                className="mt-1 block w-full"
                                value={data.packaging_type}
                                onChange={(e) => setData('packaging_type', e.target.value)}
                            />
                            <InputError message={errors.packaging_type} className="mt-1" />
                        </div>
                    </div>

                    <div className="grid grid-cols-2 gap-4">
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

                    <div className="grid grid-cols-3 gap-4">
                        <div>
                            <InputLabel htmlFor="height_mm" value="Height (mm)" />
                            <TextInput
                                id="height_mm"
                                type="number"
                                className="mt-1 block w-full"
                                value={data.height_mm}
                                onChange={(e) => setData('height_mm', e.target.value)}
                            />
                            <InputError message={errors.height_mm} className="mt-1" />
                        </div>

                        <div>
                            <InputLabel htmlFor="width_mm" value="Width (mm)" />
                            <TextInput
                                id="width_mm"
                                type="number"
                                className="mt-1 block w-full"
                                value={data.width_mm}
                                onChange={(e) => setData('width_mm', e.target.value)}
                            />
                            <InputError message={errors.width_mm} className="mt-1" />
                        </div>

                        <div>
                            <InputLabel htmlFor="depth_mm" value="Depth (mm)" />
                            <TextInput
                                id="depth_mm"
                                type="number"
                                className="mt-1 block w-full"
                                value={data.depth_mm}
                                onChange={(e) => setData('depth_mm', e.target.value)}
                            />
                            <InputError message={errors.depth_mm} className="mt-1" />
                        </div>
                    </div>

                    <div>
                        <InputLabel htmlFor="notes_supplier" value="Supplier Notes" />
                        <textarea
                            id="notes_supplier"
                            className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500"
                            rows={3}
                            value={data.notes_supplier}
                            onChange={(e) => setData('notes_supplier', e.target.value)}
                        />
                        <InputError message={errors.notes_supplier} className="mt-1" />
                    </div>

                    <div className="flex justify-end gap-4">
                        <Link
                            href={route('catalogue-items.index')}
                            className="px-4 py-2 text-gray-700 hover:text-gray-900"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            disabled={processing}
                            className="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-50"
                        >
                            {isNew ? 'Add Item' : 'Save Changes'}
                        </button>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
