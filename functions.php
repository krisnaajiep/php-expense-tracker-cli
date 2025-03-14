<?php

$file = json_decode(file_get_contents('expenses.json'), true);

/**
 * Add new expense.
 *
 * @param array $argv
 * @return string
 */
function add(array $argv): string
{
  for ($i = 2; $i <= 7; $i++) {
    if (count($argv) < 7 || $argv[2] != '--description' || $argv[4] != '--amount' || $argv[6] != '--category')
      return "# Invalid input\n# Usage: php expense-tracker.php add --description <description> --amount <amount> --category <category>\n";
  }

  if (!is_numeric($argv[5]) || $argv[5] < 1)
    return "# Invalid amount\n# Amount must be numeric and positive\n";

  global $data, $file;

  $checkBudget = checkBudget($data, $file, $argv);
  if (!is_null($checkBudget)) return $checkBudget;

  $id = count($data) + 1;

  foreach ($data as $expense) {
    if ($expense['id'] == $id) $id++;
  }

  array_push($data, [
    'id' => $id,
    'description' => $argv[3],
    'amount' => $argv[5],
    'category' => $argv[7],
    'created_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s'),
  ]);

  $file[0]['data'] = $data;
  file_put_contents('expenses.json', json_encode($file, JSON_PRETTY_PRINT));

  return "# Expense added successfully (ID: $id)\n";
}

/**
 * Update an expense.
 *
 * @param array $argv
 * @return string
 */
function update(array $argv): string
{
  global $data, $file;

  for ($i = 2; $i <= 5; $i++) {
    if (count($argv) < 5 || $argv[2] != '--id' || !in_array($argv[4], ['--description', '--amount', '--category']))
      return "# Invalid input\n\n# Usage:\n# php expense-tracker.php update --id <id> --description <description>\n# php expense-tracker.php update --id <id> --amount <amount>\n# php expense-tracker.php update --id <id> --category <category>\n";
  }

  if ($argv[4] == '--amount' && (!is_numeric($argv[5]) || $argv[5] < 1))
    return "# Invalid amount\n# Amount must be numeric and positive\n";

  foreach ($data as $key => $value) {
    if ($value['id'] == $argv[3]) {
      $id = $argv[3];
      switch (true) {
        case ($argv[4] == '--description'):
          $data[$key]['description'] = $argv[5];
          break;

        case ($argv[4] == '--amount'):
          $data[$key]['amount'] = $argv[5];
          $checkBudget = checkBudget($data, $file, $argv);
          if (!is_null($checkBudget)) return $checkBudget;
          break;

        case ($argv[4] == '--category'):
          $data[$key]['category'] = $argv[5];
          break;
      }

      $data[$key]['updated_at'] = date('Y-m-d H:i:s');

      $file[0]['data'][$key] = $data[$key];
      file_put_contents('expenses.json', json_encode($file, JSON_PRETTY_PRINT));

      return "# Expense updated successfully (ID: $id)\n";
    }
  }

  return "# Id not found\n";
}

/**
 * Delete an expense.
 *
 * @param array $argv
 * @return string
 */
function delete(array $argv): string
{
  if (!isset($argv[2]) || $argv[2] != '--id')
    return "# Task id required\n# Usage: php expense-tracker.php delete --id <id>\n";

  global $data, $file;

  foreach ($data as $key => $value) {
    if ($value['id'] == $argv[3]) {
      $id = $argv[3];
      unset($data[$key]);
      $data = array_values($data);
      $file[0]['data'] = $data;
      file_put_contents('expenses.json', json_encode($file, JSON_PRETTY_PRINT));

      return "# Expense deleted successfully (ID: $id)\n";
    }
  }

  return "# Id not found\n";
}

/**
 * Formats a single expense entry into a readable string for display.
 *
 * @param array $expense
 * @return string
 */
function fetch(array $expense): string
{
  $date = explode(' ', $expense['created_at'])[0];
  $description = strlen($expense['description']) > 22
    ? substr($expense['description'], 0, 20) . '...'
    : $expense['description'];
  $category = strlen($expense['category']) > 12
    ? substr($expense['category'], 0, 10) . '...'
    : $expense['category'];
  $row = "# " . str_pad($expense['id'], 5) . str_pad($date, 15) . str_pad($description, 25) . '$' . str_pad($expense['amount'], 10) . str_pad($category, 15);

  return $row;
}

/**
 * Display all expenses or by a specific category.
 *
 * @param array $argv
 * @return string
 */
function show(array $argv): string
{
  global $data;

  $list = ["# " . str_pad('ID', 5) . str_pad('Date', 15) . str_pad('Description', 25) . str_pad('Amount', 10) . str_pad('Category', 15)];

  foreach ($data as $expense) {
    if (isset($argv[2]) && $argv[2] == '--category') {
      if (isset($argv[3]) && $expense['category'] == $argv[3]) {
        array_push($list, fetch($expense));
      } elseif (!isset($argv[3])) {
        return "# Invalid input\n# Usage: php expense-tracker.php list --category <category>\n";
      }
    } else {
      array_push($list, fetch($expense));
    }
  }

  $list[count($list) - 1] .= "\n";
  return implode("\n", $list);
}

/**
 * View a summary of all expenses or by specific month.
 *
 * @param array $data
 * @param array $argv
 * @return string
 */
function summary(array $data, array $argv): string
{
  $summary = 0;
  $forMonth = '';

  if (!is_null($data)) {
    foreach ($data as $expense) {
      if (isset($argv[2]) && $argv[2] == '--month') {
        $date = explode(' ', $expense['created_at'])[0];
        $year = explode('-', $date)[0];
        $month = explode('-', $date)[1];

        if ($argv[3] > 12) return "# Invalid month\n";

        if ($year == date('Y') && intval($month) == $argv[3])
          $summary += intval($expense['amount']);

        $forMonth = ' for ' . date_format(date_create_from_format('n-d', $argv[3] . '-01'), 'F');
      } else {
        $summary += intval($expense['amount']);
      }
    }
  }

  return "# Total expenses$forMonth: $$summary\n";
}

/**
 * Set a budget for each month.
 *
 * @param array $argv
 * @return string
 */
function setBudget(array $argv): string
{
  global $file;

  if ($argv[1] != 'set' || $argv[1] == 'set' && (!isset($argv[2]) || $argv[2] != '--budget')) {
    return "# Please insert budget per month\n# Usage: php index.php set --budget <budget>\n";
  } else {
    if (isset($argv[3]) && is_numeric($argv[3]) && $argv[3] > 0) {
      if (isset($file[0]['budgetPerMonth'])) {
        $file[0]['budgetPerMonth'] = $argv[3];
      } else {
        array_push($file, [
          'budgetPerMonth' => $argv[3],
          'data' => [],
        ]);
      }

      file_put_contents('expenses.json', json_encode($file, JSON_PRETTY_PRINT));

      return "# Your budget per month is $$argv[3]\n";
    } else {
      return "# Invalid budget\n# Budget must be numeric and positive\n";
    }
  }
}

/**
 * Checks if the total expenses, including a potential addition or update, exceed the monthly budget limit.
 *
 * @param array $data
 * @param array $file
 * @param array $argv
 * @return string|null
 */
function checkBudget(array $data, array $file, array $argv): string|null
{
  $summary = intval(explode('$', summary($data, [null, null, '--month', intval(date('m'))]))[1]);
  if ($argv[1] == 'add') {
    $summary += $argv[5];
  }

  $maxBudget = intval($file[0]['budgetPerMonth']);
  $action = $argv[1] == 'add'
    ? 'addition'
    : 'change';

  if ($summary > $maxBudget) {
    return "# Exceeded budget per month\n# Max budget per month: $$maxBudget\n# Summary after {$action}: $$summary\n";
  } else {
    return null;
  }
}

/**
 * Display the monthly budget that has been set.
 *
 * @param array $argv
 * @return void
 */
function showBudget(array $argv)
{
  if (!isset($argv[2]) || $argv[2] != '--budget') return "# Invalid input\n# Usage: php index.php show --budget <budget>\n";

  global $file;

  $budgetPerMonth = $file[0]['budgetPerMonth'];
  return "# Your budget per month is $$budgetPerMonth\n";
}

/**
 * Export expenses to a CSV file.
 *
 * @param array $data
 * @return string
 */
function export(array $data): string
{
  if (!empty($data)) {
    $file = fopen('expenses.csv', 'w');
    fputcsv($file, array_keys($data[0]), ';');
    foreach ($data as $row) fputcsv($file, $row, ';');
    fclose($file);

    return "# Data has been exported to expenses.csv\n";
  }

  return "# Empty expenses data\n";
}

/**
 * Display guide for using PHP Expense Tracker CLI
 *
 * @return string
 */
function help(): string
{
  $yellow = "\033[33m";
  $green = "\033[32m";
  $reset = "\033[0m";

  $help =
    $green . "\nPHP Expense Tracker CLI \n" . $reset . PHP_EOL .
    $yellow . 'Usage:' . $reset . PHP_EOL .
    "  php expense-tracker.php <command> [options] <arguments>\n\n" .
    $yellow . "Available commands, options, and arguments:" . $reset . PHP_EOL .
    "  set     --budget <budget>                                                       Set monthly budget
  show    --budget                                                                Show monthly budget
  add     --description <description> --amount <amount> --category <category>     Add new expense
  update  --id <id> --category <category>                                         Update expense category
  update  --id <id> --description <description>                                   Update expense description
  update  --id <id> --amount <amount>                                             Update expense amount
  delete  --id <id>                                                               Delete an expense
  list                                                                            List all expenses
  list    --category <category>                                                   List expenses by category
  summary                                                                         View a summary of all expenses
  summary --month <month>                                                         View a summary of expenses for a specific month
  export                                                                          Export expenses data into csv file\n\n";

  return $help;
}
