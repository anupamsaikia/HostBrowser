<?php
/*
HostBrowser v2.0.0
Developers: Snehanshu Phukon[snehanshu.glt@gmail.com], Anupam Saikia[ianupamsaikia@gmail.com]
GitHub: https://github.com/SnehanshuPhukon/HostBrowser

Note:   This software is released under GNU GPL liscence.
        Therefore any one can use it, distribute, sell 
        or modify without any permission from the author.
*/

//CONFIGURATIONS START
$conf = array();//This array will contain all the settings
$conf['password'] = "hbx";//This is the password for login
//CONFIGURATIONS END

session_start();
$start_time = time();
const V = '2.0.0';
define('URL', $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].':'.$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI']);
ignore_user_abort(true);
$pastebtn = false;
$GLOBALS['echo'] = "<!--\nHostBrowser v".V."\n
Developers: Snehanshu Phukon[snehanshu.glt@gmail.com], Anupam Saikia[ianupamsaikia@gmail.com]\n
GitHub: https://github.com/SnehanshuPhukon/HostBrowser\n
MADE WITH LOVE IN INDIA\n-->\n";

$GLOBALS['echo'] .="<!doctype html><html><head><meta http-equiv='Content-Type' content='text/html;charset=UTF-8'><meta name='robots' content='noindex,nofollow'><title>HostBrowser - A non-FTP Webhost Filemanager</title><style>body{font-family:arial;margin:0px;background-color:#E0EBF6;height:100%}a{color:#06F;text-decoration:none}a:hover{text-decoration:underline}input,select,textarea,button,.btn{border-radius:5px;background-color:#EAEAEA;border:solid 1px #03F;padding:3px;color:#000}input:hover,select:hover,textarea:hover,button:hover,.btn:hover{background-color:#C8C8C8;text-decoration:none}input:focus,textarea:focus{background-color:#555;color:#FFF;border:dashed 1px #fff;}footer{bottom:0px;width:95%;clear:both;overflow:hidden;background-color:#F3CECE;text-align:center;margin-top:50px;margin-left:0px;border-top:solid 1px #03F}.inline-menu{list-style-type:none}.inline-menu>li{display:inline-block;margin-left:4px}.block{background-color:#FFF;border:solid 1px #03F;border-radius:3px;margin-top:10px}.block-header{font-size:24px;font-weight:bold;padding:3px;background-color:#F3CECE}.block-body{padding:10px;background:transparent}.opt{font-size:12px;padding:0px;margin:0px}.main{padding:5px}.overview{width:100%;height:75%;float:left;clear:left;background-color:#E0EBF6;border:solid 1px #03F}.addr_bar{width:80%}.addr_bar>form>input{width:80%;padding:5px}.browser{list-style-type:none}.browser>li{padding:4px}.odd{background-color:#FFF}.even{}.even:hover,.odd:hover{background-color:#D9D9D9}</style></head><body>";

	$act = isset($_GET['act'])?$_GET['act']:'browse';
	if(isLogged()){
			switch($act){
				default:
					$GLOBALS['echo'] .= "<table style='width:100%;height:100%;' border='0'><tr><td valign='top' width='300' style='padding:0px;max-width:300px;'><div class='block' style='width:100%;float:left;'><div class='block-header'>HostBrowser v".V."</div><div class='block-body'>A non-FTP Webhost Filemanager<br><br><a href='".$_SERVER['PHP_SELF']."'>Home</a>&nbsp;<a href='?act=logout'>Logout</a></div></div><div class='block' style='width:100%;float:left;overflow:hidden;'><div class='block-header'>Clipboard</div><div class='block-body'>";

					if(isset($_SESSION['hb_clipboard'])){
						$pastebtn = true;
						$GLOBALS['echo'] .= "<ul>";
						$cb = explode('&',$_SESSION['hb_clipboard']);
						foreach($cb as $cbo){
							$cb_array = explode('=',$cbo);
							$file = urldecode( trim( $cb_array[0]));
							$mode = $cb_array[1];
							$GLOBALS['echo'] .= "<li title='$file'>$mode: <u>".htmlspecialchars(realpath($file))."</u></li>";
						}
						$GLOBALS['echo'] .= "</ul>";
					}else $GLOBALS['echo'] .= "Clipboard is empty";
					
					$GLOBALS['echo'] .= "</div></div>";
					$path = sanitizePath(isset($_GET['path'])?$_GET['path']:".");
					$ref = isset($_GET['ref'])?$_GET['ref']:".";
					$isDir = is_dir($path);
					$thisDir = sanitizePath( $isDir? $path: dirname($path));
					$parent = (isUpperDir($path)?$path.'../':dirname($path));
					$GLOBALS['echo'] .= "</td>";
					
					$GLOBALS['echo'] .= "<td valign='top' class='main'>";
					$GLOBALS['echo'] .= "<div class='addr_bar'><form><span>We are at <a href='?path=".urlencode($ref)."&ref=".urlencode($thisDir)."' title='Goto Last location' class='btn'>&larr;</a></span>&nbsp;<input type='hidden' name='act' value='browse'><input type='text' name='path' autocomplete='off' placeholder='Enter path here...' value='".htmlspecialchars(realpath($thisDir))."'>&nbsp;<button type='submit'>Go</button></form>";
					if(file_exists($path)){
						
						$GLOBALS['echo'] .= "</div>";
						clearstatcache();
						$files = @scandir($path);
						if($isDir){
							$i = 0;
							$i = 0;
							$browsedfiles = array();
							foreach($files as $file){
								if(is_dir($thisDir.$file) && $file != '.' && $file != '..'){
									$browsedfiles[] = "<tr class='".($i%2==0?"even":"odd")."'><td><input type='checkbox' name='obj[]' value='".htmlspecialchars($thisDir.$file)."'></td><td>[DIR] <a href='?path=".urlencode($thisDir.$file).'&ref='.urlencode($thisDir)."' title='Enter this Directory'>".htmlspecialchars($file,ENT_COMPAT,"UTF-8")."/</a></td><td></td><td></td><td><a class='opt' href='?act=exec&subact=rename&file=".urlencode($thisDir.$file)."&returnto=".urlencode(URL)."'>Rename</a>&nbsp;<a class='opt' href='?act=exec&subact=del&file=".urlencode($thisDir.$file)."&returnto=".urlencode(URL)."' style='color:red;'>Delete</a>&nbsp;<a class='opt' href='?act=exec&subact=copy&file=".urlencode($thisDir.$file)."&returnto=".urlencode(URL)."'>Copy</a>&nbsp;<a class='opt' href='?act=exec&subact=cut&file=".urlencode($thisDir.$file)."&returnto=".urlencode(URL)."'>Cut</a><!--&nbsp;<a class='opt' href='?act=exec&subact=perm&file=".urlencode($thisDir.$file)."&returnto=".urlencode(URL)."'>Permissions</a>-->&nbsp;<a class='opt' href='?act=dirprop&dir=".urlencode($thisDir.$file)."&returnto=".urlencode(URL)."'>Properties</a>&nbsp;<a class='opt' href='?act=compress&dir=".urlencode($thisDir.$file)."&returnto=".urlencode(URL)."'>Compress</a></td></tr>";
									$i++;
								}
							}
							$num_of_dir = $i;
							foreach($files as $file){
								if(!is_dir($thisDir.$file)){
									$mime = explode( '/',@mime_content_type($thisDir.$file));
									$browsedfiles[] = "<tr class='".($i%2==0?"even":"odd")."'><td><input type='checkbox' name='obj[]' value='".htmlspecialchars($thisDir.$file)."'></td><td><a href='?path=".urlencode($thisDir.$file).'&ref='.urlencode($thisDir)."' title='View this file'>".htmlspecialchars($file)."</a></td><td><span class='opt'>".goodSize(@filesize( $thisDir.$file))."</span></td><td><span class='opt'>".@$mime[0].'/'.@$mime[1]."</span></td><td><a class='opt' href='?act=exec&subact=rename&file=".urlencode($thisDir.$file)."&returnto=".urlencode(URL)."'>Rename</a>&nbsp;<a class='opt' href='?act=exec&subact=del&file=".urlencode($thisDir.$file)."&returnto=".urlencode(URL)."' style='color:red;'>Delete</a>&nbsp;<a class='opt' href='?act=exec&subact=copy&file=".urlencode($thisDir.$file)."&returnto=".urlencode(URL)."'>Copy</a>&nbsp;<a class='opt' href='?act=exec&subact=cut&file=".urlencode($thisDir.$file)."&returnto=".urlencode(URL)."'>Cut</a><!--&nbsp;<a class='opt' href='?act=exec&subact=perm&file=".urlencode($thisDir.$file)."&returnto=".urlencode(URL)."'>Permissions</a>-->&nbsp;<a class='opt' href='?path=".urlencode($thisDir.$file).'&ref='.urlencode($thisDir)."'>Properties</a>&nbsp;<a class='opt' href='?act=download&file=".urlencode($thisDir.$file)."' target='_blank'>Download</a>&nbsp;".( @$mime[0] == 'text'? "<a class='opt' href='?act=edit&file=".urlencode($thisDir.$file)."' target='_blank'>Edit</a>&nbsp;":'' ).( @$mime[1] == 'zip'? "<a class='opt' href='?act=exec&subact=extract&file=".urlencode($thisDir.$file)."&suggestedDest=".urlencode($path)."'>Extract</a>&nbsp;":'' )."</td></tr>";
									$i++;
								}
							}
							$num_of_files = $i - $num_of_dir;
							$GLOBALS['echo'] .= "<br><form method='post' action='?act=bulk'><input type='hidden' name='thisDir' value='$thisDir'><div class='bulk-operators'><span>Bulk actions: </span><button type='submit' name='act' value='del'>Delete</button>&nbsp;<button type='submit' name='act' value='COPY'>Copy</button>&nbsp;<button type='submit' name='act' value='CUT'>Cut</button><input type='hidden' name='returnto' value='".URL."'>&nbsp;<a href='?act=exec&subact=new&type=dir&to=".urlencode($thisDir)."&returnto=".urlencode(URL)."' class='btn'>New folder</a>&nbsp;<a href='?act=exec&subact=new&type=file&to=".urlencode($thisDir)."&returnto=".urlencode(URL)."' class='btn'>New file</a>&nbsp;<a href='?act=upload&to=".urlencode($thisDir)."' class='btn' target='_blank'>&uarr; Upload</a>&nbsp;<a href='?act=4mserver&to=".urlencode($thisDir)."' class='btn' target='_blank'>Download from another server</a>&nbsp;";
							
							$GLOBALS['echo'] .= "<table class='browser' border='0'>";
							$GLOBALS['echo'] .= "<tr class='even'><td></td><td><a href='?path=".urlencode($parent).'&ref='.urlencode($path)."'>&uarr; Up...</a></td></tr>";
							
							$GLOBALS['echo'] .= "<tr><td></td><th>Name</th><th>Size</th><th>MIME</th><th>Actions</th></tr>";

							if($pastebtn){
								$GLOBALS['echo'] .= " <a href='?act=exec&subact=paste&to=".urlencode($thisDir)."' class='btn'>Paste</a><br><br>";
							}
							if(!empty($browsedfiles)){
								foreach($browsedfiles as $file){
									$GLOBALS['echo'] .= $file;
								}
								$GLOBALS['echo'] .= "</form>";
								
								$GLOBALS['echo'] .= "</table><p>Directories: $num_of_dir and Files: $num_of_files</p>";
							}
							else{
								$GLOBALS['echo'] .= "</table><p style='color:red;font-size:25px;'>This folder is empty.</p>";
							}
							
						}
						else{
							$mime = explode('/',mime_content_type($path));
							msg("File Informations",($mime[0] == 'image'?"<img src='?act=download&file=".urlencode($path)."' width='300'>":($mime[0] == 'text'?"<pre style='text-align:left;max-width:500px;max-height:600px;overflow:scroll;background-color:#eee;'>".htmlspecialchars(file_get_contents($path))."</pre>":($mime[0] == 'audio'?"<audio controls><source src='?act=download&file=".urlencode($path)."' type='".$mime[0].'/'.$mime[1]."'><div style='padding:5em;background-color:#f24;color:#fff;font-size:20px;'>Sorry, Your browser doesn't support HTML5 audio player.</div></audio>":($mime[0] == 'video'?"<video controls width='500'><source src='?act=download&file=".urlencode($path)."' type='".$mime[0].'/'.$mime[1]."'><div style='padding:5em;background-color:#f24;color:#fff;font-size:20px;'>Sorry, Your browser doesn't support HTML5 video player.</div></video>":'<div style="padding:5em;background-color:#f24;color:#fff;font-size:20px;">No preview available for this file.</div>') )))."<ul class='browser'><li>Filename: ".htmlspecialchars(basename($path))."</li><li>Location: ".dirname(realpath($path))."</li><li>Filesize: ".goodSize(filesize($path))."</li><li>Filetype: ".$mime[0].'/'.$mime[1]."</li><li><a class='btn' href='?act=exec&subact=rename&file=".urlencode($path)."&returnto=".urlencode(URL)."'>Rename</a> <a class='btn' href='?act=exec&subact=del&file=".urlencode($path)."&returnto=".urlencode("?path=".$parent)."' style='color:red;'>Delete</a> <a class='btn' href='?act=exec&subact=copy&file=".urlencode($path)."&returnto=".urlencode(URL)."'>Copy</a> <a class='btn' href='?act=exec&subact=cut&file=".urlencode($path)."&returnto=".urlencode(URL)."'>Cut</a><!-- <a class='btn' href='?act=exec&subact=perm&file=".urlencode($path)."&returnto=".urlencode(URL)."'>Permissions</a>--> <a class='btn' href='?act=download&file=".urlencode($path)."' target='_blank'>Download</a> ".( @$mime[0] == 'text'? "<a class='btn' href='?act=edit&file=".urlencode($path)."' target='_blank'>Edit</a>":'' )." </li></ul>");
						}
					}
					else{
						throwError("This file/folder <u>".htmlspecialchars(realpath($path))."</u> doesn't exists.");
					}
                break;
                case "4mserver":
                    $to = isset($_GET['to'])?$_GET['to']:'./';
                    if(isset($_POST['link'])){
                        $local = isset($_POST['local'])?$_POST['local']:mt_rand(100,999).".EXT";
                        $itime = time();
                        $res = copy($_POST['link'],$to.'/'.$local);
                        $ftime = time();
                        if($res){
                            msg("Successfully downloaded","The file was successfully downloaded from <u>".htmlspecialchars($_POST['link'])."</u> and saved to <u>".htmlspecialchars(realpath($to.'/'.$local))."</u>.<p><h3>Sats</h3><table cellpadding='4'><tr><td>Time taken</td><td>".($ftime-$itime)." sec</td></tr><tr><td>Average speed</td><td>".(goodSize(filesize($to.'/'.$local)/($ftime-$itime==0?1:$ftime-$itime)))."/s</td></tr></table></p>" );
                        }
                        else{
                            throwError("Oops! The file could not be downloaded from <u>".htmlspecialchars($_POST['link'])."</u>");
                        }
                    }else{
                        msg("Download file from another server","<p>You may download any file from any server. Put the URL below. It will be saved to <u>".htmlspecialchars(realpath($to))."</u></p><form method='post' action='?act=4mserver&to=".urlencode($to)."'><table><tr><td>URL:</td><td><input type='text' placeholder='http://' name='link' required autofocus></td></tr><tr><td>Local filename:</td><td><input type='text' name='local'></td></tr><tr><td></td><td><input type='submit' value='Download'></td></tr></table></form><p><b>Note:</b> The file should not be password protected.</p>");
                    }
                break;
				case "dirprop":
					$dir = isset($_GET['dir'])?$_GET['dir']:"./";
					if(is_dir($dir)){
						msg("Folder properties","Foldername: ".htmlspecialchars(basename($dir))."<br>Location: ".htmlspecialchars(sanitizePath(dirname($dir)))."<br>Size: ".goodSize(foldersize($dir))."<br><a href='?path=".urlencode($dir)."&ref=".urlencode(dirname($dir))."'>See inside this folder</a>");
					}else{
						throwError("That's not a folder you know!");
					}
				break;
				case "download":
					set_time_limit(0);
					$fullPath = isset($_GET['file'])?$_GET['file']:"";
					if(file_exists($fullPath) && !is_dir($fullPath) ){
						if($fd = fopen($fullPath,'r')){
							header("Content-type:".mime_content_type($fullPath));
							header("Content-Disposition:attachment;filename=".rawurlencode(basename($fullPath)));
							header('Content-lenght:'.filesize($fullPath));
							header('Cache-control:private');
							while(!feof($fd)){
								echo fread($fd,2048);
							}
							fclose($fd);
							exit();
						}else throwError("File couldn't be downloaded because HostBrowser couldn't open it using PHP's <code><a href='https://www.google.com/search?q=fopen()+in+PHP' target='_blank'>fopen()</a></code> function.");
					}else throwError("File couldn't be downloaded because either it doesn't exists or you don't have permission to download it.");
				break;
				case "upload":
					if(isset($_FILES['file']) && isset($_GET['confirm'])){
						$uto = isset($_POST['to'])?$_POST['to']:'/';
						if(file_exists($uto)){
							$ok = false;
							for($i=0;$i<10;$i++){
								if(move_uploaded_file($_FILES['file']['tmp_name'][$i], $uto.'/'.$_FILES['file']['name'][$i]))$ok = true;
							}
							if($ok){
								msg("Successful","The file(s) were successfuly uploaded and saved to <u>/".htmlspecialchars($uto)."</u>.");
							}
							else{
								throwError("Some file(s) couldn't be uploaded.");
							}
						}
						else{
							throwError("The destination <u>".htmlspecialchars($uto)."</u> doesn't exists.");
						}
					}
					$to = isset($_GET['to'])?$_GET['to']:'/';
					msg("Upload files","Upload your files to <u>.htmlspecialchars(realpath($to)).</u>.<br><br><form action='?act=upload&returnto=".URL."&confirm=1' method='post' enctype='multipart/form-data'><input type='file' name='file[]'><br><input type='file' name='file[]'><br><input type='file' name='file[]'><br><input type='file' name='file[]'><br><input type='file' name='file[]'><br><input type='file' name='file[]'><br><input type='file' name='file[]'><br><input type='file' name='file[]'><br><input type='file' name='file[]'><br><input type='file' name='file[]'><br><br><input type='submit' value='Upload'><input type='hidden' name='to' value='$to'></form>");
				break;
				case "bulk":
					if(isset($_POST['act'])){
						$selected = isset($_POST['obj'])?$_POST['obj']:array();
						switch($_POST['act']){
							case "del":
								foreach($selected as $obj){
									deleteObj($obj);
								}
								header('location:'.$_POST['returnto']);
								exit();
							break;
							case "COPY":
							case "CUT":
								$i =1;
								foreach($selected as $obj){
									if($i != 1)$_SESSION['hb_clipboard'] .= "&";
									$_SESSION['hb_clipboard'] .= urlencode($obj)."=".$_POST['act'];
									$i++;
								}
								header('location:'.$_POST['returnto']);
								exit();
							break;
						}
					}
				break;
				case "compress":
					if(isset($_GET['confirm'])){
						$dir = isset($_POST['dir'])?$_POST['dir']:null;
						$res = zip($dir,(isset($_POST['zipname'])?$_POST['zipname']:"New zip").'.zip',dirname($dir));
						if($res != false){
							msg("Succesfully compressed","The folder ".htmlspecialchars($dir)." was successfully compressed to <u>".htmlspecialchars(realpath(sanitizePath($res)))."</u>.<br><br><a href='".$_GET['returnto']."' class='btn'>Ok</a>");
						}else throwError("Failed!<br><br><a href='javascript:history.go(0)' class='btn'>Try again</a>&nbsp;&nbsp;<a href='".$_GET['returnto']."' class='btn'>Ok</a>");
					}
					else{
						$dir = isset($_GET['dir'])?$_GET['dir']:"./";
						msg("Compress folder","<form method='post' action='?act=compress&confirm=1&returnto=".urlencode($_GET['returnto'])."'>Compress the folder <u>".htmlspecialchars(realpath($dir))."</u> to a new zip file.<br>Choose a name: <input type='text' name='zipname' value='".htmlspecialchars(basename($dir))."-compressed'>.zip <input type='submit' value='Compress'> <a class='btn' href='".$_GET['returnto']."'>Cancel</a><input type='hidden' name='dir' value='".htmlspecialchars($dir)."'></form>");					}
				break;
				case "exec":
					$subact = isset($_GET['subact'])?$_GET['subact']:'rename';
					switch($subact){
						case 'del':
							if(isset($_GET['confirm'])){
								$f2del = isset($_GET['file'])?$_GET['file']:'';
								if(deleteObj($f2del)){
									msg("Succesful","The file <u>".htmlspecialchars(realpath($f2del))."</u> was successfully deleted.<br><br>
									<a href='".$_GET['returnto']."' class='btn'>Ok</a>");
								}
								else{
									throwError("The file/folder <u>".htmlspecialchars(realpath($f2del))."</u> couldn't be deleted.<br><br>
									<a href='?act=exec&subact=del&confirm=1&file=".urlencode($f2del)."&returnto=".urlencode($_GET['returnto'])."' class='btn'>Try again</a>  <a href='".$_GET['returnto']."' class='btn'>Cancel</a>");
								}
							}
							else{
								msg('Delete file/folder',"Are you sure you want to delete <u>".htmlspecialchars(realpath($_GET['file']))."</u> ?<br><br><a href='?act=exec&subact=del&confirm=1&file=".urlencode($_GET['file'])."&returnto=".$_GET['returnto']."' class='btn'>Yes</a> <a href='".$_GET['returnto']."' class='btn'>No</a>");
							}
						break;
						case 'rename':
							if(isset($_GET['confirm'])){
								$f2ren = isset($_GET['file'])?$_GET['file']:'';
								$newname = (isset($_GET['newname'])?$_GET['newname']:"file_".mt_rand(100,999).".ext");
								if(@rename($f2ren,dirname($f2ren)."/".$newname)){
									msg("Successfully renamed","The file/folder <u>".htmlspecialchars(realpath($f2ren))."</u> was successfuly renamed to <u>".htmlspecialchars(realpath(dirname($f2ren)."/".$newname))."</u>.<br><br><a href='"."?path=".urlencode(dirname($f2ren))."' class='btn'>Ok</a>");
								}
								else{
									throwError("The file/folder <u>".htmlspecialchars(realpath($f2ren))."</u> couldn't renamed to <u>".htmlspecialchars(realpath($newname))."</u>.<br><br><a href='?act=exec&subact=rename&confirm=1&file=".urlencode($f2ren)."&newname=".urlencode($newname)."&returnto=".urlencode($_GET['returnto'])."' class='btn'>Try again</a> <a href='".$_GET['returnto']."' class='btn'>Cancel</a>");
								}
							}
							else{
								msg('Rename file/folder',"Rename this file/folder from <u>".htmlspecialchars(realpath($_GET['file']))."</u> to <br><br><form><input type='hidden' name='act' value='exec'><input type='hidden' name='subact' value='rename'><input type='hidden' name='confirm' value='1'><input type='text' name='newname' value='".htmlspecialchars(basename($_GET['file']))."'> <input type='submit' value='Rename'><input type='hidden' name='file' value='".htmlspecialchars($_GET['file'])."'><input type='hidden' name='returnto' value='".$_GET['returnto']."'> <a href='".$_GET['returnto']."' class='btn'>Cancel</a></form>");
							}
						break;
						case 'copy':
						case 'cut':
							$mode = strtoupper( $_GET['subact']);
							$file = isset($_GET['file'])?$_GET['file']:'/';
							$_SESSION['hb_clipboard'] = urlencode($file).'='.$mode;
							header('location:'.$_GET['returnto']);
							exit();
						break;
						case 'extract':
							if(isset($_GET['confirm'])){
								if(isset($_POST['file'])){
									if(file_exists($_POST['file'])){
										if(!is_dir($_POST['file'])){
											if(mime_content_type($_POST['file']) == "application/zip"){
												$dest = isset($_POST['to'])?$_POST['to']:".";
												$zip = new ZipArchive();
												if($zip->open($_POST['file'])){
												$zip->extractTo($dest)?msg("Succesfully extracted","The zip file <u>".htmlspecialchars(realpath($_POST['file']))."</u> was succesfully extracted.<br><br><a href='?path=".(isset($_GET['returnto'])?urlencode($_GET['returnto']):".")."' class='btn'>Ok</a>"):throwError("Sorry, couldn't open the requested zip file.");
												}
												else{
													throwError("Sorry, couldn't open the requested zip file.");
												}
												$zip->close();
											}
											else{
												throwError("Oops! I can extract only <b>zip</b> files (not even rar or tar). They have a mime content type of <i>application/zip</i>.");
											}
										}
										else{
											throwError("Oops! I can't extract a folder and I bet nobody can.");
										}
									}
									else{
										throwError("Hey, that file doesn't exists! No question of extracting it.");
									}
								}else{
									throwError("Incorrect action. Please specify an zip file to extract.");
								}
							}
							else{
								$suggestedDest = isset($_GET['suggestedDest'])?$_GET['suggestedDest']:".";
								msg("Extract zip archive","<form method='post' action='?act=exec&subact=extract&confirm=1&returnto=".urlencode($suggestedDest)."'>Extract to: <input type='text' name='to' value='".htmlspecialchars($suggestedDest)."'> <button type='submit'>Extract</button> <a href='?path=".urlencode($suggestedDest)."' class='btn'>Cancel</a><input type='hidden' value='".$_GET['file']."' name='file'></form>");
							}
						break;
						case 'paste':
							if(isset($_SESSION['hb_clipboard'])){
								$cb = explode('&',$_SESSION['hb_clipboard']);
								foreach($cb as $cbo){
									$cb_array = explode('=',$cbo);
									$file = urldecode( trim( $cb_array[0]));
									$mode = $cb_array[1];
									if (file_exists($file)){
										$ok = true;
										if (is_dir($file)){
											if($mode == "COPY"){
												if(copyDir($file, $_GET['to'])){
													msg("Successfuly copied","The directory <u>".htmlspecialchars(realpath($file))."</u> was successfuly copied to <u>".htmlspecialchars(realpath($_GET['to']))."</u><br><br><a href='?path=".urlencode($_GET['to'])."' class='btn'>Ok</a>");
												}
												else{
													$ok = false;
													throwError("The directory <u>".htmlspecialchars(realpath($file))."</u> couldn't be copied to <u>".htmlspecialchars(realpath($_GET['to']))."</u><br><br><a href='?act=exec&subact=paste&to=".urlencode($_GET['to'])."' class='btn'>Try again</a><a href='?path=".urlencode($_GET['to'])."' class='btn'>Cancel</a>");
												}
											}
											else{
												if(rename($file, $_GET['to'].'/'.basename($file))){
													msg("Successfuly moved","The directory <u>".htmlspecialchars(realpath($file))."</u> was successfuly moved to <u>".htmlspecialchars(realpath($_GET['to']))."</u><br><br><a href='?path=".urlencode($_GET['to'])."' class='btn'>Ok</a>");
												}
												else{
													$ok = false;
													throwError("The directory <u>".htmlspecialchars(realpath($file))."</u> couldn't be moved to <u>".htmlspecialchars(realpath($_GET['to']))."</u><br><br><a href='?act=exec&subact=paste&to=".urlencode($_GET['to'])."' class='btn'>Try again</a><a href='?path=".urlencode($_GET['to'])."' class='btn'>Cancel</a>");
												}
											}
										}
										else{
											$to = $_GET['to'].'/'.basename($file);
											if($mode == "COPY"){
												if( copy($file,$to)){
													msg("Successful","The file <u>".htmlspecialchars(realpath($file))."</u> was successfuly copied to <u>".htmlspecialchars(realpath($to))."</u><br><br><a href='?path=".urlencode($_GET['to'])."' class='btn'>Ok</a>");
												}else{
													$ok = false;
													throwError("Failed to copy the file <u>".htmlspecialchars(realpath($file))."</u> to <u>".htmlspecialchars(realpath($to))."</u><br><br><a href='?act=exec&subact=paste&to=".urlencode($_GET['to'])."' class='btn'>Try again</a><a href='?path=".urlencode($_GET['to'])."' class='btn'>Cancel</a>");
												}
											}
											else{
												if(rename($file,$to)){
													msg("Successful","The file <u>".htmlspecialchars(realpath($file))."</u> was successfuly moved to <u>".htmlspecialchars(realpath($to))."</u><br><br><a href='?path=".urlencode($_GET['to'])."' class='btn'>Ok</a>");
												}else{
													$ok = false;
													throwError("Failed to move the file from <u>".htmlspecialchars(realpath($file))."</u> to <u>".htmlspecialchars(realpath($to))."</u><br><br><a href='?act=exec&subact=paste&to=".urlencode($_GET['to'])."' class='btn'>Try again</a><a href='?path=".urlencode($_GET['to'])."' class='btn'>Cancel</a>");
												}
											}
										}
										
									}
								}
								if($ok) session_destroy();
							}
						break;
						case "new":
							$type = isset($_GET['type'])?$_GET['type']:"dir";
							if(isset($_POST['name'])){
								if($type == "dir"){
									if(mkdir($_POST['to'].'/'.$_POST['name'])){
										msg("Succesful","The folder <u>".htmlspecialchars(realpath($_POST['to'].'/'.$_POST['name']))."</u> was successfuly created.<br><br><a href='".$_POST['returnto']."' class='btn'>Ok</a>");
									}
									else{
										throwError("The folder <u>".htmlspecialchars(realpath($_POST['to'].'/'.$_POST['name']))."</u> couldn't be created.<br><br><a href='".$_POST['returnto']."' class='btn'>Cancel</a>");
									}
								}
								else{
									if($f = fopen($_POST['to'].'/'.$_POST['name'],'w')){
										fwrite($f,"Created on ".date("d-m-Y"));
										msg("Succesful","The file <u>".htmlspecialchars(realpath($_POST['to'].'/'.$_POST['name']))."</u> was successfuly created.<br><br><a href='".$_POST['returnto']."' class='btn'>Ok</a>");
									}
									else{
										throwError("The file <u>".htmlspecialchars(realpath($_POST['to'].'/'.$_POST['name']))."</u> couldn't be created.<br><br><a href='".$_POST['returnto']."' class='btn'>Cancel</a>");
									}
								}
							}
							else{
								$to = isset($_GET['to'])?$_GET['to']:"/";
								if($type == "dir"){
									msg("Create new folder","Create new folder in <u>".htmlspecialchars(realpath($to))."</u>.<br><br><form action='?act=exec&subact=new&type=dir' method='post'>Name: <input type='text' name='name' value='New folder'><br><br><input type='submit' value='Create'><input type='hidden' name='to' value='".htmlspecialchars($to)."'><input type='hidden' name='returnto' value='".$_GET['returnto']."'></form>");
								}
								else{
									msg("Create new file","Create new file in <u>".htmlspecialchars(realpath($to))."</u>.<br><br><form action='?act=exec&subact=new&type=file' method='post'>Name: <input type='text' name='name' value='New file.txt'><br><br><input type='submit' value='Create'><input type='hidden' name='to' value='".htmlspecialchars($to)."'><input type='hidden' name='returnto' value='".$_GET['returnto']."'></form>");
								}
							}
						break;
					}
				break;
				case "edit":
					$file = isset($_GET['file'])?$_GET['file']:'/';
					$save = isset($_POST['content'])?true:false;
					if(file_exists($file) && !is_dir($file)){
						if($save){
							if(file_put_contents($file, $_POST['content'])) $saveOk = true;
							else $saveOk = false;
						}else $saveOk = false;
						
						if($_COOKIE['editor']){
							$url = basename(__FILE__).'?act=edit&file='.urlencode($file);
							$name = htmlspecialchars(realpath($file));
							$content = htmlspecialchars(file_get_contents($file));
							$ext = pathinfo($file, PATHINFO_EXTENSION);
							

							echo "
<style type='text/css' media='screen'>
.ace_editor {
	position: relative !important;
	border: 1px solid lightgray;
	margin: auto;
	height: 500px;
	width: 90%;
}
.ace_editor.fullScreen {
	height: auto;
	width: auto;
	border: 0;
	margin: 0;
	position: fixed !important;
	top: 0;
	bottom: 0;
	left: 0;
	right: 0;
	z-index: 10;
}
.fullScreen {
	overflow: hidden
}
body{
	padding: 0px;
	margin: 0px;
	overflow: hidden;
}
#editor{
	margin: auto;
	position: absolute;
	top: 30px;
	bottom: 0;
	left: 0;
	right: 0;
}
* {
box-sizing: border-box;
}
.info{
	overflow: hidden;
	text-align: center;
	padding-left: 10px;
	line-height: 30px;
	height:30px;
	color:#555;
	font-family: monospace;
	display: inline-block;
}
.info:hover{
	background-color: #ddd;
}
.file{
	width:30%;
	border-bottom:1px solid #bbb;
}
.mode{
	width: 10%;
	border-left:1px solid #bbb;
	border-bottom:1px solid #bbb;
}
.save{
	border-left:1px solid #bbb;
	border-bottom:1px solid #bbb;
	width:10%;
}
.save:hover{
	cursor: pointer;
}
.shortcuts{
	border-left:1px solid #bbb;
	border-bottom:1px solid #bbb;
	width:50%;   
}
@media only screen and (max-width:830px) {
	.file, .shortcuts, .mode{
		display: none;
	}
	.save{
		width: 100%;
	}
}
#snackbar {
    visibility: hidden;
    min-width: 250px;
    margin-left: -125px;
    background-color: #009688;
    color: #fff;
    text-align: center;
    border-radius: 20px;
    padding: 5px;
    position: fixed;
    z-index: 50;
    left: 50%;
    bottom: 30px;
}
#snackbar.show {
    visibility: visible;
    -webkit-animation: fadein 0.5s, fadeout 0.5s 0.5s;
    animation: fadein 0.5s, fadeout 0.5s 0.5s;
}
@-webkit-keyframes fadein {
    from {bottom: 0; opacity: 0;} 
    to {bottom: 30px; opacity: 1;}
}
@keyframes fadein {
    from {bottom: 0; opacity: 0;}
    to {bottom: 30px; opacity: 1;}
}
@-webkit-keyframes fadeout {
    from {bottom: 30px; opacity: 1;} 
    to {bottom: 0; opacity: 0;}
}
@keyframes fadeout {
    from {bottom: 30px; opacity: 1;}
    to {bottom: 0; opacity: 0;}
}
</style>
<div style='background-color:#eee;'><div class='info file'>$name</div><div class='info mode' id='mode'>text</div><div class='info shortcuts'>Save: <strong>Ctrl+S</strong> Settings: <strong>Ctrl+Q</strong> View Shortcuts: <strong>Ctrl+Alt+H</strong></div><div class='info save' onclick='save()'><strong>Save</strong></div></div>

<pre id='editor'>$content</pre>
<div id='snackbar'>Saved Successfully</div>

<script src='//ajaxorg.github.io/ace-builds/src-min-noconflict/ace.js'></script>
<script src='//ajaxorg.github.io/ace-builds/src-min-noconflict/ext-language_tools.js'></script>
<script src='//ajaxorg.github.io/ace-builds/src-min-noconflict/ext-settings_menu.js'></script>

<script>
	var editor = ace.edit('editor');
	var dom = ace.require('ace/lib/dom');
	ace.require('ace/ext/language_tools');
	ace.require('ace/ext/settings_menu').init(editor);

	ext = '$ext';
	mode = 'text';
	switch(ext){
		case 'html' : mode='html';break;
		case 'php' : mode='php';break;
		case 'css' : mode='css';break;
		case 'js' : mode='javascript';break;
		case 'md' : mode='markdown';break;
		case 'xml' : mode='xml';break;
		default : mode='text';break;
	}
	editor.session.setMode('ace/mode/'+mode);
	editor.setTheme('ace/theme/tomorrow_night');
	editor.setAutoScrollEditorIntoView(true);
	editor.setShowPrintMargin(false);
    editor.\$blockScrolling = Infinity;
    document.getElementById('editor').style.fontSize='14px';

	//editor.setValue('');

	var fullScreen = dom.toggleCssClass(document.body, 'fullScreen')
	dom.setCssClass(editor.container, 'fullScreen', fullScreen)
	editor.setAutoScrollEditorIntoView(!fullScreen)
	editor.resize()

	editor.setOptions({
		enableBasicAutocompletion: true,
		enableSnippets: true,
		enableLiveAutocompletion: false
	});

	editor.commands.addCommands([{
		name: 'showSettingsMenu',
		bindKey: {win: 'Ctrl-q', mac: 'Command-q'},
		exec: function(editor) {
			editor.showSettingsMenu();
		},
		readOnly: true
	}]);
	editor.commands.addCommands([{
		name: 'save',
		bindKey: {win: 'Ctrl-s', mac: 'Command-s'},
		exec: function(editor) {
			save();
		},
		readOnly: true
	}]);
	editor.commands.addCommand({
		name: 'showKeyboardShortcuts',
		bindKey: {win: 'Ctrl-Alt-h', mac: 'Command-Alt-h'},
		exec: function(editor) {
			ace.config.loadModule('ace/ext/keybinding_menu', function(module) {
				module.init(editor);
				editor.showKeyboardShortcuts()
			})
		}
	})
	editor.commands.addCommand({
		name: 'toggleWordWrap',
		bindKey: {win: 'Alt-z', mac: 'Alt-z'},
		exec: function(editor) {
			if(editor.session.getUseWrapMode())
				editor.getSession().setUseWrapMode(false);
			else
				editor.getSession().setUseWrapMode(true);
		}
	})
	editor.session.on('changeMode', function(){
		mode = editor.session.\$modeId;
		mode = mode.substr(mode.lastIndexOf('/') + 1);
		//alert(mode);
		document.getElementById('mode').innerHTML = mode;
	})

	function save(){
		content = editor.getValue();
		content = encodeURIComponent(content);
		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200){
				toast();
			}
		};
		//xhttp.open('POST', '<?php echo basename(__FILE__);?>', true);
		xhttp.open('POST', '$url', true);
		xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		xhttp.send('content=' + content);
	}
	function toast() {
		var x = document.getElementById('snackbar');
		x.className = 'show';
		setTimeout(function(){ x.className = x.className.replace('show', ''); }, 1000);
	}
</script>
							";

						}
						else{
							$GLOBALS['echo'] .= "<form method='post' action='?act=edit&file=".urlencode($file)."'><div class='block'><div class='block-header'>Editing <u>".htmlspecialchars(realpath($file))."</u><span style='float:right;margin:3px;'>".($saveOk?"Saved":"Not saved")."</span><button type='submit' style='font-size:20px;float:right;'>Save</button></div>";
							$mime = explode( '/',@mime_content_type($file));
							if($mime[0] == "text"){
								$content = htmlspecialchars(file_get_contents($file));
								$GLOBALS['echo'] .= "<div class='block-body'><textarea autofocus name='content' style='width:100%;height:500px;font-size:20px;'>$content</textarea></div></div></form>";
							}
						}
					}
				break;
				case "logout":
					unlink("hb_session.conf.php");
					setcookie("hb_session","",(time()-3600));
					setcookie("editor","",(time()-3600));
					header('location:'.$_SERVER['PHP_SELF']);
					exit();
				break;
			}
		$GLOBALS['echo'] .= "</td></tr></table>";
	}
	elseif(isset($_POST['pwd'])){//Login
		http_response_code(200);
		if($_POST['pwd'] == $conf['password']){
			$session_cookie = mt_rand(10000,99999);
			if (isset($_POST['editor'])){
                $editor = 1;
            }
            else{
                $editor = 0;
            }
			if(file_exists("hb_session.conf.php")){
				require_once("hb_session.conf.php");
				if(!empty(COOKIE)) $session_cookie = COOKIE;
				if(!empty(EDITOR)) $editor = EDITOR;
			}
			$sfile = fopen('hb_session.conf.php','w');
			if( fwrite($sfile, "<?php\n/*\nThis is a temporary data file used by HostBrowser.\nDeleting or modifiying this will log you out from HostBrowser.\n*/\nconst COOKIE = '".$session_cookie."';\nconst EDITOR = '".$editor."';?>")){
				setcookie('hb_session', $session_cookie, time()+2592000);
				setcookie('editor', $editor, time()+2592000);
				$time = time();
				while(time()-$time == 2){}
				header('location: '.(isset($_POST['returnto'])?$_POST['returnto']:$_SERVER['PHP_SELF']));
				exit();
			}
			else{
				throwError("Login failed due to some internal server problem.");
			}
		}
		else throwError("Login failed because you typed a <b style='color:red;'>wrong</b> password.<br><br><form method='post'><label>Password: <input type='password' name='pwd' autofocus></label><br><br><input type='submit' value='Submit'><input type='hidden' name='returnto' value='".(isset($_POST['returnto'])?$_POST['returnto']:$_SERVER['PHP_SELF']."?path=.")."'></form>");
	}
	else{
		$returnto = substr(URL,0,7)=="http://"?URL:(substr(URL,0,8)=="https://"?URL:(substr(URL,0,3)=="://"?"http".URL:"http://".URL));
		$GLOBALS['echo'] .=	"<center><div class='block' style='width:500px;margin-top:60px;'><div class='block-header'>HostBrowser ".V."</div><div class='block-body'><form method='post'><label>Password: <input type='password' name='pwd' autofocus></label><br><br><label><input type='checkbox' name='editor' value='true'> Advanced TextEditor <font size='2'>(network needed)</font></label><br><br><input type='submit' value='Submit'><input type='hidden' name='returnto' value='$returnto'></form></div></div></center>";

	}

    $GLOBALS['echo'] .= "<footer><ul class='inline-menu'><li><a href='".$_SERVER['PHP_SELF']."?path=.'>Home</a></li><li>|</li><li><a href='https://twitter.com/SnehanshuPhukon' title='Follow me on Twitter' target='_blank'>@SnehanshuPhukon</a></li><li>|</li><li><a href='https://twitter.com/iAnupamSaikia' title='Follow me on Twitter' target='_blank'>@iAnupamSaikia</a></li><li>|</li><li><a href='https://github.com/SnehanshuPhukon/HostBrowser' title='HostBrowser on GitHub' target='_blank'>GitHub</a></li><li>|</li><li>Page generated in ".(time()-$start_time)." sec</li></ul></footer></body></html>";

echo $GLOBALS['echo'];

//functions
function deleteObj($obj){
	if(is_dir($obj)){
		$GLOBALS['echo'] .= "<div style='width:100%;max-height:500px;overflow:scroll;padding:5px;background-color:white;s'><pre>";
		$it = new RecursiveDirectoryIterator($obj, RecursiveDirectoryIterator::SKIP_DOTS);
		$files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
		
		foreach($files as $file){
			if($file->isDir()){
				$rpath = $file->getRealPath();
				if(rmdir($rpath)) $GLOBALS['echo'] .= "Successfully deleted folder <u>$rpath</u>\n";
				else $GLOBALS['echo'] .= "<span style='color:red;'>Failed to delete folder <u>$rpath</u></span>\n";
			}
			else{
				$rpath = $file->getRealPath();
				if(unlink($rpath)) $GLOBALS['echo'] .= "Successfully deleted file <u>$rpath</u>\n";
				else $GLOBALS['echo'] .= "<span style='color:red;'>Failed to delete file <u>$rpath</u></span>\n";
			}
		}
		$GLOBALS['echo'] .= "</pre></div>";
		
		if(rmdir($obj)) return true;
		else return false;
	}
	else{
		if(unlink($obj)) return true;
		else return false;
	}
}
function copyDir($src,$dst){
	if(file_exists($src)){
		$ok = true;
		$dir = opendir($src);
		$dst = $dst.'/'.basename($src);
		
		if (!@mkdir($dst)) $ok = false;
		
		while(false !== ($file = readdir($dir))){
			if($file != '.' && $file != '..'){
				if(is_dir($src.'/'.$file)){
					if(!copyDir($src.'/'.$file, $dst)) $ok = false;
				}
				else{
					if(!copy($src.'/'.$file, $dst.'/'.$file)) $ok = false;
				}
			}
		}
		closedir($dir);
		return $ok;
	}else return false;
}
function foldersize($dir){
	if(is_dir($dir)){
		$count_size = 0;
		$count = 0;
		$dir_array = scandir($dir);
		foreach($dir_array as $key=>$filename){
			if($filename != '.' && $filename != '..'){
				if(is_dir($dir.'/'.$filename)){
					$new_foldersize = foldersize($dir.'/'.$filename);
					$count_size += $new_foldersize;
				}
				else{
					$count_size += filesize($dir.'/'.$filename);
					$count++;
				}
			}
		}
		return $count_size;
	}else return false;
}
function goodSize($size){
	if($size < 0){
		return "> 1 GB";
	}
	elseif($size<1024){
		return round($size)." B";
	}
	elseif($size>=1024 && $size <(1024*1024) ){
		$size = $size/1024;
		return round($size)." KB";
	}
	elseif($size>=(1024*1024) && $size <(1024*1024*1024) ){
		$size = $size/(1024*1024);
		return round($size)." MB";
	}
	elseif($size>=(1024*1024*1024) && $size <(1024*1024*1024*1024) ){
		$size = $size/(1024*1024*1024);
		return round($size)." GB";
	}
}
function isLogged(){
	if(isset($_COOKIE['hb_session'])){
		if(file_exists("hb_session.conf.php")){
			require_once("hb_session.conf.php");
			if($_COOKIE['hb_session'] == COOKIE) return true;
			else return false;
		}else return false;
	}else return false;
}
function throwError($err){
	msg("An Error occurred!", $err);
}
function msg($header,$body){
	$GLOBALS['echo'] .= "<center><div class='block' style='width:600px;'><div class='block-header'>$header</div><div class='block-body'>$body<br><br><a href='javascript:history.go(-1);'>&larr; Return to previous page</a></div></div></center>";
}
function isUpperDir($path){
	if(preg_match("/^[.\\/]+$/", $path)) return true;
	else return false;
}
function sanitizePath($p){
	$p .= substr($p,(strlen($p)-1),1)!='/'?(is_dir($p)?'/':''):'';
	$p = str_replace("\\","/",$p);
	$p = preg_replace("/[\/]{2,}+/","/",$p);
	$p = preg_replace("/[.]{3,}+\//","..",$p);
	return $p;
}
//ZIP Functions
function zip($src, $zipname, $dest = ''){
	$dest .= '/';
	$z = new recurseZip;
	$newzip = $z->compress($src, $dest);
	if($newzip != false){
		rename($newzip, $dest.$zipname);
		return $dest.$zipname;
	}else return false;
}
class recurseZip{
	private function recurse_zip($src,&$zip,$path){
		$dir = opendir($src);
		while(false !== ( $file = readdir($dir)) ) {
			if (( $file != '.' ) && ( $file != '..' )) {
				if ( is_dir($src . '/' . $file) ) {
					$this->recurse_zip($src . '/' . $file,$zip,$path);
				}
				else {
					$zip->addFile($src . '/' . $file,substr($src . '/' . $file,$path));
				}
			}
		}
		closedir($dir);
	}

	public function compress($src,$dst=''){
		if(substr($src,-1)==='/'){$src=substr($src,0,-1);}
		if(substr($dst,-1)==='/'){$dst=substr($dst,0,-1);}
		$path=strlen(dirname($src).'/');
		$filename=substr($src,strrpos($src,'/')+1).'.zip';
		$dst=empty($dst)? $filename : $dst.'/'.$filename;
		@unlink($dst);
		$zip = new ZipArchive;
		$res = $zip->open($dst, ZipArchive::CREATE);
		if($res !== TRUE){
			echo 'Error: Unable to create zip file';
			exit;
		}
		if(is_file($src)){$zip->addFile($src,substr($src,$path));}
		else{
			if(!is_dir($src)){
				$zip->close();
				@unlink($dst);
				echo 'Error: File not found';
				exit;
			}
			$this->recurse_zip($src,$zip,$path);
		}
		$zip->close();
		return $dst;
	}
}
?>
