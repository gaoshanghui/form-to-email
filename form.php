<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Form | PHP form to email explained</title>
  <link rel="stylesheet" href="style.css">
  <script src="https://www.google.com/recaptcha/api.js?render=6LccUMkcAAAAAK0ZorMPYnbixeurK-eMHFW63ZIM"></script>
</head>

<body>
  <?php
  // Start a new session to store input value.
  session_start();

  // $mode has three different status:
  // input: render default form
  // confirm: render confirmation 
  // send: send message to the target email address

  // Setting default mode to the input.
  $mode = "input";

  // variable for the error message.
  $errmessage = array();

  if (isset($_POST["back"]) && $_POST["back"]) {
    // Do nothing.
  } else if (isset($_POST["confirm"]) && $_POST["confirm"]) {
    // For rendering confirmation

    // Validation for name input
    if (!$_POST['name']) {
      $errmessage[] = "名前を入力してください";
    } else if (mb_strlen($_POST['name']) > 100) {
      $errmessage[] = "名前は100文字以内にしてください";
    }
    $_SESSION['name'] = htmlspecialchars($_POST['name'], ENT_QUOTES);

    // Validation for email input
    if (!$_POST['email']) {
      $errmessage[] = "Eメールを入力してください";
    } else if (mb_strlen($_POST['email']) > 200) {
      $errmessage[] = "Eメールは200文字以内にしてください";
    } else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
      $errmessage[] = "メールアドレスが不正です";
    }
    $_SESSION['email'] = htmlspecialchars($_POST['email'], ENT_QUOTES);

    // Validation for message input
    // if (!$_POST['message']) {
    //   $errmessage[] = "お問い合わせ内容を入力してください";
    // } else if (mb_strlen($_POST['message']) > 500) {
    //   $errmessage[] = "お問い合わせ内容は500文字以内にしてください";
    // }

    $_SESSION['message'] = htmlspecialchars($_POST['message'], ENT_QUOTES);

    if ($errmessage) {
      $mode = 'input';
    } else {
      $mode = 'confirm';
    }
  } else if (isset($_POST["send"]) && $_POST["send"]) {
    $name = $_SESSION['name'];
    $user_email = $_SESSION['email'];
    $message = $_SESSION['message'];

    // Composing the email message.
    $email_from = 'Your site url';
    $email_subject = "New Form Submission";
    // The email will be send in HTML format. 
    $email_body = "
        <html>
        <head>
        <title>新しいお問い合わせ</title>
        </head>
        <body>
        <p>新しいお問い合わせです</p>
        <p>Name: $name</p>
        <p>Email: $user_email</p>
        <p>Message: $message</p>
        </body>
        </html>
        ";

    /* Change the email address that you prefer to use */
    $to = "write your email address here";

    // Always set content-type when sending HTML email
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    // More headers
    $headers .= "From: $email_from \r\n";
    $headers .= "Reply-To: $visitor_email \r\n";

    mail($to, $email_subject, $email_body, $headers);

    // reset session to clear form input
    $_SESSION = array();

    // Redirect to the success page.
    header('Location: form-success.php');
  } else {
    // reset session to clear form input
    $_SESSION = array();
  }
  ?>

  <!-- 入力画面 -->
  <?php if ($mode == "input") { ?>
    <main>
      <h1>CONTACT</h1>

      <!-- Error messages will display here -->
      <?php
      if ($errmessage) {
        echo '<div style="color:red;">';
        echo implode('<br>', $errmessage);
        echo '</div>';
      }
      ?>

      <p class="required">* Required</p>
      <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div>Name: <span class="required">*</span></div>
        <input type="text" name="name" value="<?php echo $_SESSION["name"]; ?>" />
        <div>Email: <span class="required">*</span></div>
        <input type="email" name="email" value="<?php echo $_SESSION["email"]; ?>" />
        <div>Message: </div>
        <textarea name="message"><?php echo $_SESSION["message"]; ?></textarea>
        <div><input name="confirm" type="submit" value="confirm" /></div>
        <input type="hidden" name="recaptchaResponse" id="recaptchaResponse">
      </form>
    </main>
  <?php } ?>

  <!-- 確認画面 -->
  <?php if ($mode == "confirm") { ?>
    <main>
      <h1>Confirmation</h1>
      <p>Please check your information.</p>
      <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div>Name: <span class="required">*</span></div>
        <p><?php echo $_SESSION["name"]; ?></p>
        <div>Email: <span class="required">*</span></div>
        <p><?php echo $_SESSION["email"]; ?></p>
        <div>Message: </div>
        <p><?php echo $_POST['message']; ?></p>
        <div>
          <input type="submit" name="back" value="Back" />
          <input type="submit" name="send" value="Send" />
        </div>
      </form>
    </main>
  <?php } ?>

</body>

</html>