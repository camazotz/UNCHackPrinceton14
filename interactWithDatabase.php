<?php
    ini_set('display_errors', '1');
    error_reporting(E_ALL);

    // Connect to database
    $con = mysqli_connect("http://orgfree.freewebhostingarea.com/pma/", "unchackprinceton.orgfree.com", "ripvanwinkle", 
    "895543");


    if (mysqli_connect_errno())
    {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }


    // $link = Need iOS data
    // $Xcoord = Need iOS data 
    // $Ycoord = Need iOS data

    // Temporary values for now
    $upvote = 0;
    $link = "hi";
    $Xcoord = "yo";
    $Ycoord = "sup";

    // Inserting into the database
    $addToSavedFiles = "INSERT INTO savedfiles (Link, Xcoord, Ycoord, Upvote)
            VALUES ('$link', '$Xcoord', '$Ycoord', '$upvote')";
    
  
    if (!mysqli_query($con,$addToSavedFiles))
    {
        die('Error: ' . mysqli_error($con));
    }

    // Extract link from database that is within a certain x/y range of coordinate values
    // Precondition - iOS app must initialize $Xcoord and $Ycoord with the desired radius value from the user's current position
    $extractLinkFromSavedFiles = "SELECT Link FROM savedfiles WHERE Xcoord <= '$Xcoord' AND Ycoord <= '$Ycoord'";

    if (!mysqli_query($con,$extractLinkFromSavedFiles))
    {
        die('Error: ' . mysqli_error($con));
    }

    // Update the number of upvotes for a particular item by matching it with that item's link
    // Precondition - iOS app must initialize $link with the dropbox link (which serves as a key)
    $updateUpvoteSavedFiles = "UPDATE Upvote FROM savedfiles WHERE Link = '$link'";

    if (!mysqli_query($con,$updateUpvoteSavedFiles))
    {
        die('Error: ' . mysqli_error($con));
    }


    mysqli_close($con);
/>