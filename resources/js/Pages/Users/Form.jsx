import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import PasswordInput from '@/Components/PasswordInput';
import Select from '@/Components/Select';
import InputError from '@/Components/InputError';
import { Head, Link, useForm } from '@inertiajs/react';

export default function Form({ targetUser }) {
    const isNew = !targetUser;
    const title = isNew ? 'Add User' : `Edit ${targetUser.name}`;

    const { data, setData, post, put, processing, errors } = useForm({
        name: targetUser?.name ?? '',
        email: targetUser?.email ?? '',
        role: targetUser?.role ?? 'pm',
        active: targetUser?.active ?? true,
        password: '',
        password_confirmation: '',
    });

    const submit = (e) => {
        e.preventDefault();
        isNew ? post(route('users.store')) : put(route('users.update', targetUser.id));
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
                        <InputLabel htmlFor="email" value="Email" />
                        <TextInput
                            id="email"
                            type="email"
                            className="mt-1 block w-full"
                            value={data.email}
                            onChange={(e) => setData('email', e.target.value)}
                        />
                        <InputError message={errors.email} className="mt-1" />
                    </div>

                    <div className="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel htmlFor="role" value="Role" />
                            <Select
                                id="role"
                                className="mt-1 block w-full"
                                value={data.role}
                                onChange={(e) => setData('role', e.target.value)}
                            >
                                <option value="pm">Project Manager</option>
                                <option value="admin">Administrator</option>
                                <option value="read_only">Read Only</option>
                            </Select>
                            <InputError message={errors.role} className="mt-1" />
                        </div>

                        <div className="flex items-end pb-2">
                            <label className="flex items-center gap-2">
                                <input
                                    type="checkbox"
                                    checked={data.active}
                                    onChange={(e) => setData('active', e.target.checked)}
                                    className="rounded border-gray-300 text-green-600 focus:ring-green-500"
                                />
                                <span className="text-sm text-gray-700">Active</span>
                            </label>
                        </div>
                    </div>

                    <div className="border-t pt-4">
                        <InputLabel
                            htmlFor="password"
                            value={isNew ? 'Password' : 'New Password (leave blank to keep current)'}
                        />
                        <PasswordInput
                            id="password"
                            className="mt-1"
                            value={data.password}
                            onChange={(e) => setData('password', e.target.value)}
                            autoComplete="new-password"
                        />
                        <InputError message={errors.password} className="mt-1" />
                    </div>

                    <div>
                        <InputLabel htmlFor="password_confirmation" value="Confirm Password" />
                        <PasswordInput
                            id="password_confirmation"
                            className="mt-1"
                            value={data.password_confirmation}
                            onChange={(e) => setData('password_confirmation', e.target.value)}
                            autoComplete="new-password"
                        />
                        <InputError message={errors.password_confirmation} className="mt-1" />
                    </div>

                    <div className="flex justify-end gap-4">
                        <Link
                            href={route('users.index')}
                            className="px-4 py-2 text-gray-700 hover:text-gray-900"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            disabled={processing}
                            className="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-50"
                        >
                            {isNew ? 'Add User' : 'Save Changes'}
                        </button>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
