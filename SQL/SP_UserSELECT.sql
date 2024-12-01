USE COLLEGE;
DROP PROCEDURE IF EXISTS COLLEGE.SP_USER_SELECT;
CREATE PROCEDURE COLLEGE.`SP_USER_SELECT`(a_USER_ID INT)
BEGIN
    IF(a_USER_ID != 0) THEN
        SELECT USER_ID, USER_NAME, USER_LOC, USER_EMAIL, 
          DATE_FORMAT(USER_DOB, '%d/%m/%Y') AS USER_DOB,
          USER_TYPE, ACTIVE FROM USER_MAST 
        WHERE USER_ID = a_USER_ID;
    ELSE
        SELECT USER_ID, USER_NAME, USER_LOC, USER_EMAIL, 
          DATE_FORMAT(USER_DOB, '%d/%m/%Y') AS USER_DOB,
          USER_TYPE, ACTIVE FROM USER_MAST 
        ORDER BY USER_ID DESC;
    END IF;
END;
