
function onReceiptRowClick(row) {
  window.location.href = `./showReceipt.php?receiptID=${row.dataset.receiptId}`;
}
