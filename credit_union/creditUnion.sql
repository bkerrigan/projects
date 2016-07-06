/*
* Create a MySQL table named:
*   Members
* The columns of the table should include:
*   memberNumber (unique),
*   firstName,
*   lastName,
*   phone,
*   email (unique)
*
* Please choose what you think would be appropriate data types, keys, and
* any constraints
*/
CREATE  TABLE Members (
    memberNumber INT NOT NULL AUTO_INCREMENT,
    firstName    varchar(50),
    lastName     varchar(50),
    phone        varchar(25),
    email        varchar(255),
    PRIMARY KEY (email),
    CONSTRAINT member UNIQUE (memberNumber, email)
)

/*
* Create a MySQL table named:
*   Accounts
* The columns of the table should include:
*   accountNumber (unique),
*   memberNumber,
*   balance
* Please choose what you think would be appropriate data types, keys, and
* any constraints
*/
CREATE  TABLE Accounts (
    accountNumber INT NOT NULL AUTO_INCREMENT,
    memberNumber  INT NOT NULL,
    balance       DECIMAL(9,2),
    PRIMARY KEY (memberNumber),
    UNIQUE (accountNumber)
)
