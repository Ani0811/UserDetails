USE COLLEGE;
DROP PROCEDURE IF EXISTS COLLEGE.SP_USER_INSERT;
CREATE PROCEDURE COLLEGE.`SP_USER_INSERT`( 
										s_USER_NAME VARCHAR(30), 
										s_USER_LOC VARCHAR(30), 
										s_USER_EMAIL VARCHAR(50), 
										s_USER_DOB VARCHAR(10),
										s_USER_TYPE VARCHAR(10), 
										s_USER_ACTIVE VARCHAR(5), 
										s_USER_PASSWORD LONGTEXT)
BEGIN
  INSERT INTO USER_MAST 
  (
    USER_NAME, USER_LOC, USER_EMAIL, USER_DOB,
    USER_TYPE, ACTIVE, USER_PASSWORD
  )
  VALUES
  (
    TRIM(s_USER_NAME),
    TRIM(s_USER_LOC),
    TRIM(s_USER_EMAIL),
    TRIM(STR_TO_DATE(s_USER_DOB, '%d/%m/%Y')),
    TRIM(s_USER_TYPE),
    TRIM(s_USER_ACTIVE),
    TRIM(s_USER_PASSWORD)
  );

  SELECT ROW_COUNT(); 
END;
