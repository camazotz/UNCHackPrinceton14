<?php
    ini_set('display_errors', '1');
    error_reporting(E_ALL);

    $con = mysqli_connect("sql4.freemysqlhosting.net", "sql458246", "gG6*tV8!", 
    "sql458246");

    if (mysqli_connect_errno())
    {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }

    // Temporary values for now
  /*  $upvote = 0;
    $link = "hi";
    $Xcoord = "yo";
    $Ycoord = "sup";*/

    // Return values
    if ($_SERVER['HTTP_METHOD'] === 'getValues'){
        
        //just return some test values
        $extractFromSavedFiles = "SELECT * FROM savedfiles";
        $extractQuery = mysqli_query($con, $extractFromSavedFiles);

        $json = array();
        if ($extractQuery->num_rows != 0)
        {
            while ($row = $extractQuery->fetch_array())
            {
                $json[]= array(
                    'id' => $row[0],
                    'Link' => $row[1],
                    'Latitude' => $row[2],
                    'Longitude' => $row[3],
                    'Upvote' => $row[4],
                    'Type' => $row[5],
                    'Caption' => $row[6]
                );
            }

            header('Content-Type: application/json; charset=utf-8');

            $jsonString = json_encode($json);
            echo $jsonString;
         }
    }

    else if ($_SERVER['HTTP_METHOD'] === 'postValues'){ 
       $body;
       /*Sometimes the body data is attached in raw form and is not attached 
       to $_POST, this needs to be handled*/
       if($_POST == null){
          $handle  = fopen('php://input', 'r');
          $rawData = fgets($handle);
          $body = json_decode($rawData);
       }
       else{
          $body == $_POST;
       }

       $linkValue = $body->{'Link'};
       $Xcoord = $body->{'Latitude'};
       $Ycoord = $body->{'Longitude'};
       $upvote = $body->{'Upvote'};
       $type = $body->{'Type'};
       $caption = $body->{'Caption'};
       $username = $body->{'Username'};

       $checkifPresent = "SELECT * FROM savedfiles WHERE Link = '$linkValue'";
       $checkifPresentQuery = mysqli_query($con, $checkifPresent);
       $rowCheckifPresentQuery = $checkifPresentQuery->fetch_array();

       if ($checkifPresentQuery->num_rows != 0)
       {
          //Updating database Upvote count
          $updateUpvote = "UPDATE savedfiles SET Upvote = '$upvote' WHERE Link = '$linkValue'";

          if (!mysqli_query($con,$updateUpvote))
          {
            die('Error: ' . mysqli_error($con));
          }

          $getfileid = $rowCheckifPresentQuery[1];

          $getuserid = "SELECT id FROM Users WHERE Username = '$username'";
          $getuseridQuery = mysqli_query($con, $getuserid);
          $rowGetuseridQuery = $getuseridQuery->fetch_array();

          if ($getuseridQuery->num_rows != 0)
          {
             $updateUserSeen = "INSERT INTO UserFileSeen (Userid, Fileid)
             VALUES ('$rowGetuseridQuery[1]', '$getfileid')";

             if (!mysqli_query($con,$updateUserSeen))
             {
                die('Error: ' . mysqli_error($con));
             }
          }
          
       }

       else
       {
            // Inserting into the database
            $addToSavedFiles = "INSERT INTO savedfiles (Link, Latitude, Longitude, Upvote, Type, Caption)
            VALUES ('$linkValue', '$Xcoord', '$Ycoord', '$upvote', '$type', '$caption')";


            if (!mysqli_query($con,$addToSavedFiles))
            {
                die('Error: ' . mysqli_error($con));
            }

            $getfileid = "SELECT id FROM savedfiles WHERE Link = '$linkValue'";
            $getfileidQuery = mysqli_query($con, $getfileid);
            $rowGetfileidQuery = $getfileidQuery->fetch_array();

            $getuserid = "SELECT id FROM Users WHERE Username = '$username'";
            $getuseridQuery = mysqli_query($con, $getuserid);
            $rowGetuseridQuery = $getuseridQuery->fetch_array();

            if ($getuseridQuery->num_rows != 0)
            {
                $updateUserSeen = "INSERT INTO UserFileSeen (Userid, Fileid)
                VALUES ('$rowGetuseridQuery[1]', '$rowGetfileidQuery[1]')";

                if (!mysqli_query($con,$updateUserSeen))
                {
                    die('Error: ' . mysqli_error($con));
                }
            }
       }

    }

    else if ($_SERVER['HTTP_METHOD'] === 'postRegister')
    {
       $body;
       /*Sometimes the body data is attached in raw form and is not attached 
       to $_POST, this needs to be handled*/
       if($_POST == null){
          $handle  = fopen('php://input', 'r');
          $rawData = fgets($handle);
          $body = json_decode($rawData);
       }
       else{
          $body == $_POST;
       }

       $username = $body->{'Username'};
       $password = $body->{'Password'};

       $registration = "INSERT INTO Users (Username, Password)
       VALUES ('$username', '$password')";

       if (!mysqli_query($con,$registration))
       {
            die('Error: ' . mysqli_error($con));
       }

    }

    else if ($_SERVER['HTTP_METHOD'] === 'getIfUserSeen')
    {
        $body;
       /*Sometimes the body data is attached in raw form and is not attached 
       to $_POST, this needs to be handled*/
       if($_POST == null){
          $handle  = fopen('php://input', 'r');
          $rawData = fgets($handle);
          $body = json_decode($rawData);
       }
       else{
          $body == $_POST;
       }

       $username = $body->{'Username'};
       $linkValue = $body->{'Link'};

       $getuserid = "SELECT id FROM Users WHERE Username = '$username'";
       $getfileid = "SELECT id FROM savedfiles WHERE Link = '$linkValue'";

       $getuseridQuery = mysqli_query($con, $getuserid);
       $getfileidQuery = mysqli_query($con, $getfileid);
       $rowGetuseridQuery = $getuseridQuery->fetch_array();
       $rowGetfileidQuery = $getfileidQuery->fetch_array();

       $checkifSeen = "SELECT Userid FROM UserFileSeen WHERE Userid = '$rowGetuseridQuery[1]' AND Fileid = '$rowGetfileidQuery[1]'";
       $checkifSeenQuery = mysqli_query($con, $checkifSeen);

       if ($checkifSeenQuery->num_rows != 0)
       {
           echo json_encode("no");
       }

       else
       {
           echo json_encode("update");
       }
    }

    else if ($_SERVER['HTTP_METHOD'] === 'getLogin')
    {
        $body;
       /*Sometimes the body data is attached in raw form and is not attached 
       to $_POST, this needs to be handled*/
       if($_POST == null){
          $handle  = fopen('php://input', 'r');
          $rawData = fgets($handle);
          $body = json_decode($rawData);
       }
       else{
          $body == $_POST;
       }

       $username = $body->{'Username'};
       $password = $body->{'Password'};

       $getUserInfo = "SELECT * From Users Where Username = '$username'";
       $getUserInfoQuery = mysqli_query($con, $getUserInfo);
       $row = $getUserInfoQuery->fetch_array();

       if ($getUserInfoQuery->num_rows != 0)
       {
           
           if ($row[2] == $password)
           {
               echo json_encode("yes");
           }

           else
           {
               echo json_encode("no");
           }
       }

       else
       {
           echo json_encode("no");
       }
    }

    else {
       $data['error'] = 'The Service you asked for was not recognized';
       echo json_encode($data);
    }

?>