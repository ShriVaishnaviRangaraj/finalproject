<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <title>File Upload</title>
    <style>
        body{
            margin:0px;
            padding: 0px;
            overflow: hidden;
        }
        img{
            width:100%;
            height: 601px;
            margin-bottom:0px;
            padding:0px;
            /* opacity: 0.7; */
        }
       #bannerContent{
            position:absolute;
            padding-top:4%;
            padding-bottom:4%;
            margin-top:-35%;
            margin-bottom:12%; 
            margin-left:32%;
            background-color:rgba(245, 235, 235, 0.719);
            width:500px;
        }
    </style>
</head>
<body>
    <img src="https://images.unsplash.com/photo-1582816441253-6a9a623bc3f0?ixid=MnwxMjA3fDB8MHxzZWFyY2h8MjJ8fGNsZWFuaW5nJTIwY29tcGFueXxlbnwwfHwwfHw%3D&ixlib=rb-1.2.1&w=1000&q=80" alt="Cleaning.jpg" />
    <center>
        <div id=bannerContent>
            <h1>GET ALL GEOCODES</h1>
            <h5>Generate data and Send it to Corporation.</h5>
            <br>
            <?php
            require "vendor/autoload.php";
            require 'includes/PHPMailer.php';
	        require 'includes/SMTP.php';
	        require 'includes/Exception.php';

            use PhpOffice\PhpSpreadsheet\Spreadsheet;
            use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
            use PHPMailer\PHPMailer\PHPMailer;
	        use PHPMailer\PHPMailer\SMTP;
	        use PHPMailer\PHPMailer\Exception;

            if(isset($_POST['send'])) 
            {
            
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setCellValue('A1','DATE :');
                $sheet->setCellValue('B1',strval(date("d-m-Y")));
                $sheet->setCellValue('B3','LATITUDE , LONGITUDE');
                $sheet->setCellValue('E1','To Get The Best Route to The Location Search With The Latitude & Longitude In Google Map :)');
                $sheet->setCellValue('G3','ADDRESS');

                function get_image_location($image = '')
                {
                    $exif = exif_read_data($image, 0, true);
                    if($exif && isset($exif['GPS']))
                    {
                        $GPSLatitudeRef = $exif['GPS']['GPSLatitudeRef'];
                        $GPSLatitude    = $exif['GPS']['GPSLatitude'];
                        $GPSLongitudeRef= $exif['GPS']['GPSLongitudeRef'];
                        $GPSLongitude   = $exif['GPS']['GPSLongitude'];
                        
                        $lat_degrees = count($GPSLatitude) > 0 ? gps2Num($GPSLatitude[0]) : 0;
                        $lat_minutes = count($GPSLatitude) > 1 ? gps2Num($GPSLatitude[1]) : 0;
                        $lat_seconds = count($GPSLatitude) > 2 ? gps2Num($GPSLatitude[2]) : 0;
                        
                        $lon_degrees = count($GPSLongitude) > 0 ? gps2Num($GPSLongitude[0]) : 0;
                        $lon_minutes = count($GPSLongitude) > 1 ? gps2Num($GPSLongitude[1]) : 0;
                        $lon_seconds = count($GPSLongitude) > 2 ? gps2Num($GPSLongitude[2]) : 0;
                        
                        $lat_direction = ($GPSLatitudeRef == 'W' or $GPSLatitudeRef == 'S') ? -1 : 1;
                        $lon_direction = ($GPSLongitudeRef == 'W' or $GPSLongitudeRef == 'S') ? -1 : 1;
                        
                        $latitude = $lat_direction * ($lat_degrees + ($lat_minutes / 60) + ($lat_seconds / (60*60)));
                        $longitude = $lon_direction * ($lon_degrees + ($lon_minutes / 60) + ($lon_seconds / (60*60)));

                        return array('latitude'=>$latitude, 'longitude'=>$longitude);
                    }
                    else
                    {
                        return false;
                    }
                }

                function sendEmail()
                {
                    //Create instance of PHPMailer
                    $mail = new PHPMailer();
                    //Set mailer to use smtp
                    $mail->isSMTP();
                    //Define smtp host
                    $mail->Host = "smtp.gmail.com";
                    //Enable smtp authentication
                    $mail->SMTPAuth = true;
                    //Set smtp encryption type (ssl/tls)
                    $mail->SMTPSecure = "tls";
                    //Port to connect smtp
                    $mail->Port = "587";
                    //Set gmail username
                    $mail->Username = "trashcollector.host@gmail.com";
                    //Set gmail password
                    $mail->Password = "TrashAdmin@07";
                    //Email subject
                    $mail->Subject = "Garbage Complaint reg.";
                    //Set sender email
                    $mail->setFrom('trashcollector.host@gmail.com');
                    //Enable HTML
                    $mail->isHTML(true);
                    //Attachment
                    $mail->addAttachment('C:/xampp/htdocs/trashCollector/'.strval(date("d-m-Y")).'.xlsx');
                    //Email body
                    $mail->Body = "<h1>Please Find the Attachment below</h1></br><p>This is today's file of Complaints</p>";
                    //Add recipient
                    $mail->addAddress('vaishnavi160900@gmail.com');
                    //Finally send email
                    if($mail->send()) 
                        echo '<h5 style="color:Green; font-family:cursive"> File Sent </h5>'; 
                    else
                    {
                        echo '<h5 style="color:Red; font-family:cursive"> Message could not be sent. Mailer Error </h5> ';
                    }
                    //Closing smtp connection
                    $mail->smtpClose();
                }

                function gps2Num($coordPart)
                {
                    $parts = explode('/', $coordPart);
                    if(count($parts) <= 0)
                    {
                        return 0;
                    }
                    if(count($parts) == 1)
                    {
                        return $parts[0];
                    }
                    return floatval($parts[0]) / floatval($parts[1]);
                }

                $directory ="G:/My Drive/Attachments-Trash";
                $images = glob($directory . "/*.jpg");
                $flag=0;
                $SNo=0;
                $CurrVal=5;
                $set = new \Ds\Set();
                foreach($images as $image)
                {
                    $filedate=strval(date("d-m-Y",filemtime($image)));
                    $currdate=strval(date("d-m-Y"));

                    

                    if($filedate==$currdate)
                    {
                        $flag=1;
                        
                        $imgLocation = get_image_location($image);
                        if(!empty($imgLocation))
                        {
                            $imgLat = $imgLocation['latitude'];
                            $imgLng = $imgLocation['longitude'];
                            //echo $imgLat;

                            $curl = curl_init('https://us1.locationiq.com/v1/reverse.php?key=pk.a362c2ec6329eebcc272579161eb2750&lat='.$imgLat.'&lon='.$imgLng.'&format=json');

                            curl_setopt_array($curl, array(
                            CURLOPT_RETURNTRANSFER    =>  true,
                            CURLOPT_FOLLOWLOCATION    =>  true,
                            CURLOPT_MAXREDIRS         =>  10,
                            CURLOPT_TIMEOUT           =>  30,
                            CURLOPT_CUSTOMREQUEST     =>  'GET',
                            ));

                            $response = curl_exec($curl);
                            $data=json_decode($response,true);
                            $err = curl_error($curl);
                            curl_close($curl);

                            if($err) 
                            {
                                echo "cURL Error #:" . $err;
                            }

                            //echo $filedate.'/';

                            $latLong=$imgLat.','.$imgLng;
                            if(!($set->contains($latLong)))
                            {
                                $sheet->setCellValue('A'.$CurrVal,++$SNo.'. ');
                                $sheet->setCellValue('B'.$CurrVal,$latLong);
                                $sheet->setCellValue('G'.$CurrVal,$data['display_name']);
                                $CurrVal++;
                                $set->add($latLong);
                            }
                        }
                        // else
                        //     echo 'GeoTags not found';
                    }
                
                    
                }

                // $writer = new Xlsx($spreadsheet);
                // $writer->save(strval(date("d-m-Y")).'.xlsx');

                if($flag==0)
                {
                    echo '<h5 style="color:Blue; font-family:cursive"> No Complaints Today </h5>';
                }
                else
                {
                    $writer = new Xlsx($spreadsheet);
                    $writer->save(strval(date("d-m-Y")).'.xlsx');
                    sendEmail(); 
                }
            }
            ?>
            <form method="post">
                <input type="submit" class="btn btn-primary" name="send" value="Generate and Send" />
            </form>
        </div>
    </center>
</body>  
</html>