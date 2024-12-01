<?php
    require_once("config.php");
    $conn = null; $result = null; $data = null;
    function DisplayMode()
    {
       $str_Grid = null; $NoOfRows = 0; 
       try
       {
            $str_Grid = '<table id="tbl-grid" class="table table-hover">
                <thead>
                    <th style="background-color:indianred; color:beige">User Name</th>
                    <th style="background-color:indianred; color:beige">Location</th>
                    <th style="background-color:indianred; color:beige">eMail Address</th>
                    <th style="background-color:indianred; color:beige">Date of Birth</th>
                    <th style="background-color:indianred; color:beige">User Type</th>
                    <th style="background-color:indianred; color:beige">Active</th>
                </thead><tbody>';

            $conn = getConnection();
            $result = $conn->query("CALL SP_USER_SELECT(0);");
            $data = $result->fetch_all(MYSQLI_ASSOC);            
            if (!$result) {
                echo json_encode(['status' => 'error', 'message' => 'Query failed: '. mysqli_error($conn) ]);
                exit;
            }
            if($data)
            {
                $NoOfRows = $result -> num_rows;
                foreach($data as $row)
                {
                    $str_Grid .= '<tr  onclick="rowHighlight(this)">
                                    <td><a id="ulnk" data-id="' . $row['USER_ID'] . '" href=# >' . htmlspecialchars($row['USER_NAME']) . '</a></td>
                                    <td>' . htmlspecialchars($row['USER_LOC']) . '</td>
                                    <td>' . htmlspecialchars($row['USER_EMAIL']) . '</td>
                                    <td>' . htmlspecialchars($row['USER_DOB']) . '</td>
                                    <td>' . htmlspecialchars($row['USER_TYPE']) . '</td>
                                    <td>' . '<input type="checkbox" id=Chk_' . htmlspecialchars($row['USER_ID']) . ' title=Chk_' . htmlspecialchars($row['USER_ID']) . ' name="uActive" onclick="return false;" onkeydown="return false;" ';
                                    if($row['ACTIVE'] == 'Yes')
                                    {
                                        $str_Grid .= 'Value="1"  checked>';
                                    }
                                    else
                                    {
                                        $str_Grid .= 'Value="0">';
                                    }
                                    $str_Grid .= '</td>
                                </tr>';
                }
            }
            else
            {
                $str_Grid .= '<tr>
                                <td colspan="7" style="text-align: center;"><span>No Records Found!</span></td>
                            </tr>';
            }
            $str_Grid .= '</tbody></table>';
            $conn->close();
       }
       catch(Exception $e)  { ;}
       finally{ $conn = null; $result = null; $data = null; session_unset();}
       echo json_encode(['status' => 'success', 'html' => $str_Grid]);
    }
    function GetData() 
    {
        $conn = null; $UserID = null; $User_Data = [];
        try
        {
            $UserID = $_POST['U_ID'];
            if (empty($UserID)) {
                echo json_encode(['error' => 'UserID is missing.']);
                exit;
            }

            $conn = getConnection();
            $result = $conn->query("CALL SP_USER_SELECT(" . $UserID . ");");
            if (!$result) {
                echo json_encode(['status' => 'error', 'message' => 'Query failed: '. mysqli_error($conn) ]);
                exit;
            }
            if ($row = mysqli_fetch_assoc($result))
            {
                $User_Data[0] = $row['USER_ID'];
                $User_Data[1] = $row['USER_NAME'];
                $User_Data[2] = $row['USER_LOC'];
                $User_Data[3] = $row['USER_EMAIL'];
                $User_Data[4] = $row['USER_DOB'];
                $User_Data[5] = $row['USER_TYPE'];
                $User_Data[6] = $row['ACTIVE'];
            } else 
            {
                echo json_encode(['error' => 'No user found with the provided ID.']);
                exit;
            }
        }
        catch(Exception $e){;}
        finally{ $conn = null; $result = null; $UserID = null; session_unset();}
        echo json_encode($User_Data);
    } 
    function InsertMode()
    {
        $conn = null; $stmt = null; $b_Success = false; $s_DataXml = null; $Is_WITH_XML_SP = null;
        try
        {
            $conn = getConnection();

            $User_Name = $_POST['UNAME'];
            $UserLOC = $_POST['ULOC'];
            $UserEmail = $_POST['UEMAIL'];
            $UserDOB = $_POST['UDOB'];
            $UserType = $_POST['UTYPE'];
            $UserActive = $_POST['UACT'];
            $UserPwd = password_hash($_POST['UPWD'], PASSWORD_DEFAULT);
            
            $Is_WITH_XML_SP = ($_SESSION["WITH_XML_SP"] == "Y" ? "Y" : "N");

            if($Is_WITH_XML_SP == "Y")
            {
                $s_DataXml = '<USER><ROWS>';
                $s_DataXml .= '<USER_NAME>' . $User_Name . '</USER_NAME>';
                $s_DataXml .= '<USER_LOC>' . $UserLOC . '</USER_LOC>';
                $s_DataXml .= '<USER_EMAIL>' . $UserEmail . '</USER_EMAIL>';
                $s_DataXml .= '<USER_DOB>' . $UserDOB . '</USER_DOB>';
                $s_DataXml .= '<USER_TYPE>' . $UserType . '</USER_TYPE>';
                $s_DataXml .= '<USER_ACT>' . $UserActive . '</USER_ACT>';
                $s_DataXml .= '<USER_PWD>' . $UserPwd . '</USER_PWD>';
                $s_DataXml .= '</ROWS></USER>';

                $stmt = $conn -> prepare("CALL SP_USER_INSERT_XML(?);"); 
                $stmt -> bind_param("s", $s_DataXml);
            }
            else if($Is_WITH_XML_SP == "N")
            {
                $stmt = $conn -> prepare("CALL SP_USER_INSERT(?, ?, ?, ?, ?, ?, ?);"); 
                $stmt -> bind_param("sssssss", $User_Name, $UserLOC, $UserEmail, $UserDOB, $UserType, $UserActive, $UserPwd);           
            }
            if($stmt)
            {
                if ($stmt -> execute()) { $b_Success = true; }
                if ($b_Success) { echo 'This record has been inserted successfully.'; } 
                else { echo 'Insertion of this record failed: ' . mysqli_error($conn); }
            }
            $stmt -> close(); $conn -> close();
       }
       catch(Exception $e) { ;}
       finally{ $conn = null; $stmt = null; $s_DataXml = null; $Is_WITH_XML_SP = null; session_unset(); }
       return $b_Success;
    }
    function UpdateMode()
    {
        $conn = null; $stmt = null; $USER_ID = null; $s_DataXml = null;  $Is_WITH_XML_SP = null; 
        $b_Success = false; 
        try
        {
            $conn = getConnection();

            $USER_ID = $_POST['U_ID'];
            $UserLOC = $_POST['ULOC'];
            $UserEmail = $_POST['UEMAIL'];
            $UserDOB = $_POST['UDOB'];
            $UserType = $_POST['UTYPE'];
            $UserActive = $_POST['UACT'];
            $UserPwd = password_hash($_POST['UPWD'], PASSWORD_DEFAULT);

            $Is_WITH_XML_SP = ($_SESSION["WITH_XML_SP"] == "Y" ? "Y" : "N");

            if($Is_WITH_XML_SP == "Y")
            {
                $s_DataXml = '<USER><ROWS>';
                $s_DataXml .= '<USER_LOC>' . $UserLOC . '</USER_LOC>';
                $s_DataXml .= '<USER_EMAIL>' . $UserEmail . '</USER_EMAIL>';
                $s_DataXml .= '<USER_DOB>' . $UserDOB . '</USER_DOB>';
                $s_DataXml .= '<USER_TYPE>' . $UserType . '</USER_TYPE>';
                $s_DataXml .= '<USER_ACT>' . $UserActive . '</USER_ACT>';
                $s_DataXml .= '<USER_PWD>' . $UserPwd . '</USER_PWD>';
                $s_DataXml .= '<USER_ID>' . $USER_ID . '</USER_ID>';
                $s_DataXml .= '</ROWS></USER>';

                $stmt = $conn -> prepare("CALL SP_USER_UPDATE_XML(?);"); 
                $stmt -> bind_param("s", $s_DataXml);

            }
            else if($Is_WITH_XML_SP == "N")
            {
                $stmt = $conn -> prepare("CALL SP_USER_UPDATE(?, ?, ?, ?, ?, ?, ?);");
                $stmt -> bind_param("ssssssi",  $UserLOC, $UserEmail, $UserDOB, $UserType, $UserActive, $UserPwd, $USER_ID);
            }
            if($stmt)
            {
                if ($stmt -> execute()) { $b_Success = true; }
                if ($b_Success) { echo 'This record has been updated successfully.'; } 
                else { echo 'Updation of this record failed: ' . mysqli_error($conn); }
            }                                                                                  
            $stmt -> close(); $conn -> close();
        }
        catch(Exception $e)
        { 
            echo 'Updation of this record failed: ' . $e->getMessage();
        }
        finally{ $conn = null; $stmt = null; $USER_ID = null; $s_DataXml = null; $Is_WITH_XML_SP = null; session_unset();}
        return $b_Success;
    }

    function DeleteMode()
    {
        $conn = null; $stmt = null;
        $b_Success = false; $USER_ID = null;
        try
        {
            $conn = getConnection();
            //echo json_encode(['status' => 'success', 'html' => $s_DataXML]);

            $USER_ID = $_POST['U_ID'];
            $stmt = $conn -> prepare("CALL SP_USER_DELETE(?)");
            if($stmt)
            {
                $stmt -> bind_param("i", $USER_ID);
                if ($stmt -> execute()) { $b_Success = true; }
                if ($b_Success) { echo 'This record has been deleted successfully.'; } 
                else { echo 'Deletion of this record failed: ' . mysqli_error($conn); }
            }
            $stmt -> close(); $conn -> close();
        }
        catch(Exception $e){;}
        finally{ $conn = null; $stmt = null; $USER_ID = null; session_unset();}
        return $b_Success;
    }    
?>