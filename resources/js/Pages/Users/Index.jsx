import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import DataTable from '@/Components/DataTable';
import { Head, router } from '@inertiajs/react';

const ROLE_LABELS = {
    pm: 'Project Manager',
    admin: 'Administrator',
    read_only: 'Read Only',
};

export default function Index({ users }) {
    const approve = (user) => {
        router.patch(route('users.approve', user.id));
    };

    return (
        <AuthenticatedLayout title="Users">
            <Head title="Users" />

            <DataTable
                rows={users}
                addRoute="users.create"
                addLabel="Add User"
                editRoute="users.edit"
                destroyRoute="users.destroy"
                emptyMessage="No users yet."
                renderRowExtra={(row) =>
                    !row.active && (
                        <button
                            type="button"
                            onClick={() => approve(row)}
                            className="text-green-700 hover:text-green-900 mr-4 font-medium"
                        >
                            Approve
                        </button>
                    )
                }
                columns={[
                    { key: 'name', label: 'Name' },
                    { key: 'email', label: 'Email' },
                    {
                        key: 'role',
                        label: 'Role',
                        render: (row) => ROLE_LABELS[row.role] ?? row.role,
                    },
                    {
                        key: 'active',
                        label: 'Status',
                        render: (row) => (
                            <span
                                className={
                                    row.active
                                        ? 'inline-block px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800'
                                        : 'inline-block px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800'
                                }
                            >
                                {row.active ? 'Active' : 'Pending'}
                            </span>
                        ),
                    },
                ]}
            />
        </AuthenticatedLayout>
    );
}
