<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/PHPMailer/src/Exception.php';
require 'vendor/PHPMailer/src/PHPMailer.php';
require 'vendor/PHPMailer/src/SMTP.php';

require 'includes/init.php';

$email = '';
$subject = '';
$message = '';
$sent = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    $errors = [];

    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $errors[] = 'Invalid email address';
    }

    if ($subject == '') {
        $errors[] = 'Subject is required';
    }

    if ($message == '') {
        $errors[] = 'Message is required';
    }

    if (empty($errors)) {

        $mail = new PHPMailer(true);

        try {
            
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USER;
            $mail->Password = SMTP_PASS;
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom($email);
            $mail->addAddress('your_email');
            $mail->addReplyTo($email);
            $mail->Subject = $subject;
            $mail->Body = $message;

            $mail->send();

            $sent = true;

        } catch (Exception $e) {

            $errors[] = $mail->ErrorInfo;

        } 
    }
}

?>

<?php require 'includes/header.php'; ?>

<h2>Contact</h2>

<?php if ($sent) : ?>
    <p>Your message has been sent.</p>
<?php else: ?>

<?php if (!empty($errors)) : ?>
    <ul>
        <?php foreach ($errors as $error) : ?>
            <li><?= $error; ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form method="post" id="formContact">

<div class="form-group">
    <label for="email">Your email</label>
    <input type="email" name="email" id="email" class="form-control" placeholder="Your email"
    value="<?= htmlspecialchars($email) ?>">
</div>

<div class="form-group">
    <label for="subject">Subject</label>
    <input name="subject" id="subject" class="form-control" placeholder="Subject"
    value="<?= htmlspecialchars($subject) ?>">

</div>

<div class="form-group">
    <label for="message">Message</label>
    <textarea name="message" id="message" class="form-control" placeholder="Message"><?= htmlspecialchars($message) ?></textarea>
</div>

<button class="btn btn-primary">Send</button>

</form>

<?php endif; ?>

<?php require 'includes/footer.php'; ?>