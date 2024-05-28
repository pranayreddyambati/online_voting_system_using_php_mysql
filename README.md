# Online Voting System using PHP & MySQL

## Description
The online voting system is a web application developed using PHP and MySQL technologies. It aims to simulate a basic voting process where users can log in, cast their votes, and view the election results. The system is designed to showcase the implementation of fundamental functionalities typically found in real-world voting systems.

## Live Demo
Visit the live site: [Online Voting Platform](http://online-voting-platform.infinityfreeapp.com/?i=1)

## Key Features

### User Authentication
- Users are required to log in with their credentials (Aadhar card number and phone number) to access the system.
- Implemented server-side validation to ensure the security of user data.
- Utilized sessions to maintain user authentication throughout their session.

### User Registration
- New users can register for an account by providing their personal details, including first name, last name, Aadhar card number, phone number, and constituency.
- Integrated Google reCAPTCHA for added security against bots and spam.

### Dashboard
- Upon successful login, users are redirected to a dashboard where they can access various features of the system.
- The dashboard serves as the main interface for users to navigate the system and perform actions such as voting or viewing election results.

### Voting Process
- Users can cast their votes for candidates running in their respective constituencies.
- The system records and updates the vote count for each candidate in real-time.
- Ensured that users can only vote once per election to maintain the integrity of the voting process.

### Election Results
- Implemented a results page where users can view the outcome of the election for different constituencies.
- Displayed candidate details along with the number of votes received by each candidate.
- Provided a user-friendly interface for users to easily interpret the election results.

## Execution

1. Ensure that you have a web server environment with PHP and MySQL installed. You can use platforms like XAMPP, WAMP, or MAMP for local development.
2. Import the provided SQL database file (`database.sql`) into your MySQL database to create the necessary tables and populate sample data.
3. Modify the `config.php` file to update database connection details such as hostname, username, password, and database name according to your environment.
4. Make sure the database contains three tables:
    - `telangana_constituencies`
    - `users`
    - `votes`

## Mobile Responsiveness
While the primary focus of the application is on demonstrating PHP and MySQL functionalities, mobile responsiveness was not a priority during development. As a result, the application may not provide an optimal user experience on mobile devices with smaller screens. However, users can still access and interact with the system using desktop or laptop computers.

## Purpose
The purpose of this web application is to serve as a learning tool for individuals interested in understanding the implementation of key functionalities in PHP and MySQL-based web applications. It provides a practical example of how to develop an online voting system while emphasizing security, data validation, and database management principles.

## Disclaimer
This application is developed for educational purposes only and should not be used in real-world elections. It may lack certain features and security measures necessary for handling sensitive voter data in a real-world scenario. Users are encouraged to use this application responsibly and adhere to ethical guidelines when interacting with the system.

## Author
Developed by [Your Name](https://github.com/pranayreddyambati).

## Acknowledgments
- PHP
- MySQL
- Google reCAPTCHA
- [XAMPP](https://www.apachefriends.org/index.html)

