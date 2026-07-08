const eventEnd = "2026-12-19";
let endDate = new Date(eventEnd);
endDate.setDate(endDate.getDate() - 1);
console.log(endDate.toISOString().split('T')[0]);
