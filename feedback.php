<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    $to = "hakimhasbullah041@gmail.com";
    $subject = "Maklum Balas ";
    $body = "Nama: $name\nEmel: $email\n\nMaklum Balas:\n$message";

    $headers = "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";

    if (mail($to, $subject, $body, $headers)) {
        echo "<script>alert('Maklum balas anda berjaya dihantar!'); window.location.href='feedback.html';</script>";
    } else {
        error_log("Failed to send email to $to");
        echo "<script>alert('Maaf, terdapat masalah. Sila cuba lagi.'); window.location.href='feedback.html';</script>";
    }
    
} else {
    echo "<script>window.location.href='feedback.html';</script>";
}
?>