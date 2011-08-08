<html>
    <head>
        <title><?= $e->getMessage(); ?></title>
        <style type="text/css">
                body {
                        width : 800px;
                        margin : auto;
                }

                ul.code {
                        border : inset 1px;
                }
                ul.code li {
                        white-space: pre ;
                        list-style-type : none;
                        font-family : monospace;
                }
                ul.code li.line {
                        color : red;
                }

                table.trace {
                        width : 100%;
                        border-collapse : collapse;
                        border : solid 1px black;
                }
                table.thead tr {
                        background : rgb(240,240,240);
                }
                table.trace tr.odd {
                        background : white;
                }
                table.trace tr.even {
                        background : rgb(250,250,250);
                }
                table.trace td {
                        padding : 2px 4px 2px 4px;
                }
        </style>
    </head>
    <body>
        <h1>Uncaught <?= get_class( $e ); ?></h1>
        <h2><?= $e->getMessage(); ?></h2>
        <p>
                An uncaught <?= get_class( $e ); ?> was thrown on line <?= $line; ?> of file <?= basename( $file ); ?> that prevented further execution of this request.
        </p>
        <h2>Where it happened:</h2>
        <? if ( isset($lines) ) : ?>
        <code><?= $file; ?></code>
        <ul class="code">
                <? for( $i = $line - 5; $i < $line + 5; $i ++ ) : ?>
                        <? if ( $i > 0 && $i < count( $lines ) ) : ?>
                                <? if ( $i == $line-1 ) : ?>
                                        <li class="line"><?= str_replace( "\n", "", $lines[$i] ); ?></li>
                                <? else : ?>
                                        <li><?= str_replace( "\n", "", $lines[$i] ); ?></li>
                                <? endif; ?>
                        <? endif; ?>
                <? endfor; ?>
        </ul>
        <? endif; ?>

        <? if ( is_array( $e->getTrace() ) ) : ?>
        <h2>Stack trace:</h2>
                <table class="trace">
                        <thead>
                                <tr>
                                        <td>File</td>
                                        <td>Line</td>
                                        <td>Class</td>
                                        <td>Function</td>
                                        <td>Arguments</td>
                                </tr>
                        </thead>
                        <tbody>
                        <? foreach ( $e->getTrace() as $i => $trace ) : ?>
                                <tr class="<?= $i % 2 == 0 ? 'even' : 'odd'; ?>">
                                        <td><?= isset($trace[ 'file' ]) ? basename($trace[ 'file' ]) : ''; ?></td>
                                        <td><?= isset($trace[ 'line' ]) ? $trace[ 'line' ] : ''; ?></td>
                                        <td><?= isset($trace[ 'class' ]) ? $trace[ 'class' ] : ''; ?></td>
                                        <td><?= isset($trace[ 'function' ]) ? $trace[ 'function' ] : ''; ?></td>
                                        <td>
                                                <? if( isset($trace[ 'args' ])) : ?>
                                                        <? foreach ( $trace[ 'args' ] as $i => $arg ) : ?>
                                                                <span title="<?//= is_array($argvar_export( $arg, true ); ?>"><?= gettype( $arg ); ?></span>
                                                                <?= $i < count( $trace['args'] ) -1 ? ',' : ''; ?>
                                                        <? endforeach; ?>
                                                <? else : ?>
                                                NULL
                                                <? endif; ?>
                                        </td>
                                </tr>
                        <? endforeach;?>
                        </tbody>
                </table>
        <? else : ?>
                <pre><?= $e->getTraceAsString(); ?></pre>
        <? endif; ?>
    </body>
</html>