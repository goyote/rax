<?php
use Rax\Mvc\Debug;
?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Error &mdash; Rax</title>

        <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Droid+Sans:400,700">
        <link rel="stylesheet" href="/assets/fonts/meslo/stylesheet.css">
        <link rel="stylesheet" href="/assets/js/vendor/syntaxhighlighter/shCoreSunburst.css">
        <link rel="stylesheet" href="/assets/js/vendor/bootstrap/css/bootstrap.css">

        <style type="text/css">
            * { margin: 0; }
            .meslo { font-family: 'Meslo', monospace; }
            code { font-family: 'Meslo', monospace; }
            body { font: 13px/1em "Droid Sans", sans-serif; background: #003663; min-width: 320px; }
            body > .container { margin: 20px auto; width: 85%; box-shadow: 0 5px 50px rgba(0, 0, 0, 0.5); }
            .header { padding: 10px 30px 20px; background: #b00; color: #fff; }
            /*.header > div:not(:last-child) { margin: 0 0 15px; }*/
            .header .message { font-size: 20px; line-height: 20px; padding: 15px 0 2px; /*border-top: 1px solid #DD1111;*/ }
            .file-info { padding: 10px 30px; background: #810D0D; color: #fff; font-size: 11px; }
            .message code { font-family: "Droid Sans", sans-serif; }
            .message > * { display: inline; }
            .message code,
            .highlight { color: #ffb6c1; }
            .mono { font-family: "Droid Sans Mono", "Consolas", "Bitstream Vera Sans Mono", "Courier New", Courier, monospace !important; }
            .stack-trace { background: #ccc; padding: 25px 30px 30px; }
            h2 { font-weight: normal; font-size: 18px; margin: 0 0 20px; }
            h3 { font-weight: normal; font-size: 12px; }
            .string { color: red; }
            .variable { color: #00f; }
            .variable.no-value { color: gray; }
            .int, .float { color: #0099CC; }
            .array, .object, .keyword { color: #060; }
            .constant, .null, .true, .false { color: #A132FF; }
            .hellip { color: orange; text-decoration: underline; }
            .trace { border: 1px solid #fff; box-shadow: 2px 2px 0 #555; }
            .trace:not(:last-child) { margin: 0 0 20px; }
            .trace .header { line-height: 14px; background: #fff; padding: 12px 20px; color: #000; cursor: pointer; }
            .trace .file-info { padding: 7px 20px; font-size: 10px; background: #eee; border-top: 1px solid #ddd; color: #000; }
            .trace .syntaxhighlighter { font: 12px/16px 'Meslo' !important; padding: 5px !important; }
            .trace .syntaxhighlighter .gutter .line { border-right: 1px solid #fff !important; }
            .trace .syntaxhighlighter .gutter .line.highlighted { background: #fff !important; }

            .introduction { color: #eee; background: #E60000; padding: 2px; }
            .introduction .highlight { color: #ff0; }

            .footer { margin: 20px 100px; color: #8ECCFF; font-size: 11px; }

            #header-top { border-top: #f00 1px solid; font-size: 11px; }
            .clear-left { clear: left; }
            [rel=tooltip] { display: inline-block; }
            .quote { color: #ddd; }
        </style>
    </head>
    <body>

    <header>
        <h1>Rax <span>Full Stack PHP Framework</span></h1>
    </header>


    <div class="container">
        <div class="header" id="header-top">
            <div class="introduction" style="float: left;">
                <?php
                $a = array();
                if ($e instanceof ErrorException) {
                    echo '<span class="highlight">'.Rax\Mvc\Debugger::$levels[$code].'</span> Uh-oh an error occurred:';
                } else {
                    echo '<span class="highlight">'.$reflection.'</span> Uh-oh an exception was thrown:';
                }
                ?>
            </div>
            <div class="message clear-left">
                <span class="quote">&ldquo;</span><?php echo trim($message); ?><span class="quote">&rdquo;</span>
            </div>
        </div>
        <div class="file-info" style="border-top: 1px solid #670101;">
            <code title="<?php echo $file; ?>" rel="tooltip">
                <?php echo Debug::filePath($file, function($dir, $file) { return '<span class="highlight">'.$dir.'</span>'.$file; }); ?>
                [ <span class="highlight"><?php echo $line; ?></span> ]
            </code>
        </div>

        <div class="source-code">
            <?php echo Debug::highlightSourceCode($file, $line); ?>
        </div>

        <div class="stack-trace">
            <h2>Stack Trace</h2>

            <?php foreach ($trace as $i => $step): ?>
            <div class="trace">
                <div class="header">
                    <div style="float: right;">#<?php echo $i; ?></div>
                    <h3 class="code meslo">
<?php

$signature = '';
if ($step['class']) {
    $signature = '<span>'.$step['class'].'</span><span class="keyword">'.$step['type'].'</span>';
}
$signature .= '<span class="keyword">'.$step['function'].'</span>';

if (Debug::isLanguageConstruct($step['function'])) {
    $signature .= ' '.Debug::filePath($step['args'], function($dir, $file) { return '<span class="constant">'.$dir.'</span>.<span class="string">\''.substr($file, 1); }).'\'</span>';
    $signature .= ';';
} else {
    $signature .= '(';

    $i = 0;
    $count = count($step['args']);
    foreach ($step['args'] as $variable => $value) {
        if ($value === Debug::NO_VALUE) {
            $signature .= '<span class="variable no-value">$'.$variable.'</span>';
        } else {
            $signature .= '<span class="variable">$'.$variable.'</span> <span class="keyword">=</span> ';
            $signature .= Debug::value($value, function($value, $type) {
                if ($type === 'array') {
                    $value = 'array(<span class="hellip">&hellip;</span>)';
                } elseif ($type === 'object') {
                    $value = substr($value, 0, strpos($value, ' ')).'(<span class="hellip">&hellip;</span>)';
                }
                return '<span class="'.$type.'">'.$value.'</span>';
            }, 30);
        }

        if (++$i < $count) {
            $signature .= ', ';
        }
    }

    $signature .= ');';
}

    echo $signature;
?>

                    </h3>
                    <table>

                    </table>
                </div>
                <div class="file-info">
                    <code rel="tooltip" title="<?php echo $step['file']; ?>" class="clearfix">
                        <?php echo Debug::filePath($step['file'], function($dir, $file) { return '<span class="constant">'.$dir.'</span>'.$file; }); ?>
                        [ <span class="constant"><?php echo $step['line']; ?></span> ]
                    </code>
                </div>
                <div class="source-code">
                    <?php echo $step['source']; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

        <script src="/assets/js/vendor/jquery-1.8.0.js"></script>
    <script src="/assets/js/vendor/syntaxhighlighter/XRegExp.js"></script>
    <script src="/assets/js/vendor/syntaxhighlighter/shCore.js"></script>
    <script src="/assets/js/vendor/syntaxhighlighter/shBrushPhp.js"></script>
        <script src="/assets/js/vendor/bootstrap/js/bootstrap.min.js"></script>
        <script>
            $(function() {
                SyntaxHighlighter.all();
                $('body').tooltip({selector: '[rel=tooltip]'});
            });
        </script>
    </body>
</html>
