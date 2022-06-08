DROP DATABASE IF EXISTS hackademy;
CREATE DATABASE hackademy;
USE hackademy;

CREATE TABLE stack (
    id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    name VARCHAR(100) NOT NULL
);

CREATE TABLE language (
    id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    name VARCHAR(80) NOT NULL
);

CREATE TABLE training (
    id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    title VARCHAR(255) NOT NULL,
    price FLOAT NOT NULL,
    description TEXT NOT NULL,
    duration INT NOT NULL,
    date_start DATE,
    date_end DATE,
    max_students INT NOT NULL,
    stack_id INT,
    CONSTRAINT fk_training_stackid FOREIGN KEY (stack_id) REFERENCES stack(id)
);

CREATE TABLE image (
    id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    url VARCHAR(100) NOT NULL,
    training_id INT NOT NULL,
    CONSTRAINT fk_image_trainingid FOREIGN KEY (training_id) REFERENCES training(id)
);

CREATE TABLE language_training (
    training_id INT NOT NULL,
    CONSTRAINT fk_language_training_trainingid FOREIGN KEY (training_id) REFERENCES training(id),
    language_id INT NOT NULL,
    CONSTRAINT fk_language_training_languageid FOREIGN KEY (language_id) REFERENCES language(id)
);

CREATE TABLE user (
    id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    firstname VARCHAR(80) NOT NULL,
    lastname VARCHAR(80) NOT NULL,
    email VARCHAR(255) NOT NULL,
    pswd VARCHAR(32) NOT NULL,
    address VARCHAR(255),
    is_admin BOOLEAN,
    is_archived BOOLEAN
);

CREATE TABLE training_user (
    user_id INT NOT NULL,
    CONSTRAINT fk_training_user_userid FOREIGN KEY (user_id) REFERENCES user(id),
    training_id INT NOT NULL,
    CONSTRAINT fk_training_user_trainingid FOREIGN KEY (training_id) REFERENCES training(id)
);

CREATE TABLE comment (
    id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    user_id INT NOT NULL,
    CONSTRAINT fk_comment_userid FOREIGN KEY (user_id) REFERENCES user(id),
    training_id INT NOT NULL,
    CONSTRAINT fk_comment_trainingid FOREIGN KEY (training_id) REFERENCES training(id)
);

CREATE TABLE wishlist (
    id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    user_id INT NOT NULL,
    CONSTRAINT fk_wishlist_userid FOREIGN KEY (user_id) REFERENCES user(id),
    training_id INT NOT NULL,
    CONSTRAINT fk_wishlist_trainingid FOREIGN KEY (training_id) REFERENCES training(id)
);

CREATE TABLE invoice (
    id INT PRIMARY KEY AUTO_INCREMENT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total FLOAT NOT NULL,
    user_id INT NOT NULL,
    CONSTRAINT fk_invoice_userid FOREIGN KEY (user_id) REFERENCES user(id)
);

CREATE TABLE invoice_training (
    invoice_id INT NOT NULL,
    CONSTRAINT fk_invoice_training_invoiceid FOREIGN KEY (invoice_id) REFERENCES invoice(id),
    training_id INT NOT NULL,
    CONSTRAINT fk_invoice_training_trainingid FOREIGN KEY (training_id) REFERENCES training(id)
);