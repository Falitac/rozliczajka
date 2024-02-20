let usersInReceipt = [];

let receipt = null;

if(receipt === null) {
  downloadReceiptJSON();
}

fillTodayDate();
downloadReceiptJSON();

function fillTodayDate() {
  const formDate = document.querySelector("#form-receipt-date");
  let today = new Date().toJSON().slice(0,10);
  formDate.value = today;
}

function convertToGrosz(value) {
  return Math.round(value * 100);
}

function convertToPLN(value) {
  return value / 100;
}

function truncateStringValueTo2Decimal(num) {
  let dotPos = num.indexOf('.');
  let commaPos = num.indexOf(',');
  
  if(dotPos === -1) {
    dotPos = commaPos;
  }
  if(dotPos === -1) {
    return num;
  }

  if(num.substring(dotPos).length > 3) {
    num = num.substring(0, dotPos + 3);
  }

  return num;
}

function onNumberChange(input) {
  input.value = truncateStringValueTo2Decimal(input.value)
}

function updateReceipt(attribute, value) {
  let httpRequest = new XMLHttpRequest();

  httpRequest.onreadystatechange = () => {
    if(httpRequest.readyState == 4 && httpRequest.status == 200) {
      const output = document.querySelector('pre');

      if(attribute == 'saveToDatabase') {
        console.log(httpRequest.responseText);
        window.location='./';
      }
      downloadReceiptJSON();
    }
  }
  httpRequest.open("GET", `updateReceipt.php?${attribute}=${value}`, true);
  httpRequest.send();
}

function performOCR() {
  updateReceipt("performOCR", "1");
}

function updateReceiptPrice(input) {
  updateReceipt('newPrice', convertToGrosz(input.value));
}

function updateItemPrice(itemID, newPrice) {
  const operationData = `${itemID};${newPrice}`;
  updateReceipt('setItemPrice', operationData);
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
  httpRequest.open("GET", `searchUsers.php?name=${name}`, true);
  httpRequest.send();
}

function addPersonToList(textInput) {
  if(event.key !== 'Enter') {
    return;
  }
  updateReceipt('newParticipant', textInput.value);
  //findUsers(textInput.value);
  let tablePersonList = document.querySelector('#table-person-list');
  textInput.value = ''
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
      formatAllMoneyClasses();
    }
  }
  httpRequest.open("GET", `updateReceipt.php?getJSON=1`, true);
  httpRequest.send();
}

function updateInfoPrice() {
  const priceInput = document.querySelector('#form-receipt-price');
  const serverReceiptPrice = convertToPLN(receipt.price);
}

function updateDescription() {
  const descriptionInput = document.querySelector('#receipt-description');
  descriptionInput.innerHTML = receipt.description;
}

function updateItemTable() {
  let tableItemList = document.querySelector('#table-item-list');

  let rowCount = tableItemList.rows.length;
  for(let i = 1; i < rowCount - 1; i++) {
    tableItemList.rows[1].remove();
  }
  tableItemList.rows[0].cells[2].setAttribute('colspan', receipt.personList.length);

  let restSum = receipt.price;

  for(let i = receipt.itemList.length - 1; i >= 0; i--) {
    let item = receipt.itemList[i];
    let newRow = tableItemList.insertRow(1);

    restSum -= item.price;

    const nameCell = newRow.insertCell();
    nameCell.innerHTML = item.name;
    nameCell.contentEditable = true;
    nameCell.addEventListener("input", (event) => {
      console.log(`Yo: ${event}`);
      console.log(event);

    }, false);

    const priceCell = newRow.insertCell();
    priceCell.innerHTML = convertToPLN(item.price);
    priceCell.classList.toggle('td-money');
    priceCell.classList.toggle('money');
    priceCell.contentEditable = true;
    priceCell.addEventListener('keydown', function(event) {
      if (event.key === 'Enter') {
          event.preventDefault();
          let newPrice = this.innerText.replace(/[^\d.]/g, '');
          newPrice = truncateStringValueTo2Decimal(newPrice).replace('.', '');
          updateItemPrice(i, newPrice);
      }
    });

    let participantItemPrice = 0;
    if(item.payers.length !== 0) {
      participantItemPrice = convertToPLN(Math.floor(item.price / item.payers.length));
    }

    for(let j = 0; j < receipt.personList.length; j++) {
      const checkbox = newRow.insertCell();
      checkbox.classList.toggle('money');
      checkbox.classList.toggle('td-money');
      checkbox.innerHTML = 0;
      checkbox.style.color = 'white';
      checkbox.style.width = '70px';
      checkbox.style.borderRight = checkbox.style.borderLeft = '1px #fff solid';

      const includesThisPerson = item.payers.includes(receipt.personList[j])
      if(includesThisPerson) {
        checkbox.innerHTML = participantItemPrice;
        checkbox.style.color = 'var(--good-col)';
        checkbox.classList.toggle('item-checkbox-active');
      }

      checkbox.dataset.itemID = i;
      checkbox.dataset.payerID = receipt.personList[j];
      checkbox.setAttribute('onclick', 'checkboxHandler(this)');
    }

    let deleteCell = newRow.insertCell();
    deleteCell.innerHTML = 'Usuń';
    deleteCell.setAttribute('onclick', `updateReceipt('removeItem', ${i});`);

    newRow.dataset.itemID = i;
  }

  const division = convertToPLN(Math.floor(restSum / receipt.personList.length));

  const lastRow = tableItemList.insertRow(1);
  lastRow.insertCell().innerHTML = "<b>Reszta przedmiotów</b>";

  const priceCell = lastRow.insertCell();
  priceCell.innerHTML = convertToPLN(restSum);
  priceCell.classList.toggle('money');
  priceCell.classList.toggle('td-money');

  const divisionCell = lastRow.insertCell();
  divisionCell.innerHTML = division;
  divisionCell.setAttribute('colspan', receipt.personList.length);
  divisionCell.classList.toggle('money');

  lastRow.insertCell();
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
    const moneyCell = newRow.insertCell();
    moneyCell.innerHTML = receipt.shares[id] / 100;
    moneyCell.classList.toggle('money');

    let deleteCell = newRow.insertCell();
    deleteCell.innerHTML = 'Usuń';
    deleteCell.setAttribute('onclick', `updateReceipt('removeUser', ${id});`);
  }
}

function updateErrorInformer() {
  const errorInformer = document.querySelector('#error-informer');
  errorInformer.innerHTML = receipt.errorInformer;
}

function checkboxHandler(checkbox) {
  const operationData = `${checkbox.dataset.itemID};${checkbox.dataset.payerID}`;
  if(!checkbox.classList.contains('item-checkbox-active')) {
    updateReceipt("setItemPayer", operationData);
    return;
  }
  updateReceipt("unsetItemPayer", operationData);
}

function receiptSubmitToDatabase(input) {
  updateReceipt('saveToDatabase', '1');
}

