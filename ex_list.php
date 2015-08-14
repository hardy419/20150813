<?php
header("Content-type: text/html; charset=utf-8");

$src = 'merit.txt';
$data = array();
$line_num = 0;

$fh = fopen($src, 'r');
while ($line = fgets($fh)) {
    $line_num++;
    $data[$line_num] = explode("\t", $line);
    echo '<h3>Line '.$line_num.': '.var_export($data[$line_num],1).'</h3>';
}
fclose($fh);

print_r ($data);

$file = 'chinese_painting.html';

$list_unit = file_get_contents('list_unit.txt');
$list_templ = file_get_contents('list_templ.txt');

$img_files = scandir('./Img/Artwork_3.Merit/ÖÐ‡ø•ø®‹/');

$index = 1;

echo '<h3></h3>';

foreach ($img_files as $img_file) if('S' == $img_file[0] && strpos($img_file, ']')) {
    $img_file = iconv('GBK', 'UTF-8', $img_file);
    echo '<h2>Img File: '.$img_file.'</h2>';

    $id = substr($img_file,0,4);
    $dindex = -1;

    // find $id in $data
    foreach ($data as $key=>$dat) {
        if($id == $dat[0]) {
            $dindex = $key;
            break;
        }
    }

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
    array_push($str2, $data[$dindex][3], $data[$dindex][4]);
    array_push($str1, '##school##', '##school_e##');
    array_push($str2, $data[$dindex][1], $data[$dindex][2]);
    array_push($str1, '##work##', '##work_e##');
    array_push($str2, $data[$dindex][6], $data[$dindex][7]);
    array_push($str1, '##file##', '##index##');
    array_push($str2, $img_file, sprintf("%'02d", $index));

    $new_unit = str_replace($str1,$str2,$new_unit);

    $list_templ = str_replace('##list_unit##', "{$new_unit}##list_unit##", $list_templ);

    echo '<h3>Key = '.$dindex.'</h3>';

    $index++;
}

$list_templ = str_replace('##list_unit##', '', $list_templ);

file_put_contents($file, $list_templ);

echo '<h2>Done!</h2><h3>('.($index-1).' Total)</h3>';
?>