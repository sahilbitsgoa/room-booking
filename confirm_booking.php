<?php
// confirm_booking.php
// Database connection (update with your own credentials)
$host = "localhost"; // Change if needed
$dbname = "classroom_booking"; // Name of your database
$username = "root"; // Your MySQL username
$password = ""; // Your MySQL password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $selected_room = $_POST['selected_room'];
        $booking_date = $_POST['booking_date'];
        $start_time = $_POST['start_time'];
        $end_time = $_POST['end_time'];
        $user_type = $_POST['user_type'];
        $id_no = $_POST['id_no'];
        $email = $_POST['email'];
        $fullname = $_POST['fullname'];
        $contact_no = $_POST['contact_no'];
        $purpose = $_POST['purpose'];

        // Prepare the SQL query to check for existing bookings
        $stmt = $pdo->prepare("
            SELECT * FROM bookings 
            WHERE room_location = ? 
            AND booking_date = ? 
            AND (
                (start_time < ? AND end_time > ?) OR 
                (start_time < ? AND end_time > ?) OR 
                (start_time >= ? AND end_time <= ?)
            )
        ");
        
        // Bind parameters
        $stmt->execute([$selected_room, $booking_date, $start_time, $start_time, $end_time, $end_time, $start_time, $end_time]);
        
        // Fetch results
        $conflict_found = $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;

        if ($conflict_found) {
            // If there's a conflict, display an error message
            echo "<!DOCTYPE html>
            <html lang='en'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>Booking Conflict</title>
                <style>
                    body {
                        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                        background: linear-gradient(to right, #ff7e7e, #ff3d3d);
                        margin: 0;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        min-height: 100vh;
                        padding: 10px;
                    }
                    .container {
                        background-color: #ffffff;
                        padding: 30px;
                        border-radius: 12px;
                        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
                        max-width: 400px;
                        width: 100%;
                        text-align: center;
                    }
                    h2 {
                        color: #333;
                        margin-bottom: 20px;
                        font-size: 24px; /* Increased font size for heading */
                    }
                    button {
                        background-color: #ff3d3d;
                        color: white;
                        padding: 15px;
                        border: none;
                        border-radius: 8px;
                        cursor: pointer;
                        transition: background-color 0.3s, transform 0.3s;
                        width: 100%;
                        box-sizing: border-box;
                    }
                    button:hover {
                        background-color: #c70000;
                        transform: translateY(-2px);
                    }
                </style>
            </head>
            <body>
            <div class='container'>
                <h2>Booking Conflict</h2>
                <p>Sorry, the room <strong>$selected_room</strong> is already booked for the selected time.</p>
                <button onclick=\"window.history.back();\">Back to Booking</button>
            </div>
            </body>
            </html>";
        } else {
            // If no conflict, insert the new booking into the database
            $insert_stmt = $pdo->prepare("
                INSERT INTO bookings (booking_date, start_time, end_time, room_location, room_capacity)
                VALUES (?, ?, ?, ?, ?)
            ");
            
            // Assuming room_capacity is constant or retrieved from another source
            $room_capacity = $_POST['room_capacity']; // Add this field in your form as needed
            
            // Execute the insert statement
            $insert_stmt->execute([$booking_date, $start_time, $end_time, $selected_room, $room_capacity]);

            // Booking confirmed message
            echo "<!DOCTYPE html>
            <html lang='en'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>Booking Confirmation</title>
                <style>
                    body {
                        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                        background: linear-gradient(to right, #6ab04c, #2ed573);
                        margin: 0;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        min-height: 100vh;
                        padding: 10px;
                    }
                    .container {
                        background-color: #ffffff;
                        padding: 30px;
                        border-radius: 12px;
                        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
                        max-width: 600px;
                        width: 100%;
                    }
                    h2 {
                        color: #333;
                        text-align: center;
                        margin-bottom: 20px;
                        font-size: 24px; /* Increased font size for heading */
                    }
                    .confirmation-main, .confirmation-details {
                        background-color: #f0f8e1; /* Light green for confirmation box */
                        border-radius: 8px;
                        padding: 20px;
                        margin-bottom: 20px;
                        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
                    }
                    .confirmation-main {
                        background-color: #e3fcec; /* Slightly darker green */
                    }
                    .confirmation-main p, .confirmation-details p {
                        color: #555;
                        margin: 10px 0;
                        font-size: 18px; /* Increased font size for main confirmation text */
                    }
                    .confirmation-details p {
                        font-size: 16px; /* Slightly smaller font size for details */
                    }
                    button {
                        background-color: #2ed573;
                        color: white;
                        padding: 15px;
                        border: none;
                        border-radius: 8px;
                        cursor: pointer;
                        transition: background-color 0.3s, transform 0.3s;
                        width: 100%;
                        box-sizing: border-box;
                        margin-top: 20px;
                        font-size: 18px; /* Increased font size for button */
                    }
                    button:hover {
                        background-color: #26b95c;
                        transform: translateY(-2px);
                    }
                </style>
            </head>
            <body>
            <div class='container'>
                <h2>Booking Confirmation</h2>
                <div class='confirmation-main'>
                    <p>The room <strong>$selected_room</strong> has been successfully booked!</p>
                    <p><strong>Date:</strong> " . date("l, d F Y", strtotime($booking_date)) . "</p>
                    <p><strong>Time:</strong> $start_time - $end_time</p>
                </div>
                <div class='confirmation-details'>
                    <p><strong>User Type:</strong> $user_type</p>
                    <p><strong>BITS Email Id:</strong> $email</p>
                    <p><strong>Full Name:</strong> $fullname</p>
                    <p><strong>Contact No:</strong> $contact_no</p>
                    <p><strong>Purpose:</strong> $purpose</p>
                </div>
                <button onclick=\"window.location.href='index.html';\">Back to Booking</button>
            </div>
            </body>
            </html>";
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
