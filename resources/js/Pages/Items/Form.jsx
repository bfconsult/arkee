import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import Select from '@/Components/Select';
import InputError from '@/Components/InputError';
import { Head, Link, useForm } from '@inertiajs/react';

export default function Form({ item, itemCategories, packagingTypes, suppliers }) {
    const isNew = !item;
    const title = isNew ? 'Add Item' : `Edit ${item.name ?? item.catalogue_no ?? 'Item'}`;

    const { data, setData, post, put, processing, errors } = useForm({
        name: item?.name ?? '',
        catalogue_no: item?.catalogue_no ?? '',
        item_category_id: item?.item_category_id ?? '',
        height_mm: item?.height_mm ?? '',
        width_mm: item?.width_mm ?? '',
        depth_mm: item?.depth_mm ?? '',
        packaging_type_id: item?.packaging_type_id ?? '',
        supplier_id: item?.supplier_id ?? '',
        notes: item?.notes ?? '',
    });

    const submit = (e) => {
        e.preventDefault();
        isNew ? post(route('items.store')) : put(route('items.update', item.id));
    };

    return (
        <AuthenticatedLayout title={title}>
            <Head title={title} />

            <div className="max-w-2xl bg-white rounded-lg shadow p-6">
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

                    <div className="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel htmlFor="catalogue_no" value="Catalogue No." />
                            <TextInput
                                id="catalogue_no"
                                className="mt-1 block w-full"
                                value={data.catalogue_no}
                                onChange={(e) => setData('catalogue_no', e.target.value)}
                            />
                            <InputError message={errors.catalogue_no} className="mt-1" />
                        </div>

                        <div>
                            <InputLabel htmlFor="item_category_id" value="Item Category" />
                            <Select
                                id="item_category_id"
                                className="mt-1 block w-full"
                                value={data.item_category_id}
                                onChange={(e) => setData('item_category_id', e.target.value)}
                            >
                                <option value="">— None —</option>
                                {itemCategories.map((c) => (
                                    <option key={c.id} value={c.id}>
                                        {c.name}
                                    </option>
                                ))}
                            </Select>
                            <InputError message={errors.item_category_id} className="mt-1" />
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

                    <div className="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel htmlFor="packaging_type_id" value="Packaging Type" />
                            <Select
                                id="packaging_type_id"
                                className="mt-1 block w-full"
                                value={data.packaging_type_id}
                                onChange={(e) => setData('packaging_type_id', e.target.value)}
                            >
                                <option value="">— None —</option>
                                {packagingTypes.map((p) => (
                                    <option key={p.id} value={p.id}>
                                        {p.name}
                                    </option>
                                ))}
                            </Select>
                            <InputError message={errors.packaging_type_id} className="mt-1" />
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
                                href={route('items.show', item.id)}
                                className="px-4 py-2 text-sm bg-gray-100 rounded-md hover:bg-gray-200"
                            >
                                View Full Item →
                            </Link>
                            <Link
                                href={route('items.components.index', item.id)}
                                className="px-4 py-2 text-sm bg-gray-100 rounded-md hover:bg-gray-200"
                            >
                                Components →
                            </Link>
                        </div>
                    )}

                    <div className="flex justify-end gap-4">
                        <Link
                            href={route('items.index')}
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
