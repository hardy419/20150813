<?php
header("Content-type: text/html; charset=utf-8");

$src = 'merit.txt';
$file = 'chinese_painting.html';
$img_dir = './Img/3_Merit.jpeg/Chi/';
$prefix = 'artwork_c';
$columns = 14;
$abbr = 'M';
$id_index = 2;

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

$templ = file_get_contents('artwork_sXX.html');

$img_files = scandir($img_dir);

$index = 1;

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

    // clone $templ
    $new_filecontents = $templ;

    // Replacement
    $str1=array();
    $str2=array();
    array_push($str1, '##name##', '##name_e##');
    array_push($str2, $data[$dindex][4], $data[$dindex][5]);
    array_push($str1, '##school##', '##school_e##');
    array_push($str2, $data[$dindex][2], $data[$dindex][3]);
    array_push($str1, '##work##', '##work_e##');
    array_push($str2, $data[$dindex][8], $data[$dindex][9]);
    array_push($str1, '##age##');
    array_push($str2, $data[$dindex][6]);
    array_push($str1, '##file##', '##file_s##', '##index##');
    array_push($str2, $img_dir.$img_file, $img_dir.$img_file_s, sprintf("%'02d", $dindex));
    array_push($str1, '##back_url##');
    array_push($str2, $file);

    $teachers = preg_split ("/,[\s*]/", $data[$dindex][10]);
    $teachers_e = preg_split ("/,[\s*]/", $data[$dindex][11]);
    $teacher_html = '';
    foreach ($teachers as $key=>$teacher) {
        $teacher_html .= '<p class="chi">'.$teacher.'</p><p class="eng">'.$teachers_e[$key].'</p><p class="chi">&nbsp;</p>';
    }
    array_push($str1, '##teacher##');
    array_push($str2, $teacher_html);
    $new_filecontents = str_replace($str1,$str2,$new_filecontents);

    file_put_contents($prefix.sprintf("%'02d", $dindex).'.html', $new_filecontents);

    echo '<h3>File: '.$prefix.sprintf("%'02d", $dindex).'.html generated!</h3>';

    $index++;
}

echo '<h2>Done!</h2>';
?>