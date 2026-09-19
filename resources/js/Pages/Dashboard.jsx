import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function Dashboard() {
    return (
        <AuthenticatedLayout title="Dashboard">
            <Head title="Dashboard" />

            <div className="bg-white rounded-lg shadow p-6 text-gray-900">
                You're logged in!
            </div>
        </AuthenticatedLayout>
    );
}
