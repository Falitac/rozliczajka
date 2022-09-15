
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
      if(httpRequest.responseText === "Successfull") {
        cell.classList.toggle("paid");
        cell.classList.toggle("not-paid");

        updateDebtTableInfo();
      } else {
        console.error('can not update');
      }
    }
  }
  httpRequest.open("GET", `updateDatabaseReceipt.php?receiptId=${receiptId}&status=${receiptStatus}`, true);
  httpRequest.send();
}

function onReceiptRowClick(row) {
  window.location.href = `./showReceipt.php?receiptID=${row.dataset.receiptId}`;
}

function updateDebtTableInfo() {
  let httpRequest = new XMLHttpRequest();
  httpRequest.onreadystatechange = () => {
    if(httpRequest.readyState == 4 && httpRequest.status == 200) {
      const debtsInfo = document.querySelector('div.debts-info');
      debtsInfo.innerHTML = httpRequest.responseText;
    }
  };

  httpRequest.open("GET", `allDebts.php`, true);
  httpRequest.send();
}