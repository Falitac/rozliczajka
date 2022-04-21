<?php
session_start();

if(!isset($_SESSION['login-id'])) {
  header('Location: ./');
}

?>