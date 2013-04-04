<?php use Rax\Mvc\Debug; ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title><?php echo $message; ?> &mdash; Rax</title>
        <link href="http://fonts.googleapis.com/css?family=Droid+Sans:400,700" rel="stylesheet">
        <link href="/css/font-awesome.css" rel="stylesheet">
        <link href="/css/normalize.css" rel="stylesheet">
        <link href="/css/error2.css" rel="stylesheet">
    </head>
    <body>
        <header>
            <div class="logo">Rax <span>Admin</span></div>
        </header>

        <div class="container">
            <aside>
                <ul>
                    <li><a href="#" class="active">Exception <i class="icon icon-caret-right"></i></a></li>
                    <li><a href="#">Environment</a></li>
                    <li><a href="#">UA</a></li>
                    <li><a href="#">Other</a></li>
                </ul>
            </aside>

            <section>
                <header>
                    <h1>Exception</h1>
                </header>

                <div class="content">
                    <dl>
                        <dt><span>Error Message</span></dt>
                        <dd class="message">
                            <i class="icon icon-quote-left"></i>
                            <?php echo trim($message); ?>
                            <i class="icon icon-quote-right"></i>
                        </dd>

                        <dt><span>Stack Trace</span></dt>
                        <dd class="stack">
                            <div class="file">
                                <code title="<?php echo $file; ?>">
                                    <i class="icon icon-file"></i>
                                    <?php echo Debug::filePath($trace[0]['file'], function ($dir, $file){
                                        return '<span class="highlight">'.$dir.'</span>'.$file;
                                    }); ?>
                                    [ <span class="highlight"><?php echo $trace[0]['line']; ?></span> ]
                                </code>
                            </div>

                            <ul class="source-code">
                                <?php foreach ($trace as $i => $step): ?>
                                    <li class="<?php if (0 === $i) echo 'active' ?> slide slide-<?php echo $i ?>">
                                        <div class="slide-<?php echo $i ?>-source-code hide"><?php echo $step['source']; ?></div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <div class="source-code" style="position: relative; width: 100%; height: 350px; background: #272822;">
                                <div id="editor"><?php echo $trace[0]['source']; ?></div>
                            </div>
                            <div class="info clearfix">
                                <div class="calls">
                                    <ul>
                                        <?php $count = count($trace); foreach ($trace as $i => $step): ?>
                                            <li class="<?php if (0 === $i) echo 'active' ?> slide trigger slide-<?php echo $i ?>" id="slide-<?php echo $i ?>" data-line="<?php echo $step['line'] ?>">
                                                <a href="#"><span class="number"><?php echo $count - $i ?>.</span> &nbsp; <?php echo $step['call'] ?> <i class="icon icon-caret-right right"></i></a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <p class="help"><i class="icon-info-sign"></i> Click to make sticky</p>
                                </div>
                                <div class="args">
                                    <?php foreach ($trace as $i => $step): ?>
                                        <div class="<?php if (0 === $i) echo 'active' ?> slide slide-<?php echo $i ?>">
                                            <div class="header">
                                                <?php
                                                if (0 === $i) {
                                                    echo '<i class="icon-question-sign"></i> Browse the stack trace on the left for clues';
                                                } else {
                                                    echo $step['call'];
                                                }
                                                if (isset($step['args'])) {
                                                    echo '<span class="paren">(</span>';
                                                    foreach ($step['args'] as $var => $value) {
                                                        echo '<span class="arg">$'.$var.'</span>';
                                                        if ($value !== end($step['args'])) {
                                                            echo '<span class="comma">,</span> ';
                                                        }
                                                    }
                                                    echo '<span class="paren">)</span>';
                                                }
                                                ?>
                                            </div>

                                            <?php
                                                if (isset($step['args'])) {
                                                    Debug::dumpMethodArgs($step['args']);
                                                }
                                            ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </dd>
                    </dl>
                <div>
            </section>
        </div>

        <script src="http://code.jquery.com/jquery-2.0.0b2.js"></script>
        <script src="http://d1n0x3qji82z53.cloudfront.net/src-min-noconflict/ace.js"></script>
        <script>
            var editor = ace.edit('editor');
            editor.setTheme('ace/theme/monokai');
            editor.getSession().setMode('ace/mode/php');
            editor.getSession().setUseWrapMode(true);
            editor.setReadOnly(true);
            editor.setDisplayIndentGuides(false);
            editor.setShowPrintMargin(false);
            editor.gotoLine(<?php echo $trace[0]['line']; ?>);
            editor.centerSelection();
            editor.blur();

            var cancel = true;

            $('.slide.trigger').on({
                mouseenter: function(e) {
                    if (!$('.slide.locked').length && !$(this).hasClass('active')) {
                        $('.slide.active').removeClass('active');
                        $('.' + this.id).addClass('active');
                        editor.setValue($('.' + this.id + '-source-code').text());
                        editor.gotoLine($(this).data('line'));
                        editor.centerSelection();
                    }
                },
                click: function(e) {
                    e.preventDefault();
                    if ($(this).hasClass('locked')) {
                        $(this).removeClass('locked');
                    } else {
                        if (!$(this).hasClass('active')) {
                            editor.setValue($('.' + this.id + '-source-code').text());
                            editor.gotoLine($(this).data('line'));
                            editor.centerSelection();
                            $('.slide.active').removeClass('active');
                            $('.slide.locked').removeClass('locked');
                            $('.' + this.id).addClass('active');
                        }

                        $(this).addClass('locked');
                    }
                }
            });
        </script>
    </body>
</html>
