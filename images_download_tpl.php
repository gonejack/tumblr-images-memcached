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
            /*border-bottom: 1px solid #ccc;*/
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
            display: block;
            float: right;
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
<?php foreach ($imageUrls as $url) { ?>
    <figure>
        <a href="<?php echo $url?>" download="<?php echo basename($url) ?>" title="Use Google Chrome browser if any trouble happened."><img src="<?php echo $url?>""></a>
        <figcaption><a href="<?php echo $url?>" download="<?php echo basename($url) ?>">Download</a></figcaption>
    </figure>
<?php } ?>
</div>
<button id="downButton" title="Only tested on Google Chrome, switch to Google Chrome browser if any trouble happened.">Download All</button>
<script>
    var button = document.getElementById('downButton');
    button.style.display = 'none';
    button.addEventListener('click', function () {
        function baseName(str) {
            var base = str.substring(str.lastIndexOf('/') + 1);
            if(base.lastIndexOf(".") != -1)
                base = base.substring(0, base.lastIndexOf("."));
            return base;
        }

        var tempA = document.createElement('a');
        var imgs = document.getElementsByTagName('img');
        for (var i = 0, l = imgs.length; i < l; i++) {
            var img = imgs[i];

            tempA.setAttribute('href', img.src);
            tempA.setAttribute('download', baseName(img.src));
            tempA.click();

        }
    });

    var images = document.getElementsByTagName('img');
    var imagesTotalCount = images.length;
    var imagesLoadedCount = 0;

    for (var i = 0; i < imagesTotalCount; i++) {
        var img = images[i];
        img.onload = function () {
            imagesLoadedCount++;
            if (imagesLoadedCount === imagesTotalCount) {
                button.style.display = 'block';
            }
        }
    }
</script>
</body>

