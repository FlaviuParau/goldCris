/**
 * Creates a unique id for identification purposes.
 *
 * @param string separator The optional separator for grouping the generated segments: default "-"
 * @returns {*}
 */
var bgGenerateUid = function (separator) {

    var delim = separator || "-";

    function S4() {
        return (((1 + Math.random()) * 0x10000) | 0).toString(16).substring(1);
    }

    return (S4() + S4() + delim + S4() + S4());
};