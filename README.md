# PHP Expense Tracker CLI

A simple expense tracker command line interface (CLI) App built with PHP that used to to manage your finances. This application allow users to add, delete, and view expenses. This application also provide a summary of the expenses. This project is inspired by [roadmap.sh](https://roadmap.sh/projects/expense-tracker).

## Features

- **Set Budget**: Set montly budget for expenses.
- **Add a New Expense**: Create a new expense with a category, description, and amount.
- **List All Expense**: Display a list of all expense with their details.
- **List Tasks By Category**: Display a list of all tasks by Category.
- **Update an Expense**: Modify the category, description, or amount of an existing task.
- **Update Budget**: Modify montly budget for expenses.
- **Delete an Expense**: Remove an expense from the list.
- **View a Summary**: View a summary of all expenses or for a specific month (of current year).
- **Export Into CSV**: Export expenses data into a csv file.

## Prerequisites

To run this CLI tool, you’ll need:

- **PHP**: Version 7.4 or newer

## How to install

1. Clone the repository

   ```shell script
   git clone https://github.com/krisnaajiep/php-expense-tracker-cli.git
   ```

2. Change the current working directory

   ```shell script
   cd path/php-expense-tracker-cli
   ```

3. Run the task tracker
   ```shell script
   php expense-tracker.php
   ```

## How To Use

Usage: `php expense-tracker.php command <flag> <arguments>`

Available Commands:

| Command   | flags                                 | Description                                                        |
| --------- | ------------------------------------- | ------------------------------------------------------------------ |
| `set`     | `--budget`                            | Set monthly budget                                                 |
| `add`     | `--category, --description, --amount` | Add new expense                                                    |
| `update`  | `--budget`                            | Update monthly budget                                              |
| `update`  | `--id, --category`                    | Update expense category                                            |
| `update`  | `--id, --description`                 | Update expense description                                         |
| `update`  | `--id, --amount`                      | Update expense amount                                              |
| `delete`  | `--id`                                | Deleting an expense                                                |
| `list`    |                                       | List all expenses                                                  |
| `list`    | `--category`                          | Listing all expense by category                                    |
| `summary` |                                       | View a summary of all expenses                                     |
| `summary` | `--month`                             | View a summary of expenses for a specific month (of current year)z |
| `export`  |                                       | Export expenses data into csv file                                 |

### Example

- Set montly budget

  ```shell script
  php expense-tracker.php set --budget 2000
  ```

- Adding a new expense

  ```shell script
  php expense-tracker.php add --category"Food" --description "Lunch" --amount 20
  ```

- Updating montly budget

  ```shell script
  php expense-tracker.php update --budget 3000
  ```

- Updating an expense

  ```shell script
  php expense-tracker.php update --id 1 --category "Electronic"
  ```

  ```shell script
  php expense-tracker.php update --id 1 --description "Radio"
  ```

  ```shell script
  php expense-tracker.php update --id 1 --amount 60
  ```

- Deleting an expense

  ```shell script
  php expense-tracker.php delete --id 1
  ```

- Listing all expenses

  ```shell script
  php expense-tracker.php list
  ```

- Listing all expenses by specific category

  ```shell script
  php expense-tracker.php list --category "Electronic"
  ```

- View a summary of all expenses

  ```shell script
  php expense-tracker.php summary
  ```

- Listing all expenses by specific category

  ```shell script
  php expense-tracker.php summary --month 6
  ```

- Export data into csv file

  ```shell script
  php expense-tracker.php export
  ```
