# <h2>Online Voting System using PHP & MySQL</h2>

## <h3>Description:</h3>
<p>The online voting system is a web application developed using PHP and MySQL technologies. It aims to simulate a basic
    voting process where users can log in, cast their votes, and view the election results. The system is designed to
    showcase the implementation of fundamental functionalities typically found in real-world voting systems.</p>

Visit the live site: <a href="online-voting-platform.infinityfreeapp.com/?i=1">Online Voting Platform</a>

## <h3>Key Features:</h3>

<h4>User Authentication:</h4>
<ul>
    <li>Users are required to log in with their credentials (Aadhar card number and phone number) to access the system.
    </li>
    <li>Implemented server-side validation to ensure the security of user data.</li>
    <li>Utilized sessions to maintain user authentication throughout their session.</li>
</ul>

<h4>User Registration:</h4>
<ul>
    <li>New users can register for an account by providing their personal details, including first name, last name,
        Aadhar card number, phone number, and constituency.</li>
    <li>Integrated Google reCAPTCHA for added security against bots and spam.</li>
</ul>

<h4>Dashboard:</h4>
<ul>
    <li>Upon successful login, users are redirected to a dashboard where they can access various features of the system.
    </li>
    <li>The dashboard serves as the main interface for users to navigate the system and perform actions such as voting
        or viewing election results.</li>
</ul>

<h4>Voting Process:</h4>
<ul>
    <li>Users can cast their votes for candidates running in their respective constituencies.</li>
    <li>The system records and updates the vote count for each candidate in real-time.</li>
    <li>Ensured that users can only vote once per election to maintain the integrity of the voting process.</li>
</ul>

<h4>Election Results:</h4>
<ul>
    <li>Implemented a results page where users can view the outcome of the election for different constituencies.</li>
    <li>Displayed candidate details along with the number of votes received by each candidate.</li>
    <li>Provided a user-friendly interface for users to easily interpret the election results.</li>
</ul>

## <h3>Execution:</h3>
<ol>
    <li>Ensure that you have a web server environment with PHP and MySQL installed. You can use platforms like XAMPP,
        WAMP, or MAMP for local development.</li>
    <li>Import the provided SQL database file (database.sql) into your MySQL database to create the necessary tables and
        populate sample data.</li>
    <li>Modify the config.php file to update database connection details such as hostname, username, password, and
        database name according to your environment.</li>
    <li>Make sure the database contains three tables:
        <ul>
            <li>telangana_constituencies</li>
            <li>users</li>
            <li>votes</li>
        </ul>
    </li>
</ol>

### <h3>Mobile Responsiveness:</h3>
<p>While the primary focus of the application is on demonstrating PHP and MySQL functionalities, mobile responsiveness
    was not a priority during development. As a result, the application may not provide an optimal user experience on
    mobile devices with smaller screens. However, users can still access and interact with the system using desktop or
    laptop computers.</p>

### <h3>Purpose:</h3>
<p>The purpose of this web application is to serve as a learning tool for individuals interested in understanding the
    implementation of key functionalities in PHP and MySQL-based web applications. It provides a practical example of
    how to develop an online voting system while emphasizing security, data validation, and database management
    principles.</p>

### <h3>Disclaimer:</h3>
<p>This application is developed for educational purposes only and should not be used in real-world elections. It may
    lack certain features and security measures necessary for handling sensitive voter data in a real-world scenario.
    Users are encouraged to use this application responsibly and adhere to ethical guidelines when interacting with the
    system.</p>
