<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstName = htmlspecialchars($_POST['first_name']);
    $lastName = htmlspecialchars($_POST['last_name']);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $phone = htmlspecialchars($_POST['phone']);
    $message = htmlspecialchars($_POST['message']);

    if (!$email) {
        die('Invalid email address.');
    }

    $to = "amjadell300@gmail.com";
    $subject = "New Contact Form Submission";
    $body = "You have received a new message from your contact form:\n\n" .
        "First Name: $firstName\n" .
        "Last Name: $lastName\n" .
        "Email: $email\n" .
        "Phone: $phone\n\n" .
        "Message:\n$message";

    $headers = "From: $email\r\n" .
        "Reply-To: $email\r\n";

    if (mail($to, $subject, $body, $headers)) {
        echo "Message sent successfully.";
    } else {
        echo "Failed to send the message. Please try again later.";
    }
} else {
    echo "Invalid request method.";
}
