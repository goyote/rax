<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Error &mdash; Rax</title>
        <link href='http://fonts.googleapis.com/css?family=Droid+Sans:400,700|Droid+Sans+Mono' rel='stylesheet' type='text/css'>
        <style type="text/css">
            * { margin: 0; }
            body { margin: 20px 30px; font: 14px/1em "Droid Sans", sans-serif; background: #003663; }
            header { padding: 20px 30px; background: #b00; color: #fff; }
            header > div:not(:last-child) { margin: 0 0 20px; }
            header .message { font-size: 20px; }
            .info { padding: 10px 30px; background: #810D0D; color: #fff; font-size: 12px; }
            /*#kohana_error { background: #ddd; font-size: 1em; font-family:sans-serif; text-align: left; color: #111; }*/
            /*#kohana_error h1,*/
            /*#kohana_error h2 { margin: 0; padding: 1em; font-size: 1em; font-weight: normal; background: #911; color: #fff; }*/
            /*#kohana_error h1 a,*/
            /*#kohana_error h2 a { color: #fff; }*/
            /*#kohana_error h2 { background: #222; }*/
            /*#kohana_error h3 { margin: 0; padding: 0.4em 0 0; font-size: 1em; font-weight: normal; }*/
            /*#kohana_error p { margin: 0; padding: 0.2em 0; }*/
            /*#kohana_error a { color: #1b323b; }*/
            /*#kohana_error pre { overflow: auto; white-space: pre-wrap; }*/
            /*#kohana_error table { width: 100%; display: block; margin: 0 0 0.4em; padding: 0; border-collapse: collapse; background: #fff; }*/
            /*#kohana_error table td { border: solid 1px #ddd; text-align: left; vertical-align: top; padding: 0.4em; }*/
            /*#kohana_error div.content { padding: 0.4em 1em 1em; overflow: hidden; }*/
            /*#kohana_error pre.source { margin: 0 0 1em; padding: 0.4em; background: #fff; border: dotted 1px #b7c680; line-height: 1.2em; }*/
            /*#kohana_error pre.source span.line { display: block; }*/
            /*#kohana_error pre.source span.highlight { background: #f0eb96; }*/
            /*#kohana_error pre.source span.line span.number { color: #666; }*/
            /*#kohana_error ol.trace { display: block; margin: 0 0 0 2em; padding: 0; list-style: decimal; }*/
            /*#kohana_error ol.trace li { margin: 0; padding: 0; }*/
            .js .collapsed { display: none; }
        </style>
        <link rel="stylesheet" href="/shCoreSunburst.css">
<!--        <script type="text/javascript" src="/shCoreAll.js"></script>-->
<!--        <script type="text/javascript">-->
<!--//            SyntaxHighlighter.defaults['gutter'] = false;-->
<!--            SyntaxHighlighter.defaults['auto-links'] = true;-->
<!--            SyntaxHighlighter.all();-->
<!--        </script>-->


        <script type="text/javascript" src="shCore.js"></script>
        <script type="text/javascript" src="shBrushPhp.js"></script>
<!--        <link type="text/css" rel="stylesheet" href="shCoreDefault.css"/>-->
        <script type="text/javascript">SyntaxHighlighter.all();</script>
    </head>
    <body>
        <header>
            <div class="introduction">
                <?php
                if ($e instanceof ErrorException) {
                    echo sprintf('<strong>%s</strong> Uh-oh an error was thrown:', static::$levels[$code]);
                } else {
                    echo sprintf('<strong>%s</strong> Uh-oh an exception was thrown:', $class);
                }
                ?>
            </div>
            <div class="message">
                &ldquo;<?php echo $message; ?>&rdquo;
            </div>
        </header>
        <div class="info">
            <code title="<?php echo $file; ?>"><?php echo Debug::filePath($file); ?>  [ <?php echo $line; ?> ]</code>
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
