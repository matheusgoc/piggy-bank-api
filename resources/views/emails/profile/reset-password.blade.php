<html lang="en">
<body style="font-family: Arial, Helvetica, sans-serif; color: #999999">
    <div style="padding: 40px">
        <div style="width: 100%; text-align: center;">
            <img src="{{ $logo }}" alt="My Piggy Bank App logotype" style="height: 150px" />
        </div><br />
        <p>
            Hi {{ $name }},
        </p>
        <p>
            We've received a request to reset your password.
            Enter the following PIN code in the App to proceed:
        </p>
        <p style="text-align: center; font-weight: bold; font-size: 3em; color: #006600">
            {{ $pin }}
        </p>
        <hr style="border: 1px dashed #d7d7d7"/>
        <p>
            <i>If you didn't make this request, just ignore this email.</i>
        </p>
    </div>
</body>
</html>
