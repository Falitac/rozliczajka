
function onPaySwitchCellClick(cell) {
  const row = cell.parentElement;

  const receiptId = row.dataset.receiptId;
  if(cell.classList.contains('paid')) {
    setReceiptPaid(receiptId, 0, cell);
  } else if(cell.classList.contains('not-paid')) {
    setReceiptPaid(receiptId, 1, cell);
  } else {
    console.error('No class paid or not-paid in this cell!');
  }
}

function setReceiptPaid(receiptId, receiptStatus, cell) {
  let httpRequest = new XMLHttpRequest();
  httpRequest.onreadystatechange = () => {
    if(httpRequest.readyState == 4 && httpRequest.status == 200) {
      console.log(httpRequest.responseText);
      if(httpRequest.responseText === "Successfull") {
        cell.classList.toggle("paid");
        cell.classList.toggle("not-paid");
      }
    }
  }
  httpRequest.open("GET", `updateDatabaseReceipt.php?receiptId=${receiptId}&status=${receiptStatus}`, true);
  httpRequest.send();
}