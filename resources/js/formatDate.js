const MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

// Dates come from the backend as plain calendar dates (midnight UTC), so we
// read the UTC fields rather than local ones - using local getDate() etc.
// would roll the day back by one in timezones behind UTC.
export default function formatDate(value) {
    if (!value) return null;

    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return value;

    const day = String(date.getUTCDate()).padStart(2, '0');
    const month = MONTHS[date.getUTCMonth()];
    const year = String(date.getUTCFullYear()).slice(-2);

    return `${day} ${month} ${year}`;
}
