<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Log all POST data
error_log("Received POST data: " . print_r($_POST, true));

include("connection.php");

function logError($message) {
    error_log(date('[Y-m-d H:i:s] ') . $message);
}

// Check if it's an AJAX POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    $conn->begin_transaction();
    try {
        $User_ID = $_POST['User_ID'] ?? '';
        $train_id = $_POST['train_id'] ?? '';
        $no_of_seats = $_POST['no_of_seats'] ?? '';
        $ticket_type = $_POST['ticket_type'] ?? '';
        $total_fare = $_POST['total_fare'] ?? '';

        // Input validation
        $required_fields = ['no_of_seats', 'total_fare', 'ticket_type', 'train_id', 'User_ID'];
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("Missing required field: $field");
            }
        }

        // Get train details
        $sql_train = "SELECT * FROM Train_details WHERE Train_ID = ?";
        $stmt_train = $conn->prepare($sql_train);
        $stmt_train->bind_param("i", $train_id);
        $stmt_train->execute();
        $result_train = $stmt_train->get_result();

        if ($result_train->num_rows === 0) {
            throw new Exception("Train not found.");
        }
        $train = $result_train->fetch_assoc();
        $Tdate = $train['Date'];

        // Insert ticket
        $insert_ticket = "INSERT INTO Train_tickets (No_of_seats, Train_ID, Ticket_type, Date_of_journey, User_ID) VALUES (?, ?, ?, ?, ?)";
        $stmt_ticket = $conn->prepare($insert_ticket);
        if ($stmt_ticket === false) {
            throw new Exception("Failed to prepare statement: " . $conn->error);
        }

        $stmt_ticket->bind_param("iissi", $no_of_seats, $train_id, $ticket_type, $Tdate, $User_ID);

        if (!$stmt_ticket->execute()) {
            throw new Exception("Error inserting ticket: " . $stmt_ticket->error);
        }

        $ticketid = $stmt_ticket->insert_id;

        // Insert booking
        $date = date('Y-m-d');
        $insert_booking = "INSERT INTO Booking (Ticket_ID, Booking_date, Booking_Amount, User_ID) VALUES (?, ?, ?, ?)";
        $stmt_booking = $conn->prepare($insert_booking);
        $stmt_booking->bind_param("isdi", $ticketid, $date, $total_fare, $User_ID);
        if (!$stmt_booking->execute()) {
            throw new Exception("Error inserting booking: " . $stmt_booking->error);
        }

        $bookingid = $stmt_booking->insert_id;

        // Check if user is a member
        $sql_user = "SELECT Member_OR_Not FROM User_details WHERE User_ID = ?";
        $stmt_user = $conn->prepare($sql_user);
        $stmt_user->bind_param("i", $User_ID);
        $stmt_user->execute();
        $result_user = $stmt_user->get_result();
        $user = $result_user->fetch_assoc();

        $discount = ($user['Member_OR_Not'] == 'Yes') ? 20 : 0;

        // Insert payment
        $status = 'Paid';
        $payer_name = $_POST['card_name'] ?? 'Unknown';
        $pay_type = 'Credit Card'; // Assuming credit card payment
        $insert_payment = "INSERT INTO Payment_details (Booking_ID, User_ID, Status, Date, Amount, Payer_Name, Pay_type, Discount_in_percent) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_payment = $conn->prepare($insert_payment);
        $stmt_payment->bind_param("iissdssi", $bookingid, $User_ID, $status, $date, $total_fare, $payer_name, $pay_type, $discount);
        if (!$stmt_payment->execute()) {
            throw new Exception("Error inserting payment: " . $stmt_payment->error);
        }

        // Update available seats
        $update_seats_sql = "UPDATE Train_details SET Total_no_of_seats = Total_no_of_seats - ? WHERE Train_ID = ? AND Total_no_of_seats >= ?";
        $stmt_update_seats = $conn->prepare($update_seats_sql);
        $stmt_update_seats->bind_param("iii", $no_of_seats, $train_id, $no_of_seats);
        if (!$stmt_update_seats->execute()) {
            throw new Exception("Error updating seats: " . $stmt_update_seats->error);
        }

        if ($stmt_update_seats->affected_rows === 0) {
            throw new Exception("Not enough seats available.");
        }

        $conn->commit(); // Commit transaction
        echo json_encode(['status' => 'success', 'booking_id' => $bookingid]);

    } catch (Exception $e) {
        $conn->rollback();
        logError("Payment failed: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    } finally {
        $conn->close();
    }
    exit();
}

// If it's not an AJAX POST request, we display the form
$User_ID = $_POST['User_ID'] ?? '';
$train_id = $_POST['train_id'] ?? '';
$no_of_seats = $_POST['no_of_seats'] ?? '';
$ticket_type = $_POST['ticket_type'] ?? '';
$total_fare = $_POST['total_fare'] ?? '';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Page</title>
    <link rel="stylesheet" href="payment.css">
</head>
<body>
    <main>
        <div class="container">
            <div class="left_section">
                <div class="cards">
                    <div class="front_card">
                        <img src="images/card-logo.svg" alt="" class="card_logo">
                        <div class="card_container">
                            <img src="images/bg-card-front.png" alt="">
                            <h1 id="number">0000 0000 0000 0000</h1>
                            <div class="card_info">
                                <span id="name">Jane Appleseed</span>
                                <span id="date">
                                    <span id="month">00</span>
                                    /
                                    <span id="year">00</span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="back_card">
                        <img src="images/bg-card-back.png" alt="">
                        <span id="cvc">000</span>
                    </div>
                </div>
            </div>
            <div class="right_section">
                <form id="payment-form">
                    <div class="grid_1">
                        <label for="card_name">Cardholder Name</label>
                        <input type="text" id="card_name" name="card_name" placeholder="e.g. Jane Appleseed" required>
                    </div>
                    <div class="grid_2">
                        <label for="card_number">Card Number</label>
                        <input type="text" id="card_number" name="card_number" placeholder="e.g. 1234 5678 9123 0000" required maxlength="19">
                    </div>
                    <div class="card_information">
                        <div id="card_date">
                            <label>Exp. Date (MM/YY)</label>
                            <div class="two_inp">
                                <div>
                                    <input type="text" id="card_month" name="card_month" placeholder="MM" required maxlength="2">
                                </div>
                                <div>
                                    <input type="text" id="card_year" name="card_year" placeholder="YY" required maxlength="2">
                                </div>
                            </div>
                        </div>
                        <div class="grid_4">
                            <label for="card_cvc">CVC</label>
                            <input type="text" id="card_cvc" name="card_cvc" placeholder="e.g. 123" required maxlength="3">
                        </div>
                    </div>
                    <input type="hidden" name="User_ID" value="<?php echo htmlspecialchars($User_ID); ?>">
                    <input type="hidden" name="train_id" value="<?php echo htmlspecialchars($train_id); ?>">
                    <input type="hidden" name="no_of_seats" value="<?php echo htmlspecialchars($no_of_seats); ?>">
                    <input type="hidden" name="ticket_type" value="<?php echo htmlspecialchars($ticket_type); ?>">
                    <input type="hidden" name="total_fare" value="<?php echo htmlspecialchars($total_fare); ?>">
                    <button type="submit" id="submit_btn">Confirm</button>
                </form>
                <div class="thank hidden">
                    <img src="images/icon-complete.svg" alt="">
                    <h1>Thank you!</h1>
                    <p>We've added your card details</p>
                    <button>Continue</button>
                </div>
            </div>
        </div>
    </main>
    
    <script>
        const cardNumber = document.getElementById("number");
        const numberInp = document.getElementById("card_number");
        const nameInp = document.getElementById("card_name");
        const cardName = document.getElementById("name");
        const cardMonth = document.getElementById("month");
        const cardYear = document.getElementById("year");
        const monthInp = document.getElementById("card_month");
        const yearInp = document.getElementById("card_year");
        const cardCvc = document.getElementById("cvc");
        const cvcInp = document.getElementById("card_cvc");
        const form = document.getElementById("payment-form");

        function setCardNumber(e) {
            cardNumber.innerText = format(e.target.value);
        }
        function setCardName(e) {
            cardName.innerText = e.target.value || "Jane Appleseed";
        }
        function setCardMonth(e) {
            cardMonth.innerText = e.target.value || "00";
        }
        function setCardYear(e) {
            cardYear.innerText = e.target.value || "00";
        }
        function setCardCvc(e) {
            cardCvc.innerText = e.target.value || "000";
        }

        function handleSubmit(e) {
            e.preventDefault();
            const formData = new FormData(form);
            
            console.log("Form data:", Object.fromEntries(formData));

            fetch('payment1.php', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                console.log("Response status:", response.status);
                console.log("Response headers:", response.headers);
                
                if (!response.ok) {
                    return response.text().then(text => {
                        throw new Error(`HTTP error! status: ${response.status}, body: ${text}`);
                    });
                }
                return response.json();
            })
            .then(result => {
                console.log("Parsed result:", result);
                if (result.status === 'success') {
                    window.location.href = `ticket.php?booking_id=${result.booking_id}`;
                } else {
                    alert(result.message || 'An error occurred during payment processing.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(`An error occurred. Please try again later. Error details: ${error.message}`);
            });
        }

        function format(s) {
            return s.toString().replace(/\d{4}(?=.)/g, "$& ");
        }

        numberInp.addEventListener("keyup", setCardNumber);
        nameInp.addEventListener("keyup", setCardName);
        monthInp.addEventListener("keyup", setCardMonth);
        yearInp.addEventListener("keyup", setCardYear);
        cvcInp.addEventListener("keyup", setCardCvc);
        form.addEventListener("submit", handleSubmit);

        window.onerror = function(message, source, lineno, colno, error) {
            console.error("JavaScript error:", message, "at", source, ":", lineno);
        };
    </script>
</body>
</html>