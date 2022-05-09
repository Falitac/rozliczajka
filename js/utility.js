
/*
  Money in grosz
*/
function prettyPrintMoney(money) {
  money /= 100;
  return `${money.toFixed(2)} zł`;
}