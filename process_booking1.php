<?php
// process_booking.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_date = $_POST['booking_date'];
    $start_time_hour = $_POST['start_time_hour'];
    $start_time_minute = $_POST['start_time_minute'];
    $end_time_hour = $_POST['end_time_hour'];
    $end_time_minute = $_POST['end_time_minute'];
    $room_capacity = $_POST['room_capacity'];
    
    // Format start and end time for display
    $start_time = sprintf("%02d:%02d", $start_time_hour, $start_time_minute);
    $end_time = sprintf("%02d:%02d", $end_time_hour, $end_time_minute);

    // Dummy data for available rooms
    $available_rooms = [
        ['room_no' => 'DLT7', 'capacity' => 200],
        ['room_no' => 'DLT8', 'capacity' => 200],
    ];

    // If there are available rooms
    if (!empty($available_rooms)) {
        echo "<!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Available Rooms</title>
            <style>
                body {
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    background: linear-gradient(to right, #6a11cb, #2575fc);
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
                    text-align: center;
                    color: #333;
                    margin-bottom: 20px;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 20px;
                }
                th, td {
                    padding: 12px;
                    text-align: center;
                    border: 1px solid #ddd;
                }
                th {
                    background-color: #6a11cb;
                    color: white;
                }
                input[type='radio'] {
                    margin: 0;
                }
                .booking-details {
                    margin-top: 20px;
                }
                label {
                    font-weight: bold;
                    margin-bottom: 5px;
                    color: #333;
                    display: block;
                }
                input[type='text'],
                input[type='tel'],
                input[type='email'],
                select {
                    padding: 12px;
                    margin-bottom: 20px;
                    border: 1px solid #ddd;
                    border-radius: 8px;
                    font-size: 16px;
                    width: 100%;
                    box-sizing: border-box;
                }
                input[type='submit'] {
                    background-color: #6a11cb;
                    color: white;
                    padding: 15px;
                    border: none;
                    border-radius: 8px;
                    font-size: 16px;
                    cursor: pointer;
                    transition: background-color 0.3s, transform 0.3s;
                    width: 100%;
                    box-sizing: border-box;
                }
                input[type='submit']:hover {
                    background-color: #4c009e;
                    transform: translateY(-2px);
                }
            </style>
        </head>
        <body>
        <div class='container'>
            <h2>Available Rooms</h2>
            <p><strong>Date:</strong> " . date("l, d F Y", strtotime($booking_date)) . "</p>
            <p><strong>Time:</strong> $start_time - $end_time</p>
            <form action='confirm_booking.php' method='POST'>";

        echo "<table>
                <tr><th>Academic Wing / Block</th><th>Room No</th><th>Seating Capacity</th><th>Select Room</th></tr>";
        
        foreach ($available_rooms as $room) {
            echo "<tr>
                    <td>DLT</td>
                    <td>" . $room['room_no'] . "</td>
                    <td>" . $room['capacity'] . "</td>
                    <td><input type='radio' name='selected_room' value='" . $room['room_no'] . "' required></td>
                  </tr>";
        }

        echo "</table>";

        // Booking Details Section
        echo "<h3 class='booking-details'>Booking Details</h3>
            <label for='user_type'>User Type *</label>
            <select id='user_type' name='user_type' required>
                <option value=''>--Select User Type--</option>
                <option value='Student'>Student</option>
                <option value='Faculty'>Faculty</option>
                <option value='Staff'>Staff</option>
            </select>
            <label for='id_no'>ID No / PSR No</label>
            <input type='text' id='id_no' name='id_no'>
            <p style='font-size: 12px; color: #888;'>Note: Please enter only the part before the '@'. The system will append '@goa.bits-pilani.ac.in' automatically.</p>
            <label for='email'>BITS Email Id *</label>
            <input type='text' id='email' name='email' required placeholder='@goa.bits-pilani.ac.in' oninput=\"updateEmail(this.value)\">
            <input type='hidden' id='full_email' name='full_email' value=''>
            <label for='fullname'>User Fullname *</label>
            <input type='text' id='fullname' name='fullname' required>
            <label for='contact_no'>Contact No</label>
            <input type='tel' id='contact_no' name='contact_no'>
            <label for='purpose'>Purpose *</label>
            <input type='text' id='purpose' name='purpose' required>
            <input type='hidden' name='booking_date' value='$booking_date'>
            <input type='hidden' name='start_time' value='$start_time'>
            <input type='hidden' name='end_time' value='$end_time'>
            <input type='hidden' name='room_capacity' value='$room_capacity'>
            <input type='submit' value='Confirm Booking'>
            </form>
        </div>

        <script>
            function updateEmail(username) {
                const domain = '@goa.bits-pilani.ac.in';
                document.getElementById('full_email').value = username + domain;
            }
        </script>

        </body>
        </html>";
    } else {
        echo "<!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>No Rooms Available</title>
            <style>
                body {
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    background: linear-gradient(to right, #6a11cb, #2575fc);
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
                }
                button {
                    background-color: #6a11cb;
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
                    background-color: #4c009e;
                    transform: translateY(-2px);
                }
            </style>
        </head>
        <body>
        <div class='container'>
            <h2>No Rooms Available</h2>
            <p>Sorry, the room is not available for the selected time.</p>
            <button onclick=\"window.history.back();\">Back to Booking</button>
        </div>
        </body>
        </html>";
    }
} else {
    header("Location: index.html"); // Redirect if accessed directly
    exit();
}
?>
