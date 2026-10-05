<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Password reset code</title>
</head>
<body style="margin:0;padding:0;background:#FAF3E0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#FAF3E0;padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:520px;background:#ffffff;border-radius:16px;border:1px solid #E7E5E4;overflow:hidden;">

                    <tr>
                        <td style="background:#1A1A1A;padding:24px 32px;">
                            <table role="presentation" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="vertical-align:middle;">
                                        <span style="display:inline-block;width:36px;height:36px;background:#6B4423;border-radius:50%;text-align:center;line-height:36px;color:#FAF3E0;font-size:18px;font-weight:bold;">&#127850;</span>
                                    </td>
                                    <td style="vertical-align:middle;padding-left:12px;color:#FAF3E0;font-size:18px;font-weight:700;letter-spacing:-0.3px;">
                                        Oreo<span style="color:#C9A961;">Bites</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:32px;">
                            <p style="margin:0 0 8px;color:#1A1A1A;font-size:22px;font-weight:700;letter-spacing:-0.4px;">
                                Hi {{ $recipientName }},
                            </p>
                            <p style="margin:0 0 24px;color:#57534E;font-size:15px;line-height:1.6;">
                                We received a request to reset your Oreo Bites password. Enter this code to continue:
                            </p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="padding:20px 0;background:#FAF3E0;border-radius:12px;border:1px dashed #C9A961;">
                                        <span style="font-family:'Courier New',Menlo,Consolas,monospace;font-size:36px;font-weight:700;color:#1A1A1A;letter-spacing:8px;">
                                            {{ $otp }}
                                        </span>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:24px 0 0;color:#78716C;font-size:13px;line-height:1.6;">
                                This code expires in <strong style="color:#1A1A1A;">{{ $ttlMinutes }} minutes</strong>. If you didn't request this, ignore this email — your password won't change.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:20px 32px;background:#FAF3E0;border-top:1px solid #E7E5E4;">
                            <p style="margin:0;color:#A8A29E;font-size:12px;line-height:1.5;">
                                Oreo Bites &middot; UCC Congressional Campus<br>
                                This is an automated message — please do not reply.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>