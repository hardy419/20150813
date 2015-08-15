<?php
header("Content-type: text/html; charset=utf-8");

$src = 'golden.txt';
$file = 'golden_prize.html';
$img_dir = './Img/1_Gold.jpeg/';
$prefix = 'artwork_g';
$columns = 16;
$abbr = 'G';
$id_index = 1;

$data = array();
$line_num = 0;
$fh = fopen($src, 'r');
while ($line = fgets($fh)) {
    $line_num++;
    $data[$line_num] = explode("\t", $line);
    while (count ($data[$line_num]) < $columns && $line = fgets($fh)) {
        $k = count ($data[$line_num]) - 1;
        $frags = explode("\t", $line);
        $data[$line_num][$k] .= $frags[0];
        $data[$line_num][$k] = str_replace ('"', '', $data[$line_num][$k]);
        $i = 1;
        while (isset ($frags[$i])) {
            $data[$line_num][++$k] = $frags[$i];
            ++$i;
        }
    }
    echo '<h3>Line '.$line_num.': '.var_export($data[$line_num],1).'</h3>';
}
fclose($fh);

print_r ($data);

$list_unit = file_get_contents('list_unit.txt');
$list_templ = file_get_contents('list_templ.txt');

$img_files = scandir($img_dir);

$list_unit_array = array ();
$index = 1;

echo '<h3></h3>';

foreach ($img_files as $img_file) if('.'!=$img_file && '..'!=$img_file && false===strpos($img_file, '[2]')) {
    $img_file = iconv('GBK', 'UTF-8', $img_file);
    $img_file_s = substr ($img_file, 0, strlen($img_file)-4) . '[2].jpg';
    echo '<h2>Img File: '.$img_file.'</h2>';
/*
    $id = substr($img_file,0,4);
    $dindex = -1;

    // find $id in $data
    foreach ($data as $key=>$dat) {
        if($id == $dat[0]) {
            $dindex = $key;
            break;
        }
    }
*/
    // Find id in image file names
    $frags = preg_split ("/[{$abbr}_]/", $img_file);
    $dindex = $frags[$id_index];

    if (-1 == $dindex) {
        echo '<h3>ID #'.$id.' Not found in data!</h3>';
        continue;
    }

    // clone $list_unit
    $new_unit = $list_unit;

    // Replacement
    $str1=array();
    $str2=array();
    array_push($str1, '##name##', '##name_e##');
    array_push($str2, $data[$dindex][4], $data[$dindex][5]);
    array_push($str1, '##school##', '##school_e##');
    array_push($str2, $data[$dindex][2], $data[$dindex][3]);
    array_push($str1, '##work##', '##work_e##');
    array_push($str2, $data[$dindex][8], $data[$dindex][9]);
    array_push($str1, '##file##', '##file_s##', '##index##', '##prefix##');
    array_push($str2, $img_dir.$img_file, $img_dir.$img_file_s, sprintf("%'02d", $dindex), $prefix);

    $list_unit_array[$dindex] = str_replace($str1,$str2,$new_unit);

    echo '<h3>Key = '.$dindex.'</h3>';

    $index++;
}

$i = 1;
while (isset ($list_unit_array[$i])) {
    $list_templ = str_replace('##list_unit##', "{$list_unit_array[$i]}##list_unit##", $list_templ);
    $i++;
}
$list_templ = str_replace('##list_unit##', '', $list_templ);

file_put_contents($file, $list_templ);

echo '<h2>Done!</h2><h3>('.($i-1).' Total)</h3>';
?>