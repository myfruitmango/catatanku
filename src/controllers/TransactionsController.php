<?php

include '../../mainconfig.php';

if (isset($_POST['request'])) {
  switch ($_GET['type']) {
    case "createTransaction":
      // * create transaction
      $type         = isset($_POST['type']) ? trim($_POST['type']) : '';
      $date         = isset($_POST['date']) ? trim($_POST['date']) : '';
      $name         = isset($_POST['name']) ? trim($_POST['name']) : '';
      $total        = isset($_POST['total']) ? preg_replace("/[^0-9]/", "", $_POST['total']) : '0';
      $account      = isset($_POST['account']) ? trim($_POST['account']) : '';
      $category     = isset($_POST['category']) ? trim($_POST['category']) : '';
      $description  = isset($_POST['description']) ? trim($_POST['description']) : '';
      if (empty($type)) {
        header('Location:../views/pages/transaction?status=400&message=Harap Memilih Jenis Transaksi');
        exit;
      }
      if (empty($date)) {
        header('Location:../views/pages/transaction?status=400&message=Harap Memasukkan Tanggal Transaksi');
        exit;
      }
      if (empty($name)) {
        header('Location:../views/pages/transaction?status=400&message=Harap Memasukkan Nama Transaksi');
        exit;
      }
      if (empty($total)) {
        header('Location:../views/pages/transaction?status=400&message=Harap Memasukkan Total');
        exit;
      }
      if (empty($account)) {
        header('Location:../views/pages/transaction?status=400&message=Harap Memilih Akun Transaksi');
        exit;
      }
      if (empty($category)) {
        header('Location:../views/pages/transaction?status=400&message=Harap Memilih Kategori Transaksi');
        exit;
      }
      $dateExplode      = explode('-', $date);
      $dateTransaction  = $dateExplode[2] . '-' . $dateExplode[1] . '-' . $dateExplode[0];

      $account      = mysqli_query($call, "SELECT * FROM accounts WHERE uuid='$account'");
      $dataAccount  = mysqli_fetch_array($account);
      if ($dataAccount) {
        if ($type === "Income") {
          $result = intval($data['balance'] + $total);

          $updateAccount      = mysqli_query(
            $call,
            "UPDATE accounts
            SET
              balance='$result',
              updated_at='$dtme'
            WHERE uuid='$account'"
          );
          $createTransaction  = mysqli_query(
            $call,
            "INSERT INTO transactions (
              uuid,
              site_account,
              site_category,
              name,
              total,
              type,
              description,
              transaction_at,
              created_at
            ) VALUE (
              UUID(),
              '$account',
              '$category',
              '$name',
              '$total',
              '$type',
              NULLIF('$description', ''),
              '$dateTransaction',
              '$dtme'
            )"
          );
          if ($updateAccount && $createTransaction) {
            header('Location:../views/pages/transaction?status=200&message=successfully');
          } else {
            header('Location:../views/pages/transaction?status=500&message=null');
          }
        } else {
          if ($data['balance'] > $total) {
            $result = intval($data['balance'] - $total);

            $updateAccount      = mysqli_query(
              $call,
              "UPDATE accounts
            SET
              balance='$result',
              updated_at='$dtme'
            WHERE uuid='$account'"
            );
            $createTransaction  = mysqli_query(
              $call,
              "INSERT INTO transactions (
                uuid,
                site_account,
                site_category,
                name,
                total,
                type,
                description,
                transaction_at,
                created_at
              ) VALUE (
                UUID(),
                '$account',
                '$category',
                '$name',
                '$total',
                '$type',
                NULLIF('$description', ''),
                '$dateTransaction',
                '$dtme'
              )"
            );
            if ($updateAccount && $createTransaction) {
              header('Location:../views/pages/transaction?status=200&message=successfully');
            } else {
              header('Location:../views/pages/transaction?status=500&message=null');
            }
          } else {
            header('Location:../views/pages/transaction?status=400&message=Saldo Tidak Mencukupi');
          }
        }
      } else {
        header('Location:../views/pages/transaction?status=404&message=not found');
      }
      break;
    default:
      header('Location:../views/pages/transaction?status=404&message=not found type');
  }
} else {
  header('Location:../views/pages/transaction?status=500&message=bad request');
}
