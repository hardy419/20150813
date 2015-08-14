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

$templ = file_get_contents('artwork_sXX.html');

$img_files = scandir('./Img/Artwork_3.Merit/MEDIA/');

$index = 1;

foreach ($img_files as $img_file) if('S' == $img_file[0] && !strpos($img_file, ']')) {
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

    // clone $templ
    $new_filecontents = $templ;

    // Replacement
    $str1=array();
    $str2=array();
    array_push($str1, '##name##', '##name_e##');
    array_push($str2, $data[$dindex][3], $data[$dindex][4]);
    array_push($str1, '##school##', '##school_e##');
    array_push($str2, $data[$dindex][1], $data[$dindex][2]);
    array_push($str1, '##work##', '##work_e##');
    array_push($str2, $data[$dindex][6], $data[$dindex][7]);
    array_push($str1, '##age##');
    array_push($str2, $data[$dindex][5]);
    array_push($str1, '##file##', '##index##');
    array_push($str2, $img_file, sprintf("%'02d", $index));
    array_push($str1, '##teacher##');
    array_push($str2, '<p class="chi">'.$data[$dindex][8].'</p><p class="eng">'.$data[$dindex][9].'</p><p class="chi">&nbsp;</p>##teacher##');
    $new_filecontents = str_replace($str1,$str2,$new_filecontents);
    $i = 10;
    while(isset($data[$dindex][$i]) && isset($data[$dindex][$i+1])) {
        $new_filecontents = str_replace('##teacher##','<p class="chi">'.$data[$dindex][$i].'</p><p class="eng">'.$data[$dindex][$i+1].'</p><p class="chi">&nbsp;</p>##teacher##',$new_filecontents);
        $i+=2;
    }
    $new_filecontents = str_replace('##teacher##','',$new_filecontents);

    file_put_contents('artwork_m'.sprintf("%'02d", $index).'.html', $new_filecontents);

    echo '<h3>File: artwork_m'.sprintf("%'02d", $index).'.html generated!</h3>';

    $index++;
}

echo '<h2>Done!</h2>';
?>