function parseKeyTextValue(text, key) {
  const stringLocation = text.search(key) + key.length;
  const numberEndLocation = text.search('\n', stringLocation);
  const numberString = text.substring(stringLocation, numberEndLocation);
  return new Number(numberString);
}

function onPaymentAlignment(data) {

  const request = new XMLHttpRequest();

  request.open("POST", "/paymentAlignment.php", true);
  request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  request.onreadystatechange = () => {
    if(request.status == 200 && request.readyState == 4) {
      if(request.responseText.includes("Success")) {
        let paymentsAffected = parseKeyTextValue(request.responseText, "changedPayments=");
        data.parentElement.innerHTML = `<td colspan="6">Spłacono łącznie <b>${paymentsAffected}</b> paragony, po odświeżeniu strony zobaczysz zmiany</td>`;
      }
    }
  };
  let postData = `person=${data.parentElement.cells[0].innerHTML}`;
  request.send(postData);
}