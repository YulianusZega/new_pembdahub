function decimalToHex(decimalStr) {
    const num = parseInt(decimalStr, 10);
    if (isNaN(num) || num <= 0) return decimalStr;
    let hex = num.toString(16).toUpperCase();
    while (hex.length < 8) hex = '0' + hex;
    return hex;
}
console.log(decimalToHex('3376680851'));
