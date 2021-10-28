<!--

                ENDPOINT:


                        unBlur          -- > https://api.gotinder.com/v2/fast-match/teasers

                        myProfile       -- > https://api.gotinder.com/profile;

                        nextSuggestion  -- > https://api.gotinder.com/user/recs;

                        likeAll         -- > https://api.gotinder.com/like/ . $id;


 -->


<?php

/*
========================================================================================================
Funzioni Tinder
========================================================================================================

 */

function saveMyProfile()
{
    $endPoint = "https://api.gotinder.com/profile";
    $resp = getData($endPoint);
    $json = json_encode(array('data' => $resp));
    if (file_put_contents("myProfile.json", $json)) {
        echo "JSON Profile file created successfully...";
    } else {
        echo "Oops! Error creating json file...";
    }

}

function unBlur()
{
    $endPoint = "https://api.gotinder.com/v2/fast-match/teasers";
    $resp = getData($endPoint);
    $users = ($resp["data"]["results"]);
    $url = [];
    foreach ($users as $user) {
        array_push($url, $user["user"]["photos"][0]["url"]);
    }
    print_r($url);
}

function nextSuggestion()
{
    $endPoint = "https://api.gotinder.com/user/recs";
    $resp = getData($endPoint);
    $count = count($resp["results"]);
    $id = [];
    for ($i = 0; $i < $count; $i++) {
        array_push($id, $resp["results"][$i]["_id"]);

    }
    $json = json_encode(array($id));
    saveData($json);
}

// Cicla l'array con l'id per mandare il link
function likeAll()
{
    $json = file_get_contents("idNextSuggestion.json");
    $json_data = json_decode($json, true);
    $count = 0;

    foreach ($json_data as $key => $ids) {
        foreach ($ids as $key => $id) {
            $endPoint = "https://api.gotinder.com/like/" . $id;
            $resp = getData($endPoint);
            echo $count . "-";
            $count++;
        }
    }

    unlink("idNextSuggestion.json");
}

function like50()
{
    $json = file_get_contents("idNextSuggestion.json");
    $json_data = json_decode($json, true);
    $count = 0;

    if($count % 2 == 0){
        foreach ($json_data as $key => $ids) {
            foreach ($ids as $key => $id) {
                $endPoint = "https://api.gotinder.com/like/" . $id;
                $resp = getData($endPoint);
                echo $count . "-";
                $count++;
            }
        }
    }

    unlink("idNextSuggestion.json");
}

/*
========================================================================================================
                    Funzioni generiche
========================================================================================================

 */

// Richiesta cURL

function getData($endPoint)
{

    $tinderAPIToken = file_get_contents("token");
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $endPoint,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_POSTFIELDS => "",
        CURLOPT_HTTPHEADER => [
            "Content-type: application/json",
            "User-Agent: Tinder/3.0.4 (iPhone; iOS 7.1; Scale/2.00)",
            "X-Auth-Token:" . $tinderAPIToken,
            "app_version: 3",
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        print_r("cURL Error #:" . $err);
    } else {
        $resp = (json_decode($response, true));
        return $resp;
    }

}

// Salvo i dati

function saveData($json)
{
    if (file_put_contents("idNextSuggestion.json", $json, FILE_APPEND)) {
        echo "JSON Profile file created successfully...";
    } else {
        echo "Oops! Error creating json file...";
    }

}


