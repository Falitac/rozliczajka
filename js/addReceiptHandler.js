let usersInReceipt = [];

let receipt = null;
let shares = [];

if(receipt === null) {
  downloadReceiptJSON();
}

function fillTodayDate() {
  const formDate = document.querySelector("#form-receipt-date");
  let today = new Date().toJSON().slice(0,10);
  formDate.value = today;
}
/*
new QRCode(document.getElementById("qrcode"), {
  text: "||nr_bank|kwota_gr|odbiorca|tytul|||",
  width: 400,
  height: 400,
  colorDark : "#000",
  colorLight : "#fff",
  correctLevel : QRCode.CorrectLevel.M
});
*/

function convertToGrosz(value) {
  return Math.round(value * 100);
}

function convertToPLN(value) {
  return value / 100;
}

function onNumberChange(input) {
  let commaPos = input.value.indexOf('.');
  if(commaPos !== -1 && input.value.substring(commaPos).length > 3) {
    input.value = input.value.substring(0, commaPos + 3);
  }
}

function updateReceiptPrice(input) {
  updateReceipt('newPrice', convertToGrosz(input.value));
}

function changePayer() {
}

function updateReceipt(attribute, value) {
  let httpRequest = new XMLHttpRequest();

  httpRequest.onreadystatechange = () => {
    if(httpRequest.readyState == 4 && httpRequest.status == 200) {
      const output = document.querySelector('pre');
      output.innerHTML = httpRequest.responseText;

      if(attribute == 'saveToDatabase') {
        console.log(httpRequest.responseText);
        //window.location='./';
      }
      downloadReceiptJSON();
    }
  }
  httpRequest.open("GET", `updateReceipt.php?${attribute}=${value}`, true);
  httpRequest.send();
}

function findUsers(name) {
  let httpRequest = new XMLHttpRequest();
  httpRequest.onreadystatechange = () => {
    if(httpRequest.readyState == 4 && httpRequest.status == 200) {
      let userList = JSON.parse(httpRequest.responseText);

      if(userList.length === 1) {
        updateReceipt('newParticipant', userList[0]);
      }
    }
  }
  httpRequest.open("GET", `searchForPeople.php?name=${name}`, true);
  httpRequest.send();
}

function addPersonToList(textInput) {
  if(event.key !== 'Enter') {
    return;
  }
  findUsers(textInput.value);
  let tablePersonList = document.querySelector('#table-person-list');
  textInput.value = ''
}

function deleteUserFromList(something) {
}

function addItemToList(textInput) {
  if(event.key !== 'Enter') {
    return;
  }

  let itemName = document.querySelector('#input-item-name');
  updateReceipt('newItem', `${itemName.value};${convertToGrosz(textInput.value)}`);

  textInput.value = '';
  itemName.value = '';
  itemName.select();
}


function updatePersonTable() {
  let tablePersonList = document.querySelector('#table-person-list');
  let rowCount = tablePersonList.rows.length;
  for(let i = 1; i < rowCount - 1; i++) {
    tablePersonList.rows[1].remove();
  }

  for(let i = receipt.personList.length - 1; i >= 0; i--) {
    let id = receipt.personList[i];
    let newRow = tablePersonList.insertRow(1);

    const firstCell = newRow.insertCell();
    firstCell.innerHTML = i + 1;
    newRow.insertCell().innerHTML = receipt.userNames[id] + (i === 0 ? ' (Płatnik)' : '');
    newRow.insertCell().innerHTML = convertToPLN(receipt.shares[id]);

    let deleteCell = newRow.insertCell();
    deleteCell.innerHTML = 'Usuń';
    deleteCell.setAttribute('onclick', `updateReceipt('removeUser', ${id});`);
  }
}

function updateItemTable() {
  let tableItemList = document.querySelector('#table-item-list');

  let rowCount = tableItemList.rows.length;
  for(let i = 1; i < rowCount - 1; i++) {
    tableItemList.rows[1].remove();
  }

  for(let i = receipt.itemList.length - 1; i >= 0; i--) {
    let item = receipt.itemList[i];
    let newRow = tableItemList.insertRow(1);

    newRow.insertCell().innerHTML = item.name;
    const priceCell = newRow.insertCell();
    priceCell.innerHTML = convertToPLN(new Number(item.price));
    priceCell.classList.toggle('td-money');

    console.log(`item name ${item.name}`);
    let checkboxes = newRow.insertCell();
    for(let j = receipt.personList.length - 1; j >= 0; j--) {
      let checkbox = document.createElement('input');
      checkbox.setAttribute('type', 'checkbox');
      checkbox.classList.toggle('person-checkbox');
      checkbox.dataset.itemID = i;
      checkbox.dataset.payerID = receipt.personList[j];

      console.log(`person: ${receipt.personList[j]}`);
      console.log(`item payers: ${item.payers}`);


      checkbox.checked = item.payers.includes(receipt.personList[j]);
      console.log(`includes? ${item.payers.includes(receipt.personList[j])}`);
      //console.log(`checkbox state ${checkbox.checked}`);

      checkbox.setAttribute('onchange', `checkboxHandler(this);`);
      checkboxes.appendChild(checkbox);
    }

    let deleteCell = newRow.insertCell();
    deleteCell.innerHTML = 'Usuń';
    deleteCell.setAttribute('onclick', `updateReceipt('removeItem', ${i});`);

    newRow.dataset.itemID = i;
  }
}

function updateInfoPrice() {
  const priceInput = document.querySelector('#form-receipt-price');

  const serverReceiptPrice = convertToPLN(receipt.price);
  //priceInput.value = serverReceiptPrice;
}

function updateDescription() {
  const descriptionInput = document.querySelector('#receipt-description');
  descriptionInput.innerHTML = receipt.description;
}

function updateErrorInformer() {
  const errorInformer = document.querySelector('#error-informer');
  errorInformer.innerHTML = receipt.errorInformer;
}

function downloadReceiptJSON() {
  let httpRequest = new XMLHttpRequest();
  httpRequest.onreadystatechange = () => {
    if(httpRequest.readyState == 4 && httpRequest.status == 200) {
      receipt = JSON.parse(httpRequest.responseText);
      console.log(receipt);

      updateInfoPrice();
      updateDescription();
      updateItemTable();
      updatePersonTable();
      updateErrorInformer();
    }
  }
  httpRequest.open("GET", `updateReceipt.php?getJSON=1`, true);
  httpRequest.send();
}

function checkboxHandler(checkbox) {
  const operationData = `${checkbox.dataset.itemID};${checkbox.dataset.payerID}`;
  if(checkbox.checked) {
    console.log("set checkbox payerl");
    updateReceipt("setItemPayer", operationData);
    return;
  }
  updateReceipt("unsetItemPayer", operationData);
}

function receiptSubmitToDatabase(input) {
  updateReceipt('saveToDatabase', '1');
}

fillTodayDate();
downloadReceiptJSON();

