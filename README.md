 <div align="center">

  <img src="assets/images/logo.png" alt="ExpenseTracker Logo" width="320">

  <h1>Personal Expense Tracker</h1>

  **A PHP + MongoDB based personal finance management application**

  ![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
  ![MongoDB](https://img.shields.io/badge/MongoDB-47A248?style=for-the-badge&logo=mongodb&logoColor=white)
  ![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
  ![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)
  ![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
  ![Chart.js](https://img.shields.io/badge/Chart.js-FF6384?style=for-the-badge&logo=chartdotjs&logoColor=white)
  ![Composer](https://img.shields.io/badge/Composer-885630?style=for-the-badge&logo=composer&logoColor=white)

  **Track expenses. Manage income. Understand your spending.**

</div>

---

## Overview

Personal Expense Tracker is a web-based application developed as a BCA mini-project. It helps users record income and expenses, organize transactions by category, and monitor their financial activity through an interactive dashboard.

## Features

- Dashboard displaying total balance, income, expenses and recent transactions
- Monthly income-versus-expense bar chart
- Expense-by-category doughnut chart
- Add, view, edit and delete transactions
- Search transactions by title
- Filter by category, transaction type and date range
- CSRF protection on create, update and delete forms
- INR currency formatting

## Technology Stack

| Technology | Purpose                       |
| ---------- | ----------------------------- |
| PHP        | Backend application logic     |
| MongoDB    | NoSQL database                |
| HTML5      | Application structure         |
| CSS3       | Styling and responsive layout |
| JavaScript | Client-side interactions      |
| Chart.js   | Dashboard charts              |
| Composer   | PHP dependency management     |

## Requirements

1. PHP 8.0 or later
2. MongoDB Community Server running at `mongodb://127.0.0.1:27017`
3. PHP MongoDB extension (`ext-mongodb`)
4. Composer

## Setup on macOS

### 1. Check the PHP MongoDB extension

```bash
php -m | grep -i mongodb
```

If this prints nothing, install or enable the MongoDB PHP extension for the PHP version used by your terminal.

**Note:** The PHP MongoDB extension is separate from MongoDB Server and MongoDB Compass.

### 2. Install Composer dependencies

From the project root:

```bash
composer install
```

### 3. Start MongoDB

Ensure the local MongoDB server is running.

If installed with Homebrew, inspect the service name:

```bash
brew services list
```

Start the MongoDB service using the exact formula name shown in the list.

### 4. Run the PHP development server

From the project root:

```bash
php -S 127.0.0.1:8000
```

Open the application:

[**http://127.0.0.1:8000**](http://127.0.0.1:8000)

The application uses the local MongoDB database `expense_tracker` and collection `transactions`. MongoDB creates them automatically when the first transaction is inserted.

## Project Structure

```text
expense_tracker/
├── composer.json
├── README.md
├── index.php
├── dashboard.php
├── config/
│   └── database.php
├── includes/
│   ├── functions.php
│   ├── header.php
│   ├── sidebar.php
│   └── footer.php
├── transactions/
│   ├── add.php
│   ├── list.php
│   ├── edit.php
│   └── delete.php
└── assets/
    ├── images/
    │   └── logo.png
    ├── css/
    │   └── style.css
    └── js/
        └── app.js
```

## MongoDB Document Example

```json
{
  "title": "Lunch at cafeteria",
  "amount": 250.00,
  "type": "expense",
  "category": "Food",
  "date": "2026-11-17",
  "description": "Lunch",
  "created_at": "2026-11-17 13:20:00"
}
```

MongoDB automatically generates the `_id` ObjectId field.

## Main MongoDB Operations

| Operation  | PHP Library Call                                            |
| ---------- | ----------------------------------------------------------- |
| Insert     | `$collection->insertOne([...])`                             |
| Fetch many | `$collection->find($filter, ['sort' => [...]])`             |
| Fetch one  | `$collection->findOne(['_id' => $id])`                      |
| Update     | `$collection->updateOne(['_id' => $id], ['$set' => [...]])` |
| Delete     | `$collection->deleteOne(['_id' => $id])`                    |
| Totals     | Aggregation using `$group` and `$sum`                       |

## Security

- CSRF tokens are used to protect create, update and delete operations.
- User input is escaped before being rendered in HTML.
- Delete operations use POST requests.

## Limitations

- This is a local academic project, not a production financial application.
- It does not include user accounts or multi-user access control.
- Chart.js is loaded from a CDN, so charts require an internet connection.

---

<div align="center">

**Developed as a BCA Mini Project**

*PHP · MongoDB · HTML · CSS · JavaScript*

</div>
