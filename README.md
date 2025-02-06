# CoSimPlat Web Application

This README file provides instructions for installing and running the CoSimPlat web application on your local machine using XAMPP. It also includes steps for creating the necessary MySQL database and table.
This app is meant to help you visualizing your co-simulation status. If you want to setup and run your co-simulation you should refer to [CoSimPlat - Python Version](https://github.com/ESI-FAR/RESCUE-cosimplat-py). 

# Status (PoC)


This is just a Proof of Concept (PoC) created to demonstrate how the long-polling paradigm can be used to enable a co-simulation platform. Compared to WebSockets, Long-polling is an older technique used for web-based communication (e.g., quasi-instantaneous group chat) and consists of a client-side component (JavaScript in this example) and a server-side component (PHP or any other server-side language/framework).

### How It Works

The client sends a request to the server, and the connection remains open until the server has "news" for the client. This allows real-time communication between one or multiple clients without the need to send, for instance, an HTTP request every 50 milliseconds to emulate real-time communication. Long-polling enables real-time communication with just one HTTP request per client.

### PoC Simulation

In this PoC, we simulate this process:
- **Button Click**: Upon a button click, a row is added to the MySQL database.
- **Server Response**: As soon as this happens, the server closes the connection and sends a JSON response to the client. This response includes the new row, which is then rendered into the HTML table you see in the picture below.
- **Reopening the Request**: The HTTP request is then reopened, and the long-polling continues.

### Main Processes

The two main processes here are:
- `fetcher.php`: The backend responsible for monitoring the database.
- `insert.php`: Needed to add rows to the database.

![image](https://github.com/user-attachments/assets/70b934ce-b3dd-4776-a2c5-b7c45cf3566f)


## Prerequisites

Before you begin, ensure you have the following software installed:

- [XAMPP](https://www.apachefriends.org/index.html)

## Installation Steps

### 1. Install XAMPP

1. Download XAMPP from the [official website](https://www.apachefriends.org/index.html).
2. Follow the installation instructions for your operating system.
3. Start the Apache and MySQL modules from the XAMPP Control Panel.

### 2. Set Up the MySQL Database

1. Open your web browser and go to [http://localhost/phpmyadmin](http://localhost/phpmyadmin).
2. Click on the "Databases" tab.
3. In the "Create database" field, enter `cosimplat` and click "Create".

### 3. Create the `simcrono` Table

1. With the `cosimplat` database selected, go to the "SQL" tab.
2. Enter the following SQL code to create the `simcrono` table:

    ```sql
    CREATE TABLE simcrono (
    id INT AUTO_INCREMENT PRIMARY KEY,
    simgame_id INT NOT NULL,
    submodel_id INT NOT NULL,
    sim_step INT,  
    payload LONGTEXT NOT NULL,
    state_history LONGTEXT,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP);

    ```

3. Click "Go" to execute the SQL code.

### 4. Download the CoSimPlat Codebase

1. Clone the repository from GitHub to your local machine. Open a terminal and run:

    ```sh
    git clone https://github.com/yourusername/cosimplat.git
    ```

2. Move the downloaded codebase to the XAMPP `htdocs` directory. For example:

    ```sh
    mv cosimplat /opt/lampp/htdocs/     # For Linux
    mv cosimplat /Applications/XAMPP/htdocs/ # For Mac
    mv cosimplat C:\xampp\htdocs\      # For Windows
    ```


### 5. Run the Application

1. Open your web browser and go to [http://localhost/cosimplat](http://localhost/cosimplat).

You should now be able to see the CoSimPlat web application running on your local machine.

Use the main.py in https://github.com/ESI-FAR/RESCUE-cosimplat-py to run sub models. See that repos README for more information.

## Troubleshooting

- If you encounter any issues with database connections, ensure that your MySQL service is running ;).
- For further assistance, refer to the XAMPP [FAQs and Documentation](https://www.apachefriends.org/faq.html).

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
