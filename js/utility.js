
function formatPLN(number) {
  return number.toLocaleString('pl-PL', { style: 'currency', currency: 'PLN' }).replace(',', '.');
}

function formatAllMoneyClasses() {
  let moneyClasses = document.querySelectorAll('.money');
  for(let i = 0; i < moneyClasses.length; i++) {
    moneyClasses[i].innerHTML = formatPLN(new Number(moneyClasses[i].innerHTML));
  }
}

formatAllMoneyClasses();