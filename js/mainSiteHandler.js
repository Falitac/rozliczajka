
//checkbox.setAttribute('onchange', `checkboxHandler(this);`);

console.log('test');
function onPaySwitchCellClick(cell) {
  const row = cell.parentElement;

  console.log(row.dataset.receiptId);
  cell.classList.toggle("paid");
  cell.classList.toggle("not-paid");
  

}