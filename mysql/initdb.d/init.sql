CREATE TABLE test_db.users (
    id              INT         NOT NULL AUTO_INCREMENT,
    user_name       VARCHAR(20) NOT NULL,
    user_password   VARCHAR(128) NOT NULL,
    PRIMARY KEY (id)
);

CREATE TABLE test_db.todo (
    id              INT         NOT NULL AUTO_INCREMENT,
    user_id       VARCHAR(20) NOT NULL,
    title       VARCHAR(20) NOT NULL,
    main_text   VARCHAR(128) NOT NULL,
    PRIMARY KEY (id)
);