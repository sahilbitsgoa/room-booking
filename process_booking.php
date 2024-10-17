<?php
// Database connection settings
$host = "localhost"; // Change if needed
$dbname = "classroom_booking"; // Name of your database
$username = "root"; // Your MySQL username
$password = ""; // Your MySQL password

// Connect to the database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Function to check if a room is available
function checkRoomAvailability($pdo, $booking_date, $start_time, $end_time, $room_location) {
    $query = "
        SELECT * 
        FROM bookings 
        WHERE room_location = :room_location
        AND booking_date = :booking_date
        AND (
            (start_time < :end_time AND end_time > :start_time)
        )
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':room_location' => $room_location,
        ':booking_date' => $booking_date,
        ':start_time' => $start_time,
        ':end_time' => $end_time
    ]);

    return $stmt->rowCount() === 0; // If no rows are returned, room is available
}

// Function to book the room if available
function bookRoom($pdo, $booking_date, $start_time, $end_time, $room_location, $room_capacity) {
    $query = "
        INSERT INTO bookings (booking_date, start_time, end_time, room_location, room_capacity)
        VALUES (:booking_date, :start_time, :end_time, :room_location, :room_capacity)
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':booking_date' => $booking_date,
        ':start_time' => $start_time,
        ':end_time' => $end_time,
        ':room_location' => $room_location,
        ':room_capacity' => $room_capacity
    ]);
}

// Process form data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize user input
    $booking_date = htmlspecialchars($_POST['booking_date']);
    $start_time = htmlspecialchars($_POST['start_time']);
    $end_time = htmlspecialchars($_POST['end_time']);
    $room_location = htmlspecialchars($_POST['room_location']);
    $room_capacity = htmlspecialchars($_POST['room_capacity']);

    // Validate inputs
    if (empty($booking_date) || empty($start_time) || empty($end_time) || empty($room_location) || empty($room_capacity)) {
        echo "<p style='color: red;'>Please fill in all required fields.</p>";
        exit;
    }

    // Check if the room is available
    if (checkRoomAvailability($pdo, $booking_date, $start_time, $end_time, $room_location)) {
        // If available, book the room
        bookRoom($pdo, $booking_date, $start_time, $end_time, $room_location, $room_capacity);
        echo "
            <div class='success-box'>
                <p>The room has been successfully booked!</p>
                <a href='index.html' class='return-button'>Return to Booking Screen</a>
            </div>
        ";
    } else {
        // If not available, show error message
        echo "
            <div class='error-box'>
                <p>Sorry, the room is not available for the selected time.</p>
                <a href='index.html' class='return-button'>Return to Booking Screen</a>
            </div>
        ";
    }
} else {
    echo "<p style='color: red;'>Invalid request method.</p>";
}

?>

<style>
    /* Common Styles for Success and Error Boxes */
    .success-box, .error-box {
        max-width: 600px;
        margin: 20px auto;
        padding: 20px;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        animation: fade-in 0.5s ease-in-out;
    }

    /* Success message styles */
    .success-box {
        background-color: #e0ffe0;
        border: 2px solid #4CAF50;
    }

    .success-box p {
        color: #4CAF50;
        font-size: 18px;
        font-weight: bold;
    }

    /* Error message styles */
    .error-box {
        background-color: #ffe0e0;
        border: 2px solid #f44336;
    }

    .error-box p {
        color: #f44336;
        font-size: 18px;
        font-weight: bold;
    }

    /* Return button styles */
    .return-button {
        display: inline-block;
        margin-top: 20px;
        padding: 12px 24px;
        background-color: #007BFF;
        color: #fff;
        text-decoration: none;
        font-size: 16px;
        font-weight: bold;
        border-radius: 8px;
        transition: background-color 0.3s ease;
    }

    .return-button:hover {
        background-color: #0056b3;
    }

    /* Fade-in animation */
    @keyframes fade-in {
        from { opacity: 0; }
        to { opacity: 1; }
    }
</style>
