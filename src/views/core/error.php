<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Error &mdash; Rax</title>
        <link href="http://fonts.googleapis.com/css?family=Droid+Sans:400,700|Droid+Sans+Mono" rel="stylesheet" type="text/css">
        <style type="text/css">
            * { margin: 0; }
            body { margin: 20px 30px; font: 14px/1em "Droid Sans", sans-serif; background: #003663; }
            header { padding: 20px 30px; background: #b00; color: #fff; }
            header > div:not(:last-child) { margin: 0 0 20px; }
            header .message { font-size: 20px; }
            .file-info { padding: 10px 30px; background: #810D0D; color: #fff; font-size: 12px; }
            .highlight { color: #ffb6c1; }
        </style>
        <link rel="stylesheet" href="/shCoreSunburst.css">
        <script type="text/javascript" src="shCore.js"></script>
        <script type="text/javascript" src="shBrushPhp.js"></script>
        <script type="text/javascript">SyntaxHighlighter.all();</script>
    </head>
    <body>
        <header>
            <div class="introduction">
                <?php
                if ($e instanceof ErrorException) {
                    echo '<strong class="highlight">'.static::$levels[$code].'</strong> Uh-oh an error was thrown:';
                } else {
                    echo '<strong class="highlight">'.$class.'</strong> Uh-oh an exception was thrown:';
                }
                ?>
            </div>
            <div class="message">
                &ldquo;<?php echo $message; ?>&rdquo;
            </div>
        </header>
        <div class="file-info">
            <code title="<?php echo $file; ?>">
                <?php echo Debug::filePath($file, function($dir, $file) { return '<span class="highlight">'.$dir.'</span>'.$file; }); ?>
                [ <span class="highlight"><?php echo $line; ?></span> ]
            </code>
        </div>


        <div id="source-code">
            <?php echo Debug::sourceCode($file, $line); ?>
        </div>

        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.7.2.min.js"><\/script>')</script>
        <script type="text/javascript">

        </script>
    </body>
</html>
