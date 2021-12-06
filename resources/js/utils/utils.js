export const utils = {
    /**
     * Convert date to 'yyyy-mm-dd' format
     * @param {*} date_str 
     */
    format_date(date_str) {
        let d = new Date(date_str);
        return `${d.getFullYear()}-${("0" + (d.getMonth() + 1)).slice(-2)}-${("0" + d.getDate()).slice(-2)}`;
    },
    get_validation_error(error_response) {
        const errors = Object.values(error_response.response.data.payload).reduce((prev, cur) => {
            return [...prev, ...cur];
        }, []);
        return errors;
    }
}
