<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>verification</title>
</head>
<body>
    <div style="background:linear-gradient(187.16deg, #181623 0.07%, #191725 51.65%, #0D0B14 98.75%); min-height:723px;padding-top:80px; padding-left:200px; padding-right:200px;" >
        <div style="text-align:center;" >
            <img style="" src="https://i.postimg.cc/XJdb84mf/Vector.png" alt="statistic">
        </div>
        <h2 style="color:#DDCCAA; font-size:12px; text-align:center" >Movie quotes</h2>
        <div style="margin-top:70px;color:white; font-size:16px; ">
            <h2>Holla {{$user->username}} </h2>
            <p style="margin-top:24px;margin-bottom:32px;" >Thanks for joining Movie quotes! We really appreciate it. Please click the button below to verify your account:</p>
            <a style="padding:7px 13px; background-color:
            #E31221; color:white; text-decoration:none;" href="{{config('verification.vue_url') . '?verification_token=' . $user->token }}">Verify account</a>
            <p style="margin-top:37px;">If clicking doesn't work, you can try copying and pasting it to your browser:</p>
            <p style="color:#DDCCAA;" >{{config('verification.vue_url') . '?verification_token=' . $user->token }}</p>
            <p>If you have any problems, please contact us: support@moviequotes.ge</p>
            <span>MovieQuotes Crew</span>

        </div>
    </div>
</body>
</html>
