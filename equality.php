<?php
$labels = ['true', 'false', '1', '0', '-1', '"1"', '"0"', '"-1"', 'null', 'array()', 'array(1)', 'array("php")', '"php"', '""', NAN];
$values = [true, false, 1, 0, -1, "1", "0", "-1", null, array(), array(1), array("php"), "php", "", NAN];
$equality = [];

// init rows and cols
foreach($labels as $krow => $label_rows) {
    $equality[$krow] = [];

    foreach($labels as $kcol => $label_cols) {
        $equality[$krow][$kcol] = ($values[$krow] == $values[$kcol] ? "true" : "false");
    }
}
?>
<style type="text/css">
table.equality th {
    width:5em;
    color: white;
    background-color: gray;
    font-weight:normal;
    font-family:monospace;
}
table.equality th, table.equality td {
    padding: .5em;
}
table.equality td.false {
    background-color: #FFAAAA;   
}
table.equality td.itself-false {
    background-color: #FFCCCC;   
}

table.equality td.true {
    background-color: #AAFFAA;   
}
table.equality td.itself-true {
    background-color: #CCFFCC;   
}
</style>

<table class="equality">
    <thead>
        <tr>
            <th>==</th>
<?php foreach($labels as $label): ?>
            <th><?php echo $label ?></th>

<?php endforeach ?>
        </tr>
    </thead>
    <tbody>
<?php foreach($labels as $krow => $label): ?>
        <tr>
            <th><?php echo $label ?></th>

<?php   foreach($equality[$krow] as $kcol => $equality_col): ?>
            <td class="<?php echo ($krow == $kcol ? 'itself-' : '').$equality_col ?>"><?php echo $equality_col ?></td>

<?php   endforeach ?>
        </tr>

<?php endforeach ?>
    </tbody>
</table>

<pre><?php echo system('php -v') ?></pre>