<?php 

 $PID= shell_exec('./Scripts/PID.sh " \\`fastx_trimmer -f 12 -i Users/demo/dos/File_FastaQ -o Users/demo/dos/File_FastaQtmp -v > Users/demo/dos/Reporte_STDOUT 2>&1\\` " >> /dev/null & echo $!');
?>
