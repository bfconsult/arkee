import Spinner from '@/Components/Spinner';
import { router } from '@inertiajs/react';
import { useRef, useState } from 'react';

export default function ItemImages({ item }) {
    const fileInput = useRef(null);
    const [uploading, setUploading] = useState(false);

    const pickFile = () => fileInput.current?.click();

    const selectFile = (e) => {
        const file = e.target.files?.[0];
        e.target.value = '';
        if (!file) return;

        setUploading(true);

        const formData = new FormData();
        formData.append('image', file);

        router.post(route('items.attachments.store', item.id), formData, {
            forceFormData: true,
            preserveScroll: true,
            onFinish: () => setUploading(false),
        });
    };

    const remove = (attachment) => {
        if (confirm('Remove this image?')) {
            router.delete(route('items.attachments.destroy', [item.id, attachment.id]), { preserveScroll: true });
        }
    };

    return (
        <div className="bg-white rounded-lg shadow p-6 mb-6">
            <div className="flex justify-between items-center mb-4">
                <h2 className="text-lg font-semibold text-gray-900">Images</h2>
                <button
                    type="button"
                    onClick={pickFile}
                    disabled={uploading}
                    className="flex items-center gap-1.5 px-3 py-1.5 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 disabled:opacity-50"
                >
                    {uploading && <Spinner className="h-4 w-4" />}
                    {uploading ? 'Uploading...' : 'Add Image'}
                </button>
                <input ref={fileInput} type="file" accept="image/*" onChange={selectFile} className="hidden" />
            </div>

            {item.attachments.length === 0 ? (
                <p className="text-gray-500 text-sm">No images yet.</p>
            ) : (
                <div className="flex flex-wrap gap-4">
                    {item.attachments.map((attachment) => (
                        <div key={attachment.id} className="relative group">
                            <img
                                src={attachment.file_url}
                                alt=""
                                className="h-32 w-auto max-w-full object-contain rounded-md border border-gray-200 bg-gray-50"
                            />
                            <button
                                type="button"
                                onClick={() => remove(attachment)}
                                className="absolute top-1 right-1 bg-white/90 text-red-600 rounded-full w-6 h-6 flex items-center justify-center text-sm shadow opacity-0 group-hover:opacity-100 transition-opacity"
                                aria-label="Remove image"
                            >
                                ×
                            </button>
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
}
