<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Book Reservation</title>
</head>
<body style="margin:0;padding:0;background:#f4f4f4;font-family:Arial, sans-serif;">

  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f4;padding:20px 0;">
    <tr>
      <td align="center">
        <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;">
          
          <tr>
            <td style="background:#4a90e2;color:#ffffff;padding:20px;text-align:center;font-size:24px;font-weight:bold;">
              Reservation Confirmed
            </td>
          </tr>

          <tr>
            <td style="padding:25px;color:#333333;font-size:16px;">
              <p>Hello <strong><?= htmlspecialchars($userName) ?></strong>,</p>

              <p>You have successfully reserved the book:</p>

              <table cellpadding="0" cellspacing="0" style="margin-top:15px;">
                <tr>
                  <td width="120">
                    <img src="<?= htmlspecialchars($book->getImageUrl()) ?>" alt="Book cover" style="width:120px;border-radius:6px;">
                  </td>
                  <td style="padding-left:15px;vertical-align:top;">
                    <strong><?= htmlspecialchars($book->getTitle()) ?></strong><br>
                    Author: <?= htmlspecialchars($book->getAuthor()) ?><br>
                    Edition: <?= $book->getEdition() ?><br>
                    ISBN: <?= htmlspecialchars($book->getIsbn()) ?><br>
                  </td>
                </tr>
              </table>

              <br>

              <p>Your reservation is valid until:</p>
              <h2 style="color:#4a90e2;">
                  <?= $expiresAt->format("d M Y H:i") ?>
              </h2>

              <p>If you want, you can extend your reservation before it expires.</p>
            </td>
          </tr>

          <tr>
            <td style="background:#f0f0f0;padding:15px;text-align:center;color:#777;font-size:14px;">
              Library System • This is an automated message
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>

</body>
</html>
