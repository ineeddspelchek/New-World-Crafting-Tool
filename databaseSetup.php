<?php 

// Sources:
// https://stackoverflow.com/questions/247304/what-data-type-to-use-for-hashed-password-field-and-what-length
// https://dba.stackexchange.com/questions/43053/is-it-possible-to-force-drop-of-mysql-tables-with-fk

include('connect-db.php');

//GIVE RYAN PERMISSIONS
query("grant all on han5jn to 'rtn8fwq'@'%';");

//PRINT ALL GRANTS I AM ABLE TO SEE
echo var_dump(query("show grants;"));

query("SET FOREIGN_KEY_CHECKS = 0;");
query("SET UNIQUE_CHECKS = 0;");

query("drop table if exists recipe;");
query("drop table if exists item;");
query("drop table if exists input;");
query("drop table if exists output;");
query("drop table if exists player;");
query("drop table if exists bonus;");
query("drop table if exists owns_item;");
query("drop table if exists has_bonus;");
query("drop table if exists bonus_affects;");
query("drop table if exists in_player_history;");

query("SET FOREIGN_KEY_CHECKS = 1;");
query("SET UNIQUE_CHECKS = 1;");

query("CREATE TABLE recipe(
    id INT AUTO_INCREMENT,
    PRIMARY KEY(id));
");

query("CREATE TABLE item(
    name VARCHAR(50), 
    unitPrice FLOAT,
    PRIMARY KEY(name));
");

query("CREATE TABLE input(
    recipeID INT REFERENCES recipe(id), 
    itemName VARCHAR(50) REFERENCES item(name) ON DELETE CASCADE, 
    quantity INT,
    PRIMARY KEY(recipeID, itemName));    
");

query("CREATE TABLE output(
    recipeID INT REFERENCES recipe(id), 
    itemName VARCHAR(50) REFERENCES item(name) ON DELETE CASCADE, 
    quantity INT,
    PRIMARY KEY(recipeID, itemName));    
");

query("CREATE TABLE player(
    email VARCHAR(500),
    password TEXT,
    darkModeOn BOOLEAN,
    PRIMARY KEY(email));    
");

query("CREATE TABLE bonus(
    id INT AUTO_INCREMENT,
    name TEXT,
    percentage FLOAT,
    PRIMARY KEY(id));    
");

query("CREATE TABLE owns_item(
    playerEmail VARCHAR(500) REFERENCES player(email) ON DELETE CASCADE,
    itemName VARCHAR(50) REFERENCES item(name) ON DELETE CASCADE, 
    quantity INT CHECK (quantity > 0),
    PRIMARY KEY(playerEmail, itemName));    
");

query("CREATE TABLE has_bonus(
    playerEmail VARCHAR(500) REFERENCES player(email) ON DELETE CASCADE,
    bonusID INT REFERENCES bonus(id) ON DELETE CASCADE, 
    PRIMARY KEY(playerEmail, bonusID));    
");

query("CREATE TABLE bonus_affects(
    bonusID INT REFERENCES bonus(id) ON DELETE CASCADE, 
    recipeID INT REFERENCES recipe(id) ON DELETE CASCADE, 
    PRIMARY KEY(bonusID, recipeID));    
");

query("CREATE TABLE in_player_history(
    playerEmail VARCHAR(500) REFERENCES player(email) ON DELETE CASCADE,
    recipeID INT REFERENCES recipe(id) ON DELETE CASCADE,
    relativeTimeCreated INT AUTO_INCREMENT,
    quantity INT,
    PRIMARY KEY(relativeTimeCreated));    
");

query("INSERT INTO item VALUES ('Asmodeum', 39.64)");
query("INSERT INTO item VALUES ('Charcoal', 0.08)");
query("INSERT INTO item VALUES ('Cinnabar', 4.3)");
query("INSERT INTO item VALUES ('Flux', 0.18)");
query("INSERT INTO item VALUES ('Gold Ingot', 0.74)");
query("INSERT INTO item VALUES ('Gold Ore', 0.03)");
query("INSERT INTO item VALUES ('Iron Ingot', 0.36)");
query("INSERT INTO item VALUES ('Iron Ore', 0.08)");
query("INSERT INTO item VALUES ('Mythril Ingot', 17.58)");
query("INSERT INTO item VALUES ('Mythril Ore', 0.11)");
query("INSERT INTO item VALUES ('Obsidian Flux', 0.18)");
query("INSERT INTO item VALUES ('Orichalcum Ingot', 9.02)");
query("INSERT INTO item VALUES ('Orichalcum Ore', 0.7)");
query("INSERT INTO item VALUES ('Platinum Ingot', 3.3)");
query("INSERT INTO item VALUES ('Platinum Ore', 0.3)");
query("INSERT INTO item VALUES ('Prismatic Ingot', 190)");
query("INSERT INTO item VALUES ('Silver Ingot', 0.56)");
query("INSERT INTO item VALUES ('Silver Ore', 0.03)");
query("INSERT INTO item VALUES ('Starmetal Ingot', 4.8)");
query("INSERT INTO item VALUES ('Starmetal Ore', 0.2)");
query("INSERT INTO item VALUES ('Steel Ingot', 0.86)");
query("INSERT INTO item VALUES ('Tolvium', 4)");



query("INSERT INTO recipe VALUES (1)");
query("INSERT INTO input VALUES (1, 'Asmodeum', 1)");
query("INSERT INTO input VALUES (1, 'Mythril Ingot', 10)");
query("INSERT INTO input VALUES (1, 'Charcoal', 4)");
query("INSERT INTO input VALUES (1, 'Flux', 4)");
query("INSERT INTO output VALUES (1, 'Prismatic Ingot', 1)");

query("INSERT INTO recipe VALUES (2)");
query("INSERT INTO input VALUES (2, 'Mythril Ore', 12)");
query("INSERT INTO input VALUES (2, 'Orichalcum Ingot', 2)");
query("INSERT INTO input VALUES (2, 'Charcoal', 2)");
query("INSERT INTO input VALUES (2, 'Flux', 1)");
query("INSERT INTO output VALUES (2, 'Mythril Ingot', 1)");

query("INSERT INTO recipe VALUES (3)");
query("INSERT INTO input VALUES (3, 'Orichalcum Ingot', 5)");
query("INSERT INTO input VALUES (3, 'Tolvium', 1)");
query("INSERT INTO input VALUES (3, 'Cinnabar', 1)");
query("INSERT INTO input VALUES (3, 'Obsidian Flux', 1)");
query("INSERT INTO input VALUES (3, 'Charcoal', 2)");
query("INSERT INTO output VALUES (3, 'Asmodeum', 1)");

query("INSERT INTO recipe VALUES (4)");
query("INSERT INTO input VALUES (4, 'Orichalcum Ore', 8)");
query("INSERT INTO input VALUES (4, 'Starmetal Ingot', 2)");
query("INSERT INTO input VALUES (4, 'Charcoal', 2)");
query("INSERT INTO input VALUES (4, 'Flux', 1)");
query("INSERT INTO output VALUES (4, 'Orichalcum Ingot', 1)");

query("INSERT INTO recipe VALUES (5)");
query("INSERT INTO input VALUES (5, 'Starmetal Ore', 6)");
query("INSERT INTO input VALUES (5, 'Steel Ingot', 2)");
query("INSERT INTO input VALUES (5, 'Charcoal', 2)");
query("INSERT INTO input VALUES (5, 'Flux', 1)");
query("INSERT INTO output VALUES (5, 'Starmetal Ingot', 1)");

query("INSERT INTO recipe VALUES (6)");
query("INSERT INTO input VALUES (6, 'Iron Ingot', 3)");
query("INSERT INTO input VALUES (6, 'Charcoal', 2)");
query("INSERT INTO input VALUES (6, 'Flux', 1)");
query("INSERT INTO output VALUES (6, 'Steel Ingot', 1)");

query("INSERT INTO recipe VALUES (7)");
query("INSERT INTO input VALUES (7, 'Iron Ore', 4)");
query("INSERT INTO output VALUES (7, 'Iron Ingot', 1)");

query("INSERT INTO recipe VALUES (8)");
query("INSERT INTO input VALUES (8, 'Silver Ore', 4)");
query("INSERT INTO output VALUES (8, 'Silver Ingot', 1)");

query("INSERT INTO recipe VALUES (9)");
query("INSERT INTO input VALUES (9, 'Gold Ore', 5)");
query("INSERT INTO input VALUES (9, 'Silver Ingot', 2)");
query("INSERT INTO input VALUES (9, 'Flux', 1)");
query("INSERT INTO output VALUES (9, 'Gold Ingot', 1)");

query("INSERT INTO recipe VALUES (10)");
query("INSERT INTO input VALUES (10, 'Platinum Ore', 6)");
query("INSERT INTO input VALUES (10, 'Gold Ingot', 2)");
query("INSERT INTO input VALUES (10, 'Flux', 1)");
query("INSERT INTO output VALUES (10, 'Platinum Ingot', 1)");

query("INSERT INTO bonus (name, percentage) VALUES ('Smelter\'s Headgear', 0.02)");
query("INSERT INTO bonus (name, percentage) VALUES ('Smelter\'s Smock', 0.02)");
query("INSERT INTO bonus (name, percentage) VALUES ('Smelter\'s Mitts', 0.02)");
query("INSERT INTO bonus (name, percentage) VALUES ('Smelter\'s Pants', 0.02)");
query("INSERT INTO bonus (name, percentage) VALUES ('Smelter\'s Shoes', 0.02)");

query("INSERT INTO bonus_affects (bonusID, recipeID) VALUES (1, 1)");
query("INSERT INTO bonus_affects (bonusID, recipeID) VALUES (2, 1)");
query("INSERT INTO bonus_affects (bonusID, recipeID) VALUES (3, 1)");
query("INSERT INTO bonus_affects (bonusID, recipeID) VALUES (4, 1)");
query("INSERT INTO bonus_affects (bonusID, recipeID) VALUES (5, 1)");

query("INSERT INTO bonus_affects (bonusID, recipeID) VALUES (1, 2)");
query("INSERT INTO bonus_affects (bonusID, recipeID) VALUES (2, 2)");
query("INSERT INTO bonus_affects (bonusID, recipeID) VALUES (3, 2)");
query("INSERT INTO bonus_affects (bonusID, recipeID) VALUES (4, 2)");
query("INSERT INTO bonus_affects (bonusID, recipeID) VALUES (5, 2)");

query("INSERT INTO bonus_affects (bonusID, recipeID) VALUES (1, 3)");
query("INSERT INTO bonus_affects (bonusID, recipeID) VALUES (2, 3)");
query("INSERT INTO bonus_affects (bonusID, recipeID) VALUES (3, 3)");
query("INSERT INTO bonus_affects (bonusID, recipeID) VALUES (4, 3)");
query("INSERT INTO bonus_affects (bonusID, recipeID) VALUES (5, 3)");

query("INSERT INTO bonus_affects (bonusID, recipeID) VALUES (1, 4)");
query("INSERT INTO bonus_affects (bonusID, recipeID) VALUES (2, 4)");
query("INSERT INTO bonus_affects (bonusID, recipeID) VALUES (3, 4)");
query("INSERT INTO bonus_affects (bonusID, recipeID) VALUES (4, 4)");
query("INSERT INTO bonus_affects (bonusID, recipeID) VALUES (5, 4)");

query("INSERT INTO bonus_affects (bonusID, recipeID) VALUES (1, 5)");
query("INSERT INTO bonus_affects (bonusID, recipeID) VALUES (2, 5)");
query("INSERT INTO bonus_affects (bonusID, recipeID) VALUES (3, 5)");
query("INSERT INTO bonus_affects (bonusID, recipeID) VALUES (4, 5)");
query("INSERT INTO bonus_affects (bonusID, recipeID) VALUES (5, 5)");

query("INSERT INTO bonus_affects (bonusID, recipeID) VALUES (1, 6)");
query("INSERT INTO bonus_affects (bonusID, recipeID) VALUES (2, 6)");
query("INSERT INTO bonus_affects (bonusID, recipeID) VALUES (3, 6)");
query("INSERT INTO bonus_affects (bonusID, recipeID) VALUES (4, 6)");
query("INSERT INTO bonus_affects (bonusID, recipeID) VALUES (5, 6)");

query("INSERT INTO bonus_affects (bonusID, recipeID) VALUES (1, 7)");
query("INSERT INTO bonus_affects (bonusID, recipeID) VALUES (2, 7)");
query("INSERT INTO bonus_affects (bonusID, recipeID) VALUES (3, 7)");
query("INSERT INTO bonus_affects (bonusID, recipeID) VALUES (4, 7)");
query("INSERT INTO bonus_affects (bonusID, recipeID) VALUES (5, 7)");

query("INSERT INTO bonus_affects (bonusID, recipeID) VALUES (1, 8)");
query("INSERT INTO bonus_affects (bonusID, recipeID) VALUES (2, 8)");
query("INSERT INTO bonus_affects (bonusID, recipeID) VALUES (3, 8)");
query("INSERT INTO bonus_affects (bonusID, recipeID) VALUES (4, 8)");
query("INSERT INTO bonus_affects (bonusID, recipeID) VALUES (5, 8)");

query("INSERT INTO bonus_affects (bonusID, recipeID) VALUES (1, 9)");
query("INSERT INTO bonus_affects (bonusID, recipeID) VALUES (2, 9)");
query("INSERT INTO bonus_affects (bonusID, recipeID) VALUES (3, 9)");
query("INSERT INTO bonus_affects (bonusID, recipeID) VALUES (4, 9)");
query("INSERT INTO bonus_affects (bonusID, recipeID) VALUES (5, 9)");

query("INSERT INTO bonus_affects (bonusID, recipeID) VALUES (1, 10)");
query("INSERT INTO bonus_affects (bonusID, recipeID) VALUES (2, 10)");
query("INSERT INTO bonus_affects (bonusID, recipeID) VALUES (3, 10)");
query("INSERT INTO bonus_affects (bonusID, recipeID) VALUES (4, 10)");
query("INSERT INTO bonus_affects (bonusID, recipeID) VALUES (5, 10)");

?>