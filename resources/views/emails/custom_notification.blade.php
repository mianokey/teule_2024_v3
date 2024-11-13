<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dynamic Email</title>
    <style>
        /* Reset styles to make sure consistent rendering across different email clients */
        body, table, td, a {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table, td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        img {
            -ms-interpolation-mode: bicubic;
        }

        /* Styling for the email content */
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            margin-top: 5px;
            padding: 15px;
            box-sizing: border-box;
        }

        table {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            flex-wrap: wrap;
            background-color: #fff;
            border-collapse: collapse;
        }

        td {
            padding: 15px;
            vertical-align: top;
        }

        tr {
            max-width: 600px;
        }

        h1, h2, h3, h4 {
            margin: 0;
            color: #333;
        }

        p {
            margin: 0;
        }

        a {
            color: #007BFF;
            text-decoration: underline;
        }
        .content{
            font-size: 14px;
            font-weight: 500;
        }
        .content blockquote{
            background-color: #f1f1f1;
            padding: 5px;
            border-left: #3a931c 3px solid;
            border-radius: 5px;
        }

        /* Add your custom styles here, if needed */
        .logo-container {
            text-align: center;
            margin-bottom: 15px;
        }

        .logo {
            max-width: 100px; /* Allow the logo to scale down for smaller screens */
            height: auto; /* Ensure the aspect ratio is maintained */
        }
        .subject{
            margin-bottom: 5px;
        }

        /* Footer styles */
        .footer {
            background-color: #f1f1f1;
            text-align: center;
            padding: 10px;
        }

        .footer p {
            margin: 0;
            font-size: 12px;
            color: #888;
        }
        .footer .quicklinks {
           background-color: #3a931c;
           padding: 10px;
           color: white;
        }
       
        .footer .quicklinks a{
            text-decoration: none;
            color: white;
            text-transform: uppercase;
            font-weight: 600;
            border-right:#efdd13 2px solid;
            border-left: #efdd13 2px solid;
            padding-right: 5px;
            padding-left: 5px;
        }

        /* Media Queries for Responsive Layout */
        @media screen and (max-width: 600px) {
            .container {
                width: 100%;
                padding: 0;
            }

            .logo {
                max-width: 30%;
            }
        }

    </style>
</head>
<body>
    <div class="container">
        <table>
            <tr>
                <td>
                    <div class="logo-container">
                        <img src="{{ asset('assets/img/logo512.png') }}" alt="TEULE KENYA LOGO" class="logo">
                    </div>
                    <div class="content">
                    <b class="subject">SUBJECT : {!! $subject !!}</b></br>
                    {!! $notificationContent !!}
                        
                    </div>
                </td>
            </tr>
        </table>
        <!-- Footer -->
        <div class="footer">
        <p class="quicklinks">
            <a href="https://www.teulekenya.org/newsletter">get the latest newsletter</a>
            <a href="https://www.teulekenya.org/sponsorship">sponsor a child</a>
            <a href="https://www.teulekenya.org/guesthouse">book a room at guesthouse</a>
            <a href="https://www.teulekenya.org/donate">donate</a>
        </p>
            <p>
                Teule Kenya | NGO Registration Code: OP.218/051/9518/531 | +254 721582323 | P.o Box 184-00209,Oloitokitok, Kenya | 1042 Maple Avenue137 Lisle,IL 60532 | info@teulekenya.org | teuleusa@teulekenya.org
            </p>
           
        </div>
    </div>
</body>
</html>
