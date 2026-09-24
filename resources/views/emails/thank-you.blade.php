@php
$designs = [
1 => [
'header' => '#00096A',
'accent' => '#EF4700',
'soft' => '#EEF1FA',
'button' => '#00096A',
'tagline' => 'Together, we are making a difference',
],
2 => [
'header' => '#EF4700',
'accent' => '#00096A',
'soft' => '#FFF3ED',
'button' => '#EF4700',
'tagline' => 'Thank you for standing with Teule',
],
3 => [
'header' => '#147A4B',
'accent' => '#EF4700',
'soft' => '#EEF8F3',
'button' => '#147A4B',
'tagline' => 'Hope. Love. Opportunity.',
],
4 => [
'header' => '#00096A',
'accent' => '#147A4B',
'soft' => '#EEF5F2',
'button' => '#147A4B',
'tagline' => 'Your generosity helps hope grow',
],
];

$theme = $designs[$design ?? 1];

$donorName = $donation->donor?->name ?? 'Friend';

$isCash = $donation->type === 'cash';

$amount = $isCash
? $donation->currency . ' ' . number_format((float) $donation->amount, 2)
: null;
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Thank You - Teule Kenya</title>
</head>

<body style="
    margin:0;
    padding:0;
    background-color:#f4f6f8;
    font-family:Arial, Helvetica, sans-serif;
">

    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f4f6f8;">

        <tr>
            <td align="center" style="padding:30px 15px;">

                <table width="600" cellpadding="0" cellspacing="0" border="0" style="
                        max-width:600px;
                        width:100%;
                        background:#ffffff;
                        border-radius:12px;
                        overflow:hidden;
                   ">

                    {{-- HEADER --}}
                    <tr>
                        <td style="
                        background-color:{{ $theme['header'] }};
                        padding:32px 25px;
                        text-align:center;
                    ">

                            <div style="
                            font-size:28px;
                            font-weight:bold;
                            color:#ffffff;
                            letter-spacing:1px;
                        ">
                                TEULE KENYA
                            </div>

                            <div style="
                            margin-top:8px;
                            color:#ffffff;
                            font-size:14px;
                            opacity:.95;
                        ">
                                {{ $theme['tagline'] }}
                            </div>

                        </td>
                    </tr>


                    {{-- ACCENT BAR --}}
                    <tr>
                        <td style="
                        height:6px;
                        background-color:{{ $theme['accent'] }};
                        font-size:0;
                        line-height:0;
                    ">
                            &nbsp;
                        </td>
                    </tr>


                    {{-- MAIN CONTENT --}}
                    <tr>
                        <td style="
                        padding:35px 35px 25px;
                        color:#333333;
                        font-size:16px;
                        line-height:1.7;
                    ">

                            <p style="
                            margin:0 0 20px;
                            font-size:20px;
                            font-weight:bold;
                            color:{{ $theme['header'] }};
                        ">
                                Dear {{ $donorName }},
                            </p>


                            @if($isCash)

                            {{-- DONATION HIGHLIGHT --}}
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="
                                        background-color:{{ $theme['soft'] }};
                                        border-left:5px solid {{ $theme['accent'] }};
                                        margin:20px 0 25px;
                                   ">

                                <tr>
                                    <td style="
                                        padding:20px;
                                        text-align:center;
                                    ">

                                        <div style="
                                            font-size:13px;
                                            color:#666666;
                                            text-transform:uppercase;
                                            letter-spacing:1px;
                                        ">
                                            Your Generous Gift
                                        </div>

                                        <div style="
                                            margin-top:8px;
                                            font-size:28px;
                                            font-weight:bold;
                                            color:{{ $theme['header'] }};
                                        ">
                                            {{ $amount }}
                                        </div>

                                    </td>
                                </tr>

                            </table>

                            <p>
                                Thank you very much for your generous gift of
                                <strong>{{ $amount }}</strong>
                                to Teule Kenya.
                            </p>

                            @else

                            <p>
                                Thank you very much for your generous support
                                to Teule Kenya.
                            </p>

                            @endif


                            <p>
                                Your support helps us demonstrate the love of
                                Jesus Christ by caring for and empowering
                                vulnerable children and families.
                            </p>


                            {{-- PROGRAMS --}}
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:25px 0;">

                                <tr>

                                    <td width="50%" valign="top" style="
                                        padding:10px;
                                        text-align:center;
                                        background:#fafafa;
                                    ">

                                        <div style="
                                        font-size:22px;
                                        color:{{ $theme['accent'] }};
                                    ">
                                            ♥
                                        </div>

                                        <strong style="
                                        color:{{ $theme['header'] }};
                                    ">
                                            Child Care
                                        </strong>

                                        <div style="
                                        font-size:13px;
                                        color:#666666;
                                        margin-top:5px;
                                    ">
                                            Care, protection and support
                                        </div>

                                    </td>

                                    <td width="50%" valign="top" style="
                                        padding:10px;
                                        text-align:center;
                                        background:#fafafa;
                                    ">

                                        <div style="
                                        font-size:22px;
                                        color:{{ $theme['accent'] }};
                                    ">
                                            ★
                                        </div>

                                        <strong style="
                                        color:{{ $theme['header'] }};
                                    ">
                                            Education
                                        </strong>

                                        <div style="
                                        font-size:13px;
                                        color:#666666;
                                        margin-top:5px;
                                    ">
                                            Helping children grow and learn
                                        </div>

                                    </td>

                                </tr>

                                <tr>

                                    <td width="50%" valign="top" style="
                                        padding:10px;
                                        text-align:center;
                                        background:#fafafa;
                                    ">

                                        <div style="
                                        font-size:22px;
                                        color:{{ $theme['accent'] }};
                                    ">
                                            ◆
                                        </div>

                                        <strong style="
                                        color:{{ $theme['header'] }};
                                    ">
                                            Family Empowerment
                                        </strong>

                                        <div style="
                                        font-size:13px;
                                        color:#666666;
                                        margin-top:5px;
                                    ">
                                            Strengthening families and communities
                                        </div>

                                    </td>

                                    <td width="50%" valign="top" style="
                                        padding:10px;
                                        text-align:center;
                                        background:#fafafa;
                                    ">

                                        <div style="
                                        font-size:22px;
                                        color:{{ $theme['accent'] }};
                                    ">
                                            ✦
                                        </div>

                                        <strong style="
                                        color:{{ $theme['header'] }};
                                    ">
                                            Discipleship
                                        </strong>

                                        <div style="
                                        font-size:13px;
                                        color:#666666;
                                        margin-top:5px;
                                    ">
                                            Nurturing faith and hope
                                        </div>

                                    </td>

                                </tr>

                            </table>


                            <p>
                                We are deeply grateful for your partnership.
                                Your generosity helps us continue serving children
                                and families and extending hope within our
                                community.
                            </p>


                            <p style="
                            margin-top:30px;
                            margin-bottom:0;
                        ">
                                May God bless you abundantly.
                            </p>


                            <p style="
                            margin-top:25px;
                            margin-bottom:0;
                        ">
                                With gratitude,<br>

                                <strong style="
                                color:{{ $theme['header'] }};
                                font-size:17px;
                            ">
                                    Teule Kenya
                                </strong>
                            </p>

                        </td>
                    </tr>


                    {{-- STAY CONNECTED --}}
                    <tr>
                        <td style="
                        padding:25px 35px;
                        background-color:{{ $theme['soft'] }};
                        text-align:center;
                    ">

                            <div style="
                            font-size:18px;
                            font-weight:bold;
                            color:{{ $theme['header'] }};
                        ">
                                Stay Connected With Teule
                            </div>

                            <p style="
                            margin:8px 0 18px;
                            color:#666666;
                            font-size:13px;
                        ">
                                Follow our journey and see how your partnership
                                is helping transform lives.
                            </p>


                            {{-- WEBSITE --}}
                            <a href="https://teulekenya.org" style="
                                display:inline-block;
                                margin:4px;
                                padding:10px 16px;
                                background-color:{{ $theme['button'] }};
                                color:#ffffff;
                                text-decoration:none;
                                border-radius:5px;
                                font-size:13px;
                           ">
                                Visit Our Website
                            </a>


                            {{-- NEWSLETTER --}}
                            <a href="https://teulekenya.org/whatsnew" style="
                                display:inline-block;
                                margin:4px;
                                padding:10px 16px;
                                background-color:{{ $theme['accent'] }};
                                color:#ffffff;
                                text-decoration:none;
                                border-radius:5px;
                                font-size:13px;
                           ">
                                Read Our Newsletter
                            </a>

                             <a href="https://www.facebook.com/teulekenya" target="_blank" style="
                display:inline-block;
                margin:4px;
                padding:10px 18px;
                background-color:#1877F2;
                color:#ffffff;
                text-decoration:none;
                border-radius:5px;
                font-size:13px;
                font-weight:bold;
           ">
                                Facebook
                            </a>


                            <a href="https://www.instagram.com/teulekenya_/" target="_blank" style="
                display:inline-block;
                margin:4px;
                padding:10px 18px;
                background-color:#E4405F;
                color:#ffffff;
                text-decoration:none;
                border-radius:5px;
                font-size:13px;
                font-weight:bold;
           ">
                                Instagram
                            </a>



                        </td>
                    </tr>


                    {{-- FOOTER --}}
                    <tr>
                        <td style="
                        padding:20px 30px;
                        background-color:#f4f6f8;
                        text-align:center;
                        color:#777777;
                        font-size:12px;
                        line-height:1.6;
                    ">

                            <strong>Teule Kenya</strong><br>

                            Oloitokitok, Kajiado County, Kenya<br>

                            <a href="https://teulekenya.org" style="
                                color:{{ $theme['header'] }};
                                text-decoration:none;
                           ">
                                teulekenya.org
                            </a>

                        </td>
                    </tr>

                </table>

            </td>
        </tr>

    </table>

</body>

</html>