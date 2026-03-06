php <!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Password Reset | Level Up Asianista</title>
</head>

<body style="margin:0; padding:0;">

    <!-- Background Wrapper -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" 
           style="background-color:#0b1c44; background-image:url('{{ asset('images/BACKGROUND.jpg') }}'); 
                  background-size:cover; background-repeat:no-repeat; padding:40px 0;">

        <tr>
        <td align="center">

            <!-- Card -->
            <table width="400" cellpadding="0" cellspacing="0" border="0"
                   style="background:rgba(10,28,68,0.93); border-radius:16px; padding:35px; 
                          font-family:'Poppins',Arial,sans-serif; color:white;">
                <tr>
                    <td align="center">

                        <!-- Logo -->
                        <!-- <img src="{{ asset('images/LEVEL UP ASIANISTA LOGO.png') }}"
                            alt="Level Up Asianista"
                            width="200"
                            style="display:block; margin-bottom:20px;"> -->
                            
                            <!-- Title Instead of Logo -->
<h1 style="font-size:24px; font-weight:700; margin:0 0 20px 0; color:#ffd43b;">
    Level Up Asianista
</h1>

                        <!-- Header -->
                        <h2 style="font-size:22px; font-weight:700; margin:0 0 10px 0;">
                            Password Reset Request
                        </h2>

                        <!-- Message -->
                        <p style="font-size:15px; line-height:1.6; color:#e6e6e6;">
                            Hello Adventurer! 👋 <br>
                            We received a request to reset the password for your 
                            <strong>Level Up Asianista</strong> account.
                        </p>

                        <!-- Button -->
                        <a href="{{ $actionUrl }}"
                           style="display:inline-block; background:#ffd43b; color:#0b1c44;
                                  padding:12px 28px; border-radius:8px; font-weight:700;
                                  text-decoration:none; margin-top:20px;">
                            Reset Password
                        </a>

                        <!-- Info -->
                        <p style="font-size:14px; line-height:1.6; color:#cccccc; margin-top:25px;">
                            This reset link will expire in <strong>60 minutes</strong>.
                        </p>

                        <p style="font-size:14px; line-height:1.6; color:#cccccc;">
                            If you did not request this, you can safely ignore the email —
                            your account remains secure. 🛡️
                        </p>

                        <!-- Footer -->
                        <p style="color:#ffd43b; font-weight:600; margin-top:25px;">
                            Thanks for being part of Level Up Asianista!
                        </p>

                        <p style="font-size:13px; color:#777; margin-top:15px;">
                            © {{ date('Y') }} Level Up Asianista. All rights reserved.
                        </p>

                    </td>
                </tr>
            </table>

        </td>
        </tr>
    </table>

</body>
</html>
