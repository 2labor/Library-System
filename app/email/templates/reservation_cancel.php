<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Reservation Canceled</title>
</head>
<body style="background:#f4f4f4;font-family:Arial; margin:0; padding:0;">

<table width="100%" cellpadding="0" cellspacing="0" style="padding:25px 0;">
  <tr>
    <td align="center">
      <table width="600" style="background:#fff;border-radius:8px;overflow:hidden;">

        <tr>
          <td style="background:#d9534f;color:white;padding:20px;font-size:24px;text-align:center;">
            Reservation Canceled
          </td>
        </tr>

        <tr>
          <td style="padding:25px;color:#333;font-size:16px;">
            <p>Hello <strong><?= htmlspecialchars($userName) ?></strong>,</p>

            <p>Your reservation has been canceled.</p>

            <p>Reservation number:</p>

            <h2 style="color:#d9534f;"><?= htmlspecialchars($reservationId) ?></h2>

            <p>If you canceled it by mistake, feel free to reserve the book again anytime.</p>
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
