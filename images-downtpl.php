<!DOCTYPE html>
<html>
<head>
    <style>
        div#content {
            -webkit-column-count: 3;
            max-width: 1100px;
            margin: 50px auto;
        }
        figure {
            width:92%;
            background: #fefefe;
            border: 2px solid #fcfcfc;
            box-shadow: 0 1px 2px rgba(34, 25, 25, 0.4);
            margin: 0 2px 15px 0;
            padding: 10px;
            transition: opacity .4s ease-in-out;
            -webkit-column-break-inside: avoid;
            display: inline-block;
        }
        figure img {
            width: 100%;
            height: auto;
            padding-bottom: 15px;
            margin-bottom: 5px;
        }
        figure figcaption {
            text-align: center;
            overflow: hidden;
        }
        figcaption a {
            text-decoration: none;
            color: darkslateblue;
        }
        button {
            position: fixed;
            right: 50px;
            bottom: 35px;
            background-color: white;
            color: darkslateblue;
            border: none;
            font-size:1.5em;
            font-weight:bold;
            cursor: hand;
        }
        button:hover{
            color: black;
        }
    </style>
</head>
<body>
<div id="content">
    <?php
    /** @var array $URLs */
    foreach ($URLs as $URL) {
        $fileName = basename($URL);
        echo <<<EOD
    <figure>
        <a href="$URL" download="$fileName" title="Use Google Chrome browser if any trouble happened.">
            <img src="$URL">
        </a>
        <figcaption>
            <a href="$URL" download="$fileName" class="downA" title="Use Google Chrome browser if any trouble happened.">Download</a>
        </figcaption>
    </figure>

EOD;
    }
    ?>
</div>
<button id="downButton" title="Only tested on Google Chrome, switch to Google Chrome browser if any trouble happened.">Download All</button>
<script>
    var button = document.getElementById('downButton');

    button.onclick = function () {
        var downATags = document.querySelectorAll('a.downA');
        var i = 0;
        do
            downATags[i].click();
        while (i++ < downATags.length);
    };

    button.style.display = 'none';
    window.onload = function () { button.style.display = 'block'; };
</script>
</body>
</html>