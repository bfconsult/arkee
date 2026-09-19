import GuestLayout from '@/Layouts/GuestLayout';
import { Head, router } from '@inertiajs/react';
import { useState } from 'react';

export default function GetStarted() {
    const [creating, setCreating] = useState(false);

    const createProject = () => {
        setCreating(true);
        router.post(route('projects.store'), {}, {
            onError: () => setCreating(false),
        });
    };

    const logout = () => {
        router.post(route('logout'));
    };

    return (
        <GuestLayout>
            <Head title="Get Started" />

            <div className="text-center py-4">
                <h1 className="text-xl font-semibold text-gray-900 mb-2">
                    Welcome
                </h1>
                <p className="text-sm text-gray-500 mb-6">
                    Everything here — including your team — belongs to a project.
                    Create yours to get started.
                </p>

                <button
                    onClick={createProject}
                    disabled={creating}
                    className="w-full py-3 bg-green-600 text-white rounded-lg font-medium disabled:opacity-50"
                >
                    {creating ? 'Creating…' : 'Create Project'}
                </button>

                <button
                    onClick={logout}
                    className="w-full mt-3 py-2 text-sm text-gray-400"
                >
                    Log Out
                </button>
            </div>
        </GuestLayout>
    );
}
