$(document).ready(function()
{
    Display_Mode();
    Insert_Mode();
    GetData();
    Update_Mode();
    Delete_Mode();
})

function Display_Mode()
{
    $.ajax(
    {
        url: 'Display.php',
        method: 'post',
        success: function(data)
        {
            data = $.parseJSON(data);
            if(data.status == 'success')
            {
                $('#Grid_table').html(data.html);
            }
        }
    })    
}

function ValidateEmail(sEmail)
{
    var pattern = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/; 
    var b_valid = pattern.test(sEmail);
    return b_valid;
}
function rowHighlight(row)
{
    $(".highlighted").removeClass("highlighted");
    $(row).addClass("highlighted");
}

function get_focus_after_modal(str_msg, str_element)
{
    $('#message').html(str_msg);
    $('#msgDialog').modal('show').on('hidden.bs.modal', function() {
        $('#' + str_element).focus();
    });
}

function ValidateForm_Element(mVar, eElementID)
{
    let message = '';
    if((mVar == '') || (mVar.length == 0) || (mVar.toUpperCase() == '-SELECT-'))
    {
        switch(eElementID)
        {
            case 'username':
                message = "Username cannot be left blank! Enter username.";
                break;
            case 'loc':
                message = "Location cannot be left blank! Enter location.";
                break;
            case 'email':
                    message = "eMail address cannot be left blank ! Enter proper eMail address.";
                break;
            case 'DOB':
                message = "Date Of Birth cannot be left blank ! Enter proper Date Of Birth.";
                break;
            case 'uType':
                message = "Select User Type!";
                break;
            case 'uActive':
                message = "Select Active Status!";
                break;
            default:
                message = "This field cannot be blank";
                break;
        }
        get_focus_after_modal(message, eElementID); return false;
    }
    else if((eElementID == 'email') && (mVar.length > 0))
    {
        if(ValidateEmail(mVar) == false)
        {
            message = "Invalid eMail address ! Enter proper eMail address.";
            get_focus_after_modal(message, eElementID); return false;
        }
        else {return true;}
    }
    else {return true;}
}

function Validate_Form(s_objVal_1, s_objVal_2, s_objVal_3,
                        s_objVal_4, s_objVal_5, s_objVal_6)
{
    var b_Valid = false;
    if(ValidateForm_Element(s_objVal_1, 'username') == true)
    {
        if(ValidateForm_Element(s_objVal_2, 'loc') == true)
        {
            if(ValidateForm_Element(s_objVal_3, 'email') == true)
            {
                if(ValidateForm_Element(s_objVal_4, 'DOB') == true)
                {
                    if(ValidateForm_Element(s_objVal_5, 'uType') == true)
                    {
                        if(ValidateForm_Element(s_objVal_6, 'uActive') == true)
                        {
                            b_Valid = true;
                        }
                    }
                }
            }
        }
    }
    return b_Valid;
}
function GetData()
{
    var mUSER_ID = null;
    $(document).on('click','#ulnk',function()
    {
        mUSER_ID = $(this).attr('data-id');
        $.ajax(
        {
            url: 'getData.php',
            method: 'post',
            data:{U_ID: mUSER_ID},
            dataType: 'JSON',
            success: function(data)
            {
                {
                    $('#h_USER_ID').val(data[0]);
                    $('#username').val(data[1]);
                    $('#username').prop("readonly", true);
                    //$("#username").attr("readonly","readonly");
                    $('#loc').val(data[2]);
                    $('#email').val(data[3]);
                    $('#DOB').val(data[4]);
                    $('#uType').val(data[5]);
                    $('#uActive').val(data[6]);
                    $("#btn_AddUser").prop("disabled", true);
                    $("#btn_UpdateUser").prop("disabled", false);
                    $("#btn_DeleteUser").prop("disabled", false);
                }
            }
        });
    });
    mUSER_ID = null;
}

function Insert_Mode()
{
    var mUSERNAME = null; var mLOC = null; var mEMAIL = null;
    var mDOB = null; var mUTYPE = null; var mACT = null; var mPWD = null;

    $(document).on('click','#btn_AddUser',function(ev)
    {
        ev.preventDefault();
        ev.stopPropagation();

        mUSERNAME = $('#username').val();
        mLOC = $('#loc').val();
        mEMAIL = $('#email').val();
        mDOB = $('#DOB').val();
        mUTYPE = $('#uType').val();
        mACT = $('#uActive').val();
        mPWD = $('#password').val();
        if(mPWD.length == 0){mPWD = 'null';}

        if(Validate_Form(mUSERNAME, mLOC, mEMAIL, mDOB, mUTYPE, mACT) == true)
        {
            $('#confirmInsert').modal('show');

            $('#btn_InsertNo').on('click', function()
            {
                $('#confirmInsert').modal('hide');
            });    
                                        
            $('#btn_InsertYes').off().on('click', function()
            {
                $.ajax(
                {
                    url: 'AddUser.php',
                    method: 'post',
                    data: {UNAME: mUSERNAME, ULOC: mLOC, UEMAIL: mEMAIL, UDOB: mDOB, UTYPE: mUTYPE, UACT: mACT, UPWD: mPWD},
                    cache:false,
                    success: function(data)
                    {
                        $('#message').html(data);
                        $('#msgDialog').modal('show');
                        
                        $('form').trigger('reset');
                        Display_Mode();

                        $("#btn_AddUser").prop("disabled", false);
                        $("#btn_UpdateUser").prop("disabled", true);
                        $("#btn_DeleteUser").prop("disabled", true);
    
                        $('#confirmInsert').modal('hide');
                    }
                });
            });
        }
    });
    mUSERNAME = null; mLOC = null; mEMAIL = null;
    mDOB = null; mUTYPE = null; mACT = null; mPWD = null;
}

function Update_Mode()
{
    var mUSER_ID = null; var mLOC = null; var mEMAIL = null; 
    var mDOB = null; var mUTYPE = null; var mACT = null; var mPWD = null;

    $(document).on('click', '#btn_UpdateUser', function()
    {
        mUSER_ID = $('#h_USER_ID').val();
        mLOC = $('#loc').val();
        mEMAIL = $('#email').val();
        mDOB = $('#DOB').val();
        mUTYPE = $('#uType').val();
        mACT = $('#uActive').val();
        mPWD = $('#password').val();
        if(mPWD.length == 0){mPWD = 'null';}

        if(Validate_Form(mUSER_ID, mLOC, mEMAIL, mDOB, mUTYPE, mACT) == true)
        {
            $('#confirmUpdate').modal('show');

            $('#btn_UpdateNo').on('click', function()
            {
                $('#confirmUpdate').modal('hide');
            });
            
            $('#btn_UpdateYes').on('click', function()
            {
                $.ajax(
                {
                    url:'UpdateUser.php',
                    method: 'post',
                    data: { U_ID: mUSER_ID, ULOC: mLOC, UEMAIL: mEMAIL, UDOB: mDOB, UTYPE: mUTYPE, UACT: mACT, UPWD: mPWD },
                    success: function(data)
                    {
                        $('#message').html(data);
                        $('#msgDialog').modal('show');
                        
                        $('form').trigger('reset');
                        Display_Mode();
                        
                        $('#username').prop("readonly", false);
                        $("#btn_AddUser").prop("disabled", false);
                        $("#btn_UpdateUser").prop("disabled", true);
                        $("#btn_DeleteUser").prop("disabled", true);

                        $('#confirmUpdate').modal('hide');
                    }
                });
            });
        }
    });
    mUSER_ID = null; mLOC = null; mEMAIL = null; 
    mDOB = null; mUTYPE = null; mACT = null; mPWD = null;
}

function Delete_Mode()
{
    var mUserID = null;

    $(document).on('click', '#btn_DeleteUser', function()
    {
        mUserID = $('#h_USER_ID').val();

        $('#confirmDelete').modal('show');

        $('#btn_DeleteNo').on('click', function()
        {
            $('#confirmDelete').modal('hide');
        });

        $('#btn_DeleteYes').on('click', function()
        {
            $.ajax(
            {
                url: 'DeleteUser.php',
                method: 'post',
                data:{U_ID: mUserID},
                success: function(data)
                {
                    $('#message').html(data);
                    $('#msgDialog').modal('show');
                    
                    $('form').trigger('reset');
                    Display_Mode();

                    $('#username').prop("readonly", false);
                    $("#btn_AddUser").prop("disabled", false);
                    $("#btn_UpdateUser").prop("disabled", true);
                    $("#btn_DeleteUser").prop("disabled", true);

                    $('#confirmDelete').modal('hide');
                }
            });
        });
    });
    mUserID = null;
}