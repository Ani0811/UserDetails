USE COLLEGE;
DROP PROCEDURE IF EXISTS COLLEGE.SP_USER_INSERT_XML;
CREATE PROCEDURE COLLEGE.`SP_USER_INSERT_XML`(sXML LONGTEXT)
BEGIN
  DECLARE iCount INT DEFAULT 1;
  DECLARE iRowCount INT DEFAULT 0;
  
  SET iRowCount = EXTRACTVALUE(sXML, 'COUNT(//USER//ROWS)');
  
  WHILE iCount <= iRowCount DO
    INSERT INTO USER_MAST 
              (
                USER_NAME, USER_LOC, USER_EMAIL, USER_DOB,
                USER_TYPE, ACTIVE, USER_PASSWORD
              )
              VALUES
              (
                TRIM(EXTRACTVALUE(sXML, '//ROWS[$iCount]//USER_NAME')),
                TRIM(EXTRACTVALUE(sXML, '//ROWS[$iCount]//USER_LOC')),
                TRIM(EXTRACTVALUE(sXML, '//ROWS[$iCount]//USER_EMAIL')),
                TRIM(STR_TO_DATE(EXTRACTVALUE(sXML, '//ROWS[$iCount]//USER_DOB'), '%d/%m/%Y')),
                TRIM(EXTRACTVALUE(sXML, '//ROWS[$iCount]//USER_TYPE')),
                TRIM(EXTRACTVALUE(sXML, '//ROWS[$iCount]//USER_ACT')),
                TRIM(EXTRACTVALUE(sXML, '//ROWS[$iCount]//USER_PWD'))
              );
    
    SET iCount = iCount + 1;
  END WHILE;
  SELECT ROW_COUNT();
END;