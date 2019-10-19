<?php
const LOG_FILE = 'log';
const OLD_FILE = 'bin/old';

const NEW_FILE = 'testfile';

if (file_exists(NEW_FILE)) {

    $size_of_new = filesize(NEW_FILE);
    $size_of_old = filesize(OLD_FILE);

    if ($size_of_new != $size_of_old) {
        $last_modified = stat(NEW_FILE)['mtime'];

        $log = "File modified: " . date("d m Y H:i:s\n", $last_modified);

        $data1 = fopen(NEW_FILE, "r");
        $data2 = fopen(OLD_FILE, "r");

        $line_count = 1;

        $largest = $size_of_new > $size_of_old ? $data1 : $data2;

        while (!feof($largest)) {
            $line1 = fgets($data1);
            $line2 = fgets($data2);

            if ($line1 != $line2) {
                if($line1 == '' || $line1 == null) {
                    $log .= "line $line_count: removed \n";
                } elseif($line2 == '' || $line2 == null) {
                    $log .= "added new line $line_count: ". str_replace("\n", '', $line1) . "\n";
                } else {
                    $log .= "line $line_count: '$line2' changed to '$line1' \n";
                }
            }
            $line_count++;
        }

        fclose($data1);
        fclose($data2);

        copy(NEW_FILE, OLD_FILE) ? 'copied' : 'error while copying';
        file_put_contents(LOG_FILE, $log."\n", FILE_APPEND);
    }
} else {
    echo "File does not exist\n";
}
