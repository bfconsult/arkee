import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import Select from '@/Components/Select';
import InputError from '@/Components/InputError';
import { Head, Link, useForm } from '@inertiajs/react';

export default function Form({ project, purchaseOrder, suppliers, deliveryLocations, users }) {
    const isNew = !purchaseOrder;
    const title = isNew ? 'Add Purchase Order' : `Edit ${purchaseOrder.po_number ?? 'Purchase Order'}`;

    const { data, setData, post, put, processing, errors } = useForm({
        po_number: purchaseOrder?.po_number ?? '',
        supplier_id: purchaseOrder?.supplier_id ?? '',
        delivery_location_id: purchaseOrder?.delivery_location_id ?? '',
        assignee_user_id: purchaseOrder?.assignee_user_id ?? '',
        order_status: purchaseOrder?.order_status ?? 'draft',
        date_issued: purchaseOrder?.date_issued ?? '',
    });

    const submit = (e) => {
        e.preventDefault();
        isNew
            ? post(route('projects.purchase-orders.store', project.id))
            : put(route('projects.purchase-orders.update', [project.id, purchaseOrder.id]));
    };

    return (
        <AuthenticatedLayout title={title}>
            <Head title={title} />

            <div className="max-w-lg bg-white rounded-lg shadow p-6">
                <form onSubmit={submit} className="space-y-4">
                    <div className="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel htmlFor="po_number" value="PO Number" />
                            <TextInput
                                id="po_number"
                                className="mt-1 block w-full"
                                value={data.po_number}
                                onChange={(e) => setData('po_number', e.target.value)}
                                autoFocus
                            />
                            <InputError message={errors.po_number} className="mt-1" />
                        </div>

                        <div>
                            <InputLabel htmlFor="order_status" value="Status" />
                            <Select
                                id="order_status"
                                className="mt-1 block w-full"
                                value={data.order_status}
                                onChange={(e) => setData('order_status', e.target.value)}
                            >
                                <option value="draft">Draft</option>
                                <option value="sent">Sent</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="received">Received</option>
                            </Select>
                            <InputError message={errors.order_status} className="mt-1" />
                        </div>
                    </div>

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

                    <div className="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel htmlFor="delivery_location_id" value="Delivery Location" />
                            <Select
                                id="delivery_location_id"
                                className="mt-1 block w-full"
                                value={data.delivery_location_id}
                                onChange={(e) => setData('delivery_location_id', e.target.value)}
                            >
                                <option value="">— None —</option>
                                {deliveryLocations.map((d) => (
                                    <option key={d.id} value={d.id}>
                                        {d.name}
                                    </option>
                                ))}
                            </Select>
                            <InputError message={errors.delivery_location_id} className="mt-1" />
                        </div>

                        <div>
                            <InputLabel htmlFor="assignee_user_id" value="Assignee" />
                            <Select
                                id="assignee_user_id"
                                className="mt-1 block w-full"
                                value={data.assignee_user_id}
                                onChange={(e) => setData('assignee_user_id', e.target.value)}
                            >
                                <option value="">— None —</option>
                                {users.map((u) => (
                                    <option key={u.id} value={u.id}>
                                        {u.name}
                                    </option>
                                ))}
                            </Select>
                            <InputError message={errors.assignee_user_id} className="mt-1" />
                        </div>
                    </div>

                    <div>
                        <InputLabel htmlFor="date_issued" value="Date Issued" />
                        <TextInput
                            id="date_issued"
                            type="date"
                            className="mt-1 block w-full"
                            value={data.date_issued}
                            onChange={(e) => setData('date_issued', e.target.value)}
                        />
                        <InputError message={errors.date_issued} className="mt-1" />
                    </div>

                    <div className="flex justify-end gap-4">
                        <Link
                            href={route('projects.purchase-orders.index', project.id)}
                            className="px-4 py-2 text-gray-700 hover:text-gray-900"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            disabled={processing}
                            className="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-50"
                        >
                            {isNew ? 'Add Purchase Order' : 'Save Changes'}
                        </button>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
