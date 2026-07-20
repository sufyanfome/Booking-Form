<?php
header("Access-Control-Allow-Origin: *");

$url = 'https://fome.agency/update-booking-forms-new/get_updated_files.php';

$result = file_get_contents($url);


$files = json_decode($result);
$backupFilesDir = 'backup_files';
if(!is_dir($backupFilesDir)){
	mkdir($backupFilesDir);
}

$backupFiles = scandir($backupFilesDir);

//Delete old backups
foreach($backupFiles as $folder){
	if($folder !== '.' && $folder !== '..') {
		deleteDir($backupFilesDir.'/'.$folder);
	}
}

$timestamp = time();
$newBackupFolder = $backupFilesDir.'/'.$timestamp;
mkdir($newBackupFolder);

$updatedFiles = array();

foreach($files as $file => $content){
	file_put_contents($newBackupFolder.'/'.basename($file), file_get_contents($file));
	file_put_contents($file, $content);
	$updatedFiles[] = $file;
}

function deleteDir($dir) {
    $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
	$files = new RecursiveIteratorIterator($it,
				 RecursiveIteratorIterator::CHILD_FIRST);
	foreach($files as $file) {
		if ($file->isDir()){
			rmdir($file->getRealPath());
		} else {
			unlink($file->getRealPath());
		}
	}
	rmdir($dir);
}

foreach($updatedFiles as $v) {
	echo 'Updated <b>'.$v.'</b><br />';
}

echo '<div>Done</div>';