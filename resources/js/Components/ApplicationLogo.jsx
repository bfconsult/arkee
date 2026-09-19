// Placeholder mark - swap for the real logo once one exists.
export default function ApplicationLogo(props) {
    return (
        <svg
            {...props}
            viewBox="0 0 40 40"
            xmlns="http://www.w3.org/2000/svg"
            role="img"
            aria-label="Logo"
        >
            <rect width="40" height="40" rx="8" fill="#111827" />
            <circle cx="20" cy="20" r="10" fill="white" />
        </svg>
    );
}
