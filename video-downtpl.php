<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            background-color: black;
        }

        video.center {
            display: block;
            box-shadow: 0 0 30px lightslategrey;
            border-radius: 5px;
            width: 30%;
            min-width: 400px;
            margin: 100px auto;
        }

        button {
            position: fixed;
            right: 50px;
            bottom: 35px;
            background-color: transparent;
            color: mediumpurple;
            border: none;
            font-size: 1.5em;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            text-shadow: 0 0 2px white;
        }
    </style>
</head>
<body>
    <video class="center" controls>
        <source id="video" src="<?php echo $URL ?>" type="video/mp4">
        Your browser does not support the video tag, please use Google Chrome.
    </video>

    <button id="downButton">Download</button>

    <script>
        var button = document.getElementById('downButton');

        button.style.display = 'none';
        window.onload = function () {
            var source = document.getElementById('video');
            var a = document.createElement('a');

            a.href = source.src;
            a.download = a.href.split('/').pop();

            button.onclick = function () {a.click()};
            button.style.display = 'block';
        };
    </script>
</body>
</html>