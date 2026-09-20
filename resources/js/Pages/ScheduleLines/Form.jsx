import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import Select from '@/Components/Select';
import InputError from '@/Components/InputError';
import { Head, Link, useForm } from '@inertiajs/react';

export default function Form({ project, line, items, materials }) {
    const isNew = !line;
    const title = isNew ? 'Add Schedule Line' : 'Edit Schedule Line';

    const { data, setData, post, put, processing, errors } = useForm({
        item_id: line?.item_id ?? '',
        fabric_notes: line?.fabric_notes ?? '',
        quantity: line?.quantity ?? '',
        price_override: line?.price_override ?? '',
        markup_target_pct: line?.markup_target_pct ?? '',
        required_by: line?.required_by ?? '',
        include_on_po: line?.include_on_po ?? false,
        internal_cost_manual: line?.internal_cost_manual ?? '',
        component_finishes: (line?.component_finishes ?? []).map((cf) => ({
            component_id: cf.component_id,
            material_id: cf.material_id ?? '',
            finish_id: cf.finish_id ?? '',
        })),
    });

    const selectedItem = items.find((i) => String(i.id) === String(data.item_id));
    const components = selectedItem?.components ?? [];

    const defaultMaterialId = (component) => (component.is_fabric ? '' : component.material_id ?? '');

    const handleItemChange = (value) => {
        const item = items.find((i) => String(i.id) === String(value));

        setData((prevData) => ({
            ...prevData,
            item_id: value,
            component_finishes: (item?.components ?? []).map((c) => ({
                component_id: c.id,
                material_id: defaultMaterialId(c),
                finish_id: '',
            })),
        }));
    };

    const updateComponentFinish = (componentId, field, value) => {
        const exists = data.component_finishes.some((cf) => cf.component_id === componentId);
        const clearFinish = field === 'material_id' ? { finish_id: '' } : {};

        if (exists) {
            setData(
                'component_finishes',
                data.component_finishes.map((cf) =>
                    cf.component_id === componentId ? { ...cf, [field]: value, ...clearFinish } : cf
                )
            );
            return;
        }

        const component = components.find((c) => c.id === componentId);

        setData('component_finishes', [
            ...data.component_finishes,
            { component_id: componentId, material_id: defaultMaterialId(component ?? {}), finish_id: '', [field]: value },
        ]);
    };

    const submit = (e) => {
        e.preventDefault();
        isNew
            ? post(route('projects.schedule-lines.store', project.id))
            : put(route('projects.schedule-lines.update', [project.id, line.id]));
    };

    return (
        <AuthenticatedLayout title={title}>
            <Head title={title} />

            <div className="max-w-2xl bg-white rounded-lg shadow p-6">
                <form onSubmit={submit} className="space-y-4">
                    <div>
                        <InputLabel htmlFor="item_id" value="Item" />
                        <Select
                            id="item_id"
                            className="mt-1 block w-full"
                            value={data.item_id}
                            onChange={(e) => handleItemChange(e.target.value)}
                        >
                            <option value="">— Select —</option>
                            {items.map((i) => (
                                <option key={i.id} value={i.id}>
                                    {i.catalogue_no ?? `#${i.id}`}
                                    {i.item_category ? ` — ${i.item_category.name}` : ''}
                                </option>
                            ))}
                        </Select>
                        <InputError message={errors.item_id} className="mt-1" />
                    </div>

                    {components.length > 0 && (
                        <div className="border-t pt-4">
                            <h3 className="text-sm font-medium text-gray-700 mb-2">Component Finishes</h3>

                            <div className="space-y-4">
                                {components.map((component) => {
                                    const selection = data.component_finishes.find(
                                        (cf) => cf.component_id === component.id
                                    ) ?? { material_id: defaultMaterialId(component), finish_id: '' };

                                    const material = component.is_fabric
                                        ? materials.find((m) => String(m.id) === String(selection.material_id))
                                        : component.material;
                                    const finishOptions = material?.finishes ?? [];

                                    return (
                                        <div key={component.id} className="grid grid-cols-2 gap-4">
                                            <div className="col-span-2 text-sm font-medium text-gray-900">
                                                {component.name}
                                            </div>

                                            <div>
                                                <InputLabel value="Material" />
                                                {component.is_fabric ? (
                                                    <Select
                                                        id={`material_${component.id}`}
                                                        className="mt-1 block w-full"
                                                        value={selection.material_id}
                                                        onChange={(e) =>
                                                            updateComponentFinish(component.id, 'material_id', e.target.value)
                                                        }
                                                    >
                                                        <option value="">— Select —</option>
                                                        {materials.map((m) => (
                                                            <option key={m.id} value={m.id}>
                                                                {m.name}
                                                            </option>
                                                        ))}
                                                    </Select>
                                                ) : (
                                                    <div className="mt-1 py-2 text-sm text-gray-700">
                                                        {component.material?.name ?? '—'}
                                                    </div>
                                                )}
                                            </div>

                                            <div>
                                                <InputLabel htmlFor={`finish_${component.id}`} value="Finish" />
                                                <Select
                                                    id={`finish_${component.id}`}
                                                    className="mt-1 block w-full"
                                                    value={selection.finish_id}
                                                    disabled={!selection.material_id}
                                                    onChange={(e) =>
                                                        updateComponentFinish(component.id, 'finish_id', e.target.value)
                                                    }
                                                >
                                                    <option value="">— Select —</option>
                                                    {finishOptions.map((f) => (
                                                        <option key={f.id} value={f.id}>
                                                            {f.name}
                                                        </option>
                                                    ))}
                                                </Select>
                                            </div>
                                        </div>
                                    );
                                })}
                            </div>
                        </div>
                    )}

                    <div className="grid grid-cols-2 gap-4">
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

                        <div>
                            <InputLabel htmlFor="price_override" value="Price Override" />
                            <TextInput
                                id="price_override"
                                type="number"
                                step="0.01"
                                className="mt-1 block w-full"
                                value={data.price_override}
                                onChange={(e) => setData('price_override', e.target.value)}
                            />
                            <InputError message={errors.price_override} className="mt-1" />
                        </div>
                    </div>

                    <div className="grid grid-cols-3 gap-4">
                        <div>
                            <InputLabel htmlFor="markup_target_pct" value="Markup Target %" />
                            <TextInput
                                id="markup_target_pct"
                                type="number"
                                step="0.01"
                                className="mt-1 block w-full"
                                value={data.markup_target_pct}
                                onChange={(e) => setData('markup_target_pct', e.target.value)}
                            />
                            <InputError message={errors.markup_target_pct} className="mt-1" />
                        </div>

                        <div>
                            <InputLabel htmlFor="internal_cost_manual" value="Internal Cost (Manual)" />
                            <TextInput
                                id="internal_cost_manual"
                                type="number"
                                step="0.01"
                                className="mt-1 block w-full"
                                value={data.internal_cost_manual}
                                onChange={(e) => setData('internal_cost_manual', e.target.value)}
                            />
                            <InputError message={errors.internal_cost_manual} className="mt-1" />
                        </div>

                        <div>
                            <InputLabel htmlFor="required_by" value="Required By" />
                            <TextInput
                                id="required_by"
                                type="date"
                                className="mt-1 block w-full"
                                value={data.required_by}
                                onChange={(e) => setData('required_by', e.target.value)}
                            />
                            <InputError message={errors.required_by} className="mt-1" />
                        </div>
                    </div>

                    <div className="flex items-center gap-2">
                        <input
                            id="include_on_po"
                            type="checkbox"
                            checked={data.include_on_po}
                            onChange={(e) => setData('include_on_po', e.target.checked)}
                            className="rounded border-gray-300 text-green-600 focus:ring-green-500"
                        />
                        <InputLabel htmlFor="include_on_po" value="Include on Purchase Order" className="!mb-0" />
                    </div>

                    <div>
                        <InputLabel htmlFor="fabric_notes" value="Fabric Notes" />
                        <textarea
                            id="fabric_notes"
                            className="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500"
                            rows={3}
                            value={data.fabric_notes}
                            onChange={(e) => setData('fabric_notes', e.target.value)}
                        />
                        <InputError message={errors.fabric_notes} className="mt-1" />
                    </div>

                    <div className="flex justify-end gap-4">
                        <Link
                            href={route('projects.schedule-lines.index', project.id)}
                            className="px-4 py-2 text-gray-700 hover:text-gray-900"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            disabled={processing}
                            className="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-50"
                        >
                            {isNew ? 'Add Line' : 'Save Changes'}
                        </button>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
