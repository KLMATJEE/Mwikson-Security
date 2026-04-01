<?php
/**
 * Mwikson Protection - Form Handler
 * Handles: Consultation form + Newsletter/Join Community form
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$to = 'info@mwiksonprotection.co.za';
$from = 'noreply@mwiksonprotection.co.za';

$form_type = isset($_POST['form_type']) ? $_POST['form_type'] : 'contact';

if ($form_type === 'newsletter') {
    // Newsletter / Join Community form
    $name  = isset($_POST['name'])  ? strip_tags(trim($_POST['name']))  : '';
    $email = isset($_POST['email']) ? strip_tags(trim($_POST['email'])) : '';

    if (empty($name) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Please provide a valid name and email address.']);
        exit;
    }

    $subject = 'New Newsletter Subscriber — Mwikson Protection';
    $body  = "New newsletter subscriber:\n\n";
    $body .= "Name:  $name\n";
    $body .= "Email: $email\n";
    $body .= "\nSubmitted: " . date('Y-m-d H:i:s') . "\n";

    $headers  = "From: $from\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    if (mail($to, $subject, $body, $headers)) {
        echo json_encode(['success' => true, 'message' => 'You have successfully joined our community!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'There was a problem. Please try again or call us on 066 381 5610.']);
    }

} else {
    // Consultation / Send us a Message form
    $name    = isset($_POST['name'])     ? strip_tags(trim($_POST['name']))     : '';
    $email   = isset($_POST['email'])    ? strip_tags(trim($_POST['email']))    : '';
    // Mobirise uses "textarea" as the field name for the message body
    $message = isset($_POST['textarea']) ? strip_tags(trim($_POST['textarea'])) : '';
    // fallback in case field is renamed
    if (empty($message) && isset($_POST['message'])) {
        $message = strip_tags(trim($_POST['message']));
    }

    if (empty($name) || empty($email) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Debug info so you can see exactly what arrived
        $debug = 'Fields received: name=' . (empty($name) ? 'MISSING' : 'OK')
               . ', email=' . (empty($email) ? 'MISSING' : (filter_var($email, FILTER_VALIDATE_EMAIL) ? 'OK' : 'INVALID'))
               . ', message=' . (empty($message) ? 'MISSING' : 'OK');
        echo json_encode(['success' => false, 'message' => 'Please fill in all fields. (' . $debug . ')']);
        exit;
    }

    $subject = 'New Consultation Request — Mwikson Protection';
    $body  = "New consultation request from the website:\n\n";
    $body .= "Name:    $name\n";
    $body .= "Email:   $email\n";
    $body .= "Message:\n$message\n";
    $body .= "\nSubmitted: " . date('Y-m-d H:i:s') . "\n";

    $headers  = "From: $from\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    if (mail($to, $subject, $body, $headers)) {
        // Send auto-reply to the person who submitted
        $auto_subject = 'We received your consultation request — Mwikson Protection';
        $auto_body  = "Dear $name,\n\n";
        $auto_body .= "Thank you for contacting Mwikson Security Services.\n\n";
        $auto_body .= "We have received your consultation request and will contact you within 24 hours.\n\n";
        $auto_body .= "In the meantime, feel free to reach us:\n";
        $auto_body .= "Tel: 066 381 5610\n";
        $auto_body .= "Cell: 073 484 7025\n";
        $auto_body .= "Email: info@mwiksonprotection.co.za\n\n";
        $auto_body .= "Regards,\nMwikson Security Services (Pty) Ltd\n37 Main Road, Eastleigh, Edenvale";

        $auto_headers  = "From: $to\r\n";
        $auto_headers .= "X-Mailer: PHP/" . phpversion();

        mail($email, $auto_subject, $auto_body, $auto_headers);

        echo json_encode(['success' => true, 'message' => 'Thank you! Your consultation request has been received. We will contact you within 24 hours.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'There was a problem sending your message. Please call us directly on 066 381 5610.']);
    }
}
?>
