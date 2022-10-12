<?php
  include_once('database.php');
  require_once('utility.php');
  if(!isSessionStarted()) {
    session_start();
  }
  $debts = extractUserDebtDifference($_SESSION['login-id']);
  if(count($debts) > 0) {
?>

<h2>Długi:</h2>
<table>
  <tr>
    <th>Komu</th>
    <th>Wisisz mu</th>
    <th>Tobie wisi</th>
    <th>Różnica</th>
    <th>Akcja</th>
  </tr>
  <?php
    $sums = [];
    foreach($debts as $person => $value) {
      $colorIndicator = function ($value) {
        return $value == 0 ? '#efef51' : ($value > 0 ? '#60d32a' : '#ff692e');
      };
      foreach($value as $entry => $sum) {
        if(isset($sums[$entry])) {
          $sums[$entry] += $sum;
        } else {
          $sums[$entry] = $sum;
        }
      }
  ?>
      <tr>
        <td><?=$person?></td>
        <td class="money td-money"
        style="color:<?= $colorIndicator($value['Your']);?>;"
        ><?=$value['Your'] / 100?></td>
        <td class="money td-money"
        style="color:<?= $colorIndicator($value['His'])?>;"
        ><?=$value['His'] / 100?></td>
        <td class="money td-money"
        style="color:<?= $colorIndicator($value['Diff'])?>;"
        ><?=$value['Diff'] / 100?></td>
        <?php
          if($value['Diff'] < 0) {
        ?>
          <td></td>
        <?php } else {?>
          <td class="alignment" onclick="event.cancelBubble=true;onPaymentAlignment(this);">Wyrównaj długi</td>
        <?php }?>
      </tr>
  <?php } ?>
  <tr>
    <td><b>Ogółem:</b></td>
    <td class="money td-money"
    style="color:<?= $colorIndicator($sums['Your'])?>;"
    ><?=$sums['Your'] / 100?></td>
    <td class="money td-money"
    style="color:<?= $colorIndicator($sums['His'])?>;"
    ><?=$sums['His'] / 100?></td>
    <td class="money td-money"
    style="color:<?= $colorIndicator($sums['Diff'])?>;"
    ><?=$sums['Diff'] / 100?></td>
    <td></td>
  </tr>
</table>
<script src="js/paymentAlignmentHandler.js"></script>

<?php
  }

function extractUserDebtDifference($id) {
  $userCreditors = extractUserCreditors($id);
  $userDebtors   = extractUserDebtors($id);
  $result = calculateDebtDifference($userCreditors, $userDebtors);

  return $result;
}

function extractUserCreditors($id) {
  global $pdo;

  $query= "SELECT owner.name AS Creditor, sum(payments.value) AS Suma
    FROM receipts
    INNER JOIN payments
    ON payments.receipt_id=receipts.id
    INNER JOIN users AS owner
    ON owner.id=receipts.payer_id
    WHERE
      payments.user_id = :id
    AND
      payments.paid = 0
    GROUP BY receipts.payer_id, payments.user_id
  ";
  try {
    $result = $pdo->prepare($query);
    $result->execute(array('id' => $id));
  } catch (\Throwable $th) {
    print_r($th);
  }

  $userCreditors = [];
  while($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $userCreditors[$row['Creditor']] = $row['Suma'];
  }
  return $userCreditors;
}

function extractUserDebtors($id) {
  global $pdo;

  $query= "SELECT payer.name AS Debtor, sum(payments.value) AS Suma
    FROM receipts
    INNER JOIN payments
    ON payments.receipt_id=receipts.id
    INNER JOIN users AS owner
    ON owner.id=receipts.payer_id
    INNER JOIN users AS payer
    ON payer.id=payments.user_id
    WHERE
      receipts.payer_id = :id
    AND
      payments.paid = 0
    GROUP BY receipts.payer_id, payments.user_id
  ";

  try {
    $result = $pdo->prepare($query);
    $result->execute(array('id' => $id));
  } catch (\Throwable $th) {
    print_r($th);
  }

  $userDebtors = [];
  while($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $userDebtors[$row['Debtor']] = $row['Suma'];
  }

  return $userDebtors;
}

function calculateDebtDifference($userCreditors, $userDebtors) {
  $result = [];

  foreach($userDebtors as $debtor => $value) {
    $result[$debtor]['His'] = $value;
    $result[$debtor]['Your'] = 0;
    $result[$debtor]['Diff'] = $result[$debtor]['His'];
  }
  foreach($userCreditors as $creditor => $value) {
    if(!isset($result[$creditor])) {
      $result[$creditor]['His'] = 0;
      $result[$creditor]['Diff'] = 0;
    }
    $result[$creditor]['Your'] = -$value;
    $result[$creditor]['Diff'] += $result[$creditor]['Your'];
  }
  return $result;
}

?>
