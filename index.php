<?php

require_once 'init.php';

$data = isset($file[0]['data'])
  ? json_decode(file_get_contents('expenses.json'), true)[0]['data']
  : [];

require_once 'functions.php';

if (isset($argv[1]) && $argv[1] != '--help') {
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

    case 'list':
      echo read($argv);
      break;

    case 'summary':
      echo summary($data, $argv);
      break;

    case 'export':
      echo export($data);
      break;
  }
} elseif (!isset($argv[1]) || $argv[1] == '--help') {
  echo help();
}
