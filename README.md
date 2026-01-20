# Quiz Management System

A Laravel web application for quiz management with user authentication.

## Features

- User registration and login with name, age, email, phone number, and password
- Dashboard with monthly quiz access (February, March, April, May)
- Quiz selection page for each month (3 quizzes per month)

## Installation

1. Clone the repository
2. Run `composer install`
3. Copy `.env.example` to `.env` and configure your database
4. Run `php artisan key:generate`
5. Run `php artisan migrate`
6. Run `php artisan serve`
