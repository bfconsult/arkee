export default function NeedsFinishesIcon({ className = '' }) {
    return (
        <svg
            className={`inline-block w-4 h-4 text-amber-500 ${className}`}
            viewBox="0 0 20 20"
            fill="currentColor"
            role="img"
        >
            <title>Missing Material/Finish selections</title>
            <path
                fillRule="evenodd"
                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l6.518 11.598c.75 1.334-.213 2.986-1.742 2.986H3.48c-1.53 0-2.493-1.652-1.743-2.986L8.257 3.1zM11 13a1 1 0 10-2 0 1 1 0 002 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                clipRule="evenodd"
            />
        </svg>
    );
}
