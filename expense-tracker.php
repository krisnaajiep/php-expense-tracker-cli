<?php

if (
  !file_exists('expenses.json') ||
  is_null(json_decode(file_get_contents('expenses.json'), true))
) file_put_contents('expenses.json', '[]');

$file = json_decode(file_get_contents('expenses.json'), true);

$data = isset($file[0]['data'])
  ? json_decode(file_get_contents('expenses.json'), true)[0]['data']
  : [];

require_once 'functions.php';

if (isset($argv[1])) {
  switch ($argv[1]) {
    case 'add':
      echo add($argv);
      break;

    case 'update':
      echo update($argv);
      break;

    case 'delete':
      echo delete($argv);
      break;

    default:
      echo "Command not found\n";
      break;
  }
}
