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

       $checkifPresent = "SELECT Link FROM savedfiles WHERE Link = '$linkValue'";
       $checkifPresentQuery = mysqli_query($con, $checkifPresent);

       if ($checkifPresentQuery->num_rows != 0)
       {
          //Updating database Upvote count
          $updateUpvote = "UPDATE savedfiles SET Upvote = '$upvote' WHERE Link = '$linkValue'";

          if (!mysqli_query($con,$updateUpvote))
          {
            die('Error: ' . mysqli_error($con));
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
       }

    }

    else {
       $data['error'] = 'The Service you asked for was not recognized';
       echo json_encode($data);
    }

?>