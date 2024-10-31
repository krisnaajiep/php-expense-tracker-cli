<?php

if (
  !file_exists('expenses.json') ||
  is_null(json_decode(file_get_contents('expenses.json'), true))
) file_put_contents('expenses.json', '[]');

$file = json_decode(file_get_contents('expenses.json'), true);

if (isset($argv[1]) && !isset($file[0]['budgetPerMonth'])) {
  if ($argv[1] != 'set' || $argv[1] == 'set' && (!isset($argv[2]) || $argv[2] != '--budget')) {
    echo "# Please insert budget per month\n# Usage: php index.php set --budget <budget>";
    exit;
  } else {
    if (isset($argv[3]) && is_numeric($argv[3]) && $argv[3] > 0) {

      array_push($file, [
        'budgetPerMonth' => $argv[3],
        'data' => [],
      ]);

      file_put_contents('expenses.json', json_encode($file, JSON_PRETTY_PRINT));

      echo "# Your budget per month is $$argv[3]";
    } else {
      echo "# Invalid budget\n# Budget must be numeric and positive";
      exit;
    }
  }
}
