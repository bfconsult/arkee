import { Link, router, usePage } from '@inertiajs/react';
import { useEffect, useRef, useState } from 'react';
import ApplicationLogo from '@/Components/ApplicationLogo';
import Avatar from '@/Components/Avatar';
import SiteNotice from '@/Components/SiteNotice';

export default function AuthenticatedLayout({ title, children }) {
    const { auth, projects, currentProject, currentUserRole, flash } = usePage().props;
    // Always available with no current project to fall back to. Otherwise
    // admin or approver - an approver is often a relatively hands-off
    // owner rather than a day-to-day worker, and needing a second account
    // just to add another project they own had no real reason behind it.
    const canAddProject = !currentProject || currentUserRole === 'admin' || currentUserRole === 'approver';

    const [showProjectMenu, setShowProjectMenu] = useState(false);
    const [showChangeList, setShowChangeList] = useState(false);
    const projectMenuRef = useRef(null);

    useEffect(() => {
        if (!showProjectMenu) return undefined;

        const closeOnOutsideClick = (e) => {
            if (projectMenuRef.current && !projectMenuRef.current.contains(e.target)) {
                setShowProjectMenu(false);
                setShowChangeList(false);
            }
        };
        document.addEventListener('mousedown', closeOnOutsideClick);
        return () => document.removeEventListener('mousedown', closeOnOutsideClick);
    }, [showProjectMenu]);

    const toggleProjectMenu = () => {
        // Expanded by default when the menu opens - Change is the more
        // common reason to open this at all, so requiring an extra tap to
        // reveal it defeated the point of having it in the menu.
        setShowChangeList(true);
        setShowProjectMenu((v) => !v);
    };

    const selectProject = (projectId) => {
        setShowProjectMenu(false);
        setShowChangeList(false);

        // Edit/Settings are pinned to a specific project by URL, not to
        // whichever one is "current" (correct - you shouldn't get yanked
        // onto a different project's data mid-edit just because the nav
        // switched) - but leaving it at that meant the nav showed the newly
        // selected project while the page underneath kept showing the old
        // one. Jump to the same kind of page for the new project instead.
        // Edit requires admin, which this user may not have there, so fall
        // back to Settings (open to any role) rather than a 403.
        const onProjectPage = route().current('projects.edit') || route().current('projects.show');
        if (onProjectPage) {
            const isAdminOnTarget = projects.find((p) => p.id === projectId)?.pivot?.type === 'admin';
            const targetRoute = route().current('projects.edit') && isAdminOnTarget
                ? 'projects.edit'
                : 'projects.show';

            router.post(route('project.select'), { project_id: projectId }, {
                onSuccess: () => router.visit(route(targetRoute, projectId)),
            });
            return;
        }

        router.post(route('project.select'), { project_id: projectId });
    };

    const addProject = () => {
        setShowProjectMenu(false);
        setShowChangeList(false);
        router.post(route('projects.store'));
    };

    return (
        <div className="min-h-screen bg-gray-100">
            <nav className="bg-white border-b border-gray-200 fixed top-0 left-0 right-0 z-10">
                <div className="flex items-center justify-between px-4 h-14">
                    {/* Left: logo + page title */}
                    <div className="flex items-center gap-2">
                        <ApplicationLogo className="h-6 w-6" />
                        {title && (
                            <span className="text-gray-900 font-semibold text-base">{title}</span>
                        )}
                    </div>

                    {/* Right: project picker. With only one project there's
                        nothing to switch to, so the name is a plain link
                        straight to its settings page. Otherwise it opens a
                        small menu (Settings / Change) instead, since "go to
                        settings" and "switch project" are both plausible
                        things to want from tapping the name and a single
                        link/arrow split was easy to hit by mistake. */}
                    <div className="flex items-center gap-3">
                    {projects.length === 0 ? (
                        <button onClick={addProject} className="text-sm text-green-600">
                            Add Project
                        </button>
                    ) : projects.length === 1 && currentProject ? (
                        <Link
                            href={route('projects.show', currentProject.id)}
                            className="text-sm font-medium text-green-700"
                        >
                            {currentProject.name}
                        </Link>
                    ) : (
                        <div className="relative flex items-center" ref={projectMenuRef}>
                            <button
                                type="button"
                                onClick={toggleProjectMenu}
                                aria-expanded={showProjectMenu}
                                className={`text-sm font-medium ${currentProject ? 'text-green-700' : 'text-gray-500'}`}
                            >
                                {currentProject ? currentProject.name : 'Select Project'}
                                <span className="ml-1 text-xs">▾</span>
                            </button>

                            {showProjectMenu && (
                                <div className="absolute right-0 top-full mt-1 w-52 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-20">
                                    {currentProject && (
                                        <Link
                                            href={route('projects.show', currentProject.id)}
                                            className="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                                        >
                                            Settings
                                        </Link>
                                    )}
                                    <button
                                        type="button"
                                        onClick={() => setShowChangeList((v) => !v)}
                                        aria-expanded={showChangeList}
                                        className="flex w-full items-center justify-between px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                                    >
                                        Change
                                        <span className="text-xs">{showChangeList ? '▴' : '▾'}</span>
                                    </button>
                                    {showChangeList && (
                                        <div className="pb-1">
                                            {projects
                                                .filter((project) => project.id !== currentProject?.id)
                                                .map((project) => (
                                                    <button
                                                        key={project.id}
                                                        type="button"
                                                        onClick={() => selectProject(project.id)}
                                                        className="block w-full py-2 pl-8 pr-4 text-left text-sm text-gray-700 hover:bg-gray-50"
                                                    >
                                                        {project.name}
                                                    </button>
                                                ))}
                                            {canAddProject && (
                                                <button
                                                    type="button"
                                                    onClick={addProject}
                                                    className="block w-full py-2 pl-8 pr-4 text-left text-sm text-green-600"
                                                >
                                                    + Add Project
                                                </button>
                                            )}
                                        </div>
                                    )}
                                </div>
                            )}
                        </div>
                    )}

                    <Link href={route('profile.edit')} aria-label="Account">
                        <Avatar user={auth.user} size="sm" />
                    </Link>
                    </div>
                </div>
            </nav>

            {/* Main content */}
            <main className="pt-14 px-4 pb-4">
                <SiteNotice className="-mx-4 mb-4" />

                {flash?.error && (
                    <div className="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                        {flash.error}
                    </div>
                )}
                {flash?.success && (
                    <div className="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
                        {flash.success}
                    </div>
                )}
                {children}
            </main>
        </div>
    );
}
