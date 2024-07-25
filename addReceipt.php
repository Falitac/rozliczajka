<?php
  session_start();
  require_once('checkIfLogged.php');
  require_once('database.php');
  require_once('utility.php');
  require_once('receipt.php');

  $newReceipt;
  if(!isset($_SESSION['new-receipt'])) {
    $newReceipt = new Receipt();
    $currentAccount = new Account();
    $currentAccount->setFromDatabaseByID($_SESSION['login-id']);
    $newReceipt->addPayer($currentAccount);
    $_SESSION['new-receipt'] = serialize($newReceipt);
  } else {
    $newReceipt = unserialize($_SESSION['new-receipt']);
  }

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Rozliczajka</title>
  <link href="css/main.css" rel="stylesheet">
  <link rel="icon" type="image/x-icon" href="/img/favicon2.ico">
</head>
<body>
  <main>
    <header>
      <h1>Dodaj paragon</h1>
    </header>
    <div class="content">
      <a href="./" style="float: right;">Powrót do głównej</a><br>
      <div class="receipt-info">
        <div class="receipt-left-side">
          <form>
            <div class="container-price-date">
              <div class="receipt-price-item">
                <label>
                  <h2>Kwota:</h2>
                </label>
                <input type="number" id="form-receipt-price" inputmode="decimal" value="<?=$newReceipt->getPrice()/100?>" onblur="onNumberChange(this);updateReceiptPrice(this)" onclick="this.select();"></input>
              </div>
              <div class="receipt-date-item">
                <label>
                  <h2>Data:</h2>
                </label>
                <input type="date" id="form-receipt-date" onblur="updateReceipt('newDate', this.value)"></input>
              </div>
            </div>
            <label>
              <h2>Lista osób:</h2>
            </label>
            <table id="table-person-list">
              <tr>
                <th>Nr</th>
                <th>Osoba</th>
                <th>Dług</th>
                <th>Akcja</th>
              </tr>
              <tr>
                <td colspan="4" style="padding: 0;">
                  <div class="autocomplete">
                    <input class="table-text-input" onkeydown="addPersonToList(this);" type="text" placeholder="Dodaj osobę" autocomplete="off"></input>
                  </div>
                </td>
              </tr>
            </table>
            <textarea id="receipt-description" onchange="updateReceipt('setDescription', encodeURIComponent(this.value));" placeholder="Opis paragonu, przedmiotów, uwagi"></textarea>
            <div id="error-informer"></div>
          </form>
        </div>
        <div class="receipt-right-side">
          <h2>Przedmioty</h2>
          <table id="table-item-list">
            <tr>
              <th>Przedmiot</th>
              <th>Cena</th>
              <th>Płatnicy</th>
              <th>Akcja</th>
            </tr>
            <tr>
              <td style="padding: 0; height: 3em;">
                <input type="text" id="input-item-name" class="table-input" placeholder="Nazwa"></input>
              </td>
              <td style="padding: 0; height: 3em;">
                <input type="number"class="table-input" placeholder="Cena" onkeydown="addItemToList(this);" oninput="onNumberChange(this);"></input>
              </td>
            </tr>
          </table>
        </div>
      </div>
      <div class="image-section">
        <input type="button" value="Zatwierdź zdjęcie" onclick="uploadImageReceipt();"/>
        <label for="upload-image-receipt" class="receipt-image-upload">
          Wybierz zdjęcie (JPG, PNG)
        </label>
          <input
            id="upload-image-receipt"
            type="file"
            name="receipt-image"
            accept="image/*"/>
        <input type="button" value="Wykonaj OCR" onclick="performOCR();"/>
        <div class="receipt-preview">
          <p>Brak zdjęcia</p>
        </div>
        <input id="submit-save-receipt" type="submit" value="Zapisz w bazie" onclick="receiptSubmitToDatabase(this);">
      </div>
      <pre id="php-output">
      </pre>
    </div>
  </main>
  <script src="js/uploadImageHandler.js"></script>
  <script src="js/addReceiptHandler.js"></script>
  <script src="js/AsyncDatabase.js"></script>
  <script src="js/autocomplete.js"></script>
  <script src="js/utility.js"></script>
  <script>
    const input = document.querySelector('.table-text-input');
    Autocomplete.registerInput(input);

    let onInput = event => {
      AsyncDatabase.requestUserList(input.value).then((value) => {
        let userList = JSON.parse(value);
        Autocomplete.autocomplete(input, userList);
      });
    };

    input.addEventListener('input', onInput);
  </script>
</body>
</html>