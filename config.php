<?php
    error_reporting(0);
    session_start();
    function getConnection()
    {                
        $host = null; $db_name = null; $username = null; $password = null; $port = null; 
        $ConfigXML = null; $conn = null; $WITH_XML_SP = null;
        try
        {
            $ConfigXML = simplexml_load_file("Config/Config.xml");
            if ($ConfigXML === false) 
            {
                echo "Failed to load Config XML file.";  
            } 
            else 
            {
                $host = (string)$ConfigXML->HOST;
                $db_name = (string)$ConfigXML->DBNAME;
                $username = (string)$ConfigXML->USERNAME;
                $password = (string)$ConfigXML->PASSWORD;
                $port = (string)$ConfigXML->PORT;
                $WITH_XML_SP = (string)$ConfigXML->WITHXML_SP;
                $_SESSION["WITH_XML_SP"] = $WITH_XML_SP;

                $conn = new mysqli($host, $username, $password, $db_name, $port);
                if ($conn->connect_error)
                {
                    die("Connection failed: ". $conn -> connect_error);
                }
            }            
        }
        catch(Exception $e)  { echo $e->getMessage(); }
        finally{ $host = null; $db_name = null; $username = null; $password = null; $port = null; $ConfigXML = null; $WITH_XML_SP = null;}
        return $conn;
    }