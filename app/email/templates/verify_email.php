<!DOCTYPE html>
<html lang="en">
<body style="margin:0; padding:0; background:#f5f7fa; font-family:Arial, sans-serif;">

<table align="center" width="100%" cellpadding="0" cellspacing="0" style="padding: 40px 0;">
<tr>
<td align="center">

    <table width="600" cellpadding="0" cellspacing="0" style="background:white; border-radius:12px; padding:40px; box-shadow:0 4px 20px rgba(0,0,0,0.08);">

        <tr>
            <td align="left" style="font-size:22px; font-weight:bold; color:#2a2a2a;">
                Email Verification
            </td>
        </tr>

        <tr><td style="height:20px;"></td></tr>

        <tr>
            <td style="font-size:16px; color:#444;">
                Dear <?= htmlspecialchars($userName) ?>,
            </td>
        </tr>

        <tr><td style="height:15px;"></td></tr>

        <tr>
            <td style="font-size:15px; color:#555;">
                Thank you for registering with our library service.<br>
                Please use the verification code below to complete your account setup:
            </td>
        </tr>

        <tr><td style="height:25px;"></td></tr>

        <tr>
            <td align="center">
                <div style="font-size:32px; font-weight:bold; background:#2a7ae2; color:white; padding:15px 30px; border-radius:8px; display:inline-block;">
                    <?= htmlspecialchars($code) ?>
                </div>
            </td>
        </tr>

        <tr><td style="height:25px;"></td></tr>

        <tr>
            <td style="font-size:15px; color:#555;">
                This code will remain valid for <strong><?= htmlspecialchars($expires) ?> minutes</strong>.
            </td>
        </tr>

        <tr><td style="height:25px;"></td></tr>

        <tr>
            <td style="font-size:14px; color:#777; line-height:1.6;">
                If you did not request this action, you may safely ignore this message.
            </td>
        </tr>

        <tr><td style="height:35px;"></td></tr>

        <tr>
            <td style="font-size:15px; color:#444;">
                Sincerely,<br>
                <strong>The Library Team</strong>
            </td>
        </tr>

    </table>

</td>
</tr>
</table>

</body>
</html>