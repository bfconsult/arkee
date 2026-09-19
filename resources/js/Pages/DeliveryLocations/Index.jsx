import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import DataTable from '@/Components/DataTable';
import { Head } from '@inertiajs/react';

export default function Index({ deliveryLocations }) {
    return (
        <AuthenticatedLayout title="Delivery Locations">
            <Head title="Delivery Locations" />

            <DataTable
                rows={deliveryLocations}
                addRoute="delivery-locations.create"
                addLabel="Add Delivery Location"
                editRoute="delivery-locations.edit"
                destroyRoute="delivery-locations.destroy"
                emptyMessage="No delivery locations yet."
                columns={[
                    { key: 'name', label: 'Name' },
                    { key: 'address', label: 'Address' },
                ]}
            />
        </AuthenticatedLayout>
    );
}
