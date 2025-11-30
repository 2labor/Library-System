<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Reservation Extended</title>
</head>
<body style="margin:0;padding:0;background:#f4f4f4;font-family:Arial, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="padding:25px 0;">
  <tr>
    <td align="center">
      <table width="600" style="background:#ffffff;border-radius:8px;overflow:hidden;">

        <tr>
          <td style="background:#5cb85c;color:#ffffff;padding:20px;text-align:center;font-size:24px;">
            Reservation Extended
          </td>
        </tr>

        <tr>
          <td style="padding:25px;color:#333;font-size:16px;">
            <p>Hello <strong><?= htmlspecialchars($userName) ?></strong>,</p>

            <p>Your reservation has been successfully extended!</p>

            <p>Reservation number:</p>
            <h2 style="color:#5cb85c;"><?= htmlspecialchars($reservationId) ?></h2>

            <p>Book ISBN:</p>
            <h3><?= htmlspecialchars($isbn) ?></h3>

            <p>New expiration date:</p>
            <h2 style="color:#5cb85c;">
                <?= $dueDate->format("d M Y H:i") ?>
            </h2>

            <p>You can extend your reservation again before it expires.</p>
          </td>
        </tr>

        <tr>
          <td style="background:#f0f0f0;padding:15px;text-align:center;color:#777;font-size:14px;">
            Library System • Automated Message
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>

</body>
</html>
